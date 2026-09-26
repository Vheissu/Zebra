<x-layout title="Settings">
    <h1 class="page-title">Settings</h1>

    <h2 class="section-title">Profile</h2>
    <form method="POST" action="{{ route('settings.update') }}" class="form">
        @csrf @method('PUT')
        <div class="field">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" required value="{{ old('email', $user->email) }}">
            <p class="hint">Only used for password resets. Other members never see it; administrators can.</p>
            @error('email')<p class="error">{{ $message }}</p>@enderror
        </div>
        <div class="field">
            <label for="about">About</label>
            <textarea id="about" name="about" rows="5" maxlength="2000">{{ old('about', $user->about) }}</textarea>
            @error('about')<p class="error">{{ $message }}</p>@enderror
        </div>
        <div class="actions">
            <button type="submit" class="button">Save profile</button>
        </div>
    </form>

    <h2 class="section-title">Password</h2>
    <form method="POST" action="{{ route('settings.password') }}" class="form">
        @csrf @method('PUT')
        <div class="field">
            <label for="current_password">Current password</label>
            <input type="password" id="current_password" name="current_password" required autocomplete="current-password">
            @error('current_password', 'password')<p class="error">{{ $message }}</p>@enderror
        </div>
        <div class="field">
            <label for="new_password">New password</label>
            <input type="password" id="new_password" name="password" required autocomplete="new-password">
            @error('password', 'password')<p class="error">{{ $message }}</p>@enderror
        </div>
        <div class="field">
            <label for="password_confirmation">Confirm new password</label>
            <input type="password" id="password_confirmation" name="password_confirmation" required autocomplete="new-password">
        </div>
        <div class="actions">
            <button type="submit" class="button">Change password</button>
        </div>
    </form>

    <h2 class="section-title">API tokens</h2>
    <p class="lede">Tokens let scripts and apps use the <a href="{{ url('/api/v1/stories') }}">{{ config('app.name') }} API</a> as you. Send one as <code>Authorization: Bearer &lt;token&gt;</code>.</p>

    @if(session('token'))
        <div class="notice">
            <p>Here is your new token. Copy it now; it won't be shown again.</p>
            <code class="token">{{ session('token') }}</code>
        </div>
    @endif

    @if($tokens->isNotEmpty())
        <div class="table-wrap">
            <table class="table">
                <thead><tr><th>Name</th><th>Created</th><th>Last used</th><th></th></tr></thead>
                <tbody>
                @foreach($tokens as $token)
                    <tr>
                        <td>{{ $token->name }}</td>
                        <td>{{ $token->created_at->diffForHumans() }}</td>
                        <td>{{ $token->last_used_at?->diffForHumans() ?? 'Never' }}</td>
                        <td>
                            <form method="POST" action="{{ route('settings.tokens.destroy', $token->id) }}" data-confirm="Revoke this token?">
                                @csrf @method('DELETE')
                                <button type="submit" class="link-button">revoke</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    @endif

    <form method="POST" action="{{ route('settings.tokens.store') }}" class="form">
        @csrf
        <div class="field">
            <label for="token_name">New token name</label>
            <input type="text" id="token_name" name="name" maxlength="60" required placeholder="e.g. my-bot">
            @error('name', 'token')<p class="error">{{ $message }}</p>@enderror
        </div>
        <div class="actions">
            <button type="submit" class="button button--quiet">Create token</button>
        </div>
    </form>
</x-layout>
