<?php

declare(strict_types=1);

namespace App\Contract\Exception;

use DomainException;

class EmployeeDoesNotExistException extends DomainException
{
    public static function create(string $employeeId): static
    {
        return new static(
            sprintf('Employee %s does not exist', $employeeId)
        );
    }
}
