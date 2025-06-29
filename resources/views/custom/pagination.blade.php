

@if ($paginator->hasPages())
    <nav class="flex items-center justify-between mt-8">
        {{-- Paginação no centro/direita --}}
        <div class="flex items-center space-x-2">
            {{-- Anterior --}}
            @if ($paginator->onFirstPage())
                <span class="px-3 py-2 text-gray-400 bg-gray-100 rounded-lg cursor-not-allowed text-sm">
                    Anterior
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}"
                   class="px-3 py-2 text-gray-600 bg-white border border-orange-100 rounded-lg hover:bg-orange-50 hover:text-orange-600 hover:border-orange-200 transition-colors duration-200 text-sm">
                    Anterior
                </a>
            @endif

            {{-- Números das páginas --}}
            <div class="flex items-center space-x-1">
                @foreach ($elements as $element)
                    @if (is_string($element))
                        <span class="px-3 py-2 text-gray-400 text-sm">{{ $element }}</span>
                    @endif

                    @if (is_array($element))
                        @foreach ($element as $page => $url)
                            @if ($page == $paginator->currentPage())
                                <span class="px-3 py-2 bg-orange-600 text-white rounded-lg font-medium text-sm">
                                    {{ $page }}
                                </span>
                            @else
                                <a href="{{ $url }}"
                                   class="px-3 py-2 text-gray-600 bg-white border border-orange-100 rounded-lg hover:bg-orange-50 hover:text-orange-600 hover:border-orange-200 transition-colors duration-200 text-sm">
                                    {{ $page }}
                                </a>
                            @endif
                        @endforeach
                    @endif
                @endforeach
            </div>

            {{-- Próximo --}}
            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}"
                   class="px-3 py-2 text-gray-600 bg-white border border-orange-100 rounded-lg hover:bg-orange-50 hover:text-orange-600 hover:border-orange-200 transition-colors duration-200 text-sm">
                    Próximo
                </a>
            @else
                <span class="px-3 py-2 text-gray-400 bg-gray-100 rounded-lg cursor-not-allowed text-sm">
                    Próximo
                </span>
            @endif
        </div>
    </nav>
@endif
