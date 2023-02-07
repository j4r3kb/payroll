<?php

declare(strict_types=1);

namespace App\Tests\Unit\Contract\Domain\Service;

use App\Company\Domain\Entity\Department;
use App\Company\Domain\Repository\CompanyRepository;
use App\Company\Domain\ValueObject\CompanyId;
use App\Company\Domain\ValueObject\DepartmentId;
use App\Contract\Domain\Entity\Contract;
use App\Contract\Domain\Repository\ContractRepository;
use App\Contract\Domain\Service\EmploymentValidator;
use App\Contract\Domain\ValueObject\ContractDuration;
use App\Employee\Domain\Entity\Employee;
use App\Employee\Domain\Repository\EmployeeRepository;
use App\Employee\Domain\ValueObject\EmployeeId;
use Carbon\CarbonImmutable;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Uid\Uuid;

class EmploymentValidatorTest extends TestCase
{
    public function testAllChecksReturnTrue(): void
    {
        $companyRepository = $this->createMock(CompanyRepository::class);
        $companyRepository->method('findDepartmentInCompany')->willReturn(
            Department::create(CompanyId::fromString(Uuid::v4()->toRfc4122()), 'Department', null)
        );
        $contractRepository = $this->createMock(ContractRepository::class);
        $contractRepository->method('findActiveForEmployeeAndCompany')->willReturn(null);
        $employeeRepository = $this->createMock(EmployeeRepository::class);
        $employeeRepository->method('findOne')->willReturn(Employee::create('Test', 'Name'));

        $employmentValidator = new EmploymentValidator($companyRepository, $contractRepository, $employeeRepository);

        $this->assertTrue($employmentValidator->employeeExists(EmployeeId::create()));
        $this->assertTrue($employmentValidator->canEmployeeBeHiredByCompany(
            EmployeeId::create(),
            CompanyId::create(),
            ContractDuration::create(CarbonImmutable::parse('2022-01-01'), CarbonImmutable::parse('2022-12-31')))
        );
        $this->assertTrue($employmentValidator->departmentBelongsToCompany(
            DepartmentId::create(),
            CompanyId::create()
        ));
    }

    public function testAllChecksReturnFalse(): void
    {
        $companyRepository = $this->createMock(CompanyRepository::class);
        $companyRepository->method('findDepartmentInCompany')->willReturn(null);
        $contractRepository = $this->createMock(ContractRepository::class);
        $contractRepository->method('findActiveForEmployeeAndCompany')->willReturn($this->createMock(Contract::class));
        $employeeRepository = $this->createMock(EmployeeRepository::class);
        $employeeRepository->method('findOne')->willReturn(null);

        $employmentValidator = new EmploymentValidator($companyRepository, $contractRepository, $employeeRepository);

        $this->assertFalse($employmentValidator->employeeExists(EmployeeId::create()));
        $this->assertFalse($employmentValidator->canEmployeeBeHiredByCompany(
            EmployeeId::create(),
            CompanyId::create(),
            ContractDuration::create(CarbonImmutable::parse('2022-01-01'), CarbonImmutable::parse('2022-12-31')))
        );
        $this->assertFalse($employmentValidator->departmentBelongsToCompany(
            DepartmentId::create(),
            CompanyId::create()
        ));
    }
}
