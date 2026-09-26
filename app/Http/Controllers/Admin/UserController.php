<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(Request $request): View
    {
        $users = User::query()
            ->withCount('stories', 'comments')
            ->when($request->query('q'), fn ($q, $term) => $q->where(
                fn ($q) => $q->where('username', 'like', "%{$term}%")->orWhere('email', 'like', "%{$term}%")
            ))
            ->when($request->query('show') === 'banned', fn ($q) => $q->whereNotNull('banned_at'))
            ->when($request->query('show') === 'admins', fn ($q) => $q->where('is_admin', true))
            ->latest()
            ->paginate(50)
            ->withQueryString();

        return view('admin.users', ['users' => $users]);
    }

    public function ban(Request $request, User $user): RedirectResponse
    {
        if ($user->is($request->user())) {
            return back()->withErrors(['admin' => "You can't ban yourself."]);
        }

        $user->forceFill(['banned_at' => now()])->save();
        $user->tokens()->delete();

        return back()->with('status', "{$user->username} is banned.");
    }

    public function unban(User $user): RedirectResponse
    {
        $user->forceFill(['banned_at' => null])->save();

        return back()->with('status', "{$user->username} is no longer banned.");
    }

    public function toggleAdmin(Request $request, User $user): RedirectResponse
    {
        if ($user->is($request->user())) {
            return back()->withErrors(['admin' => "You can't change your own admin rights."]);
        }

        $user->forceFill(['is_admin' => ! $user->is_admin])->save();

        return back()->with('status', $user->is_admin
            ? "{$user->username} is now an administrator."
            : "{$user->username} is no longer an administrator.");
    }
}
