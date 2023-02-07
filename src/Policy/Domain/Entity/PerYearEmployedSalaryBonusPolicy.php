<?php

declare(strict_types=1);

namespace App\Policy\Domain\Entity;

use App\Contract\Domain\Entity\Contract;
use App\Policy\Domain\Exception\NegativeSalaryBonusAmountException;
use App\Policy\Domain\Exception\NegativeYearsLimitException;
use App\Policy\Domain\ValueObject\SalaryBonusPolicyId;
use Brick\Money\Money;
use DateTimeInterface;

class PerYearEmployedSalaryBonusPolicy extends AbstractSalaryBonusPolicy
{
    protected function __construct(
        private readonly int $centsAmountPerYear,
        private readonly int $yearsLimit
    )
    {
        if ($this->centsAmountPerYear < 0) {
            throw NegativeSalaryBonusAmountException::create();
        }

        if ($this->yearsLimit < 0) {
            throw NegativeYearsLimitException::create();
        }

        parent::__construct(SalaryBonusPolicyId::create());
    }

    public static function create(int $centsAmountPerYear, int $yearsLimit): static
    {
        return new static($centsAmountPerYear, $yearsLimit);
    }

    public function name(): string
    {
        return sprintf(
            '%d amount bonus per year (limit %d) policy',
            $this->centsAmountPerYear / 100,
            $this->yearsLimit
        );
    }

    public function calculateBonusFor(Contract $contract, DateTimeInterface $at): Money
    {
        if ($contract->isActive($at) === false) {
            return Money::of(0, $contract->salaryMoney()->getCurrency());
        }

        $salaryBonusAmount = ($this->centsAmountPerYear / 100) * max(0, min($this->yearsLimit, $contract->yearsInEffect()));

        return Money::of($salaryBonusAmount, $contract->salaryMoney()->getCurrency());
    }

    public function calculateTotalFor(Contract $contract, DateTimeInterface $at): Money
    {
        if ($contract->isActive($at) === false) {
            return Money::of(0, $contract->salaryMoney()->getCurrency());
        }

        $salaryBonusAmount = ($this->centsAmountPerYear / 100) * max(0, min($this->yearsLimit, $contract->yearsInEffect()));

        return $contract->salaryMoney()->plus(
            Money::of($salaryBonusAmount, $contract->salaryMoney()->getCurrency())
        );
    }
}
