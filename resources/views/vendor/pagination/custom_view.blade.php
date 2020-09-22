@if ($paginator->hasPages())
    <ul class="paginator">
        {{-- Previous Page Link --}}
        @if ($paginator->onFirstPage())
            <li class="paginator__item disabled">
            </li>
        @else
            <li class="paginator__item paginator__item--prev">
                <a href="{{ $paginator->previousPageUrl() }}">
                    <i class="icon ion-ios-arrow-back"></i>
                </a>
            </li>
        @endif

        {{-- Pagination Elements --}}
        @foreach ($elements as $element)
            {{-- Array Of Links --}}
            @if (is_array($element))
                @foreach ($element as $page => $url)
                    @if ($page == $paginator->currentPage())
                        <li class="paginator__item paginator__item--active"><a href="#">{{ $page }}</a></li>
                    @elseif (($page == $paginator->currentPage() + 1 || $page == $paginator->currentPage() + 2) || $page == $paginator->lastPage())
                        <li class="paginator__item"><a href="{{ $url }}">{{ $page }}</a></li>
                    @elseif (($page == $paginator->currentPage() - 1 || $page == $paginator->currentPage() -2)|| $page == $paginator->lastPage())
                        <li class="paginator__item "><a href="{{ $url }}">{{ $page }}</a></li></li>
                    @endif
                @endforeach
            @endif
        @endforeach

        {{-- Next Page Link --}}
        @if ($paginator->hasMorePages())
            <li class="paginator__item paginator__item--next">
                <a href="{{ $paginator->nextPageUrl() }}">
                    <i class="icon ion-ios-arrow-forward"></i>
                </a>
            </li>
        @else
            <li class="paginator__item disabled">
            </li>
        @endif
    </ul>
@endif
