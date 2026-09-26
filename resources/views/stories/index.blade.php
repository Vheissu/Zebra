<x-layout :title="$title" :section="$section">
    @isset($heading)
        <h1 class="page-title">{{ $heading }}</h1>
    @endisset

    @if($section === 'ask')
        <p class="lede">Questions and text posts. <a href="{{ route('stories.create') }}">Ask something</a>.</p>
    @endif

    @if($stories->isEmpty())
        <p class="empty">
            @if($stories->onFirstPage())
                Nothing here yet. <a href="{{ route('stories.create') }}">Submit the first story</a>.
            @else
                That's everything. <a href="{{ url()->current() }}">Back to the start</a>.
            @endif
        </p>
    @else
        <ol class="stories" start="{{ $stories->firstItem() }}">
            @foreach($stories as $story)
                @include('partials.story', ['rank' => $stories->firstItem() + $loop->index])
            @endforeach
        </ol>

        {{ $stories->links() }}
    @endif
</x-layout>
