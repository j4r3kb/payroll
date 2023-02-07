<?php

declare(strict_types=1);

namespace App\Tests\Integration\Payroll\Application\Command;

use App\Common\Application\Query\Filter;
use App\Common\Application\Query\Sort;
use App\Company\Domain\Entity\Company;
use App\Company\Domain\Repository\CompanyRepository;
use App\Company\Domain\ValueObject\CompanyId;
use App\Contract\Domain\Entity\Contract;
use App\Contract\Domain\Repository\ContractRepository;
use App\Contract\Domain\Service\EmploymentValidator;
use App\Employee\Domain\Entity\Employee;
use App\Employee\Domain\Repository\EmployeeRepository;
use App\Payroll\Application\Command\GeneratePayrollCommand;
use App\Payroll\Application\Command\GeneratePayrollHandler;
use App\Payroll\Application\Query\PayrollQuery;
use App\Policy\Domain\Entity\PercentageSalaryBonusPolicy;
use App\Policy\Domain\Entity\PerYearEmployedSalaryBonusPolicy;
use App\Policy\Domain\Repository\SalaryBonusPolicyRepository;
use Carbon\CarbonImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class GeneratePayrollHandlerTest extends KernelTestCase
{
    private ?CompanyId $companyId = null;

    public function testGeneratesPayrollWithProperData(): void
    {
        $container = $this->getContainer();
        $handler = $container->get(GeneratePayrollHandler::class);
        $command = new GeneratePayrollCommand($this->companyId->__toString(), 2022, 7);

        $handler->__invoke($command);

        $em = $container->get(EntityManagerInterface::class);
        $em->flush();

        $payrollQuery = $container->get(PayrollQuery::class);
        $payrollChoiceList = $payrollQuery->getPayrollListByCompanyAndPeriod($this->companyId->__toString(), 2022, 7);
        $payrollId = key($payrollChoiceList->choices);

        $payrollView = $payrollQuery->getPayroll($payrollId, Sort::create('salary_total'));
        $items = $payrollView->items;

        $this->assertEquals('Company name', $payrollView->companyName);
        $this->assertEquals(2022, $payrollView->year);
        $this->assertEquals(7, $payrollView->month);
        $this->assertCount(2, $items);
        $itemViewOne = current($items);
        $this->assertEquals('First2', $itemViewOne->firstName);
        $this->assertEquals('Last2', $itemViewOne->lastName);
        $this->assertEquals('Customer Service', $itemViewOne->departmentName);
        $this->assertEquals(1100, $itemViewOne->salaryBase);
        $this->assertEquals(110, $itemViewOne->salaryBonus);
        $this->assertEquals(1210, $itemViewOne->salaryTotal);
        $this->assertEquals('USD', $itemViewOne->salaryCurrency);
        $itemViewTwo = next($items);
        $this->assertEquals('First1', $itemViewTwo->firstName);
        $this->assertEquals('Last1', $itemViewTwo->lastName);
        $this->assertEquals('HR', $itemViewTwo->departmentName);
        $this->assertEquals(1000, $itemViewTwo->salaryBase);
        $this->assertEquals(1000, $itemViewTwo->salaryBonus);
        $this->assertEquals(2000, $itemViewTwo->salaryTotal);
        $this->assertEquals('USD', $itemViewTwo->salaryCurrency);

        $payrollView = $payrollQuery->getPayroll($payrollId, null, Filter::create('department_name', 'customer'));
        $items = $payrollView->items;

        $this->assertCount(1, $items);
    }

    protected function setUp(): void
    {
        parent::setUp();
        $container = $this->getContainer();

        $perYearPolicy = PerYearEmployedSalaryBonusPolicy::create(10000, 10);
        $percentagePolicy = PercentageSalaryBonusPolicy::create(10);
        $salaryBonusPolicyRepository = $container->get(SalaryBonusPolicyRepository::class);
        $salaryBonusPolicyRepository->save($percentagePolicy);
        $salaryBonusPolicyRepository->save($perYearPolicy);

        $company = Company::create('Company name');
        $this->companyId = $company->id();
        $departmentHrId = $company->addDepartment('HR', $perYearPolicy->id());
        $departmentCsId = $company->addDepartment('Customer Service', $percentagePolicy->id());
        $companyRepository = $container->get(CompanyRepository::class);
        $companyRepository->save($company);

        $employeeHr = Employee::create('First1', 'Last1');
        $employeeCs = Employee::create('First2', 'Last2');
        $employeeRepository = $container->get(EmployeeRepository::class);
        $employeeRepository->save($employeeHr);
        $employeeRepository->save($employeeCs);

        $em = $container->get(EntityManagerInterface::class);
        $em->flush();

        $employmentValidator = $container->get(EmploymentValidator::class);
        $contractHr = Contract::sign(
            $this->companyId,
            $departmentHrId,
            $employeeHr->id(),
            $perYearPolicy->id(),
            CarbonImmutable::parse('2008-01-01'),
            null,
            1000,
            'USD',
            $employmentValidator
        );
        $contractCs = Contract::sign(
            $company->id(),
            $departmentCsId,
            $employeeCs->id(),
            $percentagePolicy->id(),
            CarbonImmutable::parse('2018-01-01'),
            null,
            1100,
            'USD',
            $employmentValidator
        );
        $contractRepository = $container->get(ContractRepository::class);
        $contractRepository->save($contractHr);
        $contractRepository->save($contractCs);
        $em->flush();
    }
}
