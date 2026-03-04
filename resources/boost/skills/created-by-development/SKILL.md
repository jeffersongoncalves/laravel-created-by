---
name: created-by-development
description: Development guide for laravel-created-by, a package that automatically tracks which user created, updated, deleted, or restored Eloquent models via traits and Blueprint macros.
---

# Created By Development Skill

## When to use this skill

- When developing or extending the laravel-created-by package
- When adding new tracking traits (e.g., new model events)
- When modifying Blueprint macros for migration columns
- When writing tests for user tracking functionality
- When adding new query scopes for tracking columns
- When debugging auto-assignment of user IDs on model events

## Setup

### Requirements
- PHP 8.2+
- Laravel 11, 12, or 13
- `spatie/laravel-package-tools` ^1.14

### Installation

```bash
composer require jeffersongoncalves/laravel-created-by
```

No migrations are published by the package itself. You add columns to your own tables using the Blueprint macros.

## Package Structure

```
src/
  CreatedByServiceProvider.php          # Registers Blueprint macros
  Models/
    Concerns/
      WithCreatedBy.php                 # Trait: auto-set created_by on creating
      WithUpdatedBy.php                 # Trait: auto-set updated_by on creating/updating
      WithDeletedBy.php                 # Trait: auto-set deleted_by on deleting
      WithRestoredBy.php                # Trait: auto-set restored_by on restoring
      WithRestoredAt.php                # Trait: auto-set restored_at on restoring
    Scope/
      CreatedByScope.php                # Query macros: createdBy(), withCreatedBy()
      UpdatedByScope.php                # Query macros: updatedBy(), withUpdatedBy()
      DeletedByScope.php                # Query macros: deletedBy(), withDeletedBy()
      RestoredByScope.php               # Query macros: restoredBy(), withRestoredBy()
```

## Features

### Blueprint Macros (Service Provider)

The service provider registers Blueprint macros for adding and dropping tracking columns:

```php
// Adding columns
$table->createdBy();    // foreignIdFor(User, 'created_by')->nullable()->default(null)
$table->updatedBy();    // foreignIdFor(User, 'updated_by')->nullable()->default(null)
$table->deletedBy();    // foreignIdFor(User, 'deleted_by')->nullable()->default(null)
$table->restoredBy();   // foreignIdFor(User, 'restored_by')->nullable()->default(null)
$table->restoredAt();   // timestamp('restored_at')->nullable()->default(null)

// Dropping columns
$table->dropCreatedBy();    // dropColumn('created_by')
$table->dropUpdatedBy();    // dropColumn('updated_by')
$table->dropDeletedBy();    // dropColumn('deleted_by')
$table->dropRestoredBy();   // dropColumn('restored_by')
$table->dropRestoredAt();   // dropColumn('restored_at')
```

The user model for `foreignIdFor` is resolved from `config('auth.providers.users.model')` with a fallback to `Illuminate\Foundation\Auth\User::class`.

### WithCreatedBy Trait

Automatically sets `created_by` to the authenticated user's ID when a model is created:

```php
use JeffersonGoncalves\CreatedBy\Models\Concerns\WithCreatedBy;

class Post extends Model
{
    use WithCreatedBy;
}

// Automatic: $post->created_by = auth()->id() on creating
$post = Post::create(['title' => 'My Post']);

// Relationship
$user = $post->createdBy; // BelongsTo relationship

// Query macros
Post::createdBy($userId)->get();    // Filter by created_by
Post::withCreatedBy()->get();       // Eager load createdBy relationship
```

### WithUpdatedBy Trait

Sets `updated_by` on both `creating` and `updating` events:

```php
use JeffersonGoncalves\CreatedBy\Models\Concerns\WithUpdatedBy;

class Post extends Model
{
    use WithUpdatedBy;
}

// Set on create AND update
$post = Post::create(['title' => 'My Post']);       // updated_by = auth()->id()
$post->update(['title' => 'Updated']);               // updated_by = auth()->id()

// Relationship and query macros
$user = $post->updatedBy;
Post::updatedBy($userId)->get();
Post::withUpdatedBy()->get();
```

### WithDeletedBy Trait

Sets `deleted_by` when a model is soft-deleted. Requires `SoftDeletes`:

```php
use Illuminate\Database\Eloquent\SoftDeletes;
use JeffersonGoncalves\CreatedBy\Models\Concerns\WithDeletedBy;

class Post extends Model
{
    use SoftDeletes, WithDeletedBy;
}

// Automatic on soft delete
$post->delete(); // deleted_by = auth()->id()

// Relationship and query macros
$user = $post->deletedBy;
Post::deletedBy($userId)->get();
Post::withDeletedBy()->get();
```

### WithRestoredBy Trait

Sets `restored_by` when a soft-deleted model is restored. Requires `SoftDeletes`:

```php
use Illuminate\Database\Eloquent\SoftDeletes;
use JeffersonGoncalves\CreatedBy\Models\Concerns\WithRestoredBy;

class Post extends Model
{
    use SoftDeletes, WithRestoredBy;
}

// Automatic on restore
$post->restore(); // restored_by = auth()->id()

// Relationship and query macros
$user = $post->restoredBy;
Post::restoredBy($userId)->get();
Post::withRestoredBy()->get();
```

### WithRestoredAt Trait

Sets `restored_at` timestamp when a soft-deleted model is restored. Requires `SoftDeletes`:

```php
use Illuminate\Database\Eloquent\SoftDeletes;
use JeffersonGoncalves\CreatedBy\Models\Concerns\WithRestoredAt;

class Post extends Model
{
    use SoftDeletes, WithRestoredAt;
}

// Automatic on restore
$post->restore(); // restored_at = now()
```

Note: `WithRestoredAt` does NOT add query scopes (no scope class). It only sets the timestamp.

### Combining All Traits

```php
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use JeffersonGoncalves\CreatedBy\Models\Concerns\WithCreatedBy;
use JeffersonGoncalves\CreatedBy\Models\Concerns\WithUpdatedBy;
use JeffersonGoncalves\CreatedBy\Models\Concerns\WithDeletedBy;
use JeffersonGoncalves\CreatedBy\Models\Concerns\WithRestoredBy;
use JeffersonGoncalves\CreatedBy\Models\Concerns\WithRestoredAt;

class Post extends Model
{
    use SoftDeletes;
    use WithCreatedBy, WithUpdatedBy, WithDeletedBy, WithRestoredBy, WithRestoredAt;
}
```

### Full Migration Example

```php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('body')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->createdBy();
            $table->updatedBy();
            $table->deletedBy();
            $table->restoredBy();
            $table->restoredAt();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('posts');
    }
};
```

## Configuration

This package has no configuration file. The only configurable aspect is the user model, which is resolved from Laravel's auth config:

```php
// The user model used for foreignIdFor and BelongsTo relationships
config('auth.providers.users.model') // Fallback: Illuminate\Foundation\Auth\User::class
```

## Testing Patterns

### Testing Trait Auto-Assignment

```php
use App\Models\Post;
use App\Models\User;

it('sets created_by on creating', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $post = Post::create(['title' => 'Test']);

    expect($post->created_by)->toBe($user->id);
});

it('sets updated_by on updating', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $post = Post::create(['title' => 'Test']);
    $post->update(['title' => 'Updated']);

    expect($post->updated_by)->toBe($user->id);
});

it('sets deleted_by on soft deleting', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $post = Post::create(['title' => 'Test']);
    $post->delete();

    expect($post->deleted_by)->toBe($user->id);
});

it('sets restored_by and restored_at on restoring', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $post = Post::create(['title' => 'Test']);
    $post->delete();
    $post->restore();

    expect($post->restored_by)->toBe($user->id)
        ->and($post->restored_at)->not->toBeNull();
});
```

### Testing Query Scopes

```php
it('filters by created_by scope', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    Post::create(['title' => 'Test']);

    $posts = Post::createdBy($user->id)->get();

    expect($posts)->toHaveCount(1);
});

it('eager loads createdBy relationship', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    Post::create(['title' => 'Test']);

    $post = Post::withCreatedBy()->first();

    expect($post->relationLoaded('createdBy'))->toBeTrue()
        ->and($post->createdBy->id)->toBe($user->id);
});
```

### Testing Blueprint Macros

```php
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

it('adds created_by column via macro', function () {
    Schema::create('test_table', function (Blueprint $table) {
        $table->id();
        $table->createdBy();
    });

    expect(Schema::hasColumn('test_table', 'created_by'))->toBeTrue();

    Schema::dropIfExists('test_table');
});
```

### Testing Without Authentication

```php
it('sets null when no user is authenticated', function () {
    $post = Post::create(['title' => 'Test']);

    expect($post->created_by)->toBeNull();
});
```

### Running Tests

```bash
# Run all tests
vendor/bin/pest

# Run with coverage
vendor/bin/pest --coverage

# Static analysis
vendor/bin/phpstan analyse

# Code formatting
vendor/bin/pint
```

## Adding a New Tracking Trait

To add a new tracking trait (e.g., `WithApprovedBy`):

1. Create the trait in `src/Models/Concerns/WithApprovedBy.php`:

```php
namespace JeffersonGoncalves\CreatedBy\Models\Concerns;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User;
use JeffersonGoncalves\CreatedBy\Models\Scope\ApprovedByScope;

trait WithApprovedBy
{
    public static function bootWithApprovedBy(): void
    {
        static::addGlobalScope(new ApprovedByScope);
        // Choose the appropriate model event
        static::updating(static function ($model) {
            if ($model->isDirty('approved') && $model->approved) {
                $model->approved_by = auth()->id();
            }
        });
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(config('auth.providers.users.model', User::class));
    }
}
```

2. Create the scope in `src/Models/Scope/ApprovedByScope.php`:

```php
namespace JeffersonGoncalves\CreatedBy\Models\Scope;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class ApprovedByScope implements Scope
{
    public function apply(Builder $builder, Model $model) {}

    public function extend(Builder $builder): void
    {
        $builder->macro('approvedBy', function (Builder $builder, $value) {
            return $builder->where('approved_by', $value);
        });
        $builder->macro('withApprovedBy', function (Builder $builder) {
            return $builder->with('approvedBy');
        });
    }
}
```

3. Add Blueprint macros in `CreatedByServiceProvider::packageRegistered()`:

```php
if (! Blueprint::hasMacro('approvedBy')) {
    Blueprint::macro('approvedBy', function () {
        $this->foreignIdFor(config('auth.providers.users.model', User::class), 'approved_by')
            ->nullable()
            ->default(null);
    });
}
if (! Blueprint::hasMacro('dropApprovedBy')) {
    Blueprint::macro('dropApprovedBy', function () {
        $this->dropColumn('approved_by');
    });
}
```
