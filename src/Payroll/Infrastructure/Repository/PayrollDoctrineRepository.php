<?php

declare(strict_types=1);

namespace App\Payroll\Infrastructure\Repository;

use App\Payroll\Domain\Entity\Payroll;
use App\Payroll\Domain\Repository\PayrollRepository;
use App\Payroll\Domain\ValueObject\PayrollId;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class PayrollDoctrineRepository extends ServiceEntityRepository implements PayrollRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Payroll::class);
    }

    public function save(Payroll $payroll): void
    {
        $em = $this->getEntityManager();
        $em->persist($payroll);
    }

    public function findOne(PayrollId $payrollId): ?Payroll
    {
        return $this->find($payrollId);
    }
}
