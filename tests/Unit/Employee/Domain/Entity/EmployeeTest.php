<?php

declare(strict_types=1);

namespace App\Tests\Unit\Employee\Domain\Entity;

use App\Employee\Domain\Entity\Employee;
use PHPUnit\Framework\TestCase;
use Webmozart\Assert\InvalidArgumentException;

class EmployeeTest extends TestCase
{
    public function testFirstNameCanNotBeEmptyString(): void
    {
        $this->expectException(InvalidArgumentException::class);
        Employee::create('', 'Test');
    }

    public function testLastNameCanNotBeEmptyString(): void
    {
        $this->expectException(InvalidArgumentException::class);
        Employee::create('Test', '');
    }
}
