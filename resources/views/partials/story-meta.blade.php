@php($user = auth()->user())
{{-- In listings only the author sees edit/delete; moderators get them on the story page and in the admin area. --}}
@php($manage = ($full ?? false) || $story->isOwnedBy($user))
<div class="meta">
    <span class="seg seg--lead">
        <span data-score-for="story:{{ $story->id }}">{{ $story->score }}</span> {{ Str::plural('point', $story->score) }}
        by <a href="{{ route('users.show', $story->user) }}">{{ $story->user->username }}</a>
        <a href="{{ $story->permalink() }}"><x-ago :time="$story->created_at" /></a>
    </span>
    <span class="seg"><a href="{{ $story->permalink() }}#comments">{{ $story->comments_count ? $story->comments_count.' '.Str::plural('comment', $story->comments_count) : 'discuss' }}</a></span>
    @if($story->edited_at && ($full ?? false))
        <span class="seg" title="{{ $story->edited_at->toDayDateTimeString() }}">edited</span>
    @endif
    @if($manage && $user?->can('update', $story))
        <span class="seg"><a href="{{ route('stories.edit', $story) }}">edit</a></span>
    @endif
    @if($manage && $user?->can('delete', $story))
        <span class="seg"><form method="POST" action="{{ route('stories.destroy', $story) }}" data-confirm="Delete this story?">
            @csrf @method('DELETE')
            <button type="submit" class="link-button">delete</button>
        </form></span>
    @endif
</div>
