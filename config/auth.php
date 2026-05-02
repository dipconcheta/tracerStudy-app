<?php

/*
|--------------------------------------------------------------------------
| File: config/auth.php (bagian yang perlu diubah)
|--------------------------------------------------------------------------
| Ganti seluruh isi config/auth.php di proyek Laravel Anda dengan ini,
| atau sesuaikan bagian 'providers' dan 'passwords' saja.
*/

return [

    'defaults' => [
        'guard'     => 'web',
        'passwords' => 'User',
    ],

    'guards' => [
        'web' => [
            'driver'   => 'session',
            'provider' => 'User',
        ],
    ],

    'providers' => [
        'User' => [
            'driver' => 'eloquent',
            'model'  => App\Models\User::class,
        ],
    ],

    'passwords' => [
        'User' => [
            'provider' => 'User',
            'table'    => 'password_reset_tokens',
            'expire'   => 60,
            'throttle' => 60,
        ],
    ],

    'password_timeout' => 10800,
];
