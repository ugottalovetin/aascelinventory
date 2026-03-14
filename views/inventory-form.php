<?php

declare(strict_types=1);

$isEdit = isset($isEdit) && is_bool($isEdit) ? $isEdit : false;
$itemId = isset($itemId) ? (int) $itemId : 0;
$categories = isset($categories) && is_array($categories) ? $categories : [];
$units = isset($units) && is_array($units) ? $units : [];
$formData = isset($formData) && is_array($formData) ? $formData : [];
$errors = isset($errors) && is_array($errors) ? $errors : [];

$nameValue = (string) ($formData['name'] ?? '');
$categoryValue = (string) ($formData['category'] ?? '');
$stockValue = (string) ($formData['stock_level'] ?? '0');
$unitValue = (string) ($formData['unit'] ?? '');
$priceValue = (string) ($formData['price'] ?? '0.00');
?>

<header class="mb-7 dashboard-reveal">
    <p class="text-sm uppercase tracking-[0.28em] text-brand-400">Inventory Entry</p>
    <h2 class="mt-2 text-3xl font-semibold text-brand-500">
        <?= $isEdit ? 'Edit Inventory Item' : 'Add Inventory Item' ?>
    </h2>
    <p class="mt-2 text-sm text-slate-500">All fields are validated before the item is stored.</p>
</header>

<section class="dashboard-reveal panel-surface rounded-2xl border border-slate-200 p-5 sm:p-7">
    <form action="index.php?page=save" method="post" class="space-y-5" novalidate>
        <?php if ($isEdit): ?>
            <input type="hidden" name="id" value="<?= $itemId ?>">
        <?php endif; ?>

        <div>
            <?php if (isset($errors['name'])): ?>
                <p class="mb-2 text-sm font-semibold text-rose-600">
                    <?= htmlspecialchars((string) $errors['name'], ENT_QUOTES, 'UTF-8') ?>
                </p>
            <?php endif; ?>
            <label for="name" class="mb-2 block text-sm font-semibold text-slate-700">Product Name</label>
            <input
                id="name"
                name="name"
                type="text"
                value="<?= htmlspecialchars($nameValue, ENT_QUOTES, 'UTF-8') ?>"
                class="w-full rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-slate-800 outline-none transition focus:border-brand-300 focus:ring-2 focus:ring-brand-300/30"
                placeholder="Example: Olive Oil"
            >
        </div>

        <div class="grid gap-5 md:grid-cols-2">
            <div>
                <?php if (isset($errors['category'])): ?>
                    <p class="mb-2 text-sm font-semibold text-rose-600">
                        <?= htmlspecialchars((string) $errors['category'], ENT_QUOTES, 'UTF-8') ?>
                    </p>
                <?php endif; ?>
                <label for="category" class="mb-2 block text-sm font-semibold text-slate-700">Category</label>
                <select
                    id="category"
                    name="category"
                    class="w-full rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-slate-800 outline-none transition focus:border-brand-300 focus:ring-2 focus:ring-brand-300/30"
                >
                    <?php foreach ($categories as $category): ?>
                        <option
                            value="<?= htmlspecialchars((string) $category, ENT_QUOTES, 'UTF-8') ?>"
                            <?= (string) $category === $categoryValue ? 'selected' : '' ?>
                        >
                            <?= htmlspecialchars((string) $category, ENT_QUOTES, 'UTF-8') ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div>
                <?php if (isset($errors['unit'])): ?>
                    <p class="mb-2 text-sm font-semibold text-rose-600">
                        <?= htmlspecialchars((string) $errors['unit'], ENT_QUOTES, 'UTF-8') ?>
                    </p>
                <?php endif; ?>
                <label for="unit" class="mb-2 block text-sm font-semibold text-slate-700">Unit</label>
                <select
                    id="unit"
                    name="unit"
                    class="w-full rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-slate-800 outline-none transition focus:border-brand-300 focus:ring-2 focus:ring-brand-300/30"
                >
                    <?php foreach ($units as $unit): ?>
                        <option
                            value="<?= htmlspecialchars((string) $unit, ENT_QUOTES, 'UTF-8') ?>"
                            <?= (string) $unit === $unitValue ? 'selected' : '' ?>
                        >
                            <?= htmlspecialchars((string) $unit, ENT_QUOTES, 'UTF-8') ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <div class="grid gap-5 md:grid-cols-2">
            <div>
                <?php if (isset($errors['stock_level'])): ?>
                    <p class="mb-2 text-sm font-semibold text-rose-600">
                        <?= htmlspecialchars((string) $errors['stock_level'], ENT_QUOTES, 'UTF-8') ?>
                    </p>
                <?php endif; ?>
                <label for="stock_level" class="mb-2 block text-sm font-semibold text-slate-700">Stock Level</label>
                <input
                    id="stock_level"
                    name="stock_level"
                    type="number"
                    min="0"
                    step="1"
                    value="<?= htmlspecialchars($stockValue, ENT_QUOTES, 'UTF-8') ?>"
                    class="w-full rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-slate-800 outline-none transition focus:border-brand-300 focus:ring-2 focus:ring-brand-300/30"
                    placeholder="0"
                >
            </div>

            <div>
                <?php if (isset($errors['price'])): ?>
                    <p class="mb-2 text-sm font-semibold text-rose-600">
                        <?= htmlspecialchars((string) $errors['price'], ENT_QUOTES, 'UTF-8') ?>
                    </p>
                <?php endif; ?>
                <label for="price" class="mb-2 block text-sm font-semibold text-slate-700">Price (&#8369;)</label>
                <input
                    id="price"
                    name="price"
                    type="text"
                    value="<?= htmlspecialchars($priceValue, ENT_QUOTES, 'UTF-8') ?>"
                    class="w-full rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-slate-800 outline-none transition focus:border-brand-300 focus:ring-2 focus:ring-brand-300/30"
                    placeholder="0.00"
                >
            </div>
        </div>

        <div class="flex flex-wrap items-center gap-3 pt-2">
            <button
                type="submit"
                class="rounded-xl bg-brand-300 px-5 py-2.5 text-sm font-semibold text-white shadow-lg shadow-brand-300/40 transition hover:bg-brand-400"
            >
                <?= $isEdit ? 'Update Item' : 'Save Item' ?>
            </button>

            <a
                href="index.php?page=dashboard"
                class="rounded-xl border border-slate-300 px-5 py-2.5 text-sm font-semibold text-slate-600 transition hover:border-slate-400 hover:text-slate-800"
            >
                Back to Dashboard
            </a>
        </div>
    </form>
</section>
