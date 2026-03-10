<?php

declare(strict_types=1);

$items = isset($items) && is_array($items) ? $items : [];
$stats = isset($stats) && is_array($stats) ? $stats : [];
$successMessage = isset($successMessage) && is_string($successMessage) ? $successMessage : '';
$errorMessage = isset($errorMessage) && is_string($errorMessage) ? $errorMessage : '';

$totalItems = (int) ($stats['total_items'] ?? 0);
$lowStockCount = (int) ($stats['low_stock'] ?? 0);
$inventoryValue = (float) ($stats['inventory_value'] ?? 0);
?>

<header class="mb-7 flex flex-wrap items-end justify-between gap-3">
    <div>
        <p class="text-sm uppercase tracking-[0.28em] text-cyan-200/80">Control Center</p>
        <h2 class="mt-2 text-3xl font-semibold text-white">Inventory Dashboard</h2>
    </div>
    <a
        href="index.php?page=inventory-form"
        class="inline-flex items-center rounded-xl bg-cyan-400 px-4 py-2.5 text-sm font-semibold text-slate-950 shadow-lg shadow-cyan-900/40 transition hover:bg-cyan-300"
    >
        Add New Item
    </a>
</header>

<?php if ($successMessage !== ''): ?>
    <div class="mb-5 rounded-xl border border-emerald-400/40 bg-emerald-500/10 px-4 py-3 text-sm font-medium text-emerald-100">
        <?= htmlspecialchars($successMessage, ENT_QUOTES, 'UTF-8') ?>
    </div>
<?php endif; ?>

<?php if ($errorMessage !== ''): ?>
    <div class="mb-5 rounded-xl border border-rose-400/50 bg-rose-500/10 px-4 py-3 text-sm font-medium text-rose-100">
        <?= htmlspecialchars($errorMessage, ENT_QUOTES, 'UTF-8') ?>
    </div>
<?php endif; ?>

<section class="mb-6 grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
    <article class="panel-surface rounded-2xl border border-slate-700/60 p-5">
        <p class="text-xs uppercase tracking-[0.26em] text-slate-400">Total Items</p>
        <p class="mt-3 text-3xl font-semibold text-white"><?= $totalItems ?></p>
    </article>

    <article class="panel-surface rounded-2xl border border-slate-700/60 p-5">
        <p class="text-xs uppercase tracking-[0.26em] text-slate-400">Low Stock Alert</p>
        <p class="mt-3 text-3xl font-semibold <?= $lowStockCount > 0 ? 'text-amber-300' : 'text-emerald-300' ?>">
            <?= $lowStockCount ?>
        </p>
    </article>

    <article class="panel-surface rounded-2xl border border-slate-700/60 p-5 sm:col-span-2 xl:col-span-1">
        <p class="text-xs uppercase tracking-[0.26em] text-slate-400">Inventory Value</p>
        <p class="mt-3 text-3xl font-semibold text-cyan-200">$<?= number_format($inventoryValue, 2) ?></p>
    </article>
</section>

<section class="panel-surface rounded-2xl border border-slate-700/60 p-4 sm:p-6">
    <div class="mb-4 flex flex-wrap items-center justify-between gap-2">
        <h3 class="text-lg font-semibold text-white">Current Inventory</h3>
        <p class="text-sm text-slate-300">Showing all product lines</p>
    </div>

    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-slate-700/70 text-sm">
            <thead>
                <tr class="text-left text-xs uppercase tracking-[0.18em] text-slate-400">
                    <th class="px-3 py-3">ID</th>
                    <th class="px-3 py-3">Product</th>
                    <th class="px-3 py-3">Category</th>
                    <th class="px-3 py-3">Stock</th>
                    <th class="px-3 py-3">Unit</th>
                    <th class="px-3 py-3">Price</th>
                    <th class="px-3 py-3">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-800/80 text-slate-200">
                <?php if ($items === []): ?>
                    <tr>
                        <td class="px-3 py-5 text-slate-400" colspan="7">No inventory items found.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($items as $item): ?>
                        <?php $isLowStock = (int) $item['stock_level'] <= 10; ?>
                        <tr class="<?= $isLowStock ? 'bg-amber-400/5' : '' ?>">
                            <td class="px-3 py-3 font-medium text-slate-300"><?= (int) $item['id'] ?></td>
                            <td class="px-3 py-3 font-semibold text-white">
                                <?= htmlspecialchars((string) $item['name'], ENT_QUOTES, 'UTF-8') ?>
                            </td>
                            <td class="px-3 py-3"><?= htmlspecialchars((string) $item['category'], ENT_QUOTES, 'UTF-8') ?></td>
                            <td class="px-3 py-3 <?= $isLowStock ? 'font-semibold text-amber-300' : '' ?>">
                                <?= (int) $item['stock_level'] ?>
                            </td>
                            <td class="px-3 py-3"><?= htmlspecialchars((string) $item['unit'], ENT_QUOTES, 'UTF-8') ?></td>
                            <td class="px-3 py-3">$<?= number_format((float) $item['price'], 2) ?></td>
                            <td class="px-3 py-3">
                                <a
                                    href="index.php?page=edit&id=<?= (int) $item['id'] ?>"
                                    class="inline-flex rounded-lg border border-cyan-300/50 px-3 py-1.5 text-xs font-semibold text-cyan-100 transition hover:bg-cyan-400/10"
                                >
                                    Edit
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</section>
