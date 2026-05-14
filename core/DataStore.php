<?php

declare(strict_types=1);

namespace Core;

use PDO;
use PDOException;

class DataStore
{
    private PDO $db;
    private const LOW_STOCK_THRESHOLD = 10;

    public function __construct(private Session $session)
    {
        $dbPath = __DIR__ . '/../database.sqlite';
        
        try {
            $this->db = new PDO('sqlite:' . $dbPath);
            $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->bootstrap();
        } catch (PDOException $e) {
            die("Database connection failed: " . $e->getMessage());
        }
    }

    private function bootstrap(): void
    {
        $this->db->exec("
            CREATE TABLE IF NOT EXISTS items (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                name TEXT NOT NULL,
                category TEXT NOT NULL,
                stock_level INTEGER NOT NULL,
                unit TEXT NOT NULL,
                price REAL NOT NULL
            )
        ");

        $stmt = $this->db->query("SELECT COUNT(*) FROM items");
        if ($stmt && $stmt->fetchColumn() == 0) {
            $this->db->exec("
                INSERT INTO items (name, category, stock_level, unit, price) VALUES 
                ('Tomato Sauce', 'Condiments', 24, 'bottle', 4.75),
                ('Spaghetti', 'Dry Goods', 58, 'pack', 2.45),
                ('Mozzarella', 'Dairy', 8, 'kg', 9.90)
            ");
        }
    }

    public function getCategories(): array
    {
        return [
            'Frozen Goods',
            'Packaging Supplies',
            'Wet Goods',
            'Poultry',
            'Cooking Supplies',
        ];
    }

    public function getUnits(): array
    {
        return [
            'Unit',
            'Grams',
            'Gallon',
            'Cans',
        ];
    }

    public function all(): array
    {
        $stmt = $this->db->query("SELECT * FROM items ORDER BY id ASC");
        return $stmt ? $stmt->fetchAll(PDO::FETCH_ASSOC) : [];
    }

    public function find(int $id): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM items WHERE id = :id");
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $item = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return $item ?: null;
    }

    public function save(array $itemData, ?int $id = null): int
    {
        if ($id === null) {
            $stmt = $this->db->prepare("
                INSERT INTO items (name, category, stock_level, unit, price) 
                VALUES (:name, :category, :stock_level, :unit, :price)
            ");
        } else {
            $stmt = $this->db->prepare("
                UPDATE items SET 
                    name = :name, 
                    category = :category, 
                    stock_level = :stock_level, 
                    unit = :unit, 
                    price = :price 
                WHERE id = :id
            ");
            $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        }

        $stmt->bindValue(':name', $itemData['name']);
        $stmt->bindValue(':category', $itemData['category']);
        $stmt->bindValue(':stock_level', $itemData['stock_level'], PDO::PARAM_INT);
        $stmt->bindValue(':unit', $itemData['unit']);
        $stmt->bindValue(':price', $itemData['price']);
        
        $stmt->execute();

        return $id ?? (int) $this->db->lastInsertId();
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare("DELETE FROM items WHERE id = :id");
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->rowCount() > 0;
    }

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

    public function getAnalytics(): array
    {
        $items = $this->all();
        $totalStockUnits = 0;
        $priceSum = 0.0;
        $highValueItems = 0;
        $topItemName = 'N/A';
        $topItemValue = 0.0;

        /**
         * @var array<string, array<string, int|float>> $categoryBuckets
         */
        $categoryBuckets = [];

        foreach ($items as $item) {
            $stockLevel = (int) $item['stock_level'];
            $price = (float) $item['price'];
            $name = (string) $item['name'];
            $category = (string) $item['category'];
            $itemValue = $stockLevel * $price;

            $totalStockUnits += $stockLevel;
            $priceSum += $price;

            if ($itemValue >= 5000) {
                $highValueItems++;
            }

            if ($itemValue > $topItemValue) {
                $topItemValue = $itemValue;
                $topItemName = $name;
            }

            if (!isset($categoryBuckets[$category])) {
                $categoryBuckets[$category] = [
                    'item_count' => 0,
                    'stock_total' => 0,
                    'value_total' => 0.0,
                ];
            }

            $categoryBuckets[$category]['item_count'] = (int) $categoryBuckets[$category]['item_count'] + 1;
            $categoryBuckets[$category]['stock_total'] = (int) $categoryBuckets[$category]['stock_total'] + $stockLevel;
            $categoryBuckets[$category]['value_total'] = (float) $categoryBuckets[$category]['value_total'] + $itemValue;
        }

        $itemCount = count($items);
        $averagePrice = $itemCount > 0 ? $priceSum / $itemCount : 0.0;

        $topCategory = 'N/A';
        if ($categoryBuckets !== []) {
            uasort(
                $categoryBuckets,
                static fn (array $left, array $right): int => (float) $right['value_total'] <=> (float) $left['value_total']
            );

            $firstCategory = array_key_first($categoryBuckets);
            if (is_string($firstCategory) && $firstCategory !== '') {
                $topCategory = $firstCategory;
            }
        }

        $categoryBreakdown = [];
        foreach ($categoryBuckets as $category => $bucket) {
            $categoryBreakdown[] = [
                'category' => $category,
                'item_count' => (int) $bucket['item_count'],
                'stock_total' => (int) $bucket['stock_total'],
                'value_total' => (float) $bucket['value_total'],
            ];
        }

        return [
            'total_stock_units' => $totalStockUnits,
            'average_price' => $averagePrice,
            'distinct_categories' => count($categoryBuckets),
            'high_value_items' => $highValueItems,
            'top_category' => $topCategory,
            'top_item_name' => $topItemName,
            'top_item_value' => $topItemValue,
            'category_breakdown' => $categoryBreakdown,
        ];
    }
}
