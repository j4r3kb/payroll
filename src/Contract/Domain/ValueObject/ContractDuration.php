<?php

declare(strict_types=1);

namespace App\Contract\Domain\ValueObject;

use App\Contract\Exception\ContractEffectiveDateGreaterOrEqualTerminationDateException;
use Carbon\CarbonImmutable;
use DateTimeInterface;

final class ContractDuration
{
    private function __construct(
        private readonly DateTimeInterface $effectiveDate,
        private readonly ?DateTimeInterface $terminationDate
    )
    {
    }

    public function effectiveDate(): CarbonImmutable
    {
        return CarbonImmutable::create($this->effectiveDate);
    }

    public static function create(DateTimeInterface $effectiveDate, ?DateTimeInterface $terminationDate): self
    {
        if ($terminationDate !== null && $effectiveDate >= $terminationDate) {
            throw ContractEffectiveDateGreaterOrEqualTerminationDateException::create();
        }

        return new self($effectiveDate, $terminationDate);
    }

    public function terminationDate(): ?CarbonImmutable
    {
        return $this->terminationDate ? CarbonImmutable::create($this->terminationDate) : null;
    }

    public function isActive(DateTimeInterface $at = null): bool
    {
        if ($at === null) {
            $at = CarbonImmutable::now();
        }

        return $this->effectiveDate <= $at && ($this->terminationDate === null || $this->terminationDate >= $at);
    }
}
