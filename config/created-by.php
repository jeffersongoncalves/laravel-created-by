<?php

// config for JeffersonGoncalves/CreatedBy
return [
    /*
    |--------------------------------------------------------------------------
    | Authentication Guard
    |--------------------------------------------------------------------------
    |
    | The authentication guard used to resolve the currently logged-in user
    | whose ID will be stored in the audit columns. When set to null the
    | application's default guard is used.
    |
    */
    'guard' => null,

    /*
    |--------------------------------------------------------------------------
    | Column Names
    |--------------------------------------------------------------------------
    |
    | The column names used by the package to store the audit information. You
    | may customize these to match your existing database schema.
    |
    */
    'columns' => [
        'created_by' => 'created_by',
        'updated_by' => 'updated_by',
        'deleted_by' => 'deleted_by',
        'restored_by' => 'restored_by',
        'restored_at' => 'restored_at',
    ],
];
