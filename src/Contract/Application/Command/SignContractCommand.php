<?php

declare(strict_types=1);

namespace App\Contract\Application\Command;

use App\Common\Application\Command\AbstractCreationCommand;

final class SignContractCommand extends AbstractCreationCommand
{
    public function __construct(
        public readonly string $companyId,
        public readonly string $departmentId,
        public readonly string $employeeId,
        public readonly ?string $salaryBonusPolicyId,
        public readonly string $effectiveDate,
        public readonly ?string $terminationDate,
        public readonly int $salaryAmount,
        public readonly string $salaryCurrency
    )
    {
    }
}
