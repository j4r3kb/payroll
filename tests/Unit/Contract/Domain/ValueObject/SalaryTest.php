<?php

declare(strict_types=1);

namespace App\Tests\Unit\Contract\Domain\ValueObject;

use App\Contract\Domain\ValueObject\Salary;
use App\Contract\Exception\SalaryLowerThanMinimumException;
use Brick\Money\Money;
use PHPUnit\Framework\TestCase;

class SalaryTest extends TestCase
{
    public function testThrowsExceptionWhenSalaryIsBelowMinimum(): void
    {
        $this->expectException(SalaryLowerThanMinimumException::class);
        Salary::create(0, 'USD');
    }

    public function testReturnsMoney(): void
    {
        $salary = Salary::create(100, 'USD');
        $this->assertInstanceOf(Money::class, $salary->money());
    }
}
