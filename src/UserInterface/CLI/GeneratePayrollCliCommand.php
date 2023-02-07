<?php

declare(strict_types=1);

namespace App\UserInterface\CLI;

use App\Company\Application\Query\CompanyQuery;
use App\Payroll\Application\Command\GeneratePayrollCommand;
use App\Payroll\Application\Query\PayrollQuery;
use RangeException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Messenger\MessageBusInterface;

class GeneratePayrollCliCommand extends Command
{
    public function __construct(
        private readonly CompanyQuery $companyListQuery,
        private readonly PayrollQuery $payrollQuery,
        private readonly MessageBusInterface $commandBus
    )
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setName('payroll:generate')
            ->setDescription('Generate payroll for given month')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $style = new SymfonyStyle($input, $output);

        $companyId = $style->choice(
            'Select company',
            $this->companyListQuery->getCompanyList()->choices
        );

        $year = (int) $style->ask('Enter year (integer)');
        $month = (int) $style->ask(
            'Enter month (integer)',
            null,
            static function ($value) {
                if ($value < 1 || $value > 12) {
                    throw new RangeException('Invalid month number');
                }

                return $value;
            }
        );

        $payrollCount = $this->payrollQuery->getCountByCompanyAndPeriod($companyId, $year, $month);

        if ($payrollCount) {
            $confirm = $style->ask(
                sprintf(
                    'There are %d payrolls for %d-%d. Generate new one [y/n]?',
                    $payrollCount,
                    $year,
                    $month
                ),
                null,
                static function ($value) {
                    if (preg_match('/[yYnN]/', $value) !== 1) {
                        throw new RangeException('Enter `y` or `n`');
                    }

                    return $value;
                }
            );

            if (strtolower($confirm) === 'n') {
                $style->text('Payroll generation cancelled.');

                return CliCommandResult::SUCCESS;
            }
        }

        $this->commandBus->dispatch(new GeneratePayrollCommand($companyId, $year, $month));

        $style->text('Payroll generation request is being processed.');
        $style->text('Use `payroll:view` command to check if it is ready.');

        return CliCommandResult::SUCCESS;
    }
}
