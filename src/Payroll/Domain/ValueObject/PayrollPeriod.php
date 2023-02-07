<?php

declare(strict_types=1);

namespace App\Payroll\Domain\ValueObject;

use App\Payroll\Domain\Exception\MonthNumberInvalidException;
use Carbon\CarbonImmutable;

final class PayrollPeriod
{
    private function __construct(
        public readonly int $year,
        public readonly int $month
    )
    {
    }

    public static function create(int $year, int $month): self
    {
        if ($month < 1 || $month > 12) {
            throw MonthNumberInvalidException::create($month);
        }

        return new self($year, $month);
    }

    public function toDate(): CarbonImmutable
    {
        return CarbonImmutable::parse(sprintf('%s-%s', $this->year, $this->month))->endOfMonth();
    }
}
