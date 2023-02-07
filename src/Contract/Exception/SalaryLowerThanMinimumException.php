<?php

declare(strict_types=1);

namespace App\Contract\Exception;

use DomainException;

class SalaryLowerThanMinimumException extends DomainException
{
    public static function create(int $amount, int $minimumSalary): static
    {
        return new static(
            sprintf('Salary amount %d is lower than required minimum of %d', $amount, $minimumSalary)
        );
    }
}
