## Laravel Created By

### Overview
Laravel Created By automatically tracks which user created, updated, deleted, or restored Eloquent models. It provides traits for models and Blueprint macros for migrations, logging the authenticated user's ID into dedicated columns.

**Namespace:** `JeffersonGoncalves\CreatedBy`
**Service Provider:** `CreatedByServiceProvider` (auto-discovered)

### Key Concepts
- **Traits:** Add `WithCreatedBy`, `WithUpdatedBy`, `WithDeletedBy`, `WithRestoredBy`, or `WithRestoredAt` to models.
- **Blueprint macros:** Use `$table->createdBy()`, `$table->updatedBy()`, etc. in migrations.
- **Global scopes:** Each trait registers query macros for filtering and eager loading.
- **Auto-detection:** Uses `auth()->id()` to automatically set the user ID on model events.

### Available Traits

| Trait | Column | Event | Relationship |
|-------|--------|-------|-------------|
| `WithCreatedBy` | `created_by` | `creating` | `createdBy()` |
| `WithUpdatedBy` | `updated_by` | `creating`, `updating` | `updatedBy()` |
| `WithDeletedBy` | `deleted_by` | `deleting` | `deletedBy()` |
| `WithRestoredBy` | `restored_by` | `restoring` | `restoredBy()` |
| `WithRestoredAt` | `restored_at` | `restoring` | -- |

### Model Usage

@verbatim
<code-snippet name="model-usage" lang="php">
use JeffersonGoncalves\CreatedBy\Models\Concerns\WithCreatedBy;
use JeffersonGoncalves\CreatedBy\Models\Concerns\WithUpdatedBy;
use JeffersonGoncalves\CreatedBy\Models\Concerns\WithDeletedBy;
use JeffersonGoncalves\CreatedBy\Models\Concerns\WithRestoredBy;
use JeffersonGoncalves\CreatedBy\Models\Concerns\WithRestoredAt;

class Post extends Model
{
    use WithCreatedBy, WithUpdatedBy, WithDeletedBy, WithRestoredBy, WithRestoredAt;
    use SoftDeletes; // Required for WithDeletedBy, WithRestoredBy, WithRestoredAt
}
</code-snippet>
@endverbatim

### Migration Macros

@verbatim
<code-snippet name="migration-macros" lang="php">
Schema::create('posts', function (Blueprint $table) {
    $table->id();
    $table->string('title');
    $table->timestamps();
    $table->softDeletes();
    $table->createdBy();    // Adds nullable created_by foreign ID
    $table->updatedBy();    // Adds nullable updated_by foreign ID
    $table->deletedBy();    // Adds nullable deleted_by foreign ID
    $table->restoredBy();   // Adds nullable restored_by foreign ID
    $table->restoredAt();   // Adds nullable restored_at timestamp
});

// Drop macros for rollback
$table->dropCreatedBy();
$table->dropUpdatedBy();
$table->dropDeletedBy();
$table->dropRestoredBy();
$table->dropRestoredAt();
</code-snippet>
@endverbatim

### Query Scopes

Each trait adds query macros via global scopes:

@verbatim
<code-snippet name="query-scopes" lang="php">
// Filter by user ID
Post::createdBy($userId)->get();
Post::updatedBy($userId)->get();
Post::deletedBy($userId)->get();
Post::restoredBy($userId)->get();

// Eager load relationships
Post::withCreatedBy()->get();
Post::withUpdatedBy()->get();
Post::withDeletedBy()->get();
Post::withRestoredBy()->get();
</code-snippet>
@endverbatim

### Configuration
- No config file. The user model is resolved from `config('auth.providers.users.model')` with fallback to `Illuminate\Foundation\Auth\User`.
- All columns are nullable foreign IDs referencing the configured user model.
- Each trait is independent -- use only the ones you need.

### Conventions
- Traits are in `JeffersonGoncalves\CreatedBy\Models\Concerns\` namespace.
- Scopes are in `JeffersonGoncalves\CreatedBy\Models\Scope\` namespace.
- `WithDeletedBy`, `WithRestoredBy`, and `WithRestoredAt` require `SoftDeletes` on the model.
- `WithUpdatedBy` sets `updated_by` on both `creating` and `updating` events.
- All `BelongsTo` relationships use the user model from auth config.
