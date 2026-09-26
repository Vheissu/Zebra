<x-layout :title="Str::limit($comment->body, 60)">
    <p class="context">
        <span class="seg seg--lead">On <a href="{{ $comment->story->permalink() }}">{{ $comment->story->title }}</a></span>
        @if($comment->parent)
            <span class="seg"><a href="{{ $comment->parent->permalink() }}">parent</a></span>
        @endif
        <span class="seg"><a href="{{ $comment->story->permalink() }}#c{{ $comment->id }}">in context</a></span>
    </p>

    <ul class="thread">
        @include('partials.comment', ['comment' => $comment, 'op' => $comment->story->user_id])
    </ul>

    @auth
        <div id="reply">
            @include('partials.comment-form', ['action' => route('comments.store', $comment->story), 'label' => 'Reply to '.$comment->user->username, 'parent' => $comment->id])
        </div>

        <template id="reply-template">
            @include('partials.comment-form', ['action' => route('comments.store', $comment->story), 'label' => 'Reply', 'parent' => 0, 'inline' => true])
        </template>
    @endauth
</x-layout>
