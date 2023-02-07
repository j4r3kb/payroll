<?php

declare(strict_types=1);

namespace App\Tests\Unit\Policy\Domain\Entity;

use App\Contract\Domain\Entity\Contract;
use App\Policy\Domain\Entity\NoSalaryBonusPolicy;
use Brick\Money\Money;
use Carbon\CarbonImmutable;
use PHPUnit\Framework\TestCase;

class NoSalaryBonusPolicyTest extends TestCase
{
    private ?NoSalaryBonusPolicy $policy = null;

    private ?Contract $activeContract = null;

    private ?Contract $inactiveContract = null;

    public function testReturnsZeroAsBonus(): void
    {
        $this->assertTrue(
            Money::of(0, 'USD')->isEqualTo(
                $this->policy->calculateBonusFor($this->activeContract, CarbonImmutable::now())
            )
        );
    }

    public function testReturnsContractSalaryAsTotalForActiveContract(): void
    {
        $this->assertTrue(
            Money::of(1000, 'USD')->isEqualTo(
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

    protected function setUp(): void
    {
        parent::setUp();
        $this->policy = NoSalaryBonusPolicy::create();
        $this->activeContract = $this->prepareActiveContract();
        $this->inactiveContract = $this->prepareInactiveContract();
    }

    private function prepareActiveContract(): Contract
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
