<?php

declare(strict_types=1);

namespace App\Tests\Unit\Contract\Domain\ValueObject;

use App\Contract\Domain\ValueObject\ContractDuration;
use App\Contract\Exception\ContractEffectiveDateGreaterOrEqualTerminationDateException;
use Carbon\CarbonImmutable;
use PHPUnit\Framework\TestCase;

class ContractDurationTest extends TestCase
{
    public function testThrowsExceptionWhenContractTerminationDateIsLessOrEqualEffectiveDate(): void
    {
        $this->expectException(ContractEffectiveDateGreaterOrEqualTerminationDateException::class);
        ContractDuration::create(
            CarbonImmutable::parse('2022-01-01'),
            CarbonImmutable::parse('2022-01-01')
        );
    }

    public function testIsActiveWhenTerminationDateIsNotSet(): void
    {
        $contractDuration = ContractDuration::create(CarbonImmutable::parse('2022-01-01'), null);
        $this->assertTrue($contractDuration->isActive(CarbonImmutable::parse('2050-01-01')));
        $this->assertFalse($contractDuration->isActive(CarbonImmutable::parse('2021-12-31')));
    }

    public function testIsActiveWhenTerminationDateIsSet(): void
    {
        $contractDuration = ContractDuration::create(
            CarbonImmutable::parse('2022-01-01'),
            CarbonImmutable::parse('2022-06-30')
        );
        $this->assertTrue($contractDuration->isActive(CarbonImmutable::parse('2022-06-30')));
        $this->assertFalse($contractDuration->isActive(CarbonImmutable::parse('2022-07-01')));
        $this->assertFalse($contractDuration->isActive(CarbonImmutable::parse('2021-12-31')));
    }
}
