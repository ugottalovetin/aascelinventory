<?php

declare(strict_types=1);

$activePage = isset($activePage) && is_string($activePage) ? $activePage : 'dashboard';
$title = isset($title) && is_string($title) ? $title : 'Restaurant Inventory';
$content = isset($content) && is_string($content) ? $content : '';

$dashboardClasses = $activePage === 'dashboard'
    ? 'border border-cyan-300/40 bg-cyan-400/10 text-cyan-100'
    : 'text-slate-300 hover:bg-slate-800/70 hover:text-white';

$formClasses = $activePage === 'form'
    ? 'border border-cyan-300/40 bg-cyan-400/10 text-cyan-100'
    : 'text-slate-300 hover:bg-slate-800/70 hover:text-white';
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
                    fontFamily: {
                        display: ['Sora', 'ui-sans-serif', 'sans-serif'],
                    },
                },
            },
        };
    </script>
    <link rel="stylesheet" href="assets/css/app.css">
</head>
<body class="inventory-bg min-h-screen font-display text-slate-100 antialiased">
    <div class="pointer-events-none fixed inset-0 soft-grid opacity-30"></div>

    <div class="relative min-h-screen lg:grid lg:grid-cols-[260px_1fr]">
        <aside class="panel-surface border-b border-slate-700/80 p-6 lg:border-b-0 lg:border-r">
            <p class="text-xs font-semibold uppercase tracking-[0.32em] text-cyan-200/80">Stock Hub</p>
            <h1 class="mt-3 text-2xl font-semibold text-white">Restaurant Inventory</h1>
            <p class="mt-2 text-sm text-slate-300">Native PHP mini-framework dashboard.</p>

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

    <div id="butterfly-cursor" class="cursor-butterfly" aria-hidden="true">
        <svg viewBox="0 0 96 96" role="presentation" focusable="false">
            <path d="M48 50C48 32 30 18 12 20C14 39 24 53 48 56Z" fill="#34d399"></path>
            <path d="M48 50C48 32 66 18 84 20C82 39 72 53 48 56Z" fill="#22d3ee"></path>
            <path d="M48 48C48 64 34 78 16 76C18 60 26 50 48 46Z" fill="#60a5fa"></path>
            <path d="M48 48C48 64 62 78 80 76C78 60 70 50 48 46Z" fill="#38bdf8"></path>
            <line x1="48" y1="26" x2="48" y2="70" stroke="#e2e8f0" stroke-width="3" stroke-linecap="round"></line>
            <circle cx="48" cy="22" r="4" fill="#e2e8f0"></circle>
        </svg>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/gsap.min.js" referrerpolicy="no-referrer"></script>
    <script src="assets/js/app.js"></script>
</body>
</html>
