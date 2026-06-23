<?php

namespace JeffersonGoncalves\CreatedBy\Tests;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use JeffersonGoncalves\CreatedBy\CreatedByServiceProvider;
use JeffersonGoncalves\CreatedBy\Tests\TestSupport\Models\TestUser;
use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    protected function setUp(): void
    {
        parent::setUp();

        Factory::guessFactoryNamesUsing(
            fn (string $modelName) => 'JeffersonGoncalves\\CreatedBy\\Database\\Factories\\'.class_basename($modelName).'Factory'
        );
    }

    protected function getPackageProviders($app)
    {
        return [
            CreatedByServiceProvider::class,
        ];
    }

    public function getEnvironmentSetUp($app)
    {
        config()->set('database.default', 'testing');
        config()->set('auth.providers.users.model', TestUser::class);

        Schema::create('users', function (Blueprint $table): void {
            $table->id();
            $table->string('name')->nullable();
            $table->timestamps();
        });

        Schema::create('test_models', function (Blueprint $table): void {
            $table->id();
            $table->string('name')->nullable();
            $table->string('secret')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->unsignedBigInteger('deleted_by')->nullable();
            $table->unsignedBigInteger('restored_by')->nullable();
            $table->timestamp('restored_at')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }
}
