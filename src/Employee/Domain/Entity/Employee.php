<?php

declare(strict_types=1);

namespace App\Employee\Domain\Entity;

use App\Employee\Domain\ValueObject\EmployeeId;
use Webmozart\Assert\Assert;

class Employee
{
    private readonly string $id;

    private function __construct(
        EmployeeId $id,
        public readonly string $firstName,
        public readonly string $lastName
    )
    {
        $this->id = $id->__toString();
    }

    public static function create(
        string $firstName,
        string $lastName,
    ): static
    {
        Assert::stringNotEmpty($firstName, 'First name can not be empty');
        Assert::stringNotEmpty($lastName, 'Last name can not be empty');

        return new static(
            EmployeeId::create(),
            $firstName,
            $lastName,
        );
    }

    public function id(): EmployeeId
    {
        return EmployeeId::fromString($this->id);
    }
}
