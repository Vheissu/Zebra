<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $code }} | {{ config('app.name') }}</title>
    @vite(['resources/css/app.css'])
</head>
<body>
<div class="page">
    <header class="masthead">
        <a href="{{ url('/') }}" class="wordmark">
            <svg viewBox="0 0 20 20" aria-hidden="true"><path fill="currentColor" d="M0 0h4l6 20H6zM8 0h4l6 20h-4zM16 0h4v6.7z"/></svg>
            {{ config('app.name') }}
        </a>
    </header>
    <main>
        <h1 class="page-title">{{ $heading }}</h1>
        <p class="lede">{{ $message }}</p>
        <p><a href="{{ url('/') }}">Back to the front page</a></p>
    </main>
</div>
</body>
</html>
