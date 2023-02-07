<?php

declare(strict_types=1);

namespace App\Company\Domain\Repository;

use App\Company\Domain\Entity\Company;
use App\Company\Domain\Entity\Department;
use App\Company\Domain\ValueObject\CompanyId;
use App\Company\Domain\ValueObject\DepartmentId;

interface CompanyRepository
{
    public function save(Company $company): void;

    public function findOne(CompanyId $companyId): ?Company;

    public function findDepartmentInCompany(DepartmentId $departmentId, CompanyId $companyId): ?Department;
}
