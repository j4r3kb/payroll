<?php

declare(strict_types=1);

namespace App\Tests\Unit\Company\Domain\Entity;

use App\Company\Domain\Entity\Department;
use App\Company\Domain\ValueObject\CompanyId;
use App\Company\Domain\ValueObject\DepartmentId;
use App\Policy\Domain\ValueObject\SalaryBonusPolicyId;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Uid\Uuid;

class DepartmentTest extends TestCase
{
    private ?Department $department = null;

    public function testIdentifiers(): void
    {
        $this->assertInstanceOf(DepartmentId::class, $this->department->id());
        $this->assertInstanceOf(CompanyId::class, $this->department->companyId());
        $this->assertInstanceOf(SalaryBonusPolicyId::class, $this->department->salaryBonusPolicyId());
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->department = Department::create(
            CompanyId::fromString(Uuid::v4()->toRfc4122()),
            'Test Name',
            SalaryBonusPolicyId::fromString(Uuid::v4()->toRfc4122())
        );
    }
}
