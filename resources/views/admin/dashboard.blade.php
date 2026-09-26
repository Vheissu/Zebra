<x-layout title="Moderation">
    @include('admin.nav')

    <dl class="stats">
        @foreach($totals as $label => [$total, $week])
            <div>
                <dt>{{ $label }}</dt>
                <dd>{{ number_format($total) }} <small>{{ number_format($week) }} this week</small></dd>
            </div>
        @endforeach
    </dl>

    <h2 class="section-title">Recent downvotes</h2>
    @if($flagged->isEmpty())
        <p class="empty">No downvotes yet. Things are calm.</p>
    @else
        <div class="table-wrap">
            <table class="table">
                <thead><tr><th>When</th><th>On</th><th>Reason</th><th>By</th></tr></thead>
                <tbody>
                @foreach($flagged as $vote)
                    @php($item = $vote->votable)
                    <tr>
                        <td>{{ $vote->created_at->diffForHumans() }}</td>
                        <td>
                            @if($item instanceof \App\Models\Story)
                                Story: <a href="{{ $item->permalink() }}">{{ Str::limit($item->title, 70) }}</a>
                            @else
                                Comment: <a href="{{ $item->permalink() }}">{{ Str::limit($item->body, 70) }}</a>
                            @endif
                            ({{ $item->score }})
                        </td>
                        <td>{{ $vote->reason?->reason ?? '—' }}</td>
                        <td><a href="{{ route('users.show', $vote->user) }}">{{ $vote->user->username }}</a></td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    @endif
</x-layout>
