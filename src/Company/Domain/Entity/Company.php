<?php

declare(strict_types=1);

namespace App\Company\Domain\Entity;

use App\Company\Domain\Exception\DepartmentDoesNotBelongToCompanyException;
use App\Company\Domain\Exception\DepartmentNameAlreadyTakenException;
use App\Company\Domain\ValueObject\CompanyId;
use App\Company\Domain\ValueObject\DepartmentId;
use App\Policy\Domain\ValueObject\SalaryBonusPolicyId;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Webmozart\Assert\Assert;

class Company
{
    private readonly string $id;

    private function __construct(
        CompanyId $id,
        public readonly string $name,
        private Collection $departments
    )
    {
        $this->id = $id->__toString();
    }

    public function addDepartment(string $departmentName, ?SalaryBonusPolicyId $salaryBonusPolicyId): DepartmentId
    {
        Assert::stringNotEmpty($departmentName);
        if ($this->isDepartmentNameAlreadyTaken($departmentName)) {
            throw DepartmentNameAlreadyTakenException::create($departmentName);
        }

        $department = Department::create($this->id(), $departmentName, $salaryBonusPolicyId);
        $this->departments->set($department->id()->__toString(), $department);

        return $department->id();
    }

    private function isDepartmentNameAlreadyTaken(string $departmentName): bool
    {
        return $this->departments->exists(
            static function (string $id, Department $department) use ($departmentName) {
                return $department->name === $departmentName;
            }
        );
    }

    public static function create(string $name): static
    {
        Assert::stringNotEmpty($name, 'Company\'s name can not be empty');

        return new static(
            CompanyId::create(),
            $name,
            new ArrayCollection()
        );
    }

    public function id(): CompanyId
    {
        return CompanyId::fromString($this->id);
    }

    public function departmentName(DepartmentId $departmentId): string
    {
        $department = $this->getDepartment($departmentId);

        return $department->name;
    }

    private function getDepartment(DepartmentId $departmentId): Department
    {
        $department = $this->departments->get($departmentId->__toString());
        if ($department === null) {
            throw DepartmentDoesNotBelongToCompanyException::create($departmentId->__toString(), $this->id);
        }

        return $department;
    }
}
