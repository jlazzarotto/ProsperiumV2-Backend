<?php

declare(strict_types=1);

namespace App\Identity\Application\Service;

use App\Company\Domain\Repository\CompanyRepositoryInterface;
use App\Company\Domain\Repository\EmpresaRepositoryInterface;
use App\Company\Domain\Repository\UnidadeNegocioRepositoryInterface;
use App\Identity\Application\DTO\CreateUserRequest;
use App\Identity\Application\DTO\UpdateUserProfilesRequest;
use App\Identity\Application\DTO\UpdateUserRequest;
use App\Identity\Domain\Entity\User;
use App\Identity\Domain\Entity\UserCompany;
use App\Identity\Domain\Entity\UserPerfil;
use App\Identity\Domain\Repository\PerfilRepositoryInterface;
use App\Identity\Domain\Repository\UserCompanyRepositoryInterface;
use App\Identity\Domain\Repository\UserEmpresaRepositoryInterface;
use App\Identity\Domain\Repository\UserPerfilRepositoryInterface;
use App\Identity\Domain\Repository\UserRepositoryInterface;
use App\Identity\Domain\Repository\UserUnidadeRepositoryInterface;
use App\Shared\Application\Validation\RequestValidator;
use App\Shared\Domain\Contract\TransactionRunnerInterface;
use App\Shared\Domain\Exception\ResourceNotFoundException;
use App\Shared\Domain\Exception\ValidationException;
use App\Shared\Infrastructure\Auditoria\AuditoriaLogger;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

final class UserService
{
    public function __construct(
        private readonly UserRepositoryInterface $userRepository,
        private readonly UserCompanyRepositoryInterface $userCompanyRepository,
        private readonly UserEmpresaRepositoryInterface $userEmpresaRepository,
        private readonly UserUnidadeRepositoryInterface $userUnidadeRepository,
        private readonly UserPerfilRepositoryInterface $userPerfilRepository,
        private readonly PerfilRepositoryInterface $perfilRepository,
        private readonly CompanyRepositoryInterface $companyRepository,
        private readonly EmpresaRepositoryInterface $empresaRepository,
        private readonly UnidadeNegocioRepositoryInterface $unidadeRepository,
        private readonly RequestValidator $requestValidator,
        private readonly TransactionRunnerInterface $transactionRunner,
        private readonly UserPasswordHasherInterface $passwordHasher,
        private readonly AuditoriaLogger $auditoriaLogger
    ) {
    }

    public function hasUsers(): bool
    {
        return $this->userRepository->countAll() > 0;
    }

    public function create(CreateUserRequest $request): User
    {
        $this->requestValidator->validate($request);
        $isBootstrap = !$this->hasUsers();

        if ($isBootstrap && $request->role !== User::ROLE_ROOT) {
            throw new ValidationException([
                'role' => ['O primeiro usuario do sistema deve ser ROLE_ROOT.'],
            ]);
        }

        if ($request->role === User::ROLE_ROOT) {
            return $this->createRootUser($request);
        }

        if ($request->companyId === null || $request->companyId <= 0) {
            throw new ValidationException([
                'companyId' => ['ROLE_ADMIN deve estar vinculado a pelo menos uma company.'],
            ]);
        }

        $company = $this->companyRepository->findById((int) $request->companyId);
        if ($company === null) {
            throw new ResourceNotFoundException('Company nao encontrada.');
        }

        $existingUser = $this->userRepository->findByEmailAndCompany($request->email, (int) $company->getId());
        if ($existingUser !== null) {
            throw new ValidationException([
                'email' => ['Email ja cadastrado nesta company.'],
            ]);
        }

        $empresas = [];
        foreach ($request->empresaIds as $empresaId) {
            $empresa = $this->empresaRepository->findById($empresaId);
            if ($empresa === null || $empresa->getCompany()->getId() !== $company->getId()) {
                throw new ValidationException([
                    'empresaIds' => ['Empresa invalida para a company informada.'],
                ]);
            }
            $empresas[$empresaId] = $empresa;
        }

        $unidades = [];
        foreach ($request->unidadeIds as $unidadeId) {
            $unidade = $this->unidadeRepository->findById($unidadeId);
            if ($unidade === null || $unidade->getCompany()->getId() !== $company->getId()) {
                throw new ValidationException([
                    'unidadeIds' => ['Unidade invalida para a company informada.'],
                ]);
            }
            $unidades[$unidadeId] = $unidade;
        }

        $profileCodes = $request->profileCodes !== [] ? array_values(array_unique($request->profileCodes)) : ['company_admin'];
        $perfis = $this->perfilRepository->findByCodigos($profileCodes, (int) $company->getId());
        if (count($perfis) !== count($profileCodes)) {
            throw new ValidationException([
                'profileCodes' => ['Um ou mais perfis informados nao existem para a company.'],
            ]);
        }

        return $this->transactionRunner->run(function () use ($request, $company, $empresas, $unidades, $perfis): User {
            $user = new User(
                $request->nome,
                $request->email,
                '',
                $request->status,
                $request->role,
                $company
            );

            $hashedPassword = $this->passwordHasher->hashPassword($user, $request->password);
            $user = new User($request->nome, $request->email, $hashedPassword, $request->status, $request->role, $company);
            $this->userRepository->save($user);
            $this->userCompanyRepository->save(new UserCompany($user, $company, true, $request->status));

            foreach ($empresas as $empresa) {
                $this->userEmpresaRepository->save(new \App\Identity\Domain\Entity\UserEmpresa($user, $company, $empresa, $request->status));
            }

            foreach ($unidades as $unidade) {
                $this->userUnidadeRepository->save(new \App\Identity\Domain\Entity\UserUnidade($user, $company, $unidade, $request->status));
            }

            foreach ($perfis as $perfil) {
                $this->userPerfilRepository->save(new UserPerfil($user, $company, $perfil, null, null, $request->status));
            }

            $this->auditoriaLogger->log(
                (int) $company->getId(),
                'user',
                'identity.user.created',
                [
                    'userId' => $user->getId(),
                    'email' => $user->getEmail(),
                ]
            );

            return $user;
        });
    }

    private function createRootUser(CreateUserRequest $request): User
    {
        // Para ROOT, verificar se já existe outro ROOT
        $existingRoot = $this->userRepository->findByEmail($request->email);
        if ($existingRoot !== null) {
            throw new ValidationException([
                'email' => ['Email ja cadastrado.'],
            ]);
        }

        $company = null;
        if ($request->companyId !== null && $request->companyId > 0) {
            $company = $this->companyRepository->findById((int) $request->companyId);
        }

        return $this->transactionRunner->run(function () use ($request, $company): User {
            $user = new User(
                $request->nome,
                $request->email,
                '',
                $request->status,
                User::ROLE_ROOT,
                $company
            );

            $hashedPassword = $this->passwordHasher->hashPassword($user, $request->password);
            $user = new User($request->nome, $request->email, $hashedPassword, $request->status, User::ROLE_ROOT, $company);
            $this->userRepository->save($user);

            if ($company !== null) {
                $this->userCompanyRepository->save(new UserCompany($user, $company, true, $request->status));
            }

            $this->auditoriaLogger->log(
                $company !== null ? (int) $company->getId() : null,
                'user',
                'identity.root_user.created',
                [
                    'userId' => $user->getId(),
                    'email' => $user->getEmail(),
                ]
            );

            return $user;
        });
    }

    public function getById(int $id): User
    {
        $user = $this->userRepository->findById($id);
        if ($user === null) {
            throw new ResourceNotFoundException('Usuario nao encontrado.');
        }

        return $user;
    }

    /**
     * @return list<User>
     */
    public function list(?int $companyId = null, ?string $status = null): array
    {
        return $this->userRepository->listAll($companyId, $status);
    }

    public function update(int $userId, UpdateUserRequest $request): User
    {
        $this->requestValidator->validate($request);

        $user = $this->userRepository->findById($userId);
        if ($user === null) {
            throw new ResourceNotFoundException('Usuario nao encontrado.');
        }

        $company = null;
        if ($request->companyId !== null && $request->companyId > 0) {
            $company = $this->companyRepository->findById((int) $request->companyId);
            if ($company === null) {
                throw new ResourceNotFoundException('Company nao encontrada.');
            }
        }

        if ($request->role !== User::ROLE_ROOT && $company === null) {
            throw new ValidationException([
                'companyId' => ['ROLE_ADMIN deve estar vinculado a uma company.'],
            ]);
        }

        // Verificar email duplicado na mesma company
        if ($company !== null) {
            $existingUser = $this->userRepository->findByEmailAndCompany($request->email, (int) $company->getId());
            if ($existingUser !== null && $existingUser->getId() !== $userId) {
                throw new ValidationException([
                    'email' => ['Email ja cadastrado nesta company.'],
                ]);
            }
        }

        return $this->transactionRunner->run(function () use ($user, $request, $company): User {
            $user->update(
                $request->nome,
                $request->email,
                $request->status,
                $request->role,
                $company,
                $request->mfaHabilitado
            );

            if ($request->password !== null && $request->password !== '') {
                $hashedPassword = $this->passwordHasher->hashPassword($user, $request->password);
                $user->changePassword($hashedPassword);
            }

            $this->userRepository->save($user);

            // Atualizar vínculos de empresas
            if ($company !== null) {
                $this->syncUserEmpresas($user, $company, $request->empresaIds);
                $this->syncUserUnidades($user, $company, $request->unidadeIds);

                $profileCodes = $request->profileCodes !== [] ? array_values(array_unique($request->profileCodes)) : [];
                if ($profileCodes !== []) {
                    $this->syncUserPerfis($user, $company, $profileCodes);
                }

                // Garantir vínculo user_companies
                if (!$this->userCompanyRepository->userHasCompany((int) $user->getId(), (int) $company->getId())) {
                    $this->userCompanyRepository->save(new UserCompany($user, $company, true, $request->status));
                }
            }

            $this->auditoriaLogger->log(
                $company !== null ? (int) $company->getId() : null,
                'user',
                'identity.user.updated',
                [
                    'userId' => $user->getId(),
                    'email' => $user->getEmail(),
                ]
            );

            return $user;
        });
    }

    public function delete(int $userId): void
    {
        $user = $this->userRepository->findById($userId);
        if ($user === null) {
            throw new ResourceNotFoundException('Usuario nao encontrado.');
        }

        if ($user->isRoot()) {
            throw new ValidationException([
                'role' => ['Nao e permitido excluir usuarios ROLE_ROOT.'],
            ]);
        }

        $companyId = $user->getCompany()?->getId();

        $user->update(
            $user->getNome(),
            $user->getEmail(),
            User::STATUS_INATIVO,
            $user->getRole(),
            $user->getCompany(),
            $user->isMfaHabilitado()
        );

        $this->userRepository->save($user);

        $this->auditoriaLogger->log(
            $companyId !== null ? (int) $companyId : null,
            'user',
            'identity.user.inactivated',
            [
                'userId' => $user->getId(),
                'email' => $user->getEmail(),
            ]
        );
    }

    public function desbloquear(int $userId): User
    {
        $user = $this->userRepository->findById($userId);
        if ($user === null) {
            throw new ResourceNotFoundException('Usuario nao encontrado.');
        }

        $user->desbloquear();
        $this->userRepository->save($user);

        $this->auditoriaLogger->log(
            $user->getCompany()?->getId() !== null ? (int) $user->getCompany()->getId() : null,
            'user',
            'identity.user.desbloqueado',
            [
                'userId' => $user->getId(),
                'email' => $user->getEmail(),
            ]
        );

        return $user;
    }

    public function updateProfiles(int $userId, UpdateUserProfilesRequest $request): User
    {
        $this->requestValidator->validate($request);

        $user = $this->userRepository->findById($userId);
        if ($user === null) {
            throw new ResourceNotFoundException('Usuario nao encontrado.');
        }

        $company = $this->companyRepository->findById((int) $request->companyId);
        if ($company === null) {
            throw new ResourceNotFoundException('Company nao encontrada.');
        }

        if (!$this->userCompanyRepository->userHasCompany($userId, (int) $company->getId())) {
            throw new ValidationException([
                'companyId' => ['Usuario nao esta vinculado a company informada.'],
            ]);
        }

        $profileCodes = $request->profileCodes !== [] ? array_values(array_unique($request->profileCodes)) : ['company_admin'];
        $perfis = $this->perfilRepository->findByCodigos($profileCodes, (int) $company->getId());

        if (count($perfis) !== count($profileCodes)) {
            throw new ValidationException([
                'profileCodes' => ['Um ou mais perfis informados nao existem para a company.'],
            ]);
        }

        $this->transactionRunner->run(function () use ($user, $company, $perfis): void {
            $this->userPerfilRepository->deleteByUserAndCompany((int) $user->getId(), (int) $company->getId());

            foreach ($perfis as $perfil) {
                $this->userPerfilRepository->save(new UserPerfil($user, $company, $perfil));
            }
        });

        $this->auditoriaLogger->log(
            (int) $company->getId(),
            'user',
            'identity.user.profiles.updated',
            [
                'userId' => $user->getId(),
                'companyId' => $company->getId(),
                'profileCodes' => $profileCodes,
            ]
        );

        return $user;
    }

    /**
     * Registra login bem-sucedido.
     */
    public function registerSuccessfulLogin(User $user): void
    {
        $user->registerSuccessfulLogin();
        $this->userRepository->save($user);

        $this->auditoriaLogger->log(
            $user->getCompany()?->getId() !== null ? (int) $user->getCompany()->getId() : null,
            'user',
            'identity.user.login',
            [
                'userId' => $user->getId(),
                'email' => $user->getEmail(),
            ]
        );
    }

    /**
     * Registra falha de login.
     */
    public function registerFailedLogin(User $user): void
    {
        $user->registerFailedLogin();
        $this->userRepository->save($user);

        $this->auditoriaLogger->log(
            $user->getCompany()?->getId() !== null ? (int) $user->getCompany()->getId() : null,
            'user',
            'identity.user.login_failed',
            [
                'userId' => $user->getId(),
                'email' => $user->getEmail(),
                'attempts' => $user->getFailedLoginAttempts(),
                'bloqueado' => $user->isBloqueado(),
            ]
        );
    }

    /**
     * @param list<int> $empresaIds
     */
    private function syncUserEmpresas(User $user, \App\Company\Domain\Entity\Company $company, array $empresaIds): void
    {
        if ($empresaIds === []) {
            return;
        }

        foreach ($empresaIds as $empresaId) {
            $empresa = $this->empresaRepository->findById($empresaId);
            if ($empresa === null || $empresa->getCompany()->getId() !== $company->getId()) {
                throw new ValidationException([
                    'empresaIds' => ['Empresa invalida para a company informada.'],
                ]);
            }
        }
    }

    /**
     * @param list<int> $unidadeIds
     */
    private function syncUserUnidades(User $user, \App\Company\Domain\Entity\Company $company, array $unidadeIds): void
    {
        if ($unidadeIds === []) {
            return;
        }

        foreach ($unidadeIds as $unidadeId) {
            $unidade = $this->unidadeRepository->findById($unidadeId);
            if ($unidade === null || $unidade->getCompany()->getId() !== $company->getId()) {
                throw new ValidationException([
                    'unidadeIds' => ['Unidade invalida para a company informada.'],
                ]);
            }
        }
    }

    /**
     * @param list<string> $profileCodes
     */
    private function syncUserPerfis(User $user, \App\Company\Domain\Entity\Company $company, array $profileCodes): void
    {
        $perfis = $this->perfilRepository->findByCodigos($profileCodes, (int) $company->getId());
        if (count($perfis) !== count($profileCodes)) {
            throw new ValidationException([
                'profileCodes' => ['Um ou mais perfis informados nao existem para a company.'],
            ]);
        }

        $this->userPerfilRepository->deleteByUserAndCompany((int) $user->getId(), (int) $company->getId());

        foreach ($perfis as $perfil) {
            $this->userPerfilRepository->save(new UserPerfil($user, $company, $perfil));
        }
    }
}
