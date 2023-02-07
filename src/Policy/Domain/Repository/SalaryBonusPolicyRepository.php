<?php

declare(strict_types=1);

namespace App\Policy\Domain\Repository;

use App\Policy\Domain\Entity\SalaryBonusPolicy;
use App\Policy\Domain\ValueObject\SalaryBonusPolicyId;

interface SalaryBonusPolicyRepository
{
    public function save(SalaryBonusPolicy $salaryBonusPolicy): void;

    public function findOne(SalaryBonusPolicyId $salaryBonusPolicyId): ?SalaryBonusPolicy;

    public function all(): array;
}
