<?php

declare(strict_types=1);

namespace App\Company\Domain\Exception;

use RuntimeException;

class CompanyNotFoundException extends RuntimeException
{
    public static function create(string $companyId): static
    {
        return new static(sprintf('Company %s not found', $companyId));
    }
}
