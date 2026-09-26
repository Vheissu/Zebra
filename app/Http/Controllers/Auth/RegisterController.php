<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class RegisterController extends Controller
{
    public function create(): View
    {
        return view('auth.register');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'username' => [
                'required', 'string', 'min:2', 'max:20', 'regex:/^[A-Za-z0-9_-]+$/',
                function (string $attribute, string $value, $fail) {
                    if (User::whereUsername($value)->exists()) {
                        $fail('That username is taken.');
                    }
                },
            ],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Password::defaults()],
        ], [
            'username.regex' => 'Usernames can only use letters, numbers, dashes and underscores.',
            'email.unique' => 'There is already an account with that email address.',
        ]);

        $user = User::create($data);

        Auth::login($user);

        $request->session()->regenerate();

        return redirect()->route('home')->with('status', 'Welcome to '.config('app.name').", {$user->username}.");
    }
}
