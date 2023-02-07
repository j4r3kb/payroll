<?php

declare(strict_types=1);

namespace App\Contract\Domain\Service;

use App\Company\Domain\Repository\CompanyRepository;
use App\Company\Domain\ValueObject\CompanyId;
use App\Company\Domain\ValueObject\DepartmentId;
use App\Contract\Domain\Repository\ContractRepository;
use App\Contract\Domain\ValueObject\ContractDuration;
use App\Employee\Domain\Repository\EmployeeRepository;
use App\Employee\Domain\ValueObject\EmployeeId;

class EmploymentValidator
{
    public function __construct(
        private CompanyRepository $companyRepository,
        private ContractRepository $contractRepository,
        private EmployeeRepository $employeeRepository
    )
    {
    }

    public function employeeExists(EmployeeId $employeeId): bool
    {
        return (bool) $this->employeeRepository->findOne($employeeId);
    }

    public function canEmployeeBeHiredByCompany(
        EmployeeId $employeeId,
        CompanyId $companyId,
        ContractDuration $contractDuration
    ): bool
    {
        return !$this->contractRepository->findActiveForEmployeeAndCompany(
            $employeeId,
            $companyId,
            $contractDuration
        );
    }

    public function departmentBelongsToCompany(DepartmentId $departmentId, CompanyId $companyId): bool
    {
        return (bool) $this->companyRepository->findDepartmentInCompany($departmentId, $companyId);
    }
}
