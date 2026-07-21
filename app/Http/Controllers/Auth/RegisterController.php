<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;

class RegisterController extends Controller
{
    use RegistersUsers;

    protected $redirectTo = '/welcome';

    public function __construct()
    {
        $this->middleware('guest');
    }

    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => ['required', 'string', 'max:255'],
            'full_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);
    }
    protected function create(array $data)
{
    return User::create([
        'name' => $data['name'],
        'full_name' => $data['full_name'],
        'email' => $data['email'],
        'password' => Hash::make($data['password']),
        'language' => app()->getLocale(),
    ]);
}
    protected function registered(Request $request, $user)
{
    $message = __('messages.registration_success');
    return redirect('/welcome')->with('success', $message);
}
}