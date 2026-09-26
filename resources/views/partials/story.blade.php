<li class="story" id="s{{ $story->id }}">
    <span class="story__rank">{{ $rank }}.</span>
    @include('partials.vote', ['item' => $story])
    <div>
        <h2 class="story__title">
            <a href="{{ $story->href() }}" @if($story->url) rel="ugc noopener" @endif>{{ $story->title }}</a>
            @if($story->domain)
                <span class="domain">(<a href="{{ route('domain', $story->domain) }}">{{ $story->domain }}</a>)</span>
            @endif
        </h2>
        @include('partials.story-meta')
    </div>
</li>
