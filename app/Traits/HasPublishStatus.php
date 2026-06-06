<?php

namespace App\Traits;

use Illuminate\Support\Facades\Schema;

/**
 * Unifies publish state across legacy (is_published) and current (status) columns.
 */
trait HasPublishStatus
{
    protected static ?bool $hasStatusColumn = null;

    protected static ?bool $hasIsPublishedColumn = null;

    protected static function resolvePublishColumns(): void
    {
        if (static::$hasStatusColumn !== null) {
            return;
        }

        $table = (new static)->getTable();
        static::$hasStatusColumn = Schema::hasColumn($table, 'status');
        static::$hasIsPublishedColumn = Schema::hasColumn($table, 'is_published');
    }

    public function scopePublished($query)
    {
        static::resolvePublishColumns();

        if (static::$hasStatusColumn) {
            return $query->where('status', 'published');
        }

        if (static::$hasIsPublishedColumn) {
            return $query->where('is_published', 1);
        }

        return $query;
    }

    public function scopeDraft($query)
    {
        static::resolvePublishColumns();

        if (static::$hasStatusColumn) {
            return $query->where('status', 'draft');
        }

        if (static::$hasIsPublishedColumn) {
            return $query->where('is_published', 0);
        }

        return $query;
    }

    public static function publishAttributes(bool $published): array
    {
        static::resolvePublishColumns();

        $attrs = [];

        if (static::$hasStatusColumn) {
            $attrs['status'] = $published ? 'published' : 'draft';
        }

        if (static::$hasIsPublishedColumn) {
            $attrs['is_published'] = $published ? 1 : 0;
        }

        return $attrs;
    }

    public function isPublished(): bool
    {
        static::resolvePublishColumns();

        if (static::$hasStatusColumn) {
            return ($this->attributes['status'] ?? 'draft') === 'published';
        }

        if (static::$hasIsPublishedColumn) {
            return (bool) ($this->attributes['is_published'] ?? false);
        }

        return false;
    }

    /** Readable status for admin UI (works with legacy is_published column). */
    public function getStatusAttribute($value): string
    {
        if ($value !== null && $value !== '') {
            return (string) $value;
        }

        return $this->isPublished() ? 'published' : 'draft';
    }
}
