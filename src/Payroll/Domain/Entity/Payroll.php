<?php

declare(strict_types=1);

namespace App\Payroll\Domain\Entity;

use App\Company\Domain\ValueObject\CompanyId;
use App\Payroll\Domain\ValueObject\PayrollId;
use App\Payroll\Domain\ValueObject\PayrollPeriod;
use Carbon\CarbonImmutable;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

class Payroll
{
    public readonly DateTimeInterface $createdAt;
    private readonly string $id;
    private readonly string $companyId;
    private Collection $items;

    private function __construct(
        PayrollId $id,
        CompanyId $companyId,
        public readonly string $companyName,
        private readonly PayrollPeriod $period,
    )
    {
        $this->id = $id->__toString();
        $this->companyId = $companyId->__toString();
        $this->createdAt = CarbonImmutable::now();
        $this->items = new ArrayCollection();
    }

    public static function create(
        CompanyId $companyId,
        string $companyName,
        PayrollPeriod $period
    ): static
    {
        return new static(
            PayrollId::create(),
            $companyId,
            $companyName,
            $period
        );
    }

    public function addItem(PayrollItem $item): void
    {
        $this->items->add($item);
    }
}
