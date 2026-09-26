<x-layout title="Reset your password">
    <h1 class="page-title">Reset your password</h1>
    <p class="lede">Enter the email address on your account and we'll send you a link to choose a new password.</p>

    <form method="POST" action="{{ route('password.email') }}" class="form">
        @csrf
        <div class="field">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" required autofocus autocomplete="email" value="{{ old('email') }}">
            @error('email')<p class="error">{{ $message }}</p>@enderror
        </div>
        <div class="actions">
            <button type="submit" class="button">Send reset link</button>
            <a href="{{ route('login') }}">Back to sign in</a>
        </div>
    </form>
</x-layout>
