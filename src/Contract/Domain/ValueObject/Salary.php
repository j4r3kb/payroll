<?php

declare(strict_types=1);

namespace App\Contract\Domain\ValueObject;

use App\Contract\Exception\SalaryLowerThanMinimumException;
use Brick\Money\Money;

final class Salary
{
    private const MINIMUM_SALARY = 1;

    private int $amount;

    private string $currency;

    private function __construct(Money $money)
    {
        $this->amount = $money->getAmount()->toInt();
        $this->currency = $money->getCurrency()->getCurrencyCode();
    }

    public static function create(int $amount, string $currency): self
    {
        if ($amount <= self::MINIMUM_SALARY) {
            throw SalaryLowerThanMinimumException::create($amount, self::MINIMUM_SALARY);
        }

        return new self(Money::of($amount, $currency));
    }

    public function money(): Money
    {
        return Money::of($this->amount, $this->currency);
    }
}
