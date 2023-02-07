<?php

declare(strict_types=1);

namespace App\Tests\Unit\Policy\Domain\Entity;

use App\Contract\Domain\Entity\Contract;
use App\Policy\Domain\Entity\PercentageSalaryBonusPolicy;
use App\Policy\Domain\Exception\NegativeSalaryBonusRatePerHundredException;
use Brick\Money\Money;
use Carbon\CarbonImmutable;
use PHPUnit\Framework\TestCase;

class PercentageSalaryBonusPolicyTest extends TestCase
{
    private ?PercentageSalaryBonusPolicy $policy = null;

    private ?Contract $activeContract = null;

    private ?Contract $inactiveContract = null;

    public function testReturnsCorrectAmountAsBonusForActiveContract(): void
    {
        $this->assertTrue(
            Money::of(300, 'USD')->isEqualTo(
                $this->policy->calculateBonusFor($this->activeContract, CarbonImmutable::now())
            )
        );
    }

    public function testReturnsZeroAsBonusForInactiveContract(): void
    {
        $this->assertTrue(
            Money::of(0, 'USD')->isEqualTo(
                $this->policy->calculateBonusFor($this->inactiveContract, CarbonImmutable::now())
            )
        );
    }

    public function testReturnsCorrectAmountAsTotalForActiveContract(): void
    {
        $this->assertTrue(
            Money::of(1300, 'USD')->isEqualTo(
                $this->policy->calculateTotalFor($this->activeContract, CarbonImmutable::now())
            )
        );
    }

    public function testReturnsZeroAsTotalForInactiveContract(): void
    {
        $this->assertTrue(
            Money::of(0, 'USD')->isEqualTo(
                $this->policy->calculateTotalFor($this->inactiveContract, CarbonImmutable::now())
            )
        );
    }

    public function testCanNotCreateWithNegativeRatePerHundred(): void
    {
        $this->expectException(NegativeSalaryBonusRatePerHundredException::class);
        PercentageSalaryBonusPolicy::create(-1);
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->policy = PercentageSalaryBonusPolicy::create(30);
        $this->activeContract = $this->prepareContract();
        $this->inactiveContract = $this->prepareInactiveContract();
    }

    private function prepareContract(): Contract
    {
        $contract = $this->createMock(Contract::class);
        $contract->method('salaryMoney')->willReturn(Money::of(1000, 'USD'));
        $contract->method('isActive')->willReturn(true);
        $contract->method('yearsInEffect')->willReturn(2);

        return $contract;
    }

    private function prepareInactiveContract(): Contract
    {
        $contract = $this->createMock(Contract::class);
        $contract->method('salaryMoney')->willReturn(Money::of(1000, 'USD'));
        $contract->method('isActive')->willReturn(false);
        $contract->method('yearsInEffect')->willReturn(2);

        return $contract;
    }
}
