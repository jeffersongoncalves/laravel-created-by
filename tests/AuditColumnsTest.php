<?php

use JeffersonGoncalves\CreatedBy\Tests\TestSupport\Models\TestModel;
use JeffersonGoncalves\CreatedBy\Tests\TestSupport\Models\TestUser;

it('sets created_by and updated_by when creating while authenticated', function () {
    $user = TestUser::create(['name' => 'Author']);
    $this->actingAs($user);

    $model = TestModel::create(['name' => 'Post']);

    expect($model->created_by)->toBe($user->id)
        ->and($model->updated_by)->toBe($user->id);

    $this->assertDatabaseHas('test_models', [
        'id' => $model->id,
        'created_by' => $user->id,
        'updated_by' => $user->id,
    ]);
});

it('updates updated_by but keeps created_by when updating', function () {
    $author = TestUser::create(['name' => 'Author']);
    $editor = TestUser::create(['name' => 'Editor']);

    $this->actingAs($author);
    $model = TestModel::create(['name' => 'Post']);

    $this->actingAs($editor);
    $model->update(['name' => 'Edited']);

    expect($model->fresh()->created_by)->toBe($author->id)
        ->and($model->fresh()->updated_by)->toBe($editor->id);
});

it('persists deleted_by on soft delete', function () {
    $user = TestUser::create(['name' => 'Remover']);
    $this->actingAs($user);

    $model = TestModel::create(['name' => 'Post']);
    $model->delete();

    expect($model->deleted_by)->toBe($user->id);

    $this->assertDatabaseHas('test_models', [
        'id' => $model->id,
        'deleted_by' => $user->id,
    ]);
    $this->assertSoftDeleted('test_models', ['id' => $model->id]);
});

it('persists restored_by and restored_at on restore', function () {
    $author = TestUser::create(['name' => 'Author']);
    $restorer = TestUser::create(['name' => 'Restorer']);

    $this->actingAs($author);
    $model = TestModel::create(['name' => 'Post']);
    $model->delete();

    $this->actingAs($restorer);
    $model->restore();

    $fresh = $model->fresh();

    expect($fresh->restored_by)->toBe($restorer->id)
        ->and($fresh->restored_at)->not->toBeNull()
        ->and($fresh->deleted_at)->toBeNull();

    $this->assertDatabaseHas('test_models', [
        'id' => $model->id,
        'restored_by' => $restorer->id,
    ]);
});

it('leaves audit columns null when no user is authenticated', function () {
    $model = TestModel::create(['name' => 'Console']);

    expect($model->created_by)->toBeNull()
        ->and($model->updated_by)->toBeNull();

    $model->delete();
    expect($model->deleted_by)->toBeNull();

    $model->restore();
    expect($model->fresh()->restored_by)->toBeNull()
        ->and($model->fresh()->restored_at)->not->toBeNull();
});

it('filters records through the createdBy query macro', function () {
    $author = TestUser::create(['name' => 'Author']);
    $other = TestUser::create(['name' => 'Other']);

    $this->actingAs($author);
    $mine = TestModel::create(['name' => 'Mine']);

    $this->actingAs($other);
    TestModel::create(['name' => 'Theirs']);

    $results = TestModel::query()->createdBy($author->id)->get();

    expect($results)->toHaveCount(1)
        ->and($results->first()->id)->toBe($mine->id);
});

it('eager loads relations through the withCreatedBy query macro', function () {
    $author = TestUser::create(['name' => 'Author']);
    $this->actingAs($author);
    TestModel::create(['name' => 'Post']);

    $model = TestModel::query()->withCreatedBy()->first();

    expect($model->relationLoaded('createdBy'))->toBeTrue()
        ->and($model->createdBy->id)->toBe($author->id);
});
