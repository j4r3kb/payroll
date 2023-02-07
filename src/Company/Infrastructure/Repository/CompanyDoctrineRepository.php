<?php

declare(strict_types=1);

namespace App\Company\Infrastructure\Repository;

use App\Company\Domain\Entity\Company;
use App\Company\Domain\Entity\Department;
use App\Company\Domain\Repository\CompanyRepository;
use App\Company\Domain\ValueObject\CompanyId;
use App\Company\Domain\ValueObject\DepartmentId;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class CompanyDoctrineRepository extends ServiceEntityRepository implements CompanyRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Company::class);
    }

    public function save(Company $company): void
    {
        $em = $this->getEntityManager();
        $em->persist($company);
    }

    public function findOne(CompanyId $companyId): ?Company
    {
        return $this->find($companyId);
    }

    public function findDepartmentInCompany(DepartmentId $departmentId, CompanyId $companyId): ?Department
    {
        $em = $this->getEntityManager();
        $query = $em->createQuery(
            'SELECT d FROM App\Company\Domain\Entity\Company c ' .
            'JOIN App\Company\Domain\Entity\Department d ' .
            'WHERE c.id = :companyId ' .
            'AND d.id = :departmentId '
        )->setParameters(
            [
                'companyId' => $companyId,
                'departmentId' => $departmentId,
            ]
        )->setMaxResults(1);

        return $query->getOneOrNullResult();
    }
}
