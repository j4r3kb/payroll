<?php

declare(strict_types=1);

namespace App\Common\Application\Query;

abstract class AbstractChoiceView
{
    protected function __construct(
        public readonly array $choices
    )
    {
    }

    public static function create(array $choices): static
    {
        return new static($choices);
    }
}
