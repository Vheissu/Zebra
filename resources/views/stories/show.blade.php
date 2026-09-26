<x-layout :title="$story->title">
    <article>
        <div class="story story--lead">
            @include('partials.vote', ['item' => $story])
            <div>
                <h1 class="story__title">
                    <a href="{{ $story->href() }}" @if($story->url) rel="ugc noopener" @endif>{{ $story->title }}</a>
                    @if($story->domain)
                        <span class="domain">(<a href="{{ route('domain', $story->domain) }}">{{ $story->domain }}</a>)</span>
                    @endif
                </h1>
                @include('partials.story-meta', ['full' => true])
            </div>
        </div>

        @if($story->text)
            <div class="body story-text">{{ \App\Support\Formatter::render($story->text) }}</div>
        @endif
    </article>

    <section id="comments" aria-label="Comments">
        @auth
            @include('partials.comment-form', ['action' => route('comments.store', $story), 'label' => 'Add a comment'])
        @else
            <p class="context"><a href="{{ route('login') }}">Sign in</a> or <a href="{{ route('register') }}">join</a> to comment.</p>
        @endauth

        @if($comments->isNotEmpty())
            <ul class="thread">
                @foreach($comments as $comment)
                    @include('partials.comment', ['comment' => $comment, 'op' => $story->user_id])
                @endforeach
            </ul>
        @endif
    </section>

    @auth
        <template id="reply-template">
            @include('partials.comment-form', ['action' => route('comments.store', $story), 'label' => 'Reply', 'parent' => 0, 'inline' => true])
        </template>
    @endauth
</x-layout>
