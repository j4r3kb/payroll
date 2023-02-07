<?php

declare(strict_types=1);

namespace App\Tests\Unit\Contract\Domain\Entity;

use App\Company\Domain\ValueObject\CompanyId;
use App\Company\Domain\ValueObject\DepartmentId;
use App\Contract\Domain\Entity\Contract;
use App\Contract\Domain\Service\EmploymentValidator;
use App\Employee\Domain\ValueObject\EmployeeId;
use App\Policy\Domain\ValueObject\SalaryBonusPolicyId;
use Carbon\CarbonImmutable;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Uid\Uuid;

class ContractTest extends TestCase
{
    public function testIsActiveAndYearsInEffectReturnsCorrectValues(): void
    {
        $employmentValidator = $this->createMock(EmploymentValidator::class);
        $employmentValidator->method('employeeExists')->willReturn(true);
        $employmentValidator->method('canEmployeeBeHiredByCompany')->willReturn(true);
        $employmentValidator->method('departmentBelongsToCompany')->willReturn(true);

        $contract = Contract::sign(
            CompanyId::fromString(Uuid::v4()->toRfc4122()),
            DepartmentId::fromString(Uuid::v4()->toRfc4122()),
            EmployeeId::fromString(Uuid::v4()->toRfc4122()),
            SalaryBonusPolicyId::fromString(Uuid::v4()->toRfc4122()),
            CarbonImmutable::parse('2020-01-01'),
            CarbonImmutable::parse('2023-12-31'),
            1000,
            'USD',
            $employmentValidator
        );

        $at = CarbonImmutable::parse('2022-01-01');
        $this->assertEquals(2, $contract->yearsInEffect($at));
        $this->assertTrue($contract->isActive($at));
    }
}
