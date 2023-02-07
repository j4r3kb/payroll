<?php

declare(strict_types=1);

namespace App\Payroll\Domain\Service;

use App\Company\Domain\Exception\CompanyNotFoundException;
use App\Company\Domain\Repository\CompanyRepository;
use App\Company\Domain\ValueObject\CompanyId;
use App\Contract\Domain\Repository\ContractRepository;
use App\Employee\Domain\Repository\EmployeeRepository;
use App\Payroll\Domain\Entity\Payroll;
use App\Payroll\Domain\Entity\PayrollItem;
use App\Payroll\Domain\Repository\PayrollRepository;
use App\Payroll\Domain\ValueObject\PayrollPeriod;
use App\Policy\Domain\Repository\SalaryBonusPolicyRepository;

class PayrollGenerator
{
    public function __construct(
        private CompanyRepository $companyRepository,
        private ContractRepository $contractRepository,
        private EmployeeRepository $employeeRepository,
        private PayrollRepository $payrollRepository,
        private SalaryBonusPolicyRepository $salaryBonusPolicyRepository
    )
    {
    }

    public function generate(CompanyId $companyId, PayrollPeriod $payrollPeriod): void
    {
        $company = $this->companyRepository->findOne($companyId);
        if ($company === null) {
            throw CompanyNotFoundException::create($companyId->__toString());
        }

        $payroll = Payroll::create($companyId, $company->name, $payrollPeriod);
        $payrollDate = $payrollPeriod->toDate();
        $activeContracts = $this->contractRepository->findActiveForCompany($companyId, $payrollPeriod);

        foreach ($activeContracts as $contract) {
            $departmentName = $company->departmentName($contract->departmentId());
            $employee = $this->employeeRepository->findOne($contract->employeeId());
            $salaryBonusPolicy = $this->salaryBonusPolicyRepository->findOne($contract->salaryBonusPolicyId());
            $salaryBonus = $salaryBonusPolicy->calculateBonusFor($contract, $payrollDate)
                ->getAmount()->toInt()
            ;
            $salaryTotal = $salaryBonusPolicy->calculateTotalFor($contract, $payrollDate)
                ->getAmount()->toInt()
            ;

            $payroll->addItem(
                PayrollItem::create(
                    $employee->firstName,
                    $employee->lastName,
                    $departmentName,
                    $contract->salaryMoney()->getAmount()->toInt(),
                    $salaryBonus,
                    $salaryTotal,
                    $contract->salaryMoney()->getCurrency()->getCurrencyCode(),
                    $salaryBonusPolicy->name(),
                    $payroll
                )
            );
        }

        $this->payrollRepository->save($payroll);
    }
}
