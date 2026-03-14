<?php

declare(strict_types=1);

$activePage = isset($activePage) && is_string($activePage) ? $activePage : 'dashboard';
$title = isset($title) && is_string($title) ? $title : 'Restaurant Inventory';
$content = isset($content) && is_string($content) ? $content : '';
$cssVersion = (string) @filemtime(__DIR__ . '/../assets/css/app.css');
$jsVersion = (string) @filemtime(__DIR__ . '/../assets/js/app.js');

$dashboardClasses = $activePage === 'dashboard'
    ? 'border border-brand-300/35 bg-brand-200/75 text-brand-500 shadow-sm'
    : 'text-slate-700 hover:bg-brand-200/50 hover:text-brand-500';

$formClasses = $activePage === 'form'
    ? 'border border-brand-300/35 bg-brand-200/75 text-brand-500 shadow-sm'
    : 'text-slate-700 hover:bg-brand-200/50 hover:text-brand-500';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title, ENT_QUOTES, 'UTF-8') ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Sora:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        brand: {
                            100: '#fcfff6',
                            200: '#f5d96e',
                            300: '#dc551d',
                            400: '#a42310',
                            500: '#400c0c',
                        }
                    },
                    fontFamily: {
                        display: ['Sora', 'ui-sans-serif', 'sans-serif'],
                    },
                },
            },
        };
    </script>
    <link rel="stylesheet" href="assets/css/app.css?v=<?= htmlspecialchars($cssVersion, ENT_QUOTES, 'UTF-8') ?>">
</head>
<body
    class="inventory-bg min-h-screen font-display text-slate-800 antialiased"
    style="background:#fcfff6 !important;"
    data-page="<?= htmlspecialchars($activePage, ENT_QUOTES, 'UTF-8') ?>"
>
    <div class="pointer-events-none fixed inset-0 soft-grid opacity-30"></div>

    <div class="relative min-h-screen lg:grid lg:grid-cols-[260px_1fr]">
        <aside class="panel-surface border-b border-brand-200 p-6 lg:border-b-0 lg:border-r">
            <p class="text-xs font-semibold uppercase tracking-[0.32em] text-brand-300">Chick-n-Dip</p>
            <h1 class="mt-3 text-2xl font-semibold text-brand-500">System Inventory</h1>
            <p class="mt-2 text-sm text-slate-600">Manage your restaurant items.</p>

            <nav class="mt-8 space-y-2">
                <a
                    href="index.php?page=dashboard"
                    class="block rounded-xl px-4 py-3 text-sm font-semibold transition <?= $dashboardClasses ?>"
                >
                    Inventory Dashboard
                </a>
                <a
                    href="index.php?page=inventory-form"
                    class="block rounded-xl px-4 py-3 text-sm font-semibold transition <?= $formClasses ?>"
                >
                    Add Inventory Item
                </a>
            </nav>
        </aside>

        <main class="px-5 py-6 sm:px-8 sm:py-8 lg:px-10 lg:py-10">
            <?= $content ?>
        </main>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/gsap.min.js" referrerpolicy="no-referrer"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js" referrerpolicy="no-referrer"></script>
    <script src="assets/js/app.js?v=<?= htmlspecialchars($jsVersion, ENT_QUOTES, 'UTF-8') ?>"></script>
</body>
</html>
