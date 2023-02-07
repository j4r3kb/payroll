<?php

declare(strict_types=1);

namespace App\Payroll\Application\Query;

class PayrollView
{
    /**
     * @param PayrollItemView[] $items
     */
    protected function __construct(
        public readonly string $companyName,
        public readonly int $year,
        public readonly int $month,
        public readonly string $createdAt,
        public readonly array $items
    )
    {
    }

    public static function create(
        string $companyName,
        int $year,
        int $month,
        string $createdAt,
        array $items
    ): static
    {
        $items = array_filter($items, static function ($item) {
            return $item instanceof PayrollItemView;
        });

        return new static(
            $companyName,
            $year,
            $month,
            $createdAt,
            $items
        );
    }

    public function itemsToArray(): array
    {
        $items = [];
        foreach ($this->items as $item) {
            $items[] = $item->toArray();
        }

        return $items;
    }
}
