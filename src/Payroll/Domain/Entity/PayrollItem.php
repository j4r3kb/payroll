<?php

declare(strict_types=1);

namespace App\Payroll\Domain\Entity;

use Symfony\Component\Uid\Uuid;

class PayrollItem
{
    private function __construct(
        public readonly string $id,
        public readonly string $firstName,
        public readonly string $lastName,
        public readonly string $departmentName,
        public readonly int $salaryBase,
        public readonly int $salaryBonus,
        public readonly int $salaryTotal,
        public readonly string $salaryCurrency,
        public readonly string $salaryBonusType,
        private readonly Payroll $payroll
    )
    {
    }

    public static function create(
        string $firstName,
        string $lastName,
        string $departmentName,
        int $salaryBase,
        int $salaryBonus,
        int $salaryTotal,
        string $salaryCurrency,
        string $salaryBonusType,
        Payroll $payroll
    ): static
    {
        return new static(
            Uuid::v4()->toRfc4122(),
            $firstName,
            $lastName,
            $departmentName,
            $salaryBase,
            $salaryBonus,
            $salaryTotal,
            $salaryCurrency,
            $salaryBonusType,
            $payroll
        );
    }
}
