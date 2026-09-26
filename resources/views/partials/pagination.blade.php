@if($paginator->hasPages())
    <nav class="pager" aria-label="Pages">
        @if(! $paginator->onFirstPage())
            <a href="{{ $paginator->previousPageUrl() }}" rel="prev">&larr; Previous</a>
        @endif
        <span class="muted">Page {{ $paginator->currentPage() }} of {{ $paginator->lastPage() }}, {{ number_format($paginator->total()) }} in all</span>
        @if($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" rel="next">Next &rarr;</a>
        @endif
    </nav>
@endif
