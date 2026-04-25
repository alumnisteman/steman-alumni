<?php

namespace App\Observers;

use App\Models\ActivityLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class ActivityObserver
{
    /**
     * Handle the Model "created" event.
     */
    public function created(Model $model): void
    {
        $this->logActivity('Created', $model);
    }

    /**
     * Handle the Model "updated" event.
     */
    public function updated(Model $model): void
    {
        // Don't log if only timestamps changed
        if ($model->wasChanged() && !$model->wasChanged(['updated_at'])) {
            $this->logActivity('Updated', $model);
        }
    }

    /**
     * Handle the Model "deleted" event.
     */
    public function deleted(Model $model): void
    {
        $this->logActivity('Deleted', $model);
    }

    /**
     * Helper to log activity
     */
    private function logActivity(string $action, Model $model): void
    {
        // Only log if we have an authenticated user (to avoid logging automated background tasks, unless desired)
        if (Auth::check()) {
            $modelName = class_basename($model);
            
            // Try to get a meaningful identifier from the model
            $identifier = $model->name ?? $model->title ?? $model->id ?? 'Unknown';

            ActivityLog::create([
                'user_id'     => Auth::id(),
                'action'      => "{$action} {$modelName}",
                'description' => "{$action} {$modelName} record: {$identifier}",
                'ip_address'  => Request::ip(),
                'user_agent'  => Request::userAgent(),
            ]);
        }
    }
}
