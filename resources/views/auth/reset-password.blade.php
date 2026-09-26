<x-layout title="Choose a new password">
    <h1 class="page-title">Choose a new password</h1>

    <form method="POST" action="{{ route('password.update') }}" class="form">
        @csrf
        <input type="hidden" name="token" value="{{ $token }}">
        <div class="field">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" required autocomplete="email" value="{{ old('email', $email) }}">
            @error('email')<p class="error">{{ $message }}</p>@enderror
        </div>
        <div class="field">
            <label for="password">New password</label>
            <input type="password" id="password" name="password" required autofocus autocomplete="new-password">
            @error('password')<p class="error">{{ $message }}</p>@enderror
        </div>
        <div class="field">
            <label for="password_confirmation">Confirm new password</label>
            <input type="password" id="password_confirmation" name="password_confirmation" required autocomplete="new-password">
        </div>
        <div class="actions">
            <button type="submit" class="button">Reset password</button>
        </div>
    </form>
</x-layout>
