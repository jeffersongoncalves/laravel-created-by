<?php

namespace JeffersonGoncalves\CreatedBy\Models\Concerns;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User;

/**
 * @mixin \Illuminate\Database\Eloquent\Model
 * @mixin \Illuminate\Database\Eloquent\SoftDeletes
 */
trait WithRestoredAt
{
    public static function bootWithRestoredAt(): void
    {
        static::restoring(static function ($model) {
            $model->restored_at = now();
        });
    }
}
