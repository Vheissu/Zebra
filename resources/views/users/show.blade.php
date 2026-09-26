<x-layout :title="$user->username">
    <h1 class="page-title">{{ $user->username }}</h1>

    @if($user->isBanned())
        <p class="notice notice--error">This account is suspended.</p>
    @endif

    <dl class="facts">
        <dt>Joined</dt>
        <dd><time datetime="{{ $user->created_at->toIso8601String() }}" title="{{ $user->created_at->toFormattedDateString() }}">{{ $user->created_at->diffForHumans() }}</time></dd>
        <dt>Karma</dt>
        <dd>{{ number_format($user->karma) }}</dd>
        @if($user->is_admin)
            <dt>Role</dt>
            <dd>Administrator</dd>
        @endif
        <dt>About</dt>
        <dd class="body">
            @if($user->about)
                {{ \App\Support\Formatter::render($user->about) }}
            @else
                <span class="lede">Nothing yet.</span>
            @endif
        </dd>
    </dl>

    <div class="meta meta--profile">
        <span class="seg"><a href="{{ route('users.stories', $user) }}">{{ number_format($storyCount) }} {{ Str::plural('submission', $storyCount) }}</a></span>
        <span class="seg"><a href="{{ route('users.comments', $user) }}">{{ number_format($commentCount) }} {{ Str::plural('comment', $commentCount) }}</a></span>
        @if(auth()->user()?->is($user))
            <span class="seg"><a href="{{ route('settings') }}">edit profile</a></span>
        @endif
    </div>
</x-layout>
