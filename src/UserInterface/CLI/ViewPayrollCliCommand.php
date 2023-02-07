<?php

declare(strict_types=1);

namespace App\UserInterface\CLI;

use App\Common\Application\Query\Filter;
use App\Common\Application\Query\Sort;
use App\Common\Application\Query\SortOrder;
use App\Company\Application\Query\CompanyQuery;
use App\Payroll\Application\Query\PayrollQuery;
use RangeException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class ViewPayrollCliCommand extends Command
{
    public function __construct(
        private readonly CompanyQuery $companyListQuery,
        private readonly PayrollQuery $payrollQuery
    )
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setName('payroll:view')
            ->setDescription('View payroll for given month')
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

        $payrollList = $this->payrollQuery->getPayrollListByCompanyAndPeriod($companyId, $year, $month);
        if ($payrollList->choices === []) {
            $style->text('No payroll for given period found.');

            return CliCommandResult::SUCCESS;
        }

        $payrollId = $style->choice(
            'Select payroll version',
            $payrollList->choices
        );

        $sortChoice = $style->choice(
            'Choose field to sort by',
            array_replace(['' => 'default'], $this->payrollQuery::availableColumns())
        );
        if ($sortChoice !== '') {
            $order = $style->choice(
                'Choose order direction',
                array_map(static function (SortOrder $order) {
                    return $order->value;
                }, SortOrder::cases())
            );
            $sort = Sort::create($sortChoice, SortOrder::tryFrom($order));
        }

        $filterChoice = $style->choice(
            'Choose field to filter by',
            array_replace(['' => 'none'], $this->payrollQuery::availableColumns())
        );
        if ($filterChoice !== '') {
            $like = $style->ask('Enter filter term');
            $filter = Filter::create($filterChoice, $like);
        }

        $payrollView = $this->payrollQuery->getPayroll($payrollId, $sort ?? null, $filter ?? null);

        $style->table($this->payrollQuery::availableColumns(), $payrollView->itemsToArray());

        return CliCommandResult::SUCCESS;
    }
}
