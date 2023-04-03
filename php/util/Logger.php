<?php

declare(strict_types=1);

class Logger
{
    public static function log(string $label, mixed $value)
    {
        $now = DateTime::createFromFormat('U.u', microtime(true) . '');
        $timestamp = $now->format('Y-m-d H:i:s.u');
        $flags = JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES;
        $formatted = json_encode($value, $flags);

        error_log('[' . $timestamp . ']' . $label . ': ' . $formatted);
    }
}
