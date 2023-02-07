<?php

declare(strict_types=1);

namespace App\Payroll\Domain\Exception;

use RangeException;

class MonthNumberInvalidException extends RangeException
{
    public static function create(int $month): static
    {
        return new static(sprintf('Given month number %d is not valid', $month));
    }
}
