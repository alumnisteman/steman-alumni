<?php

namespace App\Traits;

use Illuminate\Support\Facades\DB;
use Exception;

trait WithDatabaseTransactions
{
    /**
     * Execute a callback within a database transaction.
     * Automatically rolls back on exception.
     *
     * @param callable $callback
     * @param int $attempts Number of retry attempts
     * @return mixed
     * @throws Exception
     */
    protected function executeInTransaction(callable $callback, int $attempts = 1)
    {
        return DB::transaction(function () use ($callback) {
            return $callback();
        }, $attempts);
    }

    /**
     * Execute a callback with automatic retry on deadlock.
     *
     * @param callable $callback
     * @param int $maxAttempts
     * @return mixed
     * @throws Exception
     */
    protected function executeWithRetry(callable $callback, int $maxAttempts = 3)
    {
        $attempt = 0;
        
        while ($attempt < $maxAttempts) {
            try {
                return $this->executeInTransaction($callback);
            } catch (Exception $e) {
                $attempt++;
                
                if ($attempt >= $maxAttempts) {
                    throw $e;
                }
                
                // Exponential backoff
                $delay = pow(2, $attempt) * 100000; // microseconds
                usleep($delay);
            }
        }
    }
}
