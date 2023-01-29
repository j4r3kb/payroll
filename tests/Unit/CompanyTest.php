<?php

declare(strict_types=1);

namespace App\Tests\Unit;

use App\Company\Domain\Entity\Company;
use PHPUnit\Framework\TestCase;

class CompanyTest extends TestCase
{
    public function testCreatesCompanyWithGivenTinAndName(): void
    {
        $company = Company::create('tax_id', 'Test Company');
        $this->assertEquals('tax_id', $company->taxId());
        $this->assertEquals('Test Company', $company->name());
    }
}
