<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class SettingsController extends Controller
{
    public function edit(Request $request): View
    {
        return view('settings.edit', [
            'user' => $request->user(),
            'tokens' => $request->user()->tokens()->latest()->get(),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $user = $request->user();

        $data = $request->validate([
            'email' => ['required', 'email', 'max:255', Rule::unique('users')->ignore($user)],
            'about' => ['nullable', 'string', 'max:2000'],
        ]);

        $user->update($data);

        return back()->with('status', 'Profile saved.');
    }

    public function password(Request $request): RedirectResponse
    {
        $data = $request->validateWithBag('password', [
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        $request->user()->update(['password' => $data['password']]);

        return back()->with('status', 'Password changed.');
    }

    public function createToken(Request $request): RedirectResponse
    {
        $data = $request->validateWithBag('token', [
            'name' => ['required', 'string', 'max:60'],
        ]);

        $token = $request->user()->createToken($data['name']);

        return back()->with('token', $token->plainTextToken);
    }

    public function destroyToken(Request $request, int $token): RedirectResponse
    {
        $request->user()->tokens()->whereKey($token)->delete();

        return back()->with('status', 'Token revoked.');
    }
}
