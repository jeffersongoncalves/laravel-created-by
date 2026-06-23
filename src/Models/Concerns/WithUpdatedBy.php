<?php

namespace JeffersonGoncalves\CreatedBy\Models\Concerns;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User;
use JeffersonGoncalves\CreatedBy\Models\Scope\UpdatedByScope;

/**
 * @mixin Model
 */
trait WithUpdatedBy
{
    public static function bootWithUpdatedBy(): void
    {
        static::addGlobalScope(new UpdatedByScope);
        static::creating(static function (Model $model): void {
            $column = config('created-by.columns.updated_by', 'updated_by');
            $model->{$column} = auth()->guard(config('created-by.guard'))->id();
        });
        static::updating(static function (Model $model): void {
            $column = config('created-by.columns.updated_by', 'updated_by');
            $model->{$column} = auth()->guard(config('created-by.guard'))->id();
        });
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(
            config('auth.providers.users.model', User::class),
            config('created-by.columns.updated_by', 'updated_by')
        );
    }
}
