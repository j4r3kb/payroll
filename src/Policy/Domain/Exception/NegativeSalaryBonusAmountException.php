<?php

declare(strict_types=1);

namespace App\Policy\Domain\Exception;

use DomainException;

class NegativeSalaryBonusAmountException extends DomainException
{
    public static function create(): static
    {
        return new static('Annual amount for Salary Bonus Policy must not be negative');
    }
}
