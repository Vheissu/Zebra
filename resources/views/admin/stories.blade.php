<x-layout title="Moderation: stories">
    @include('admin.nav')
    @include('admin.filters', ['placeholder' => 'Search titles', 'options' => ['' => 'All', 'deleted' => 'Deleted']])

    <div class="table-wrap">
        <table class="table">
            <thead><tr><th>Story</th><th>By</th><th class="num">Score</th><th class="num">Comments</th><th>Posted</th><th></th></tr></thead>
            <tbody>
            @forelse($stories as $story)
                <tr @class(['is-deleted' => $story->trashed()])>
                    <td>
                        @if($story->trashed())
                            {{ $story->title }}
                        @else
                            <a href="{{ $story->permalink() }}">{{ $story->title }}</a>
                        @endif
                        @if($story->domain)<span class="domain">({{ $story->domain }})</span>@endif
                    </td>
                    <td><a href="{{ route('users.show', $story->user) }}">{{ $story->user->username }}</a></td>
                    <td class="num">{{ $story->score }}</td>
                    <td class="num">{{ $story->comments_count }}</td>
                    <td>{{ $story->created_at->diffForHumans() }}</td>
                    <td>
                        @if($story->trashed())
                            <form method="POST" action="{{ route('admin.stories.restore', $story->id) }}">@csrf<button class="link-button">restore</button></form>
                        @else
                            <form method="POST" action="{{ route('admin.stories.destroy', $story) }}" data-confirm="Delete this story?">@csrf @method('DELETE')<button class="link-button">delete</button></form>
                        @endif
                    </td>
                </tr>
            @empty
                <tr><td colspan="6">No stories match.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>

    {{ $stories->links() }}
</x-layout>
