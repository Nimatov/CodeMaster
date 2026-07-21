<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class AdminMiddleware
{
    public function handle($request, Closure $next)
    {
        if (Auth::check()) {
            // Проверяем по полю is_admin
            if (Auth::user()->is_admin) {
                return $next($request);
            }
            
            // ИЛИ проверяем по email (старый способ)
            if (Auth::user()->email == 'admin@example.com') {
                return $next($request);
            }
            
            // Проверяем, не заблокирован ли пользователь
            if (Auth::user()->is_blocked) {
                Auth::logout();
                return redirect('/login')->with('error', 'Ваш аккаунт заблокирован!');
            }
        }
        
        abort(403, 'Доступ запрещен');
    }
}