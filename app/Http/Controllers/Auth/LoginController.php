<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{

    // use AuthenticatesUsers;

    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {

        $request->validate([
            'email' => 'required|string',
            'password' => 'required|string',
        ]);

        $user = User::where('email', $request->email)
                    ->orWhere('username', $request->email)
                    ->first();

        if (!$user) {
            return back()->withInput()->with('error', 'User not found! Please check your credentials.');
        }

        if (!Hash::check($request->password, $user->password)) {
            return back()->withInput()->with('error', 'Incorrect E-mail or Password!');
        }

        if (isset($user->status) && $user->status != 'A') {
            return back()->withInput()->with('error', 'Your account is inactive. Please contact the administrator.');
        }

        Auth::login($user);
        return redirect($this->redirectTo());

    }

    protected function redirectTo()
    {
        return route('home');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login')->with('success', 'Logged out successfully!');
    }

}
