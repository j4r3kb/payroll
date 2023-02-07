<?php

declare(strict_types=1);

namespace App\Company\Domain\Entity;

use App\Company\Domain\ValueObject\CompanyId;
use App\Company\Domain\ValueObject\DepartmentId;
use App\Policy\Domain\ValueObject\SalaryBonusPolicyId;
use Webmozart\Assert\Assert;

class Department
{
    private readonly string $id;

    private readonly string $companyId;

    private readonly ?string $salaryBonusPolicyId;

    private function __construct(
        DepartmentId $id,
        CompanyId $companyId,
        public readonly string $name,
        ?SalaryBonusPolicyId $salaryBonusPolicyId
    )
    {
        $this->id = $id->__toString();
        $this->companyId = $companyId->__toString();
        $this->salaryBonusPolicyId = $salaryBonusPolicyId?->__toString();
    }

    public static function create(
        CompanyId $companyId,
        string $name,
        ?SalaryBonusPolicyId $salaryBonusPolicyId
    ): static
    {
        Assert::stringNotEmpty($name, 'Department\'s name can not be empty');

        return new static(
            DepartmentId::create(),
            $companyId,
            $name,
            $salaryBonusPolicyId
        );
    }

    public function id(): DepartmentId
    {
        return DepartmentId::fromString($this->id);
    }

    public function companyId(): CompanyId
    {
        return CompanyId::fromString($this->companyId);
    }

    public function salaryBonusPolicyId(): ?SalaryBonusPolicyId
    {
        return $this->salaryBonusPolicyId ? SalaryBonusPolicyId::fromString($this->salaryBonusPolicyId) : null;
    }
}
