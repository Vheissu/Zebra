<x-layout title="Moderation: members">
    @include('admin.nav')
    @include('admin.filters', ['placeholder' => 'Search username or email', 'options' => ['' => 'All', 'banned' => 'Banned', 'admins' => 'Admins']])

    <div class="table-wrap">
        <table class="table">
            <thead><tr><th>Member</th><th>Email</th><th class="num">Karma</th><th class="num">Stories</th><th class="num">Comments</th><th>Joined</th><th></th></tr></thead>
            <tbody>
            @forelse($users as $member)
                <tr>
                    <td>
                        <a href="{{ route('users.show', $member) }}">{{ $member->username }}</a>
                        @if($member->is_admin) (admin) @endif
                        @if($member->isBanned()) (banned) @endif
                    </td>
                    <td>{{ $member->email }}</td>
                    <td class="num">{{ number_format($member->karma) }}</td>
                    <td class="num">{{ $member->stories_count }}</td>
                    <td class="num">{{ $member->comments_count }}</td>
                    <td>{{ $member->created_at->diffForHumans() }}</td>
                    <td>
                        @unless($member->is(auth()->user()))
                            @if($member->isBanned())
                                <form method="POST" action="{{ route('admin.users.unban', $member) }}">@csrf<button class="link-button">unban</button></form>
                            @else
                                <form method="POST" action="{{ route('admin.users.ban', $member) }}" data-confirm="Ban {{ $member->username }}? They will be signed out and their API tokens revoked.">@csrf<button class="link-button">ban</button></form>
                            @endif
                            <span class="seg-divider" aria-hidden="true">|</span>
                            <form method="POST" action="{{ route('admin.users.admin', $member) }}" data-confirm="{{ $member->is_admin ? 'Remove admin rights from' : 'Make' }} {{ $member->username }}{{ $member->is_admin ? '?' : ' an administrator?' }}">@csrf<button class="link-button">{{ $member->is_admin ? 'remove admin' : 'make admin' }}</button></form>
                        @endunless
                    </td>
                </tr>
            @empty
                <tr><td colspan="7">No members match.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>

    {{ $users->links() }}
</x-layout>
