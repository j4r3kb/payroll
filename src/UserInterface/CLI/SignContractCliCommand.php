<?php

declare(strict_types=1);

namespace App\UserInterface\CLI;

use App\Company\Application\Query\CompanyQuery;
use App\Company\Application\Query\DepartmentQuery;
use App\Contract\Application\Command\SignContractCommand;
use App\Employee\Application\Query\EmployeeQuery;
use Brick\Money\Currency;
use Carbon\CarbonImmutable;
use RuntimeException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Messenger\MessageBusInterface;

class SignContractCliCommand extends Command
{
    public function __construct(
        private readonly CompanyQuery $companyQuery,
        private readonly DepartmentQuery $departmentQuery,
        private readonly EmployeeQuery $employeeQuery,
        private readonly MessageBusInterface $commandBus
    )
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setName('contract:sign')
            ->setDescription('Sign contract between company and employee')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $style = new SymfonyStyle($input, $output);

        $companyId = $style->choice(
            'Select company',
            $this->companyQuery->getCompanyList()->choices
        );
        $contractEffectiveDate = $style->ask(
            'Enter contract effective date [YYYY-MM-DD]',
            null,
            static function ($value) {
                CarbonImmutable::parse($value);

                return $value;
            }
        );

        $contractTerminationDate = $style->ask(
            'Enter contract termination date [YYYY-MM-DD or none]',
            null,
            static function ($value) use ($contractEffectiveDate) {
                if ($value !== null && CarbonImmutable::parse($contractEffectiveDate) >= CarbonImmutable::parse($value)) {
                    throw new RuntimeException(
                        'Contract termination date must be greater than effective date or empty'
                    );
                }

                return $value;
            }
        );

        $employeeChoiceList = $this->employeeQuery->findEmployeesNotHiredByCompany(
            $companyId,
            CarbonImmutable::parse($contractEffectiveDate),
            CarbonImmutable::parse($contractTerminationDate)
        );
        if ($employeeChoiceList->choices === []) {
            $style->info('No employee without contract available');

            return CliCommandResult::FAILURE;
        }

        $employeeId = $style->choice(
            'Select employee',
            $employeeChoiceList->choices
        );

        $departmentId = $style->choice(
            'Select department',
            $this->departmentQuery->getDepartmentList()->choices
        );

        $salaryBonusPolicyId = $this->departmentQuery->getDepartmentSalaryBonusPolicyId($departmentId);

        $salaryAmount = $style->ask(
            'Enter base salary amount',
            null,
            static function ($value) {
                if (preg_match('/^\d+$/', $value) !== 1) {
                    throw new RuntimeException('Base salary amount must be a positive integer number');
                }

                return (int) $value;
            }
        );

        $salaryCurrency = $style->ask(
            'Enter base salary currency symbol',
            null,
            static function ($value) {
                Currency::of($value);

                return $value;
            }
        );

        $this->commandBus->dispatch(
            $command = new SignContractCommand(
                $companyId,
                $departmentId,
                $employeeId,
                $salaryBonusPolicyId,
                $contractEffectiveDate,
                $contractTerminationDate,
                $salaryAmount,
                $salaryCurrency
            )
        );

        $style->text('Added contract with id ' . $command->createdId());

        return CliCommandResult::SUCCESS;
    }
}
