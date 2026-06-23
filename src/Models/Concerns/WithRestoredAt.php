<?php

namespace JeffersonGoncalves\CreatedBy\Models\Concerns;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @mixin Model
 * @mixin SoftDeletes
 */
trait WithRestoredAt
{
    public static function bootWithRestoredAt(): void
    {
        static::restoring(static function (Model $model): void {
            $column = config('created-by.columns.restored_at', 'restored_at');
            $model->{$column} = now();
        });
    }
}
