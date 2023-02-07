<?php

declare(strict_types=1);

namespace App\Policy\Domain\Entity;

use App\Contract\Domain\Entity\Contract;
use Brick\Money\Money;
use DateTimeInterface;

class NoSalaryBonusPolicy extends AbstractSalaryBonusPolicy
{
    protected function __construct()
    {
        parent::__construct(null);
    }

    public static function create(): static
    {
        return new static();
    }

    public function name(): string
    {
        return 'No bonus policy';
    }

    public function calculateBonusFor(Contract $contract, DateTimeInterface $at): Money
    {
        return Money::of(0, $contract->salaryMoney()->getCurrency());
    }

    public function calculateTotalFor(Contract $contract, DateTimeInterface $at): Money
    {
        if ($contract->isActive($at) === false) {
            return Money::of(0, $contract->salaryMoney()->getCurrency());
        }

        return $contract->salaryMoney();
    }
}
