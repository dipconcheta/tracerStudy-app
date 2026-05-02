<?php

/*
|--------------------------------------------------------------------------
| File: bootstrap/app.php  (Laravel 11)
|--------------------------------------------------------------------------
| Tambahkan alias middleware 'cek.peran' agar bisa dipakai di routes.
| Untuk Laravel 10 ke bawah, daftarkan di app/Http/Kernel.php:
|
|   protected $routeMiddleware = [
|       'cek.peran' => \App\Http\Middleware\CekPeran::class,
|   ];
*/

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Alias middleware peran
        $middleware->alias([
            'cek.peran' => \App\Http\Middleware\CekPeran::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
