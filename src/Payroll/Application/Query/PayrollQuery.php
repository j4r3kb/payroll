<?php

declare(strict_types=1);

namespace App\Payroll\Application\Query;

use App\Common\Application\Query\Filter;
use App\Common\Application\Query\Sort;

interface PayrollQuery
{
    public static function availableColumns(): array;

    public function getPayroll(string $payrollId, ?Sort $sort, ?Filter $filter = null): ?PayrollView;

    public function getPayrollListByCompanyAndPeriod(string $companyId, int $year, int $month): PayrollChoiceView;

    public function getCountByCompanyAndPeriod(string $companyId, int $year, int $month): int;
}
