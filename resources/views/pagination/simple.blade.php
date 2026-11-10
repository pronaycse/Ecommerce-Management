@if ($paginator->hasPages())
    <div class="pager">
        @if ($paginator->onFirstPage())
            <span class="off">&laquo; Previous</span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}">&laquo; Previous</a>
        @endif

        <span>Page {{ $paginator->currentPage() }} of {{ $paginator->lastPage() }}</span>

        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}">Next &raquo;</a>
        @else
            <span class="off">Next &raquo;</span>
        @endif
    </div>
@endif
