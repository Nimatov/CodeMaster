<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class SetLocale
{
    public function handle($request, Closure $next)
    {
        // Сначала проверяем сессию
        $locale = Session::get('locale');
        
        // Если пользователь авторизован — берем из базы
        if (Auth::check()) {
            $userLocale = Auth::user()->language;
            if ($userLocale) {
                $locale = $userLocale;
                Session::put('locale', $locale);
            }
        }
        
        // Если язык не установлен — берем из конфига
        if (!$locale) {
            $locale = config('app.locale', 'ru');
        }
        
        App::setLocale($locale);
        
        return $next($request);
    }
}