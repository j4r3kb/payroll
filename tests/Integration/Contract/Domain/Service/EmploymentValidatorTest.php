<?php

declare(strict_types=1);

namespace App\Tests\Integration\Contract\Domain\Service;

use App\Company\Domain\Entity\Company;
use App\Company\Domain\Repository\CompanyRepository;
use App\Company\Domain\ValueObject\DepartmentId;
use App\Contract\Domain\Entity\Contract;
use App\Contract\Domain\Repository\ContractRepository;
use App\Contract\Domain\Service\EmploymentValidator;
use App\Contract\Domain\ValueObject\ContractDuration;
use App\Employee\Domain\Entity\Employee;
use App\Employee\Domain\Repository\EmployeeRepository;
use App\Employee\Domain\ValueObject\EmployeeId;
use Carbon\CarbonImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Uid\Uuid;

class EmploymentValidatorTest extends KernelTestCase
{
    private ?CompanyRepository $companyRepository = null;

    private ?ContractRepository $contractRepository = null;

    private ?EmployeeRepository $employeeRepository = null;

    private ?EmploymentValidator $validator = null;

    public function testEmployeeExistsReturnsFalse(): void
    {
        $this->assertFalse(
            $this->validator->employeeExists(EmployeeId::fromString(Uuid::v4()->toRfc4122()))
        );
    }

    public function testEmployeeExistsReturnsTrue(): void
    {
        $employee = Employee::create('First', 'Last');
        $this->employeeRepository->save($employee);

        $this->assertTrue(
            $this->validator->employeeExists($employee->id())
        );
    }

    public function testCanEmployeeBeHiredByCompanyReturnsFalse(): void
    {
        $employee = Employee::create('First', 'Last');
        $this->employeeRepository->save($employee);
        $company = Company::create('Company');
        $departmentId = $company->addDepartment('Department', null);
        $this->companyRepository->save($company);
        $em = $this->getContainer()->get(EntityManagerInterface::class);
        $em->flush();
        $validator = $this->createMock(EmploymentValidator::class);
        $validator->method('employeeExists')->willReturn(true);
        $validator->method('canEmployeeBeHiredByCompany')->willReturn(true);
        $validator->method('departmentBelongsToCompany')->willReturn(true);

        $contract = Contract::sign(
            $company->id(),
            $departmentId,
            $employee->id(),
            null,
            CarbonImmutable::parse('2022-01-01'),
            null,
            1000,
            'USD',
            $validator
        );
        $this->contractRepository->save($contract);
        $em->flush();

        $this->assertFalse(
            $this->validator->canEmployeeBeHiredByCompany(
                $employee->id(),
                $company->id(),
                ContractDuration::create(CarbonImmutable::parse('2025-01-01'), null)
            )
        );
    }

    public function testCanEmployeeBeHiredByCompanyReturnsTrue(): void
    {
        $employee = Employee::create('First', 'Last');
        $this->employeeRepository->save($employee);
        $company = Company::create('Company');
        $departmentId = $company->addDepartment('Department', null);
        $this->companyRepository->save($company);
        $em = $this->getContainer()->get(EntityManagerInterface::class);
        $em->flush();
        $validator = $this->createMock(EmploymentValidator::class);
        $validator->method('employeeExists')->willReturn(true);
        $validator->method('canEmployeeBeHiredByCompany')->willReturn(true);
        $validator->method('departmentBelongsToCompany')->willReturn(true);

        $contract = Contract::sign(
            $company->id(),
            $departmentId,
            $employee->id(),
            null,
            CarbonImmutable::parse('2020-01-01'),
            CarbonImmutable::parse('2021-01-01'),
            1000,
            'USD',
            $validator
        );
        $this->contractRepository->save($contract);
        $em->flush();

        $this->assertTrue(
            $this->validator->canEmployeeBeHiredByCompany(
                $employee->id(),
                $company->id(),
                ContractDuration::create(CarbonImmutable::parse('2022-01-01'), null)
            )
        );
    }

    public function testDepartmentBelongsToCompanyReturnsFalse(): void
    {
        $company = Company::create('Company');
        $this->companyRepository->save($company);
        $em = $this->getContainer()->get(EntityManagerInterface::class);
        $em->flush();

        $this->assertFalse(
            $this->validator->departmentBelongsToCompany(
                DepartmentId::fromString(Uuid::v4()->toRfc4122()), $company->id()
            )
        );
    }

    public function testDepartmentBelongsToCompanyReturnsTrue(): void
    {
        $company = Company::create('Company');
        $departmentId = $company->addDepartment('Department', null);
        $this->companyRepository->save($company);
        $em = $this->getContainer()->get(EntityManagerInterface::class);
        $em->flush();

        $this->assertTrue($this->validator->departmentBelongsToCompany($departmentId, $company->id()));
    }

    protected function setUp(): void
    {
        parent::setUp();
        $container = $this->getContainer();
        $this->companyRepository = $container->get(CompanyRepository::class);
        $this->contractRepository = $container->get(ContractRepository::class);
        $this->employeeRepository = $container->get(EmployeeRepository::class);
        $this->validator = $container->get(EmploymentValidator::class);
    }
}
