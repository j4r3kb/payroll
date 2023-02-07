<?php

declare(strict_types=1);

namespace App\Policy\Application\Command;

use App\Common\Application\Command\AbstractCreationCommand;

final class AddSalaryBonusPolicyCommand extends AbstractCreationCommand
{
    public function __construct(
        public readonly string $salaryBonusPolicyClass,
        public readonly array $parameters
    )
    {
    }
}
