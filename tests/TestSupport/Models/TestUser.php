<?php

namespace JeffersonGoncalves\CreatedBy\Tests\TestSupport\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class TestUser extends Authenticatable
{
    public $table = 'users';

    protected $guarded = [];
}
