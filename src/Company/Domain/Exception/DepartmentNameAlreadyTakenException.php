<?php

declare(strict_types=1);

namespace App\Company\Domain\Exception;

use DomainException;

class DepartmentNameAlreadyTakenException extends DomainException
{
    public static function create(string $departmentName): static
    {
        return new static(
            sprintf('Department with name "%s" already exists.', $departmentName)
        );
    }
}
