<?php

namespace App\Helpers;

use Illuminate\Support\Facades\App;

class LanguageHelper
{
    public static function getCurrentLanguageName()
    {
        $locale = App::getLocale();
        $languages = [
            'ru' => '🇷🇺 Русский',
            'uz' => '🇺🇿 O\'zbekcha',
            'en' => '🇬🇧 English',
        ];
        
        return $languages[$locale] ?? '🌐 Язык';
    }
    
    public static function getLanguages()
    {
        return [
            'ru' => ['name' => 'Русский', 'flag' => '🇷🇺'],
            'uz' => ['name' => "O'zbekcha", 'flag' => '🇺🇿'],
            'en' => ['name' => 'English', 'flag' => '🇬🇧'],
        ];
    }
}