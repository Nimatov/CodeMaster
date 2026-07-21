<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;

class LanguageController extends Controller
{
    public function change(Request $request)
    {
        $language = $request->language;
        $supportedLanguages = ['ru', 'uz', 'en'];
        
        if (!in_array($language, $supportedLanguages)) {
            $language = 'ru';
        }
        
        // Сохраняем в сессию
        Session::put('locale', $language);
        App::setLocale($language);
        
        // Если пользователь авторизован — сохраняем в базу
        if (Auth::check()) {
            $user = Auth::user();
            $user->language = $language;
            $user->save();
        }
        
        return redirect()->back();
    }
}