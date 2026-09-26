<x-layout title="Join">
    <h1 class="page-title">Join {{ config('app.name') }}</h1>
    <p class="lede">Already have an account? <a href="{{ route('login') }}">Sign in</a>.</p>

    <form method="POST" action="{{ route('register') }}" class="form">
        @csrf
        <div class="field">
            <label for="username">Username</label>
            <input type="text" id="username" name="username" required autofocus autocomplete="username" maxlength="20" value="{{ old('username') }}">
            <p class="hint">Up to 20 letters, numbers, dashes or underscores. This is how people will see you.</p>
            @error('username')<p class="error">{{ $message }}</p>@enderror
        </div>
        <div class="field">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" required autocomplete="email" value="{{ old('email') }}">
            <p class="hint">Only used if you forget your password.</p>
            @error('email')<p class="error">{{ $message }}</p>@enderror
        </div>
        <div class="field">
            <label for="password">Password</label>
            <input type="password" id="password" name="password" required autocomplete="new-password">
            <p class="hint">At least 8 characters.</p>
            @error('password')<p class="error">{{ $message }}</p>@enderror
        </div>
        <div class="field">
            <label for="password_confirmation">Confirm password</label>
            <input type="password" id="password_confirmation" name="password_confirmation" required autocomplete="new-password">
        </div>
        <div class="actions">
            <button type="submit" class="button">Create account</button>
        </div>
    </form>
</x-layout>
