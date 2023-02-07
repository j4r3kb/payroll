<?php

declare(strict_types=1);

namespace App\Payroll\Application\Query;

class PayrollItemView
{
    protected function __construct(
        public readonly string $firstName,
        public readonly string $lastName,
        public readonly string $departmentName,
        public readonly int $salaryBase,
        public readonly int $salaryBonus,
        public readonly int $salaryTotal,
        public readonly string $salaryCurrency,
        public readonly string $salaryBonusType
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
        string $salaryBonusType
    ): static
    {
        return new static(
            $firstName,
            $lastName,
            $departmentName,
            $salaryBase,
            $salaryBonus,
            $salaryTotal,
            $salaryCurrency,
            $salaryBonusType
        );
    }

    public static function fromArray(array $data): static
    {
        return new static(...$data);
    }

    public function toArray(): array
    {
        return [
            $this->firstName,
            $this->lastName,
            $this->departmentName,
            $this->salaryBase,
            $this->salaryBonus,
            $this->salaryTotal,
            $this->salaryCurrency,
            $this->salaryBonusType,
        ];
    }
}
