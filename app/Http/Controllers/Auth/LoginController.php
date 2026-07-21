<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    use AuthenticatesUsers;

    protected $redirectTo = '/welcome';

    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }
    
    protected function authenticated(Request $request, $user)
    {
        if ($user->is_blocked) {
            Auth::logout();
            return redirect('/login')->with('error', 'Ваш аккаунт заблокирован!');
        }
        return redirect()->intended($this->redirectPath());
    }
}