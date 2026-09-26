<x-layout title="Moderation: comments">
    @include('admin.nav')
    @include('admin.filters', ['placeholder' => 'Search comments', 'options' => ['' => 'All', 'deleted' => 'Deleted']])

    <div class="table-wrap">
        <table class="table">
            <thead><tr><th>Comment</th><th>By</th><th>On</th><th class="num">Score</th><th>Posted</th><th></th></tr></thead>
            <tbody>
            @forelse($comments as $comment)
                <tr @class(['is-deleted' => $comment->trashed()])>
                    <td>
                        @if($comment->trashed())
                            {{ Str::limit($comment->body, 120) }}
                        @else
                            <a href="{{ $comment->permalink() }}">{{ Str::limit($comment->body, 120) }}</a>
                        @endif
                    </td>
                    <td><a href="{{ route('users.show', $comment->user) }}">{{ $comment->user->username }}</a></td>
                    <td>{{ Str::limit($comment->story?->title, 50) }}</td>
                    <td class="num">{{ $comment->score }}</td>
                    <td>{{ $comment->created_at->diffForHumans() }}</td>
                    <td>
                        @if($comment->trashed())
                            <form method="POST" action="{{ route('admin.comments.restore', $comment->id) }}">@csrf<button class="link-button">restore</button></form>
                        @else
                            <form method="POST" action="{{ route('admin.comments.destroy', $comment) }}" data-confirm="Delete this comment?">@csrf @method('DELETE')<button class="link-button">delete</button></form>
                        @endif
                    </td>
                </tr>
            @empty
                <tr><td colspan="6">No comments match.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>

    {{ $comments->links() }}
</x-layout>
