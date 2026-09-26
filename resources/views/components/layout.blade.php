@props(['title' => null, 'section' => null])
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ? $title.' | '.config('app.name') : config('app.name').': '.config('zebra.tagline') }}</title>
    <meta name="description" content="{{ config('zebra.tagline') }}">
    <link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml">
    @fonts
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
<div class="page">
    <header class="masthead">
        <a href="{{ route('home') }}" class="wordmark">
            <svg viewBox="0 0 20 20" aria-hidden="true"><path fill="currentColor" d="M0 0h4l6 20H6zM8 0h4l6 20h-4zM16 0h4v6.7z"/></svg>
            {{ config('app.name') }}
        </a>

        <nav aria-label="Sections">
            <ul class="sections">
                <li><a href="{{ route('home') }}" @if($section === 'hot') aria-current="page" @endif>Top</a></li>
                <li><a href="{{ route('newest') }}" @if($section === 'newest') aria-current="page" @endif>New</a></li>
                <li><a href="{{ route('ask') }}" @if($section === 'ask') aria-current="page" @endif>Ask</a></li>
                <li><a href="{{ route('comments.index') }}" @if($section === 'comments') aria-current="page" @endif>Comments</a></li>
                <li><a href="{{ route('stories.create') }}" @if($section === 'submit') aria-current="page" @endif>Submit</a></li>
            </ul>
        </nav>

        <ul class="account">
            @auth
                <li><a href="{{ route('users.show', auth()->user()) }}">{{ auth()->user()->username }}</a> ({{ number_format(auth()->user()->karma) }})</li>
                <li><a href="{{ route('settings') }}">Settings</a></li>
                @can('admin')
                    <li><a href="{{ route('admin.dashboard') }}">Admin</a></li>
                @endcan
                <li>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="link-button">Sign out</button>
                    </form>
                </li>
            @else
                <li><a href="{{ route('login') }}">Sign in</a></li>
                <li><a href="{{ route('register') }}">Join</a></li>
            @endauth
        </ul>
    </header>

    @include('partials.notices')

    <main>
        {{ $slot }}
    </main>

    <footer class="colophon">
        <p>{{ config('app.name') }}: {{ config('zebra.tagline') }}</p>
        <p><span class="seg"><a href="{{ route('newest') }}">New</a></span> <span class="seg"><a href="{{ route('ask') }}">Ask</a></span> <span class="seg"><a href="{{ url('/api/v1/stories') }}">API</a></span></p>
    </footer>
</div>

<div class="toast" role="status" aria-live="polite" hidden></div>

@isset($voteReasons)
    <dialog id="downvote-dialog" aria-labelledby="downvote-title">
        <form method="dialog">
            <h2 id="downvote-title">Why the downvote?</h2>
            <div class="field">
                <label for="downvote-reason" class="visually-hidden">Reason</label>
                <select id="downvote-reason" name="reason" required>
                    <option value="">Pick a reason</option>
                    @foreach($voteReasons as $reason)
                        <option value="{{ $reason->id }}">{{ $reason->reason }}</option>
                    @endforeach
                </select>
            </div>
            <div class="actions">
                <button type="submit" value="confirm" class="button">Downvote</button>
                <button type="submit" value="cancel" class="button button--quiet" formnovalidate>Cancel</button>
            </div>
        </form>
    </dialog>
@endisset
</body>
</html>
