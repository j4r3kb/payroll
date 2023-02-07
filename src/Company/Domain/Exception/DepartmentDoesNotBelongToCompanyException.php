<?php

declare(strict_types=1);

namespace App\Company\Domain\Exception;

use DomainException;

class DepartmentDoesNotBelongToCompanyException extends DomainException
{
    public static function create(string $departmentId, string $companyId): static
    {
        return new static(
            sprintf('Department %s does not belong to Company %s', $departmentId, $companyId)
        );
    }
}
