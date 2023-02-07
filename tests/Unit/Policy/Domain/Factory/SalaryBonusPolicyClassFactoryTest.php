<?php

declare(strict_types=1);

namespace App\Tests\Unit\Policy\Domain\Factory;

use App\Policy\Domain\Entity\PercentageSalaryBonusPolicy;
use App\Policy\Domain\Entity\PerYearEmployedSalaryBonusPolicy;
use App\Policy\Domain\Enum\SalaryBonusPolicyType;
use App\Policy\Domain\Factory\SalaryBonusPolicyClassFactory;
use PHPUnit\Framework\TestCase;

class SalaryBonusPolicyClassFactoryTest extends TestCase
{
    public function testReturnsProperClassPerType(): void
    {
        $this->assertEquals(
            PercentageSalaryBonusPolicy::class,
            SalaryBonusPolicyClassFactory::getClassByType(SalaryBonusPolicyType::PERCENTAGE_BONUS)
        );
        $this->assertEquals(
            PerYearEmployedSalaryBonusPolicy::class,
            SalaryBonusPolicyClassFactory::getClassByType(SalaryBonusPolicyType::PER_YEAR_EMPLOYED_BONUS)
        );
    }
}
