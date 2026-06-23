<?php

namespace JeffersonGoncalves\CreatedBy\Models\Concerns;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User;
use JeffersonGoncalves\CreatedBy\Models\Scope\RestoredByScope;

/**
 * @mixin Model
 * @mixin SoftDeletes
 */
trait WithRestoredBy
{
    public static function bootWithRestoredBy(): void
    {
        static::addGlobalScope(new RestoredByScope);
        static::restoring(static function (Model $model): void {
            $column = config('created-by.columns.restored_by', 'restored_by');
            $model->{$column} = auth()->guard(config('created-by.guard'))->id();
        });
    }

    public function restoredBy(): BelongsTo
    {
        return $this->belongsTo(
            config('auth.providers.users.model', User::class),
            config('created-by.columns.restored_by', 'restored_by')
        );
    }
}
