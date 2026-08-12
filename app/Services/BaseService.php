<?php

namespace App\Services;

abstract class BaseService
{
    /**
     * Log info with prefix.
     *
     * @param string $message
     * @param array $context
     */
    protected function logInfo(string $message, array $context = []): void
    {
        \Illuminate\Support\Facades\Log::info('[' . static::class . '] ' . $message, $context);
    }

    /**
     * Log error with prefix.
     *
     * @param string $message
     * @param array $context
     */
    protected function logError(string $message, array $context = []): void
    {
        \Illuminate\Support\Facades\Log::error('[' . static::class . '] ' . $message, $context);
    }
}
