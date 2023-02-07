<?php

declare(strict_types=1);

namespace App\Common\Application\Query;

final class Filter
{
    private function __construct(
        public readonly string $fieldName,
        public readonly string $like
    )
    {
    }

    public static function create(
        string $fieldName,
        string $like
    ): self
    {
        return new self($fieldName, $like);
    }
}
