<?php

declare(strict_types=1);

namespace App\Policy\Domain\Exception;

use RuntimeException;

class SalaryBonusPolicyOfTypeDoesNotExistException extends RuntimeException
{
    public static function create(string $type): static
    {
        return new static(sprintf('Salary Bonus Policy of type %s does not exist', $type));
    }
}
