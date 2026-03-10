<?php

declare(strict_types=1);

namespace Core;

use RuntimeException;

class View
{
    public function __construct(private string $viewsPath)
    {
    }

    /**
     * @param array<string, mixed> $data
     */
    public function render(string $view, array $data = []): void
    {
        $viewFile = $this->viewsPath . '/' . $view . '.php';
        $layoutFile = $this->viewsPath . '/layout.php';

        if (!is_file($viewFile)) {
            throw new RuntimeException('View file not found: ' . $viewFile);
        }

        if (!is_file($layoutFile)) {
            throw new RuntimeException('Layout file not found: ' . $layoutFile);
        }

        extract($data, EXTR_SKIP);

        ob_start();
        require $viewFile;
        $outputBuffer = ob_get_clean();
        $content = is_string($outputBuffer) ? $outputBuffer : '';

        $title = isset($title) && is_string($title) ? $title : 'Restaurant Inventory';

        require $layoutFile;
    }
}
