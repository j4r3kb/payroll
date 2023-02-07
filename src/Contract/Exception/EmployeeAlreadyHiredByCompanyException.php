<?php

declare(strict_types=1);

namespace App\Contract\Exception;

use DomainException;

class EmployeeAlreadyHiredByCompanyException extends DomainException
{
    public static function create(string $employeeId, string $companyId): static
    {
        return new static(
            sprintf('Employee %s is already hired by Company %s', $employeeId, $companyId)
        );
    }
}
