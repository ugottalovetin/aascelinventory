<?php

declare(strict_types=1);

$items = isset($items) && is_array($items) ? $items : [];
$stats = isset($stats) && is_array($stats) ? $stats : [];
$analytics = isset($analytics) && is_array($analytics) ? $analytics : [];
$successMessage = isset($successMessage) && is_string($successMessage) ? $successMessage : '';
$errorMessage = isset($errorMessage) && is_string($errorMessage) ? $errorMessage : '';

$totalItems = (int) ($stats['total_items'] ?? 0);
$lowStockCount = (int) ($stats['low_stock'] ?? 0);
$inventoryValue = (float) ($stats['inventory_value'] ?? 0);
$totalStockUnits = (int) ($analytics['total_stock_units'] ?? 0);
$averagePrice = (float) ($analytics['average_price'] ?? 0);
$distinctCategories = (int) ($analytics['distinct_categories'] ?? 0);
$highValueItems = (int) ($analytics['high_value_items'] ?? 0);
$topCategory = (string) ($analytics['top_category'] ?? 'N/A');
$topItemName = (string) ($analytics['top_item_name'] ?? 'N/A');
$topItemValue = (float) ($analytics['top_item_value'] ?? 0);
$categoryBreakdown = isset($analytics['category_breakdown']) && is_array($analytics['category_breakdown'])
    ? $analytics['category_breakdown']
    : [];
$topCategoryValue = 0.0;

if ($categoryBreakdown !== [] && isset($categoryBreakdown[0]['value_total'])) {
    $topCategoryValue = (float) $categoryBreakdown[0]['value_total'];
}

$analyticsChartPoints = [];
foreach ($categoryBreakdown as $categoryData) {
    $analyticsChartPoints[] = [
        'category' => (string) ($categoryData['category'] ?? 'Unknown'),
        'item_count' => (int) ($categoryData['item_count'] ?? 0),
        'stock_total' => (int) ($categoryData['stock_total'] ?? 0),
        'value_total' => (float) ($categoryData['value_total'] ?? 0),
    ];
}

$analyticsChartJson = json_encode($analyticsChartPoints);
if ($analyticsChartJson === false) {
    $analyticsChartJson = '[]';
}
?>

<header class="dashboard-reveal mb-7 flex flex-wrap items-end justify-between gap-3">
    <div>
        <p class="text-sm uppercase tracking-[0.28em] text-brand-400">Control Center</p>
        <h2 class="mt-2 text-3xl font-semibold text-brand-500">Inventory Dashboard</h2>
    </div>
    <a
        href="index.php?page=inventory-form"
        class="inline-flex items-center rounded-xl bg-brand-300 px-4 py-2.5 text-sm font-semibold text-white shadow-lg shadow-brand-300/40 transition hover:bg-brand-400"
    >
        Add New Item
    </a>
</header>

<?php if ($successMessage !== ''): ?>
    <div class="mb-5 rounded-xl border border-emerald-500/40 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-800">
        <?= htmlspecialchars($successMessage, ENT_QUOTES, 'UTF-8') ?>
    </div>
<?php endif; ?>

<?php if ($errorMessage !== ''): ?>
    <div class="mb-5 rounded-xl border border-rose-500/50 bg-rose-50 px-4 py-3 text-sm font-medium text-rose-800">
        <?= htmlspecialchars($errorMessage, ENT_QUOTES, 'UTF-8') ?>
    </div>
<?php endif; ?>

<section class="mb-6 grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
    <article class="dashboard-card panel-surface rounded-2xl border border-slate-200 p-5">
        <p class="text-xs uppercase tracking-[0.26em] text-slate-500">Total Items</p>
        <p class="mt-3 text-3xl font-semibold text-brand-500"><?= $totalItems ?></p>
    </article>

    <article class="dashboard-card panel-surface rounded-2xl border border-slate-200 p-5">
        <p class="text-xs uppercase tracking-[0.26em] text-slate-500">Low Stock Alert</p>
        <p class="mt-3 text-3xl font-semibold <?= $lowStockCount > 0 ? 'text-brand-400' : 'text-emerald-600' ?>">
            <?= $lowStockCount ?>
        </p>
    </article>

    <article class="dashboard-card panel-surface rounded-2xl border border-slate-200 p-5 sm:col-span-2 xl:col-span-1">
        <p class="text-xs uppercase tracking-[0.26em] text-slate-500">Inventory Value</p>
        <p class="mt-3 text-3xl font-semibold text-brand-400">&#8369;<?= number_format($inventoryValue, 2) ?></p>
    </article>
</section>

<section class="mb-6 grid gap-4 md:grid-cols-2 xl:grid-cols-4">
    <article class="dashboard-card panel-surface rounded-2xl border border-slate-200 p-5">
        <p class="text-xs uppercase tracking-[0.26em] text-slate-500">Total Stock Units</p>
        <p class="mt-3 text-3xl font-semibold text-brand-500"><?= number_format($totalStockUnits) ?></p>
    </article>

    <article class="dashboard-card panel-surface rounded-2xl border border-slate-200 p-5">
        <p class="text-xs uppercase tracking-[0.26em] text-slate-500">Average Unit Price</p>
        <p class="mt-3 text-3xl font-semibold text-brand-400">&#8369;<?= number_format($averagePrice, 2) ?></p>
    </article>

    <article class="dashboard-card panel-surface rounded-2xl border border-slate-200 p-5">
        <p class="text-xs uppercase tracking-[0.26em] text-slate-500">Categories</p>
        <p class="mt-3 text-3xl font-semibold text-brand-500"><?= $distinctCategories ?></p>
    </article>

    <article class="dashboard-card panel-surface rounded-2xl border border-slate-200 p-5">
        <p class="text-xs uppercase tracking-[0.26em] text-slate-500">High Value SKUs</p>
        <p class="mt-3 text-3xl font-semibold text-brand-500"><?= $highValueItems ?></p>
    </article>
</section>

<section id="inventory-analytics-panel" class="dashboard-card panel-surface mb-6 rounded-2xl border border-slate-200 p-4 sm:p-6">
    <div class="mb-4 flex flex-wrap items-start justify-between gap-3">
        <div>
            <h3 class="text-lg font-semibold text-brand-500">Inventory Analytics</h3>
            <p class="text-sm text-slate-500">
                Top category: <span class="font-semibold text-brand-400"><?= htmlspecialchars($topCategory, ENT_QUOTES, 'UTF-8') ?></span>
                | Top item: <span class="font-semibold text-brand-400"><?= htmlspecialchars($topItemName, ENT_QUOTES, 'UTF-8') ?></span>
                (&#8369;<?= number_format($topItemValue, 2) ?>)
            </p>
        </div>

        <?php if ($categoryBreakdown !== []): ?>
            <div class="flex flex-wrap items-center gap-2">
                <button
                    type="button"
                    data-analytics-view="current"
                    class="analytics-switch-btn rounded-full border border-brand-300 bg-brand-300 px-3 py-1.5 text-xs font-semibold text-white shadow-sm transition"
                >
                    Current
                </button>
                <button
                    type="button"
                    data-analytics-view="graph"
                    class="analytics-switch-btn rounded-full border border-slate-300 bg-white px-3 py-1.5 text-xs font-semibold text-slate-600 transition hover:border-brand-300 hover:text-brand-500"
                >
                    Graph
                </button>
                <button
                    type="button"
                    data-analytics-view="column"
                    class="analytics-switch-btn rounded-full border border-slate-300 bg-white px-3 py-1.5 text-xs font-semibold text-slate-600 transition hover:border-brand-300 hover:text-brand-500"
                >
                    Column
                </button>
                <button
                    type="button"
                    data-analytics-view="pie"
                    class="analytics-switch-btn rounded-full border border-slate-300 bg-white px-3 py-1.5 text-xs font-semibold text-slate-600 transition hover:border-brand-300 hover:text-brand-500"
                >
                    Pie
                </button>
                <button
                    type="button"
                    data-analytics-view="line"
                    class="analytics-switch-btn rounded-full border border-slate-300 bg-white px-3 py-1.5 text-xs font-semibold text-slate-600 transition hover:border-brand-300 hover:text-brand-500"
                >
                    Line
                </button>
                <button
                    type="button"
                    data-analytics-view="bar"
                    class="analytics-switch-btn rounded-full border border-slate-300 bg-white px-3 py-1.5 text-xs font-semibold text-slate-600 transition hover:border-brand-300 hover:text-brand-500"
                >
                    Bar
                </button>
            </div>
        <?php endif; ?>
    </div>

    <?php if ($categoryBreakdown === []): ?>
        <p class="text-sm text-slate-500">No analytics available yet. Add inventory items to see trends.</p>
    <?php else: ?>
        <div id="analytics-current-view" class="space-y-3">
            <?php foreach (array_slice($categoryBreakdown, 0, 5) as $categoryData): ?>
                <?php
                $categoryName = (string) ($categoryData['category'] ?? 'Unknown');
                $categoryValue = (float) ($categoryData['value_total'] ?? 0);
                $barWidth = $topCategoryValue > 0
                    ? min(100, ($categoryValue / $topCategoryValue) * 100)
                    : 0;
                ?>
                <div>
                    <div class="mb-1 flex items-center justify-between text-xs text-slate-600">
                        <span class="font-semibold text-slate-700"><?= htmlspecialchars($categoryName, ENT_QUOTES, 'UTF-8') ?></span>
                        <span>
                            <?= (int) ($categoryData['item_count'] ?? 0) ?> items,
                            <?= (int) ($categoryData['stock_total'] ?? 0) ?> stock,
                            &#8369;<?= number_format($categoryValue, 2) ?>
                        </span>
                    </div>
                    <div class="h-2.5 rounded-full bg-slate-100">
                        <div
                            class="h-2.5 rounded-full bg-brand-300 transition-all"
                            style="width: <?= number_format($barWidth, 2, '.', '') ?>%;"
                        ></div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <div id="analytics-chart-view" class="hidden rounded-xl border border-slate-100 bg-white/60 p-3">
            <div class="h-[340px]">
                <canvas
                    id="analytics-chart-canvas"
                    data-chart-points="<?= htmlspecialchars($analyticsChartJson, ENT_QUOTES, 'UTF-8') ?>"
                ></canvas>
            </div>
        </div>
    <?php endif; ?>
</section>

<section class="dashboard-table panel-surface rounded-2xl border border-slate-200 p-4 sm:p-6">
    <div class="mb-4 flex flex-wrap items-center justify-between gap-2">
        <h3 class="text-lg font-semibold text-brand-500">Current Inventory</h3>
        <p class="text-sm text-slate-500">Scrollable list of all product lines</p>
    </div>

    <div class="max-h-[460px] overflow-auto rounded-xl border border-slate-100">
        <table class="min-w-full divide-y divide-slate-200 text-sm">
            <thead class="sticky top-0 z-10 bg-white">
                <tr class="text-left text-xs uppercase tracking-[0.18em] text-slate-500">
                    <th class="px-3 py-3">ID</th>
                    <th class="px-3 py-3">Product</th>
                    <th class="px-3 py-3">Category</th>
                    <th class="px-3 py-3">Stock</th>
                    <th class="px-3 py-3">Unit</th>
                    <th class="px-3 py-3">Price</th>
                    <th class="px-3 py-3">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 text-slate-700">
                <?php if ($items === []): ?>
                    <tr>
                        <td class="px-3 py-5 text-slate-500 text-center" colspan="7">No inventory items found.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($items as $item): ?>
                        <?php $isLowStock = (int) $item['stock_level'] <= 10; ?>
                        <tr class="table-row-item <?= $isLowStock ? 'bg-brand-200/20' : 'hover:bg-slate-50' ?> transition-colors">
                            <td class="px-3 py-3 font-medium text-slate-600"><?= (int) $item['id'] ?></td>
                            <td class="px-3 py-3 font-semibold text-brand-500">
                                <?= htmlspecialchars((string) $item['name'], ENT_QUOTES, 'UTF-8') ?>
                            </td>
                            <td class="px-3 py-3 text-slate-600"><?= htmlspecialchars((string) $item['category'], ENT_QUOTES, 'UTF-8') ?></td>
                            <td class="px-3 py-3 <?= $isLowStock ? 'font-bold text-brand-400' : '' ?>">
                                <?= (int) $item['stock_level'] ?>
                            </td>
                            <td class="px-3 py-3 text-slate-600"><?= htmlspecialchars((string) $item['unit'], ENT_QUOTES, 'UTF-8') ?></td>
                            <td class="px-3 py-3 text-slate-700">&#8369;<?= number_format((float) $item['price'], 2) ?></td>
                            <td class="px-3 py-3">
                                <div class="flex flex-wrap items-center gap-2">
                                    <a
                                        href="index.php?page=edit&id=<?= (int) $item['id'] ?>"
                                        class="inline-flex rounded-lg border border-brand-300 px-3 py-1.5 text-xs font-semibold text-brand-400 transition hover:bg-brand-300 hover:text-white"
                                    >
                                        Edit
                                    </a>
                                    <form
                                        action="index.php?page=delete"
                                        method="post"
                                        onsubmit="return confirm('Remove this item from inventory?');"
                                    >
                                        <input type="hidden" name="id" value="<?= (int) $item['id'] ?>">
                                        <button
                                            type="submit"
                                            class="inline-flex rounded-lg border border-rose-300 px-3 py-1.5 text-xs font-semibold text-rose-600 transition hover:bg-rose-500 hover:text-white"
                                        >
                                            Remove
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</section>
