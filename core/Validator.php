<?php

declare(strict_types=1);

namespace Core;

class Validator
{
    /**
     * @var array<string, string>
     */
    private array $errors = [];

    /**
     * @param array<string, mixed> $input
     * @param array<int, string> $allowedCategories
     * @param array<int, string> $allowedUnits
     *
     * @return array<string, mixed>
     */
    public function validateInventory(
        array $input,
        array $allowedCategories,
        array $allowedUnits
    ): array {
        $this->errors = [];

        $sanitizedInput = [
            'name' => trim((string) ($input['name'] ?? '')),
            'category' => trim((string) ($input['category'] ?? '')),
            'stock_level' => trim((string) ($input['stock_level'] ?? '')),
            'unit' => trim((string) ($input['unit'] ?? '')),
            'price' => trim((string) ($input['price'] ?? '')),
        ];

        $this->validateName($sanitizedInput['name']);
        $this->validateCategory($sanitizedInput['category'], $allowedCategories);
        $this->validateStockLevel($sanitizedInput['stock_level']);
        $this->validateUnit($sanitizedInput['unit'], $allowedUnits);
        $this->validatePrice($sanitizedInput['price']);

        $validatedData = [];

        if ($this->errors === []) {
            $validatedData = [
                'name' => $sanitizedInput['name'],
                'category' => $sanitizedInput['category'],
                'stock_level' => (int) $sanitizedInput['stock_level'],
                'unit' => $sanitizedInput['unit'],
                'price' => (float) $sanitizedInput['price'],
            ];
        }

        return [
            'isValid' => $this->errors === [],
            'errors' => $this->errors,
            'input' => $sanitizedInput,
            'data' => $validatedData,
        ];
    }

    /**
     * @return array<string, string>
     */
    public function errors(): array
    {
        return $this->errors;
    }

    private function validateName(string $name): void
    {
        if ($name === '') {
            $this->addError('name', 'Product name is required.');
            return;
        }

        $compactName = str_replace(' ', '', $name);

        if ($compactName === '' || !ctype_alnum($compactName)) {
            $this->addError('name', 'Product name must be alphanumeric.');
        }
    }

    /**
     * @param array<int, string> $allowedCategories
     */
    private function validateCategory(string $category, array $allowedCategories): void
    {
        if ($category === '') {
            $this->addError('category', 'Category is required.');
            return;
        }

        if (!in_array($category, $allowedCategories, true)) {
            $this->addError('category', 'Select a valid category.');
        }
    }

    private function validateStockLevel(string $stockLevel): void
    {
        if ($stockLevel === '') {
            $this->addError('stock_level', 'Stock level is required.');
            return;
        }

        $validatedInt = filter_var($stockLevel, FILTER_VALIDATE_INT);

        if ($validatedInt === false || (int) $stockLevel < 0) {
            $this->addError('stock_level', 'Stock level must be a non-negative integer.');
        }
    }

    /**
     * @param array<int, string> $allowedUnits
     */
    private function validateUnit(string $unit, array $allowedUnits): void
    {
        if ($unit === '') {
            $this->addError('unit', 'Unit is required.');
            return;
        }

        if (!in_array($unit, $allowedUnits, true)) {
            $this->addError('unit', 'Select a valid unit.');
        }
    }

    private function validatePrice(string $price): void
    {
        if ($price === '') {
            $this->addError('price', 'Price is required.');
            return;
        }

        if (!preg_match('/^\d+(?:\.\d{1,2})?$/', $price)) {
            $this->addError('price', 'Price must be a valid decimal value.');
            return;
        }

        if ((float) $price < 0) {
            $this->addError('price', 'Price cannot be negative.');
        }
    }

    private function addError(string $field, string $message): void
    {
        $this->errors[$field] = $message;
    }
}
