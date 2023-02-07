<?php

declare(strict_types=1);

namespace App\Company\Application\Command;

use App\Common\Application\Command\AbstractCreationCommand;

final class AddDepartmentCommand extends AbstractCreationCommand
{
    public function __construct(
        public readonly string $companyId,
        public readonly string $name,
        public readonly ?string $salaryBonusPolicyId
    )
    {
    }
}
