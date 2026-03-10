<?php

declare(strict_types=1);

spl_autoload_register(
    static function (string $className): void {
        $prefix = 'Core\\';

        if (strpos($className, $prefix) !== 0) {
            return;
        }

        $relativeClass = substr($className, strlen($prefix));
        $classFile = __DIR__ . '/core/' . str_replace('\\', '/', $relativeClass) . '.php';

        if (is_file($classFile)) {
            require_once $classFile;
        }
    }
);

$app = new Core\App(__DIR__);
$app->run();
