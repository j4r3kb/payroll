<?php

declare(strict_types=1);

namespace App\Policy\Domain\Entity;

use App\Contract\Domain\Entity\Contract;
use App\Policy\Domain\ValueObject\SalaryBonusPolicyId;
use Brick\Money\Money;
use DateTimeInterface;
use ReflectionClass;

abstract class AbstractSalaryBonusPolicy implements SalaryBonusPolicy
{
    protected readonly ?string $id;

    protected function __construct(?SalaryBonusPolicyId $id)
    {
        $this->id = $id?->__toString();
    }

    public static function parameters(): array
    {
        $r = new ReflectionClass(static::class);
        $parameters = [];
        foreach ($r->getConstructor()->getParameters() as $parameter) {
            $parameters[$parameter->getName()] = $parameter->getType()->getName();
        }

        return $parameters;
    }

    public function id(): ?SalaryBonusPolicyId
    {
        return $this->id ? SalaryBonusPolicyId::fromString($this->id) : null;
    }

    abstract public function calculateBonusFor(Contract $contract, DateTimeInterface $at): Money;

    abstract public function calculateTotalFor(Contract $contract, DateTimeInterface $at): Money;
}
