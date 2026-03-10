<?php

declare(strict_types=1);

namespace Core;

class DataStore
{
    private const SESSION_KEY = 'inventory_items';

    private const NEXT_ID_KEY = 'inventory_next_id';

    private const LOW_STOCK_THRESHOLD = 10;

    public function __construct(private Session $session)
    {
        $this->bootstrap();
    }

    /**
     * Seed session storage when inventory data does not exist.
     */
    private function bootstrap(): void
    {
        if ($this->session->has(self::SESSION_KEY) && $this->session->has(self::NEXT_ID_KEY)) {
            return;
        }

        $seedData = [
            1 => [
                'id' => 1,
                'name' => 'Tomato Sauce',
                'category' => 'Condiments',
                'stock_level' => 24,
                'unit' => 'bottle',
                'price' => 4.75,
            ],
            2 => [
                'id' => 2,
                'name' => 'Spaghetti',
                'category' => 'Dry Goods',
                'stock_level' => 58,
                'unit' => 'pack',
                'price' => 2.45,
            ],
            3 => [
                'id' => 3,
                'name' => 'Mozzarella',
                'category' => 'Dairy',
                'stock_level' => 8,
                'unit' => 'kg',
                'price' => 9.90,
            ],
        ];

        $this->session->set(self::SESSION_KEY, $seedData);
        $this->session->set(self::NEXT_ID_KEY, 4);
    }

    /**
     * @return array<int, string>
     */
    public function getCategories(): array
    {
        return [
            'Produce',
            'Dairy',
            'Meat',
            'Condiments',
            'Beverages',
            'Dry Goods',
        ];
    }

    /**
     * @return array<int, string>
     */
    public function getUnits(): array
    {
        return [
            'kg',
            'liter',
            'pcs',
            'bottle',
            'pack',
        ];
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function all(): array
    {
        $items = $this->session->get(self::SESSION_KEY, []);

        if (!is_array($items)) {
            return [];
        }

        ksort($items);

        return $items;
    }

    /**
     * @return array<string, mixed>|null
     */
    public function find(int $id): ?array
    {
        $items = $this->all();

        return $items[$id] ?? null;
    }

    /**
     * @param array<string, mixed> $itemData
     */
    public function save(array $itemData, ?int $id = null): int
    {
        $items = $this->all();

        if ($id === null) {
            $id = (int) $this->session->get(self::NEXT_ID_KEY, 1);
            $this->session->set(self::NEXT_ID_KEY, $id + 1);
        }

        $items[$id] = [
            'id' => $id,
            'name' => (string) $itemData['name'],
            'category' => (string) $itemData['category'],
            'stock_level' => (int) $itemData['stock_level'],
            'unit' => (string) $itemData['unit'],
            'price' => (float) $itemData['price'],
        ];

        $this->session->set(self::SESSION_KEY, $items);

        return $id;
    }

    /**
     * @return array<string, int|float>
     */
    public function getStats(): array
    {
        $items = $this->all();
        $lowStockCount = 0;
        $inventoryValue = 0.0;

        foreach ($items as $item) {
            $stockLevel = (int) $item['stock_level'];
            $price = (float) $item['price'];

            if ($stockLevel <= self::LOW_STOCK_THRESHOLD) {
                $lowStockCount++;
            }

            $inventoryValue += $stockLevel * $price;
        }

        return [
            'total_items' => count($items),
            'low_stock' => $lowStockCount,
            'inventory_value' => $inventoryValue,
        ];
    }
}
