{{-- One comment and, recursively, its replies. --}}
@php
    $user = auth()->user();
    $deleted = $comment->trashed();
    $replies = $comment->relationLoaded('children') ? $comment->children : collect();
    $count = $replies->count();
@endphp
<li class="comment {{ $deleted ? 'comment--deleted' : '' }}" id="c{{ $comment->id }}" data-comment="{{ $comment->id }}">
    <div class="comment__row">
        @include('partials.vote', ['item' => $comment])
        <div>
            <div class="comment__head">
                @if($deleted)
                    <span class="seg">[deleted]</span>
                @else
                    <span class="seg"><a href="{{ route('users.show', $comment->user) }}" class="author {{ isset($op) && $comment->user_id === $op ? 'is-op' : '' }}">{{ $comment->user->username }}</a></span>
                    <span class="seg"><span data-score-for="comment:{{ $comment->id }}">{{ $comment->score }}</span>&nbsp;{{ Str::plural('point', $comment->score) }}</span>
                @endif
                <span class="seg"><a href="{{ $comment->permalink() }}"><x-ago :time="$comment->created_at" /></a></span>
                @if($comment->edited_at && ! $deleted)
                    <span class="seg" title="{{ $comment->edited_at->toDayDateTimeString() }}">edited</span>
                @endif
                @isset($showStory)
                    <span class="seg seg--lead">on <a href="{{ $comment->story->permalink() }}">{{ Str::limit($comment->story->title, 70) }}</a></span>
                @endisset
                @if($count && ! isset($showStory))
                    <button type="button" class="toggle" data-toggle aria-expanded="true" title="Collapse thread">[–]</button>
                @endif
            </div>

            <div class="comment__body body">
                @if($deleted)
                    <p>This comment was deleted.</p>
                @else
                    {{ \App\Support\Formatter::render($comment->body) }}
                @endif
            </div>

            @unless($deleted)
                <div class="comment__foot">
                    @auth
                        <span class="seg"><a href="{{ $comment->permalink() }}#reply" data-reply="{{ $comment->id }}">reply</a></span>
                    @endauth
                    @if($user?->can('update', $comment))
                        <span class="seg"><a href="{{ route('comments.edit', $comment) }}">edit</a></span>
                    @endif
                    @if($user?->can('delete', $comment))
                        <span class="seg"><form method="POST" action="{{ route('comments.destroy', $comment) }}" data-confirm="Delete this comment?">
                            @csrf @method('DELETE')
                            <button type="submit" class="link-button">delete</button>
                        </form></span>
                    @endif
                </div>
            @endunless
        </div>
    </div>
    <div class="reply-slot"></div>

    @if($count)
        <ul class="thread">
            @foreach($replies as $reply)
                @include('partials.comment', ['comment' => $reply])
            @endforeach
        </ul>
    @endif
</li>
