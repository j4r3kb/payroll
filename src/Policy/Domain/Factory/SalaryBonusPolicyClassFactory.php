<?php

declare(strict_types=1);

namespace App\Policy\Domain\Factory;

use App\Policy\Domain\Entity\PercentageSalaryBonusPolicy;
use App\Policy\Domain\Entity\PerYearEmployedSalaryBonusPolicy;
use App\Policy\Domain\Enum\SalaryBonusPolicyType;
use App\Policy\Domain\Exception\SalaryBonusPolicyOfTypeDoesNotExistException;

class SalaryBonusPolicyClassFactory
{
    public static function getClassByType(SalaryBonusPolicyType $salaryBonusPolicyType): string
    {
        return match ($salaryBonusPolicyType) {
            SalaryBonusPolicyType::PERCENTAGE_BONUS => PercentageSalaryBonusPolicy::class,
            SalaryBonusPolicyType::PER_YEAR_EMPLOYED_BONUS => PerYearEmployedSalaryBonusPolicy::class,
            default => throw SalaryBonusPolicyOfTypeDoesNotExistException::create($salaryBonusPolicyType->value),
        };
    }
}
