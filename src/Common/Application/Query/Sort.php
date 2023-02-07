<?php

declare(strict_types=1);

namespace App\Common\Application\Query;

final class Sort
{
    private function __construct(
        public readonly string $fieldName,
        public readonly SortOrder $sortOrder
    )
    {
    }

    public static function create(
        string $fieldName,
        SortOrder $sortOrder = SortOrder::ASC
    ): self
    {
        return new self($fieldName, $sortOrder);
    }
}
