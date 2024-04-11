@if ($paginator->hasPages())
<ul class="pagination">
    {{-- Previous Page Link --}}
    @if ($paginator->onFirstPage())
        <li class="pagination__item pagination__item--disabled" aria-disabled="true" aria-label="@lang('pagination.previous')">
            <span class="pagination__link" aria-hidden="true">
                <span class="material-symbols-outlined">chevron_left</span>
            </span>
        </li>
    @else
        <li class="pagination__item">
            <a class="pagination__link" href="{{ $paginator->previousPageUrl() }}" rel="prev" aria-label="@lang('pagination.previous')">
                <span class="material-symbols-outlined">chevron_left</span>
            </a>
        </li>
    @endif

    @if($paginator->currentPage() > 3)
        <li class="pagination__item">
            <a class="pagination__link" href="{{ $paginator->url(1) }}">1</a>
        </li>
    @endif

    @if($paginator->currentPage() > 4)
        <li class="pagination__dots">...</li>
    @endif

    @foreach(range(1, $paginator->lastPage()) as $i)
        @if($i >= $paginator->currentPage() - 2 && $i <= $paginator->currentPage() + 2)
            @if ($i == $paginator->currentPage())
                <li class="pagination__item">
                    <a class="pagination__link pagination__link--active">{{ $i }}</a>
                </li>
            @else
                <li class="pagination__item">
                    <a class="pagination__link" href="{{ $paginator->url($i) }}">{{ $i }}</a>
                </li>
            @endif
        @endif
    @endforeach

    @if($paginator->currentPage() < $paginator->lastPage() - 3)
        <li class="pagination__dots">...</li>
    @endif

    @if($paginator->currentPage() < $paginator->lastPage() - 2)
        <li class="pagination__item">
            <a class="pagination__link" href="{{ $paginator->url($paginator->lastPage()) }}">{{ $paginator->lastPage() }}</a>
        </li>
    @endif

    {{-- Next Page Link --}}
    @if ($paginator->hasMorePages())
        <li class="pagination__item">
            <a class="pagination__link" href="{{ $paginator->nextPageUrl() }}" rel="next" aria-label="@lang('pagination.next')">
                <span class="material-symbols-outlined">chevron_right</span>
            </a>
        </li>
    @else
        <li class="pagination__item pagination__item--disabled" aria-disabled="true" aria-label="@lang('pagination.next')">
            <span class="pagination__link" aria-hidden="true">
                <span class="material-symbols-outlined">chevron_right</span>
            </span>
        </li>
    @endif

</ul>
@endif
