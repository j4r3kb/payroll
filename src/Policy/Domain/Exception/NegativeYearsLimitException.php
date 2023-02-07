<?php

declare(strict_types=1);

namespace App\Policy\Domain\Exception;

use DomainException;

class NegativeYearsLimitException extends DomainException
{
    public static function create(): static
    {
        return new static('Years limit for Salary Bonus Policy must not be negative.');
    }
}
