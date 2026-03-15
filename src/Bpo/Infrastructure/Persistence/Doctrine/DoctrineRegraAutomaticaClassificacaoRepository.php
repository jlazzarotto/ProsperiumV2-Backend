<?php
declare(strict_types=1);
namespace App\Bpo\Infrastructure\Persistence\Doctrine;
use App\Bpo\Domain\Entity\RegraAutomaticaClassificacao;
use App\Bpo\Domain\Repository\RegraAutomaticaClassificacaoRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
/** @extends ServiceEntityRepository<RegraAutomaticaClassificacao> */
final class DoctrineRegraAutomaticaClassificacaoRepository extends ServiceEntityRepository implements RegraAutomaticaClassificacaoRepositoryInterface
{
    public function __construct(ManagerRegistry $r){parent::__construct($r,RegraAutomaticaClassificacao::class);}
    public function save(RegraAutomaticaClassificacao $regra): void { $em=$this->getEntityManager(); $em->persist($regra); $em->flush(); }
    public function findActiveMatches(int $companyId, ?int $empresaId, ?int $unidadeId, string $texto): array
    {
        $qb=$this->createQueryBuilder('r')->andWhere('r.company = :companyId')->andWhere('r.status = :status')->andWhere('(r.empresa IS NULL OR r.empresa = :empresaId)')->andWhere('(r.unidade IS NULL OR r.unidade = :unidadeId)')->setParameter('companyId',$companyId)->setParameter('status','active')->setParameter('empresaId',$empresaId)->setParameter('unidadeId',$unidadeId);
        $regras=$qb->getQuery()->getResult();
        return array_values(array_filter($regras, static fn(RegraAutomaticaClassificacao $regra): bool => $regra->matches($texto)));
    }
}
