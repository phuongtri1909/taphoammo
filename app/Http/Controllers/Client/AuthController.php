<?php

namespace App\Http\Controllers\Client;

use Exception;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ], [
            'email.required' => 'Please enter your email.',
            'email.email' => 'The email you entered is invalid.',
            'password.required' => 'Please enter your password.',
        ]);

        try {

            $oldSessionId = session()->getId();

            $user = User::where('email', $request->email)->first();
            if (!$user) {
                return redirect()->back()->withInput()->withErrors([
                    'email' => 'Invalid credentials. Please try again.',
                ]);
            }

            if ($user->active == false) {
                return redirect()->back()->withInput()->withErrors([
                    'email' => 'Invalid credentials. Please try again.',
                ]);
            }

            if (!password_verify($request->password, $user->password)) {
                return redirect()->back()->withInput()->withErrors([
                    'email' => 'Invalid credentials. Please try again.',
                ]);
            }


            Auth::login($user);


            $user->ip_address = $request->ip();
            $user->save();

            return redirect()->route('dashboard');
        } catch (Exception $e) {
            return redirect()->back()->withInput()->with('error', 'An error occurred during login. Please try again later.');
        }
    }

    public function logout(Request $request)
    {

        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }

}
