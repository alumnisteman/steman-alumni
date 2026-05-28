<?php

namespace App\Traits;

use Illuminate\Support\Facades\Log;
use Exception;

trait WithErrorHandling
{
    /**
     * Execute a callback with comprehensive error handling.
     *
     * @param callable $callback
     * @param string $operationName
     * @param mixed $defaultReturn
     * @return mixed
     */
    protected function executeWithErrorHandling(callable $callback, string $operationName, $defaultReturn = null)
    {
        try {
            return $callback();
        } catch (Exception $e) {
            Log::error("Error in {$operationName}: " . $e->getMessage(), [
                'exception' => $e,
                'trace' => $e->getTraceAsString()
            ]);
            
            // Send alert to Telegram if configured
            $this->sendErrorAlert($operationName, $e);
            
            return $defaultReturn;
        }
    }

    /**
     * Send error alert to Telegram.
     *
     * @param string $operationName
     * @param Exception $e
     * @return void
     */
    protected function sendErrorAlert(string $operationName, Exception $e)
    {
        try {
            $telegramService = app(\App\Services\TelegramService::class);
            if ($telegramService) {
                $message = "❌ Error in {$operationName}\n\n";
                $message .= "Message: {$e->getMessage()}\n";
                $message .= "File: {$e->getFile()}:{$e->getLine()}\n";
                $message .= "Time: " . now()->toDateTimeString();
                
                $telegramService->sendMessage($message);
            }
        } catch (Exception $telegramError) {
            // Don't fail if Telegram fails
            Log::error("Failed to send Telegram alert: " . $telegramError->getMessage());
        }
    }

    /**
     * Validate data with custom rules.
     *
     * @param array $data
     * @param array $rules
     * @return array
     * @throws \Illuminate\Validation\ValidationException
     */
    protected function validateData(array $data, array $rules)
    {
        $validator = validator($data, $rules);
        
        if ($validator->fails()) {
            Log::warning("Validation failed", [
                'errors' => $validator->errors()->toArray(),
                'data' => $data
            ]);
            
            throw new \Illuminate\Validation\ValidationException($validator);
        }
        
        return $validator->validated();
    }
}
