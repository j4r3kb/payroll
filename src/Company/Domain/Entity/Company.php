<?php

declare(strict_types=1);

namespace App\Company\Domain\Entity;

class Company
{
    public function __construct(
        private string $taxId,
        private string $name
    ) {
    }

    public static function create(string $taxId, string $name): static
    {
        return new static($taxId, $name);
    }

    public function taxId(): string
    {
        return $this->taxId;
    }

    public function name(): string
    {
        return $this->name;
    }
}
