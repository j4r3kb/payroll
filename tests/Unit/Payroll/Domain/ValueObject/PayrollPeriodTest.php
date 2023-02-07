<?php

declare(strict_types=1);

namespace App\Tests\Unit\Payroll\Domain\ValueObject;

use App\Payroll\Domain\Exception\MonthNumberInvalidException;
use App\Payroll\Domain\ValueObject\PayrollPeriod;
use PHPUnit\Framework\TestCase;

class PayrollPeriodTest extends TestCase
{
    public function testThrowsExceptionWhenMonthIsInvalid(): void
    {
        $this->expectException(MonthNumberInvalidException::class);
        PayrollPeriod::create(2020, 13);
        $this->expectException(MonthNumberInvalidException::class);
        PayrollPeriod::create(2020, 0);
    }

    public function testReturnsDatetimeAtEndOfMonth(): void
    {
        $payrollPeriod = PayrollPeriod::create(2022, 6);
        $this->assertEquals(
            '2022-06-30 23:59:59',
            $payrollPeriod->toDate()->format('Y-m-d H:i:s')
        );
    }
}
