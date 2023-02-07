<?php

declare(strict_types=1);

namespace App\Contract\Infrastructure\Repository;

use App\Company\Domain\ValueObject\CompanyId;
use App\Contract\Domain\Entity\Contract;
use App\Contract\Domain\Repository\ContractRepository;
use App\Contract\Domain\ValueObject\ContractDuration;
use App\Contract\Domain\ValueObject\ContractId;
use App\Employee\Domain\ValueObject\EmployeeId;
use App\Payroll\Domain\ValueObject\PayrollPeriod;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class ContractDoctrineRepository extends ServiceEntityRepository implements ContractRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Contract::class);
    }

    public function save(Contract $contract): void
    {
        $em = $this->getEntityManager();
        $em->persist($contract);
    }

    public function findOne(ContractId $contractId): ?Contract
    {
        return $this->find($contractId);
    }

    public function findActiveForEmployeeAndCompany(
        EmployeeId $employeeId,
        CompanyId $companyId,
        ContractDuration $contractDuration
    ): ?Contract
    {
        $em = $this->getEntityManager();
        $dql = 'SELECT c FROM App\Contract\Domain\Entity\Contract c ' .
            'WHERE c.employeeId = :employeeId ' .
            'AND c.companyId = :companyId ' .
            'AND ((c.duration.terminationDate IS NOT NULL ' .
            'AND c.duration.terminationDate > :effectiveDate) ' .
            'OR c.duration.terminationDate IS NULL) ';
        $parameters = [
            'employeeId' => $employeeId,
            'companyId' => $companyId,
            'effectiveDate' => $contractDuration->effectiveDate(),
        ];

        if ($contractDuration->terminationDate() !== null) {
            $dql .= 'OR c.duration.effectiveDate > :terminationDate';
            $parameters['terminationDate'] = $contractDuration->terminationDate();
        }

        $query = $em->createQuery($dql)
            ->setParameters($parameters)
            ->setMaxResults(1)
        ;

        return $query->getOneOrNullResult();
    }

    public function findActiveForCompany(CompanyId $companyId, PayrollPeriod $payrollPeriod): array
    {
        $em = $this->getEntityManager();
        $query = $em->createQuery(
            'SELECT c FROM App\Contract\Domain\Entity\Contract c ' .
            'WHERE c.companyId = :companyId ' .
            'AND c.duration.effectiveDate <= :date ' .
            'AND (c.duration.terminationDate >= :date OR c.duration.terminationDate IS NULL)'
        )->setParameters(
            [
                'companyId' => $companyId,
                'date' => $payrollPeriod->toDate(),
            ]
        );

        return $query->getResult();
    }
}
