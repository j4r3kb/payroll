<?php

declare(strict_types=1);

namespace App\Tests\Unit\Company\Domain\Entity;

use App\Company\Domain\Entity\Company;
use App\Company\Domain\Exception\DepartmentDoesNotBelongToCompanyException;
use App\Company\Domain\Exception\DepartmentNameAlreadyTakenException;
use App\Company\Domain\ValueObject\CompanyId;
use App\Company\Domain\ValueObject\DepartmentId;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Uid\Uuid;

class CompanyTest extends TestCase
{
    public function testIdentifier(): void
    {
        $company = Company::create('Test');
        $this->assertInstanceOf(CompanyId::class, $company->id());
    }

    public function testReturnsDepartmentNameById(): void
    {
        $company = Company::create('Test');
        $departmentId = $company->addDepartment('Test Department', null);

        $this->assertInstanceOf(DepartmentId::class, $departmentId);
        $this->assertEquals('Test Department', $company->departmentName($departmentId));
    }

    public function testCanNotAddDepartmentIfNameIsAlreadyTaken(): void
    {
        $company = Company::create('Test');
        $company->addDepartment('Test Department', null);

        $this->expectException(DepartmentNameAlreadyTakenException::class);
        $company->addDepartment('Test Department', null);
    }

    public function testThrowsExceptionWhenDepartmentDoesNotBelongToCompany(): void
    {
        $company = Company::create('Test');
        $company->addDepartment('Test Department', null);

        $this->expectException(DepartmentDoesNotBelongToCompanyException::class);
        $company->departmentName(DepartmentId::fromString(Uuid::v4()->toRfc4122()));
    }
}
