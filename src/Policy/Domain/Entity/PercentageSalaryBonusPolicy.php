<?php

declare(strict_types=1);

namespace App\Policy\Domain\Entity;

use App\Contract\Domain\Entity\Contract;
use App\Policy\Domain\Exception\NegativeSalaryBonusRatePerHundredException;
use App\Policy\Domain\ValueObject\SalaryBonusPolicyId;
use Brick\Money\Money;
use DateTimeInterface;

class PercentageSalaryBonusPolicy extends AbstractSalaryBonusPolicy
{
    protected function __construct(
        private readonly int $ratePerHundred
    )
    {
        if ($this->ratePerHundred < 0) {
            throw NegativeSalaryBonusRatePerHundredException::create();
        }

        parent::__construct(SalaryBonusPolicyId::create());
    }

    public static function create(int $ratePerHundred): static
    {
        return new static($ratePerHundred);
    }

    public function name(): string
    {
        return sprintf('%d percent bonus policy', $this->ratePerHundred);
    }

    public function calculateBonusFor(Contract $contract, DateTimeInterface $at): Money
    {
        if ($contract->isActive($at) === false) {
            return Money::of(0, $contract->salaryMoney()->getCurrency());
        }

        return $contract->salaryMoney()->multipliedBy($this->ratePerHundred / 100);
    }

    public function calculateTotalFor(Contract $contract, DateTimeInterface $at): Money
    {
        if ($contract->isActive($at) === false) {
            return Money::of(0, $contract->salaryMoney()->getCurrency());
        }

        return $contract->salaryMoney()->plus(
            $contract->salaryMoney()->multipliedBy($this->ratePerHundred / 100)
        );
    }
}
