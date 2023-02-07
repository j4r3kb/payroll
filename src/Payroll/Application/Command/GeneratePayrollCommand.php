<?php

declare(strict_types=1);

namespace App\Payroll\Application\Command;

final class GeneratePayrollCommand
{
    public function __construct(
        public readonly string $companyId,
        public readonly int $year,
        public readonly int $month
    )
    {
    }
}
