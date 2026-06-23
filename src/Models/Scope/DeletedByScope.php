<?php

namespace JeffersonGoncalves\CreatedBy\Models\Scope;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class DeletedByScope implements Scope
{
    public function apply(Builder $builder, Model $model) {}

    public function extend(Builder $builder): void
    {
        $builder->macro('deletedBy', function (Builder $builder, $value) {
            return $builder->where(config('created-by.columns.deleted_by', 'deleted_by'), $value);
        });
        $builder->macro('withDeletedBy', function (Builder $builder) {
            return $builder->with('deletedBy');
        });
    }
}
