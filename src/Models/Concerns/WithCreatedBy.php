<?php

namespace JeffersonGoncalves\CreatedBy\Models\Concerns;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User;
use JeffersonGoncalves\CreatedBy\Models\Scope\CreatedByScope;

/**
 * @mixin Model
 */
trait WithCreatedBy
{
    public static function bootWithCreatedBy(): void
    {
        static::addGlobalScope(new CreatedByScope);
        static::creating(static function (Model $model): void {
            $column = config('created-by.columns.created_by', 'created_by');
            $model->{$column} = auth()->guard(config('created-by.guard'))->id();
        });
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(
            config('auth.providers.users.model', User::class),
            config('created-by.columns.created_by', 'created_by')
        );
    }
}
