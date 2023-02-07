<?php

declare(strict_types=1);

namespace App\Contract\Domain\Entity;

use App\Company\Domain\Exception\DepartmentDoesNotBelongToCompanyException;
use App\Company\Domain\ValueObject\CompanyId;
use App\Company\Domain\ValueObject\DepartmentId;
use App\Contract\Domain\Service\EmploymentValidator;
use App\Contract\Domain\ValueObject\ContractDuration;
use App\Contract\Domain\ValueObject\ContractId;
use App\Contract\Domain\ValueObject\Salary;
use App\Contract\Exception\EmployeeAlreadyHiredByCompanyException;
use App\Contract\Exception\EmployeeDoesNotExistException;
use App\Employee\Domain\ValueObject\EmployeeId;
use App\Policy\Domain\ValueObject\SalaryBonusPolicyId;
use Brick\Money\Money;
use Carbon\CarbonImmutable;
use DateTimeInterface;

class Contract
{
    private readonly string $id;

    private readonly string $companyId;

    private readonly string $departmentId;

    private readonly string $employeeId;

    private readonly ?string $salaryBonusPolicyId;

    private function __construct(
        ContractId $id,
        CompanyId $companyId,
        DepartmentId $departmentId,
        EmployeeId $employeeId,
        ?SalaryBonusPolicyId $salaryBonusPolicyId,
        private readonly ContractDuration $duration,
        public readonly Salary $salary
    )
    {
        $this->id = $id->__toString();
        $this->companyId = $companyId->__toString();
        $this->departmentId = $departmentId->__toString();
        $this->employeeId = $employeeId->__toString();
        $this->salaryBonusPolicyId = $salaryBonusPolicyId?->__toString();
    }

    public static function sign(
        CompanyId $companyId,
        DepartmentId $departmentId,
        EmployeeId $employeeId,
        ?SalaryBonusPolicyId $salaryBonusPolicyId,
        DateTimeInterface $effectiveDate,
        ?DateTimeInterface $terminationDate,
        int $salaryAmount,
        string $salaryCurrency,
        EmploymentValidator $employmentValidator
    ): static
    {
        if ($employmentValidator->departmentBelongsToCompany($departmentId, $companyId) === false) {
            throw DepartmentDoesNotBelongToCompanyException::create($departmentId->__toString(), $companyId->__toString());
        }

        if ($employmentValidator->employeeExists($employeeId) === false) {
            throw EmployeeDoesNotExistException::create($employeeId->__toString());
        }

        $contractDuration = ContractDuration::create($effectiveDate, $terminationDate);
        if ($employmentValidator->canEmployeeBeHiredByCompany($employeeId, $companyId, $contractDuration) === false) {
            throw EmployeeAlreadyHiredByCompanyException::create($employeeId->__toString(), $companyId->__toString());
        }

        return new static(
            ContractId::create(),
            $companyId,
            $departmentId,
            $employeeId,
            $salaryBonusPolicyId,
            $contractDuration,
            Salary::create($salaryAmount, $salaryCurrency)
        );
    }

    public function id(): ContractId
    {
        return ContractId::fromString($this->id);
    }

    public function companyId(): CompanyId
    {
        return CompanyId::fromString($this->companyId);
    }

    public function departmentId(): DepartmentId
    {
        return DepartmentId::fromString($this->departmentId);
    }

    public function employeeId(): EmployeeId
    {
        return EmployeeId::fromString($this->employeeId);
    }

    public function salaryBonusPolicyId(): ?SalaryBonusPolicyId
    {
        return $this->salaryBonusPolicyId ? SalaryBonusPolicyId::fromString($this->salaryBonusPolicyId) : null;
    }

    public function salaryMoney(): Money
    {
        return $this->salary->money();
    }

    public function yearsInEffect(DateTimeInterface $till = null): int
    {
        return $this->duration->effectiveDate()->diffInYears(CarbonImmutable::parse($till), false);
    }

    public function isActive(DateTimeInterface $at = null): bool
    {
        return $this->duration->isActive($at);
    }
}
