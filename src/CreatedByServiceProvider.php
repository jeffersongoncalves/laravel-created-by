<?php

namespace JeffersonGoncalves\CreatedBy;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Foundation\Auth\User;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class CreatedByServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('laravel-created-by')
            ->hasConfigFile();
    }

    public function packageRegistered(): void
    {
        $createdBy = config('created-by.columns.created_by', 'created_by');
        $updatedBy = config('created-by.columns.updated_by', 'updated_by');
        $deletedBy = config('created-by.columns.deleted_by', 'deleted_by');
        $restoredBy = config('created-by.columns.restored_by', 'restored_by');
        $restoredAt = config('created-by.columns.restored_at', 'restored_at');

        if (! Blueprint::hasMacro('createdBy')) {
            Blueprint::macro('createdBy', function () use ($createdBy) {
                $this->foreignIdFor(config('auth.providers.users.model', User::class), $createdBy)
                    ->nullable()
                    ->default(null);
            });
        }
        if (! Blueprint::hasMacro('updatedBy')) {
            Blueprint::macro('updatedBy', function () use ($updatedBy) {
                $this->foreignIdFor(config('auth.providers.users.model', User::class), $updatedBy)
                    ->nullable()
                    ->default(null);
            });
        }
        if (! Blueprint::hasMacro('deletedBy')) {
            Blueprint::macro('deletedBy', function () use ($deletedBy) {
                $this->foreignIdFor(config('auth.providers.users.model', User::class), $deletedBy)
                    ->nullable()
                    ->default(null);
            });
        }
        if (! Blueprint::hasMacro('restoredBy')) {
            Blueprint::macro('restoredBy', function () use ($restoredBy) {
                $this->foreignIdFor(config('auth.providers.users.model', User::class), $restoredBy)
                    ->nullable()
                    ->default(null);
            });
        }
        if (! Blueprint::hasMacro('restoredAt')) {
            Blueprint::macro('restoredAt', function () use ($restoredAt) {
                $this->timestamp($restoredAt)->nullable()->default(null);
            });
        }
        if (! Blueprint::hasMacro('dropCreatedBy')) {
            Blueprint::macro('dropCreatedBy', function () use ($createdBy) {
                $this->dropColumn($createdBy);
            });
        }
        if (! Blueprint::hasMacro('dropUpdatedBy')) {
            Blueprint::macro('dropUpdatedBy', function () use ($updatedBy) {
                $this->dropColumn($updatedBy);
            });
        }
        if (! Blueprint::hasMacro('dropDeletedBy')) {
            Blueprint::macro('dropDeletedBy', function () use ($deletedBy) {
                $this->dropColumn($deletedBy);
            });
        }
        if (! Blueprint::hasMacro('dropRestoredBy')) {
            Blueprint::macro('dropRestoredBy', function () use ($restoredBy) {
                $this->dropColumn($restoredBy);
            });
        }
        if (! Blueprint::hasMacro('dropRestoredAt')) {
            Blueprint::macro('dropRestoredAt', function () use ($restoredAt) {
                $this->dropColumn($restoredAt);
            });
        }
    }
}
