<?php

declare(strict_types=1);

namespace App\Company\Application\Command;

use App\Common\Application\Command\AbstractCreationCommand;

final class AddCompanyCommand extends AbstractCreationCommand
{
    public function __construct(
        public readonly string $companyName
    )
    {
    }
}
