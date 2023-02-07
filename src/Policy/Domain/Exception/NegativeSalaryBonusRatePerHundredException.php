<?php

declare(strict_types=1);

namespace App\Policy\Domain\Exception;

use DomainException;

class NegativeSalaryBonusRatePerHundredException extends DomainException
{
    public static function create(): static
    {
        return new static('Rate per hundred for Salary Bonus Policy must not be negative');
    }
}
