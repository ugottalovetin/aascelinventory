<?php

declare(strict_types=1);

namespace Core;

class Session
{
    private const FLASH_KEY = '__flash';

    public function __construct()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public function has(string $key): bool
    {
        return array_key_exists($key, $_SESSION);
    }

    public function get(string $key, mixed $default = null): mixed
    {
        return $_SESSION[$key] ?? $default;
    }

    public function set(string $key, mixed $value): void
    {
        $_SESSION[$key] = $value;
    }

    public function setFlash(string $key, mixed $value): void
    {
        if (!isset($_SESSION[self::FLASH_KEY]) || !is_array($_SESSION[self::FLASH_KEY])) {
            $_SESSION[self::FLASH_KEY] = [];
        }

        $_SESSION[self::FLASH_KEY][$key] = $value;
    }

    public function getFlash(string $key, mixed $default = null): mixed
    {
        if (!isset($_SESSION[self::FLASH_KEY]) || !is_array($_SESSION[self::FLASH_KEY])) {
            return $default;
        }

        if (!array_key_exists($key, $_SESSION[self::FLASH_KEY])) {
            return $default;
        }

        $value = $_SESSION[self::FLASH_KEY][$key];
        unset($_SESSION[self::FLASH_KEY][$key]);

        return $value;
    }
}
