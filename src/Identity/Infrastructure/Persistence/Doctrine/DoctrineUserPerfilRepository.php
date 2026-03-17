<?php

declare(strict_types=1);

namespace App\Identity\Infrastructure\Persistence\Doctrine;

use App\Identity\Domain\Entity\UserPerfil;
use App\Identity\Domain\Repository\UserPerfilRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<UserPerfil>
 */
final class DoctrineUserPerfilRepository extends ServiceEntityRepository implements UserPerfilRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserPerfil::class);
    }

    public function save(UserPerfil $userPerfil): void
    {
        $em = $this->getEntityManager();
        $em->persist($userPerfil);
        $em->flush();
    }

    public function userHasPermission(int $userId, string $permissionCode, ?int $companyId = null, ?int $empresaId = null, ?int $unidadeId = null): bool
    {
        // perfis_acesso e perfil_acesso_permissoes foram movidas para DB tenant.
        // Esta consulta requer acesso ao tenant DB, não disponível em contexto control.
        // TODO: implementar consultando o tenant EM quando houver context disponível.
        return false;
    }

    public function listProfileCodesByUser(int $userId, ?int $companyId = null): array
    {
        // perfis_acesso foi movida para DB tenant.
        // Se estamos em context de tenant, consultamos lá.
        // Se não, retornamos vazio (ex: login ROLE_ROOT sem company selecionada)
        return [];
    }

    public function listPermissionCodesByUser(int $userId, ?int $companyId = null): array
    {
        // perfis_acesso e perfil_acesso_permissoes foram movidas para DB tenant.
        // Se estamos em context de tenant, consultamos lá.
        // Se não, retornamos vazio (ex: login ROLE_ROOT sem company selecionada)
        return [];
    }

    public function deleteByUserAndCompany(int $userId, int $companyId): void
    {
        $this->getEntityManager()
            ->createQuery('DELETE FROM App\Identity\Domain\Entity\UserPerfil up WHERE up.user = :userId AND up.company = :companyId')
            ->setParameter('userId', $userId)
            ->setParameter('companyId', $companyId)
            ->execute();
    }
}
