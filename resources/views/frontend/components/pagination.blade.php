@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Pagination Navigation" class="d-flex justify-content-center">
        <ul class="pagination justify-content-center flex-wrap gap-1">
            {{-- Previous Page Link --}}
            @if ($paginator->onFirstPage())
                <li class="page-item disabled" aria-disabled="true" aria-label="@lang('pagination.previous')">
                    <span class="page-link rounded-pill border-0 shadow-sm px-3 py-2 text-muted">
                        <i class="la la-angle-left me-1"></i>Prev
                    </span>
                </li>
            @else
                <li class="page-item">
                    <a class="page-link rounded-pill border-0 shadow-sm px-3 py-2"
                       href="{{ $paginator->previousPageUrl() }}" rel="prev" aria-label="@lang('pagination.previous')">
                        <i class="la la-angle-left me-1"></i>Prev
                    </a>
                </li>
            @endif

            {{-- Pagination Elements --}}
            @foreach ($elements as $element)
                {{-- "Three Dots" Separator --}}
                @if (is_string($element))
                    <li class="page-item disabled" aria-disabled="true">
                        <span class="page-link rounded-pill border-0 px-3 py-2 text-muted bg-transparent">
                            {{ $element }}
                        </span>
                    </li>
                @endif

                {{-- Array Of Links --}}
                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <li class="page-item active" aria-current="page">
                                <span class="page-link rounded-pill border-0 px-3 py-2 fw-semibold shadow-sm">
                                    {{ $page }}
                                </span>
                            </li>
                        @else
                            <li class="page-item">
                                <a class="page-link rounded-pill border-0 px-3 py-2"
                                   href="{{ $url }}">
                                    {{ $page }}
                                </a>
                            </li>
                        @endif
                    @endforeach
                @endif
            @endforeach

            {{-- Next Page Link --}}
            @if ($paginator->hasMorePages())
                <li class="page-item">
                    <a class="page-link rounded-pill border-0 shadow-sm px-3 py-2"
                       href="{{ $paginator->nextPageUrl() }}" rel="next" aria-label="@lang('pagination.next')">
                        Next<i class="la la-angle-right ms-1"></i>
                    </a>
                </li>
            @else
                <li class="page-item disabled" aria-disabled="true" aria-label="@lang('pagination.next')">
                    <span class="page-link rounded-pill border-0 shadow-sm px-3 py-2 text-muted">
                        Next<i class="la la-angle-right ms-1"></i>
                    </span>
                </li>
            @endif
        </ul>
    </nav>
@endif
