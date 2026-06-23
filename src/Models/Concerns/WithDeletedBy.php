<?php

namespace JeffersonGoncalves\CreatedBy\Models\Concerns;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User;
use JeffersonGoncalves\CreatedBy\Models\Scope\DeletedByScope;

/**
 * @mixin Model
 */
trait WithDeletedBy
{
    public static function bootWithDeletedBy(): void
    {
        static::addGlobalScope(new DeletedByScope);

        // When the model uses SoftDeletes the "trashed" event fires right after
        // the soft delete query runs. Setting "deleted_by" during the "deleting"
        // event is not enough because runSoftDelete() only persists the
        // deleted_at (and updated_at) columns, ignoring other dirty attributes.
        if (method_exists(static::class, 'softDeleted')) {
            static::softDeleted(static function (Model $model): void {
                $column = config('created-by.columns.deleted_by', 'deleted_by');
                $model->{$column} = auth()->guard(config('created-by.guard'))->id();
                $model->saveQuietly();
            });

            return;
        }

        static::deleting(static function (Model $model): void {
            $column = config('created-by.columns.deleted_by', 'deleted_by');
            $model->{$column} = auth()->guard(config('created-by.guard'))->id();
        });
    }

    public function deletedBy(): BelongsTo
    {
        return $this->belongsTo(
            config('auth.providers.users.model', User::class),
            config('created-by.columns.deleted_by', 'deleted_by')
        );
    }
}
