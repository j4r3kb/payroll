<?php

declare(strict_types=1);

namespace App\Policy\Infrastructure\Repository;

use App\Policy\Domain\Entity\AbstractSalaryBonusPolicy;
use App\Policy\Domain\Entity\SalaryBonusPolicy;
use App\Policy\Domain\Repository\SalaryBonusPolicyRepository;
use App\Policy\Domain\ValueObject\SalaryBonusPolicyId;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class SalaryBonusPolicyDoctrineRepository extends ServiceEntityRepository implements SalaryBonusPolicyRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AbstractSalaryBonusPolicy::class);
    }

    public function save(SalaryBonusPolicy $salaryBonusPolicy): void
    {
        $em = $this->getEntityManager();
        $em->persist($salaryBonusPolicy);
    }

    public function findOne(SalaryBonusPolicyId $salaryBonusPolicyId): ?SalaryBonusPolicy
    {
        return $this->find($salaryBonusPolicyId);
    }

    public function all(): array
    {
        return $this->findAll();
    }
}
