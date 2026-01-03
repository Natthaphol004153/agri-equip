<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        
        // ✅ 1. ลงทะเบียนชื่อ 'admin' ตรงนี้ (สำคัญมาก!)
        $middleware->alias([
            'is_admin' => \App\Http\Middleware\IsAdmin::class,
        ]);

        // ✅ 2. ตั้งค่า Redirect (กันหน้า Login วนลูป)
        $middleware->redirectUsersTo(function (Request $request) {
            if (Auth::check()) {
                // ถ้าเป็น Admin ไปหน้า Admin
                if (Auth::user()->role === 'admin') {
                    return route('admin.dashboard');
                }
                // ถ้าเป็น Staff ไปหน้า Staff Dashboard
                return route('staff.dashboard');
            }
            return route('login');
        });

    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();