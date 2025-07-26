<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="google-adsense-account" content="ca-pub-3613355728057212">
    <title>{{ $title ?? 'Dashboard' }} - Relatopia</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Styles / Scripts -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=ca-pub-3613355728057212"
     crossorigin="anonymous"></script>
    <style>
        .slider-orange::-webkit-slider-thumb {
            appearance: none;
            height: 20px;
            width: 20px;
            border-radius: 50%;
            background: #ea580c;
            cursor: pointer;
        }

        .slider-orange::-moz-range-thumb {
            height: 20px;
            width: 20px;
            border-radius: 50%;
            background: #ea580c;
            cursor: pointer;
            border: none;
        }

        /* Animações personalizadas */
        @keyframes progress {
            from { width: 100%; }
            to { width: 0%; }
        }

        @keyframes pulse-gentle {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.8; }
        }

        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-2px); }
            75% { transform: translateX(2px); }
        }

        @keyframes bounce-in {
            0% { transform: scale(0.3) translateY(-50px); opacity: 0; }
            50% { transform: scale(1.05); }
            70% { transform: scale(0.9); }
            100% { transform: scale(1) translateY(0); opacity: 1; }
        }

        .animate-progress {
            animation: progress linear;
        }

        .animate-pulse-gentle {
            animation: pulse-gentle 2s infinite;
        }

        .animate-shake {
            animation: shake 0.5s ease-in-out;
        }

        .animate-bounce-in {
            animation: bounce-in 0.6s ease-out;
        }

        /* Loading skeleton */
        .skeleton {
            background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
            background-size: 200% 100%;
            animation: loading 1.5s infinite;
        }

        @keyframes loading {
            0% { background-position: 200% 0; }
            100% { background-position: -200% 0; }
        }

        /* Micro interações */
        .btn-primary {
            @apply bg-emerald-600 text-white px-4 py-2 rounded-lg font-medium transition-all duration-200 transform hover:bg-emerald-700 hover:scale-105 active:scale-95 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2;
        }

        .btn-secondary {
            @apply bg-gray-100 text-gray-700 px-4 py-2 rounded-lg font-medium transition-all duration-200 transform hover:bg-gray-200 hover:scale-105 active:scale-95 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2;
        }

        .card-hover {
            @apply transition-all duration-300 hover:shadow-xl hover:-translate-y-1;
        }

        .input-focus {
            @apply transition-all duration-200 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 focus:scale-[1.02];
        }
    </style>
</head>
<body class="bg-gray-50 min-h-screen">
    @php
        $user = auth()->user();
        $relacionamento = \App\Models\Relacionamento::where(function($q) use ($user) {
            $q->where('user_id_1', $user->id)
            ->orWhere('user_id_2', $user->id);
        })->where('status', 'ativo')->first();

        // Verifica se há convite pendente recebido
        $conviteRecebido = \App\Models\Relacionamento::where('user_id_2', $user->id)
            ->where('status', 'pendente')->first();
    @endphp
    <!-- Navbar para usuários logados -->
    <nav class="fixed top-0 left-0 right-0 bg-white/95 backdrop-blur-md border-b border-emerald-100 z-50 shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <!-- Logo -->
                <div class="flex items-center">
                    <a href="{{ route('dashboard') }}" class="flex items-center hover:opacity-80 transition-opacity duration-200">
                        <img class="h-8 w-auto" src="/relatopia.png" alt="Relatopia">
                    </a>
                </div>

                <!-- Menu desktop -->
                <div class="hidden md:flex items-center space-x-6">
                    <a href="{{ route('dashboard') }}" class="text-gray-700 hover:text-emerald-600 px-3 py-2 rounded-lg text-sm font-medium transition-colors duration-200 hover:bg-emerald-50">
                        Dashboard
                    </a>

                    <!-- Dropdown do Calendário -->
                    <div class="relative group">
                        <button class="text-gray-700 hover:text-emerald-600 px-3 py-2 rounded-lg text-sm font-medium transition-colors duration-200 hover:bg-emerald-50 flex items-center">
                            Calendário
                            <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                        <div class="absolute left-0 w-48 bg-white rounded-lg shadow-lg border border-gray-200 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 z-50">
                                                        <a href="{{ route('calendario') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-emerald-50 hover:text-emerald-600 rounded-t-lg">
                                <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                                Meu Calendário
                            </a>
                            @if($relacionamento)
                                <a href="{{ route('calendario.casal') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-emerald-50 hover:text-emerald-600 rounded-b-lg">
                                    <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                                    </svg>
                                    Calendário do Casal
                                </a>
                            @endif
                        </div>
                    </div>

                    <a href="{{ route('historico') }}" class="text-gray-700 hover:text-emerald-600 px-3 py-2 rounded-lg text-sm font-medium transition-colors duration-200 hover:bg-emerald-50">
                        Historico
                    </a>

                    @if($relacionamento)
                        <div class="relative group">
                            <button id="parceiroDropdownBtn" class="flex items-center text-gray-700 hover:text-emerald-600 px-3 py-2 rounded-lg text-sm font-medium transition-colors duration-200 hover:bg-emerald-50 focus:outline-none">
                                Parceiro
                                <svg class="ml-1 w-4 h-4 text-gray-500 group-hover:text-emerald-600 transition-colors duration-200" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </button>
                            <div id="parceiroDropdown" class="hidden group-hover:block absolute right-0 w-52 bg-white border rounded shadow-lg z-50">
                                <a href="{{ route('dashboard-parceiro', $relacionamento->id) }}" class="block px-4 py-2 text-gray-700 text-sm font-medium hover:bg-emerald-50">Dashboard do parceiro</a>
                                <a href="{{ route('historico-parceiro', $relacionamento->id) }}" class="block px-4 py-2 text-gray-700 text-sm font-medium hover:bg-emerald-50">Histórico do parceiro</a>
                            </div>
                        </div>
                    @endif
                    <a href="{{ route('perfil') }}" class="relative text-gray-700 hover:text-emerald-600 px-3 py-2 rounded-lg text-sm font-medium transition-colors duration-200 hover:bg-emerald-50">
                        Perfil
                        @if($conviteRecebido)
                            <span class="absolute -top-1 -right-1 inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-white bg-red-600 rounded-full animate-pulse">
                                !
                            </span>
                        @endif
                    </a>

                    <!-- Botão Sair com transição verde → vermelho -->
                    <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <button type="submit" class="bg-emerald-600 hover:bg-red-600 text-white px-4 py-2 rounded-lg text-sm font-medium transition-all duration-300 shadow-md hover:shadow-lg">
                            Sair
                        </button>
                    </form>
                </div>

                <!-- Botão hambúrguer mobile -->
                <div class="md:hidden">
                    <button id="mobileMenuBtn" type="button" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-emerald-600 hover:bg-emerald-50 focus:outline-none focus:ring-2 focus:ring-emerald-500 transition-colors duration-200" aria-controls="mobile-menu" aria-expanded="false">
                        <span class="sr-only">Abrir menu principal</span>
                        <!-- Ícone hambúrguer -->
                        <svg id="hamburgerIcon" class="block h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                        <!-- Ícone X -->
                        <svg id="closeIcon" class="hidden h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <!-- Menu mobile -->
        <div id="mobileMenu" class="md:hidden hidden">
            <div class="px-2 pt-2 pb-3 space-y-1 sm:px-3 bg-white border-t border-emerald-100">
                <a href="{{ route('dashboard') }}" class="text-gray-700 hover:text-emerald-600 hover:bg-emerald-50 block px-3 py-2 rounded-md text-base font-medium transition-colors duration-200">Dashboard</a>

                <!-- Seção de Calendário Mobile -->
                <div class="border-t border-gray-200 pt-2 mt-2">
                    <div class="px-3 py-2 text-xs font-semibold text-gray-500 uppercase tracking-wider">Calendário</div>
                    <a href="{{ route('calendario') }}" class="text-gray-700 hover:text-emerald-600 hover:bg-emerald-50 block px-3 py-2 rounded-md text-base font-medium transition-colors duration-200">Meu Calendário</a>
                    @if($relacionamento)
                        <a href="{{ route('calendario.casal') }}" class="text-gray-700 hover:text-emerald-600 hover:bg-emerald-50 block px-3 py-2 rounded-md text-base font-medium transition-colors duration-200">Calendário do Casal</a>
                    @endif
                </div>

                <a href="{{ route('historico') }}" class="text-gray-700 hover:text-emerald-600 hover:bg-emerald-50 block px-3 py-2 rounded-md text-base font-medium transition-colors duration-200">Historico</a>

                @if($relacionamento)
                    <div class="border-t border-gray-200 pt-2 mt-2">
                        <div class="px-3 py-2 text-xs font-semibold text-gray-500 uppercase tracking-wider">Parceiro</div>
                        <a href="{{ route('dashboard-parceiro', $relacionamento->id) }}" class="text-gray-700 hover:text-emerald-600 hover:bg-emerald-50 block px-3 py-2 rounded-md text-base font-medium transition-colors duration-200">Dashboard do parceiro</a>
                        <a href="{{ route('historico-parceiro', $relacionamento->id) }}" class="text-gray-700 hover:text-emerald-600 hover:bg-emerald-50 block px-3 py-2 rounded-md text-base font-medium transition-colors duration-200">Histórico do parceiro</a>
                    </div>
                @endif

                <a href="{{ route('perfil') }}" class="relative text-gray-700 hover:text-emerald-600 hover:bg-emerald-50 block px-3 py-2 rounded-md text-base font-medium transition-colors duration-200">
                    Perfil
                    @if($conviteRecebido)
                        <span class="absolute top-2 right-3 inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-white bg-red-600 rounded-full animate-pulse">
                            !
                        </span>
                    @endif
                </a>

                <div class="border-t border-gray-200 pt-2 mt-2">
                    <form method="POST" action="{{ route('logout') }}" class="block">
                        @csrf
                        <button type="submit" class="w-full text-left bg-emerald-600 hover:bg-red-600 text-white block px-3 py-2 rounded-md text-base font-medium transition-all duration-300">
                            Sair
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </nav>

    <!-- Conteúdo principal -->
    <main class="pt-16">
        {{ $slot }}
    </main>

    <!-- Loading Overlay Global -->
    <div id="globalLoading" class="fixed inset-0 bg-black bg-opacity-30 z-50 items-center justify-center hidden">
        <div class="bg-white rounded-xl p-6 shadow-lg flex items-center space-x-3">
            <div class="animate-spin rounded-full h-6 w-6 border-b-2 border-emerald-600"></div>
            <span class="text-gray-700 font-medium">Carregando...</span>
        </div>
    </div>

    <!-- Toast Notifications Container -->
    <div id="notifications" class="fixed top-4 right-4 z-50 space-y-2"></div>
    <footer class="bg-gradient-to-r from-emerald-50 to-teal-50 border-t border-emerald-100 py-3">
        <div class="max-w-7xl mx-auto px-4">
            <div class="flex flex-col sm:flex-row items-center justify-between gap-2">
                <!-- Texto principal -->
                <div class="text-center sm:text-left">
                    <p class="text-xs sm:text-sm text-emerald-700 font-medium">
                        Created by
                        <span class="text-transparent bg-clip-text bg-gradient-to-r from-emerald-600 to-teal-600 font-semibold">
                            Rupelio
                        </span>
                    </p>
                </div>

                <!-- Decoração opcional -->
                <div class="flex items-center space-x-1">
                    <div class="w-2 h-2 bg-emerald-400 rounded-full animate-pulse"></div>
                    <div class="w-1.5 h-1.5 bg-teal-400 rounded-full animate-pulse delay-100"></div>
                    <div class="w-1 h-1 bg-emerald-300 rounded-full animate-pulse delay-200"></div>
                </div>
            </div>
        </div>
    </footer>
    <!-- Modal para registrar sentimentos -->
    <div id="sentimentModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 items-center justify-center hidden backdrop-blur-sm">
        <div class="bg-white rounded-2xl p-6 max-w-md w-full mx-4 transform transition-all duration-500 scale-95 opacity-0 shadow-2xl border border-emerald-100" id="sentimentModalContent">
            <!-- Header do Modal -->
            <div class="flex justify-between items-center mb-6">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 bg-gradient-to-br from-orange-400 to-orange-600 rounded-full flex items-center justify-center">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h1.01M15 10h1.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800">Como você está se sentindo?</h3>
                </div>
                <button onclick="closeSentimentModal()" class="text-gray-400 hover:text-gray-600 transition-colors duration-200 p-2 hover:bg-gray-100 rounded-full">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <!-- Conteúdo do Modal -->
            <form id="sentimentForm" onsubmit="submitSentiment(event)" class="space-y-6">
                <input type="hidden" id="sentimentoId">

                <!-- Horário Atual -->
                <div class="space-y-2">
                    <label class="block text-sm font-semibold text-gray-700 flex items-center">
                        <svg class="w-4 h-4 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Horário
                    </label>
                    <input type="time" id="horarioAtual" class="w-full px-4 py-3 bg-gray-50 border-2 border-gray-200 rounded-xl text-gray-700 transition-all duration-200 focus:border-orange-500 focus:bg-white focus:ring-2 focus:ring-orange-200 input-focus">
                </div>

                <!-- Tipo de Sentimento -->
                <div class="space-y-2">
                    <label class="block text-sm font-semibold text-gray-700 flex items-center">
                        <svg class="w-4 h-4 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h1.01M15 10h1.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Como você está se sentindo?
                    </label>
                    <select id="tipoSentimento" class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-orange-500 focus:ring-2 focus:ring-orange-200 transition-all duration-200 input-focus" required>
                        <option value="" disabled selected>Selecione um sentimento</option>
                        <option value="feliz">😊 Feliz</option>
                        <option value="triste">😢 Triste</option>
                        <option value="ansioso">😰 Ansioso</option>
                        <option value="calmo">😌 Calmo</option>
                        <option value="raiva">😠 Com raiva</option>
                        <option value="empolgado">🤩 Empolgado</option>
                        <option value="frustrado">😤 Frustrado</option>
                        <option value="amoroso">🥰 Amoroso</option>
                        <option value="preocupado">😟 Preocupado</option>
                        <option value="grato">🙏 Grato</option>
                        <option value="entediado">😐 Entediado</option>
                        <option value="cansado">😴 Cansado</option>
                        <option value="estressado">😣 Estressado</option>
                        <option value="confiante">😎 Confiante</option>
                    </select>
                </div>

                <!-- Nível de Intensidade -->
                <div class="space-y-3">
                    <label class="block text-sm font-semibold text-gray-700 flex items-center justify-between">
                        <span class="flex items-center">
                            <svg class="w-4 h-4 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                            </svg>
                            Intensidade
                        </span>
                        <span class="text-lg font-bold text-orange-600 bg-orange-100 px-3 py-1 rounded-full" id="nivelDisplay">5</span>
                    </label>
                    <div class="px-2">
                        <input type="range" id="nivelIntensidade" min="1" max="10" value="5" class="w-full h-3 bg-gradient-to-r from-green-300 via-yellow-300 to-red-400 rounded-lg appearance-none cursor-pointer slider-orange transition-all duration-200 hover:scale-105">
                        <div class="flex justify-between text-xs text-gray-500 mt-2 px-1">
                            <span class="flex items-center">
                                <div class="w-2 h-2 bg-green-400 rounded-full mr-1"></div>
                                Fraco
                            </span>
                            <span class="flex items-center">
                                <div class="w-2 h-2 bg-red-400 rounded-full mr-1"></div>
                                Intenso
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Descrição do Motivo -->
                <div class="space-y-2">
                    <label class="block text-sm font-semibold text-gray-700 flex items-center">
                        <svg class="w-4 h-4 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                        O que está causando esse sentimento?
                    </label>
                    <textarea id="descricaoMotivo" class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-orange-500 focus:ring-2 focus:ring-orange-200 resize-none transition-all duration-200 input-focus" rows="4" placeholder="Descreva detalhadamente o que está acontecendo ou o que causou esse sentimento..." required></textarea>
                    <div class="text-xs text-gray-500 flex items-center">
                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Seja específico para ajudar a identificar padrões futuros
                    </div>
                </div>

                <!-- Botões -->
                <div class="flex space-x-3 pt-4">
                    <button type="button" onclick="closeSentimentModal()" class="flex-1 px-6 py-3 border-2 border-gray-200 text-gray-700 rounded-xl hover:bg-gray-50 hover:border-gray-300 transition-all duration-200 font-medium btn-secondary">
                        Cancelar
                    </button>
                    <button type="submit" class="flex-1 px-6 py-3 bg-gradient-to-r from-orange-500 to-orange-600 text-white rounded-xl hover:from-orange-600 hover:to-orange-700 transition-all duration-200 font-medium shadow-lg hover:shadow-xl transform hover:scale-105 active:scale-95 btn-primary">
                        <span class="flex items-center justify-center">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                            </svg>
                            Registrar Sentimento
                        </span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Funções utilitárias para UX melhorada
        function showGlobalLoading() {
            const loading = document.getElementById('globalLoading');
            loading.classList.remove('hidden');
            loading.classList.add('flex');
        }

        function hideGlobalLoading() {
            const loading = document.getElementById('globalLoading');
            loading.classList.add('hidden');
            loading.classList.remove('flex');
        }

        function setButtonLoading(buttonElement, loading = true) {
            if (loading) {
                buttonElement.disabled = true;
                const originalText = buttonElement.textContent;
                buttonElement.dataset.originalText = originalText;
                buttonElement.innerHTML = `
                    <div class="flex items-center justify-center">
                        <div class="animate-spin rounded-full h-4 w-4 border-b-2 border-white mr-2"></div>
                        Carregando...
                    </div>
                `;
                buttonElement.classList.add('opacity-75');
            } else {
                buttonElement.disabled = false;
                buttonElement.textContent = buttonElement.dataset.originalText || 'Enviar';
                buttonElement.classList.remove('opacity-75');
            }
        }

        function shakeElement(element) {
            element.classList.add('animate-shake');
            setTimeout(() => {
                element.classList.remove('animate-shake');
            }, 500);
        }

        function bounceInElement(element) {
            element.classList.add('animate-bounce-in');
            setTimeout(() => {
                element.classList.remove('animate-bounce-in');
            }, 600);
        }

        // Sistema de notificações melhorado
                    function showNotification(message, type = 'success', duration = 4000) {
            const container = document.getElementById('notifications');
            const notificationId = 'notification-' + Date.now();

            // Estilos baseados no tipo
            const styles = {
                success: {
                    bg: 'bg-emerald-50 border-emerald-200',
                    text: 'text-emerald-800',
                    icon: `<div class="w-6 h-6 bg-emerald-100 rounded-full flex items-center justify-center">
                            <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                           </div>`,
                    title: 'Sucesso!'
                },
                error: {
                    bg: 'bg-red-50 border-red-200',
                    text: 'text-red-800',
                    icon: `<div class="w-6 h-6 bg-red-100 rounded-full flex items-center justify-center">
                            <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                           </div>`,
                    title: 'Erro!'
                },
                warning: {
                    bg: 'bg-yellow-50 border-yellow-200',
                    text: 'text-yellow-800',
                    icon: `<div class="w-6 h-6 bg-yellow-100 rounded-full flex items-center justify-center">
                            <svg class="w-4 h-4 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                            </svg>
                           </div>`,
                    title: 'Atenção!'
                },
                info: {
                    bg: 'bg-blue-50 border-blue-200',
                    text: 'text-blue-800',
                    icon: `<div class="w-6 h-6 bg-blue-100 rounded-full flex items-center justify-center">
                            <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                           </div>`,
                    title: 'Informação'
                }
            };

            const style = styles[type];

            // Criar elemento da notificação
            const notification = document.createElement('div');
            notification.id = notificationId;
            notification.className = `
                max-w-sm w-full ${style.bg} border-2 ${style.text} rounded-xl shadow-lg
                transform transition-all duration-500 ease-out translate-x-full opacity-0
                hover:scale-105 cursor-pointer
            `;

            notification.innerHTML = `
                <div class="p-4">
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            ${style.icon}
                        </div>
                        <div class="ml-3 flex-1">
                            <p class="text-sm font-semibold">${style.title}</p>
                            <p class="mt-1 text-sm leading-relaxed">${message}</p>
                        </div>
                        <div class="ml-4 flex-shrink-0">
                            <button onclick="removeNotification('${notificationId}')" class="rounded-full p-1 hover:bg-white hover:bg-opacity-20 transition-colors duration-200 focus:outline-none">
                                <svg class="w-4 h-4 ${style.text} opacity-60 hover:opacity-100" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                    <!-- Barra de progresso -->
                    <div class="mt-3 w-full bg-white bg-opacity-30 rounded-full h-1">
                        <div class="bg-current h-1 rounded-full animate-progress" style="width: 100%; animation: progress ${duration}ms linear;"></div>
                    </div>
                </div>
            `;

            // Adicionar ao container
            container.appendChild(notification);

            // Animar entrada
            setTimeout(() => {
                notification.classList.remove('translate-x-full', 'opacity-0');
                notification.classList.add('translate-x-0', 'opacity-100');
            }, 100);

            // Auto remover após duration
            setTimeout(() => {
                removeNotification(notificationId);
            }, duration);
        }
        function removeNotification(notificationId) {
            const notification = document.getElementById(notificationId);
            if (notification) {
                notification.classList.add('translate-x-full', 'opacity-0');
                setTimeout(() => {
                    notification.remove();
                }, 300);
            }
        }
        async function submitSentiment(event) {
            event.preventDefault();

            const sentimentoId = document.getElementById('sentimentoId').value;
            const tipoSentimento = document.getElementById('tipoSentimento').value;
            const nivelIntensidade = document.getElementById('nivelIntensidade').value;
            const horario = document.getElementById('horarioAtual').value;
            const descricao = document.getElementById('descricaoMotivo').value.trim();

            // Validações com feedback visual melhorado
            const sentimentSelect = document.getElementById('tipoSentimento');
            const descricaoTextarea = document.getElementById('descricaoMotivo');

            if (!tipoSentimento) {
                shakeElement(sentimentSelect);
                showNotification('Por favor, selecione como você está se sentindo', 'warning');
                sentimentSelect.focus();
                return;
            }

            if (!descricao) {
                shakeElement(descricaoTextarea);
                showNotification('Por favor, adicione uma descrição para continuar', 'warning');
                descricaoTextarea.focus();
                return;
            }

            // Feedback visual no botão
            const submitButton = event.target.querySelector('button[type="submit"]');
            setButtonLoading(submitButton, true);

            try {
                const url = sentimentoId
                    ? `/api/sentimento/${sentimentoId}`
                    : '/api/sentimento';

                const method = sentimentoId ? 'PUT' : 'POST';

                const response = await fetch(url, {
                    method: method,
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        tipo_sentimento: tipoSentimento,
                        nivel_intensidade: nivelIntensidade,
                        descricao: descricao,
                        horario: horario
                    })
                });

                if (response.ok) {
                    // Sucesso com animação
                    bounceInElement(submitButton);
                    showNotification(sentimentoId ? 'Sentimento atualizado com sucesso!' : 'Sentimento registrado com sucesso!', 'success');
                    closeSentimentModal();

                    // Reset form
                    document.getElementById('sentimentForm').reset();
                    document.getElementById('sentimentoId').value = '';
                    document.getElementById('nivelDisplay').textContent = '5';

                    // Atualizar dados se possível
                    if (typeof atualizarSentimentoCard === 'function') {
                        atualizarSentimentoCard();
                    } else {
                        setTimeout(() => location.reload(), 1000);
                    }
                } else {
                    const error = await response.json();
                    shakeElement(submitButton);
                    showNotification('Erro ao salvar: ' + (error.message || 'Algo deu errado'), 'error');
                }
            } catch (error) {
                console.error('Erro:', error);
                shakeElement(submitButton);
                showNotification('Erro de conexão. Verifique sua internet e tente novamente.', 'error');
            } finally {
                setButtonLoading(submitButton, false);
            }
        }
        function closeSentimentModal(){
            const modalSentiment = document.getElementById('sentimentModal');
            const sentimentContent = document.getElementById('sentimentModalContent');

            // Animar saída
            sentimentContent.classList.add('scale-95', 'opacity-0');
            sentimentContent.classList.remove('scale-100', 'opacity-100');

            setTimeout(() => {
                modalSentiment.classList.add('hidden');
                modalSentiment.classList.remove('flex');
            }, 300);

            // Reset form
            document.getElementById('sentimentForm').reset();
            document.getElementById('sentimentoId').value = '';
            document.getElementById('nivelDisplay').textContent = '5';
        }

        // Melhorar a função de abertura do modal
        function openSentimentModal(){
            const modalSentiment = document.getElementById('sentimentModal');
            const sentimentContent = document.getElementById('sentimentModalContent');

            const date = new Date();
            const hora = date.getHours().toString().padStart(2, '0');
            const minutos = date.getMinutes().toString().padStart(2, '0');

            const horarioFormatado = hora + ":" + minutos;

            document.getElementById('horarioAtual').value = horarioFormatado;

            modalSentiment.classList.remove('hidden');
            modalSentiment.classList.add('flex');

            // Animação de entrada melhorada
            setTimeout(() => {
                sentimentContent.classList.remove('scale-95', 'opacity-0');
                sentimentContent.classList.add('scale-100', 'opacity-100');
                bounceInElement(sentimentContent);
            }, 50);

            // Configurar o slider de intensidade
            document.getElementById('nivelIntensidade').addEventListener('input', function() {
                const valor = this.value;
                const display = document.getElementById('nivelDisplay');
                display.textContent = valor;

                // Animar o display
                display.classList.add('animate-pulse-gentle');
                setTimeout(() => {
                    display.classList.remove('animate-pulse-gentle');
                }, 300);
            });

            // Foco no primeiro campo
            setTimeout(() => {
                document.getElementById('tipoSentimento').focus();
            }, 400);
        }

        // Adicionar eventos de teclado para acessibilidade
        document.addEventListener('keydown', function(e) {
            const modal = document.getElementById('sentimentModal');
            if (!modal.classList.contains('hidden') && e.key === 'Escape') {
                closeSentimentModal();
            }
        });

        // Click fora do modal para fechar
        document.addEventListener('click', function(e) {
            const modal = document.getElementById('sentimentModal');
            const modalContent = document.getElementById('sentimentModalContent');

            if (!modal.classList.contains('hidden') && e.target === modal) {
                closeSentimentModal();
            }
        });

        // JavaScript para controlar o menu mobile
        document.addEventListener('DOMContentLoaded', function() {
            const mobileMenuBtn = document.getElementById('mobileMenuBtn');
            const mobileMenu = document.getElementById('mobileMenu');
            const hamburgerIcon = document.getElementById('hamburgerIcon');
            const closeIcon = document.getElementById('closeIcon');

            if (mobileMenuBtn && mobileMenu) {
                mobileMenuBtn.addEventListener('click', function() {
                    const isHidden = mobileMenu.classList.contains('hidden');

                    if (isHidden) {
                        // Mostrar menu
                        mobileMenu.classList.remove('hidden');
                        hamburgerIcon.classList.add('hidden');
                        closeIcon.classList.remove('hidden');
                        mobileMenuBtn.setAttribute('aria-expanded', 'true');
                    } else {
                        // Esconder menu
                        mobileMenu.classList.add('hidden');
                        hamburgerIcon.classList.remove('hidden');
                        closeIcon.classList.add('hidden');
                        mobileMenuBtn.setAttribute('aria-expanded', 'false');
                    }
                });

                // Fechar menu ao clicar em um link
                const mobileLinks = mobileMenu.querySelectorAll('a, button[type="submit"]');
                mobileLinks.forEach(link => {
                    link.addEventListener('click', function() {
                        mobileMenu.classList.add('hidden');
                        hamburgerIcon.classList.remove('hidden');
                        closeIcon.classList.add('hidden');
                        mobileMenuBtn.setAttribute('aria-expanded', 'false');
                    });
                });

                // Fechar menu ao redimensionar para desktop
                window.addEventListener('resize', function() {
                    if (window.innerWidth >= 768) { // md breakpoint
                        mobileMenu.classList.add('hidden');
                        hamburgerIcon.classList.remove('hidden');
                        closeIcon.classList.add('hidden');
                        mobileMenuBtn.setAttribute('aria-expanded', 'false');
                    }
                });
            }
        });

    </script>
</body>
</html>
