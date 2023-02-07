<?php

declare(strict_types=1);

namespace App\Employee\Application\Command;

use App\Common\Application\Command\AbstractCreationCommand;

final class AddEmployeeCommand extends AbstractCreationCommand
{
    public function __construct(
        public readonly string $firstName,
        public readonly string $lastName
    )
    {
    }
}
