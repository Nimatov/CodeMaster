<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    /**
     * Главная страница
     */
    public function index()
    {
        if (Auth::check()) {
            return redirect('/welcome');
        }
        return view('welcome');
    }

    /**
     * Страница приветствия после входа
     */
    public function welcome()
    {
        return view('welcome');
    }
}