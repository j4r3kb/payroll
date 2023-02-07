<?php

declare(strict_types=1);

namespace App\Contract\Domain\Repository;

use App\Company\Domain\ValueObject\CompanyId;
use App\Contract\Domain\Entity\Contract;
use App\Contract\Domain\ValueObject\ContractDuration;
use App\Contract\Domain\ValueObject\ContractId;
use App\Employee\Domain\ValueObject\EmployeeId;
use App\Payroll\Domain\ValueObject\PayrollPeriod;

interface ContractRepository
{
    public function save(Contract $contract): void;

    public function findOne(ContractId $contractId): ?Contract;

    public function findActiveForEmployeeAndCompany(
        EmployeeId $employeeId,
        CompanyId $companyId,
        ContractDuration $contractDuration
    ): ?Contract;

    /**
     * @return Contract[]
     */
    public function findActiveForCompany(CompanyId $companyId, PayrollPeriod $payrollPeriod): array;
}
