<?php

declare(strict_types=1);

namespace App\Tests\Unit\Payroll\Application\Query;

use App\Payroll\Application\Query\PayrollItemView;
use App\Payroll\Application\Query\PayrollView;
use PHPUnit\Framework\TestCase;

class PayrollViewTest extends TestCase
{
    public function testAddsItemsOfCorrectClass(): void
    {
        $items = [
            $this->createMock(PayrollItemView::class),
            3,
            'string',
            $this->createMock(PayrollItemView::class),
        ];

        $payrollView = PayrollView::create(
            'Company name',
            2020,
            5,
            '2020-05-31',
            $items
        );

        $this->assertCount(2, $payrollView->items);
    }
}
