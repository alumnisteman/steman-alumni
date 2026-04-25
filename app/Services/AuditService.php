<?php
namespace App\Services;

use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class AuditService
{
    /**
     * Create an immutable audit log entry.
     * 
     * @param string $action
     * @param array $meta Data snapshot
     * @param string|null $customHash Optional custom hash (e.g. from the entity being audited)
     * @return AuditLog
     */
    public function log(string $action, array $meta = [], ?string $customHash = null)
    {
        $userId = Auth::id();
        $timestamp = now()->toDateTimeString();
        
        // Generate verifiable integrity hash if not provided
        // We use action, userId, timestamp and meta.
        $hash = $customHash ?: $this->calculateHash($action, $userId, $timestamp, $meta);

        return AuditLog::create([
            'action' => $action,
            'user_id' => $userId,
            'meta' => $meta,
            'hash' => $hash,
            'created_at' => $timestamp // Ensure timestamp matches the hash
        ]);
    }

    /**
     * Calculate hash deterministically
     */
    private function calculateHash($action, $userId, $timestamp, $meta): string
    {
        $salt = config('app.key'); // Use app key as salt
        return hash('sha256', $action . $userId . $timestamp . json_encode($meta) . $salt);
    }

    /**
     * Verify the integrity of a specific audit log
     */
    public function verifyIntegrity(AuditLog $log): bool
    {
        if (empty($log->hash)) return false;
        
        $expected = $this->calculateHash($log->action, $log->user_id, $log->created_at->toDateTimeString(), $log->meta);
        return $log->hash === $expected;
    }
}
