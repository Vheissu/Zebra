@if($paginator->hasPages())
    <nav class="pager" aria-label="Pages">
        @if(! $paginator->onFirstPage())
            <a href="{{ $paginator->previousPageUrl() }}" rel="prev">&larr; Previous</a>
        @endif
        @if($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" rel="next">More &rarr;</a>
        @endif
    </nav>
@endif
