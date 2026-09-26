{{-- Up/down arrows for a story or comment. Each arrow is a real form, so voting works without JavaScript. --}}
@php
    $key = $item->getMorphClass().':'.$item->id;
    $current = (int) ($votes[$key] ?? 0);
    $user = auth()->user();
@endphp
<div class="vote" data-vote="{{ $current }}" data-item="{{ $key }}">
    @if(! $user)
        <a href="{{ route('login') }}" class="vote__button vote__button--up" title="Sign in to vote">
            <svg viewBox="0 0 10 8" aria-hidden="true"><path d="M5 0l5 8H0z"/></svg>
            <span class="visually-hidden">Sign in to vote</span>
        </a>
    @elseif(! $item->isOwnedBy($user) && ! $item->trashed())
        <form method="POST" action="{{ route('vote', [$item->getMorphClass(), $item->id]) }}">
            @csrf
            <input type="hidden" name="direction" value="{{ $current === 1 ? 'none' : 'up' }}">
            <button type="submit" class="vote__button vote__button--up" data-direction="up" aria-pressed="{{ $current === 1 ? 'true' : 'false' }}" title="Upvote">
                <svg viewBox="0 0 10 8" aria-hidden="true"><path d="M5 0l5 8H0z"/></svg>
                <span class="visually-hidden">Upvote</span>
            </button>
        </form>
        @if($user->canDownvote())
            <form method="POST" action="{{ route('vote', [$item->getMorphClass(), $item->id]) }}">
                @csrf
                <input type="hidden" name="direction" value="{{ $current === -1 ? 'none' : 'down' }}">
                <button type="submit" class="vote__button vote__button--down" data-direction="down" aria-pressed="{{ $current === -1 ? 'true' : 'false' }}" title="Downvote">
                    <svg viewBox="0 0 10 8" aria-hidden="true"><path d="M5 8L0 0h10z"/></svg>
                    <span class="visually-hidden">Downvote</span>
                </button>
            </form>
        @endif
    @endif
</div>
