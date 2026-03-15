<?php

declare(strict_types=1);

namespace App\Identity\Application\Service;

use App\Company\Domain\Repository\CompanyRepositoryInterface;
use App\Company\Domain\Repository\EmpresaRepositoryInterface;
use App\Company\Domain\Repository\UnidadeNegocioRepositoryInterface;
use App\Identity\Application\DTO\CreateUserRequest;
use App\Identity\Application\DTO\UpdateUserProfilesRequest;
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

        if ($this->userRepository->findByEmail($request->email) !== null) {
            throw new ValidationException([
                'email' => ['Email já cadastrado.'],
            ]);
        }

        if ($isBootstrap && $request->role !== User::ROLE_ROOT) {
            throw new ValidationException([
                'role' => ['O primeiro usuário do sistema deve ser ROLE_ROOT.'],
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
            throw new ResourceNotFoundException('Company não encontrada.');
        }

        $empresas = [];

        foreach ($request->empresaIds as $empresaId) {
            $empresa = $this->empresaRepository->findById($empresaId);

            if ($empresa === null || $empresa->getCompany()->getId() !== $company->getId()) {
                throw new ValidationException([
                    'empresaIds' => ['Empresa inválida para a company informada.'],
                ]);
            }

            $empresas[$empresaId] = $empresa;
        }

        $unidades = [];

        foreach ($request->unidadeIds as $unidadeId) {
            $unidade = $this->unidadeRepository->findById($unidadeId);

            if ($unidade === null || $unidade->getCompany()->getId() !== $company->getId()) {
                throw new ValidationException([
                    'unidadeIds' => ['Unidade inválida para a company informada.'],
                ]);
            }

            $unidades[$unidadeId] = $unidade;
        }

        $profileCodes = $request->profileCodes !== [] ? array_values(array_unique($request->profileCodes)) : ['company_admin'];
        $perfis = $this->perfilRepository->findByCodigos($profileCodes, (int) $company->getId());

        if (count($perfis) !== count($profileCodes)) {
            throw new ValidationException([
                'profileCodes' => ['Um ou mais perfis informados não existem para a company.'],
            ]);
        }

        return $this->transactionRunner->run(function () use ($request, $company, $empresas, $unidades, $perfis): User {
            $user = new User(
                $request->nome,
                $request->email,
                '',
                $request->status,
                $request->role
            );

            $hashedPassword = $this->passwordHasher->hashPassword($user, $request->password);
            $user = new User($request->nome, $request->email, $hashedPassword, $request->status, $request->role);
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
        return $this->transactionRunner->run(function () use ($request): User {
            $user = new User(
                $request->nome,
                $request->email,
                '',
                $request->status,
                User::ROLE_ROOT
            );

            $hashedPassword = $this->passwordHasher->hashPassword($user, $request->password);
            $user = new User($request->nome, $request->email, $hashedPassword, $request->status, User::ROLE_ROOT);
            $this->userRepository->save($user);

            $this->auditoriaLogger->log(
                null,
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

    /**
     * @return list<User>
     */
    public function list(?int $companyId = null): array
    {
        return $this->userRepository->listAll($companyId);
    }

    public function updateProfiles(int $userId, UpdateUserProfilesRequest $request): User
    {
        $this->requestValidator->validate($request);

        $user = $this->userRepository->findById($userId);
        if ($user === null) {
            throw new ResourceNotFoundException('Usuário não encontrado.');
        }

        $company = $this->companyRepository->findById((int) $request->companyId);
        if ($company === null) {
            throw new ResourceNotFoundException('Company não encontrada.');
        }

        if (!$this->userCompanyRepository->userHasCompany($userId, (int) $company->getId())) {
            throw new ValidationException([
                'companyId' => ['Usuário não está vinculado à company informada.'],
            ]);
        }

        $profileCodes = $request->profileCodes !== [] ? array_values(array_unique($request->profileCodes)) : ['company_admin'];
        $perfis = $this->perfilRepository->findByCodigos($profileCodes, (int) $company->getId());

        if (count($perfis) !== count($profileCodes)) {
            throw new ValidationException([
                'profileCodes' => ['Um ou mais perfis informados não existem para a company.'],
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
}
