<x-layout title="Sign in">
    <h1 class="page-title">Sign in</h1>
    <p class="lede">New here? <a href="{{ route('register') }}">Join {{ config('app.name') }}</a>.</p>

    <form method="POST" action="{{ route('login') }}" class="form">
        @csrf
        <div class="field">
            <label for="username">Username</label>
            <input type="text" id="username" name="username" required autofocus autocomplete="username" value="{{ old('username') }}">
            @error('username')<p class="error">{{ $message }}</p>@enderror
        </div>
        <div class="field">
            <label for="password">Password</label>
            <input type="password" id="password" name="password" required autocomplete="current-password">
            @error('password')<p class="error">{{ $message }}</p>@enderror
        </div>
        <label class="checkbox"><input type="checkbox" name="remember" value="1"> Keep me signed in</label>
        <div class="actions">
            <button type="submit" class="button">Sign in</button>
            <a href="{{ route('password.request') }}">Forgot your password?</a>
        </div>
    </form>
</x-layout>
