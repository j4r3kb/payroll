<?php

declare(strict_types=1);

namespace App\Common\Application\Command;

use App\Common\Domain\ValueObject\Identifier;

interface CreationCommand
{
    public function setCreatedId(string $id): void;

    public function createdId(): ?string;
}
