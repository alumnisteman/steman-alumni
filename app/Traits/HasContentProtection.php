<?php

namespace App\Traits;

use App\Services\ContentModerationService;
use Illuminate\Support\Facades\Log;

trait HasContentProtection
{
    /**
     * Boot the trait to attach listeners.
     */
    protected static function bootHasContentProtection()
    {
        static::saving(function ($model) {
            // 1. Automatic Sanitization (Anti-XSS)
            $sanitizable = property_exists($model, 'sanitizable') ? $model->sanitizable : ['content', 'message', 'title'];
            
            foreach ($sanitizable as $field) {
                if ($model->isDirty($field) && !empty($model->$field)) {
                    $model->$field = strip_tags($model->$field);
                }
            }

            // 2. Automatic Word Filtering (Anti-SARA/Profanity)
            // We check the same fields for clean content
            foreach ($sanitizable as $field) {
                if ($model->isDirty($field) && !empty($model->$field)) {
                    if (!ContentModerationService::isClean($model->$field)) {
                        $violation = ContentModerationService::getViolation($model->$field);
                        Log::warning("Blocked unsafe content in " . class_basename($model) . " field '{$field}': Contains '{$violation}'");
                        
                        // Throwing a validation exception that Laravel's controller can catch
                        throw \Illuminate\Validation\ValidationException::withMessages([
                            $field => '⚠️ PERINGATAN ADMIN: Konten Anda mengandung kata-kata yang melanggar aturan komunitas.'
                        ]);
                    }
                }
            }
        });
    }
}
