<?php

declare(strict_types=1);

namespace App\Policy\Domain\Exception;

use RuntimeException;

class SalaryBonusPolicyNotFoundException extends RuntimeException
{
    public static function create(string $salaryBonusPolicyId): static
    {
        return new static(sprintf('Salary Bonus Policy %s not found', $salaryBonusPolicyId));
    }
}
