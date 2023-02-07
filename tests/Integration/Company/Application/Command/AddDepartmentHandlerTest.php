<?php

declare(strict_types=1);

namespace App\Tests\Integration\Company\Application\Command;

use App\Company\Application\Command\AddDepartmentCommand;
use App\Company\Application\Command\AddDepartmentHandler;
use App\Company\Domain\Entity\Company;
use App\Company\Domain\Exception\CompanyNotFoundException;
use App\Company\Domain\Repository\CompanyRepository;
use App\Company\Domain\ValueObject\CompanyId;
use App\Company\Domain\ValueObject\DepartmentId;
use App\Policy\Domain\Entity\PercentageSalaryBonusPolicy;
use App\Policy\Domain\Exception\SalaryBonusPolicyNotFoundException;
use App\Policy\Domain\Repository\SalaryBonusPolicyRepository;
use App\Policy\Domain\ValueObject\SalaryBonusPolicyId;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Uid\Uuid;

class AddDepartmentHandlerTest extends KernelTestCase
{
    private ?CompanyRepository $companyRepository = null;

    private ?SalaryBonusPolicyRepository $salaryBonusPolicyRepository = null;

    private ?CompanyId $companyId = null;

    private ?SalaryBonusPolicyId $salaryBonusPolicyId = null;

    private ?AddDepartmentHandler $handler = null;

    public function testThrowsExceptionWhenCompanyIsNotFound(): void
    {
        $command = new AddDepartmentCommand(Uuid::v4()->toRfc4122(), 'Department name', null);

        $this->expectException(CompanyNotFoundException::class);

        $this->handler->__invoke($command);
    }

    public function testThrowsExceptionWhenSalaryBonusPolicyIsNotFound(): void
    {
        $command = new AddDepartmentCommand(
            $this->companyId->__toString(),
            'Department name',
            SalaryBonusPolicyId::create()->__toString()
        );

        $this->expectException(SalaryBonusPolicyNotFoundException::class);

        $this->handler->__invoke($command);
    }

    public function testAddsDepartmentWithoutSalaryBonusPolicy(): void
    {
        $command = new AddDepartmentCommand(
            $this->companyId->__toString(),
            'Department name',
            null
        );

        $this->handler->__invoke($command);
        $company = $this->companyRepository->findOne($this->companyId);

        $this->assertIsString($command->createdId());
        $this->assertEquals(
            'Department name',
            $company->departmentName(DepartmentId::fromString($command->createdId()))
        );
    }

    public function testAddsDepartmentWithSalaryBonusPolicy(): void
    {
        $command = new AddDepartmentCommand(
            $this->companyId->__toString(),
            'Department name',
            $this->salaryBonusPolicyId->__toString()
        );

        $this->handler->__invoke($command);
        $company = $this->companyRepository->findOne($this->companyId);

        $this->assertIsString($command->createdId());
        $this->assertEquals(
            'Department name',
            $company->departmentName(DepartmentId::fromString($command->createdId()))
        );
    }

    protected function setUp(): void
    {
        parent::setUp();
        $container = $this->getContainer();
        $company = Company::create('Test Company');
        $this->companyId = $company->id();
        $this->companyRepository = $container->get(CompanyRepository::class);
        $this->companyRepository->save($company);
        $this->handler = $container->get(AddDepartmentHandler::class);
        $salaryBonusPolicy = PercentageSalaryBonusPolicy::create(10);
        $this->salaryBonusPolicyId = $salaryBonusPolicy->id();
        $this->salaryBonusPolicyRepository = $container->get(SalaryBonusPolicyRepository::class);
        $this->salaryBonusPolicyRepository->save($salaryBonusPolicy);
    }
}
