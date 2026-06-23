<?php

namespace JeffersonGoncalves\CreatedBy\Models\Scope;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class RestoredByScope implements Scope
{
    public function apply(Builder $builder, Model $model) {}

    public function extend(Builder $builder): void
    {
        $builder->macro('restoredBy', function (Builder $builder, $value) {
            return $builder->where(config('created-by.columns.restored_by', 'restored_by'), $value);
        });
        $builder->macro('withRestoredBy', function (Builder $builder) {
            return $builder->with('restoredBy');
        });
    }
}
