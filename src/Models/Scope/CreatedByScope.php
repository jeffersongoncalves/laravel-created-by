<?php

namespace JeffersonGoncalves\CreatedBy\Models\Scope;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class CreatedByScope implements Scope
{
    public function apply(Builder $builder, Model $model) {}

    public function extend(Builder $builder): void
    {
        $builder->macro('createdBy', function (Builder $builder, $value) {
            return $builder->where(config('created-by.columns.created_by', 'created_by'), $value);
        });
        $builder->macro('withCreatedBy', function (Builder $builder) {
            return $builder->with('createdBy');
        });
    }
}
