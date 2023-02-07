<?php

declare(strict_types=1);

namespace App\Policy\Domain\Entity;

use App\Contract\Domain\Entity\Contract;
use App\Policy\Domain\ValueObject\SalaryBonusPolicyId;
use Brick\Money\Money;
use DateTimeInterface;

interface SalaryBonusPolicy
{
    public static function parameters(): array;

    public function id(): ?SalaryBonusPolicyId;

    public function name(): string;

    public function calculateBonusFor(Contract $contract, DateTimeInterface $at): Money;

    public function calculateTotalFor(Contract $contract, DateTimeInterface $at): Money;
}
