<?php

declare(strict_types=1);

namespace App\Policy\Domain\Enum;

enum SalaryBonusPolicyType: string
{
    case PERCENTAGE_BONUS = 'percentage-bonus';

    case PER_YEAR_EMPLOYED_BONUS = 'per-year-employed-bonus';
}
