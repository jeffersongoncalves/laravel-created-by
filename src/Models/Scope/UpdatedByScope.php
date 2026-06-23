<?php

namespace JeffersonGoncalves\CreatedBy\Models\Scope;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class UpdatedByScope implements Scope
{
    public function apply(Builder $builder, Model $model) {}

    public function extend(Builder $builder): void
    {
        $builder->macro('updatedBy', function (Builder $builder, $value) {
            return $builder->where(config('created-by.columns.updated_by', 'updated_by'), $value);
        });
        $builder->macro('withUpdatedBy', function (Builder $builder) {
            return $builder->with('updatedBy');
        });
    }
}
