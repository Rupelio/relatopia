<x-dashboard-layout title="Histórico de Sentimentos">
    @php
    $emojis = [
        'feliz' => '😊',
        'triste' => '😢',
        'ansioso' => '😰',
        'calmo' => '😌',
        'raiva' => '😠',
        'empolgado' => '🤩',
        'frustrado' => '😤',
        'amoroso' => '🥰',
        'preocupado' => '😟',
        'grato' => '🙏'
    ];

    $cores = [
        'feliz' => 'green',
        'triste' => 'blue',
        'ansioso' => 'yellow',
        'calmo' => 'cyan',
        'raiva' => 'red',
        'empolgado' => 'purple',
        'frustrado' => 'orange',
        'amoroso' => 'pink',
        'preocupado' => 'gray',
        'grato' => 'emerald'
    ];
    @endphp
    <div class="min-h-[calc(100vh-4rem)] py-8 px-4">
        <div class="max-w-6xl mx-auto">
            <!-- Header -->
            <div class="mb-8">
                <div class="flex items-center mb-4">
                    <div class="p-3 bg-orange-100 rounded-lg mr-4">
                        <svg class="w-8 h-8 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900">Histórico de Sentimentos</h1>
                        <p class="text-gray-600">Acompanhe seus padrões emocionais ao longo do tempo</p>
                    </div>
                </div>

                <!-- Estatísticas -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                    <div class="bg-white rounded-xl p-4 shadow-sm border border-orange-100">
                        <div class="text-2xl font-bold text-orange-600">{{ $estatisticas['total'] }}</div>
                        <div class="text-sm text-gray-600">Total de registros</div>
                    </div>
                    <div class="bg-white rounded-xl p-4 shadow-sm border border-orange-100">
                        <div class="text-2xl font-bold text-{{ $cores[$estatisticas['mais_comum']->tipo_sentimento] }}-600">{{ ucfirst($estatisticas['mais_comum']->tipo_sentimento) }}</div>
                        <div class="text-sm text-gray-600">Sentimento mais comum</div>
                    </div>
                    <div class="bg-white rounded-xl p-4 shadow-sm border border-orange-100">
                        <div class="text-2xl font-bold text-orange-600">{{ number_format($estatisticas['media_intensidade'], 1) }}</div>
                        <div class="text-sm text-gray-600">Intensidade média</div>
                    </div>
                    <div class="bg-white rounded-xl p-4 shadow-sm border border-orange-100">
                        <div class="text-2xl font-bold text-orange-600">{{ $estatisticas['esta_semana'] }}</div>
                        <div class="text-sm text-gray-600">Esta semana</div>
                    </div>
                </div>
            </div>

            <!-- Filtros -->
            <div class="bg-white rounded-xl shadow-sm border border-orange-100 p-6 mb-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Filtros</h3>
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Data início</label>
                        <input type="date" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Data fim</label>
                        <input type="date" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Sentimento</label>
                        <select class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500">
                            <option value="">Todos</option>
                            <option value="feliz">😊 Feliz</option>
                            <option value="triste">😢 Triste</option>
                            <option value="ansioso">😰 Ansioso</option>
                            <option value="calmo">😌 Calmo</option>
                            <option value="raiva">😠 Com raiva</option>
                        </select>
                    </div>
                    <div class="flex items-end">
                        <button class="w-full px-4 py-2 bg-orange-600 text-white rounded-lg hover:bg-orange-700 transition-colors duration-200">
                            Filtrar
                        </button>
                    </div>
                </div>
            </div>

            <!-- Lista de Registros -->
            <div class="space-y-4">
                @forelse ($sentimentos as $sentimento)
                    @php
                        $emoji = $emojis[$sentimento->tipo_sentimento] ?? '😐';
                        $cor = $cores[$sentimento->tipo_sentimento] ?? 'gray';
                    @endphp
                    <div class="bg-white rounded-xl shadow-sm border-l-4 border-l-{{ $cor }}-400 p-6">
                        <div class="flex items-start justify-between">
                            <div class="flex items-start space-x-4">
                                <div class="text-3xl">{{ $emoji }}</div>
                                <div>
                                    <h4 class="text-lg font-semibold text-gray-800">
                                        {{ ucfirst($sentimento->tipo_sentimento) }}
                                    </h4>
                                    <p class="text-sm text-gray-600">
                                        {{ $sentimento->horario->format('d/m/Y \à\s H:i') }}
                                    </p>
                                    <p class="text-gray-700 mt-2">
                                        {{ $sentimento->descricao }}
                                    </p>
                                </div>
                            </div>
                            <div class="flex items-center space-x-2">
                                <span class="px-2 py-1 bg-{{ $cor }}-100 text-{{ $cor }}-800 text-xs rounded-full">
                                    {{ $sentimento->nivel_intensidade }}/10
                                </span>
                            </div>
                        </div>
                    </div>
                @empty
                <!-- Estado vazio (quando não há registros) -->
                <div class="hidden bg-white rounded-xl shadow-sm border border-gray-200 p-12 text-center" id="emptyState">
                    <div class="text-6xl mb-4">📊</div>
                    <h3 class="text-xl font-semibold text-gray-800 mb-2">Nenhum registro encontrado</h3>
                    <p class="text-gray-600 mb-6">Comece registrando seus sentimentos para acompanhar seus padrões emocionais</p>
                    <a href="/dashboard" class="inline-flex items-center px-4 py-2 bg-orange-600 text-white rounded-lg hover:bg-orange-700 transition-colors duration-200">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                        Registrar Sentimento
                    </a>
                </div>

                @endforelse


            </div>

            <!-- Paginação -->
            <div class="flex justify-center mt-8">
                <nav class="flex items-center space-x-2">
                    <button class="px-3 py-2 text-sm text-gray-500 hover:text-gray-700 disabled:opacity-50" disabled>
                        Anterior
                    </button>
                    <button class="px-3 py-2 text-sm bg-orange-600 text-white rounded">1</button>
                    <button class="px-3 py-2 text-sm text-gray-700 hover:text-gray-900 rounded hover:bg-gray-100">2</button>
                    <button class="px-3 py-2 text-sm text-gray-700 hover:text-gray-900 rounded hover:bg-gray-100">3</button>
                    <button class="px-3 py-2 text-sm text-gray-500 hover:text-gray-700">
                        Próximo
                    </button>
                </nav>
            </div>
        </div>
    </div>
</x-dashboard-layout>
