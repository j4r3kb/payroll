<?php

declare(strict_types=1);

namespace App\Contract\Exception;

use DomainException;

class ContractEffectiveDateGreaterOrEqualTerminationDateException extends DomainException
{
    public static function create(): static
    {
        return new static('Contract effective date can not be greater than termination date');
    }
}
