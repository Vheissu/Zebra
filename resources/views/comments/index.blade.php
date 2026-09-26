<x-layout :title="$heading ?? 'Comments'" :section="$section">
    <h1 class="page-title">{{ $heading ?? 'Newest comments' }}</h1>

    @if($comments->isEmpty())
        <p class="empty">No comments yet.</p>
    @else
        <ul class="comment-list">
            @foreach($comments as $comment)
                @include('partials.comment', ['comment' => $comment, 'showStory' => true])
            @endforeach
        </ul>

        {{ $comments->links() }}
    @endif
</x-layout>
