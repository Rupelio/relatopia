<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Dashboard' }} - Relatopia</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Styles / Scripts -->
    <script src="https://cdn.tailwindcss.com"></script>
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
    </style>
</head>
<body class="bg-gray-50 min-h-screen">
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
                    <a href="{{ route('historico') }}" class="text-gray-700 hover:text-emerald-600 px-3 py-2 rounded-lg text-sm font-medium transition-colors duration-200 hover:bg-emerald-50">
                        Historico
                    </a>
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
    <div id="sentimentModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 items-center justify-center hidden">
        <div class="bg-white rounded-xl p-6 max-w-md w-full mx-4 transform transition-all duration-300 scale-95 opacity-0" id="sentimentModalContent">
            <!-- Header do Modal -->
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold text-gray-800">Como você está se sentindo?</h3>
                <button onclick="closeSentimentModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <!-- Conteúdo do Modal -->
            <form id="sentimentForm" onsubmit="submitSentiment(event)">
                <input type="hidden" id="sentimentoId">
                <!-- Horário Atual -->
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Horário</label>
                    <input type="time" id="horarioAtual" class="w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded-lg text-gray-500">
                </div>

                <!-- Tipo de Sentimento -->
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Como você está se sentindo?</label>
                    <select id="tipoSentimento" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500" required>
                        <option value="">Selecione um sentimento</option>
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
                    </select>
                </div>

                <!-- Nível de Intensidade -->
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Intensidade: <span id="nivelDisplay" class="font-bold text-orange-600">5</span>/10
                    </label>
                    <input type="range" id="nivelIntensidade" min="1" max="10" value="5" class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer slider-orange">
                    <div class="flex justify-between text-xs text-gray-500 mt-1">
                        <span>Fraco</span>
                        <span>Intenso</span>
                    </div>
                </div>

                <!-- Descrição do Motivo -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">O que está causando esse sentimento?</label>
                    <textarea id="descricaoMotivo" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 resize-none" rows="3" placeholder="Descreva o que está acontecendo ou o que causou esse sentimento..." required></textarea>
                </div>

                <!-- Botões -->
                <div class="flex space-x-3">
                    <button type="button" onclick="closeSentimentModal()" class="flex-1 px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors duration-200">
                        Cancelar
                    </button>
                    <button type="submit" class="flex-1 px-4 py-2 bg-orange-600 text-white rounded-lg hover:bg-orange-700 transition-colors duration-200">
                        Registrar Sentimento
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
            function showNotification(message, type = 'info', duration = 4000) {
            const container = document.getElementById('notifications');
            const notificationId = 'notification-' + Date.now();

            // Definir cores e ícones por tipo
            const styles = {
                success: {
                    bg: 'bg-green-50 border-green-200',
                    text: 'text-green-800',
                    icon: `<svg class="w-6 h-6 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"/>
                        </svg>`,
                    title: 'Sucesso!'
                },
                error: {
                    bg: 'bg-red-50 border-red-200',
                    text: 'text-red-800',
                    icon: `<svg class="w-6 h-6 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"/>
                        </svg>`,
                    title: 'Erro!'
                },
                warning: {
                    bg: 'bg-yellow-50 border-yellow-200',
                    text: 'text-yellow-800',
                    icon: `<svg class="w-6 h-6 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"/>
                        </svg>`,
                    title: 'Atenção!'
                },
                info: {
                    bg: 'bg-blue-50 border-blue-200',
                    text: 'text-blue-800',
                    icon: `<svg class="w-6 h-6 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"/>
                        </svg>`,
                    title: 'Informação'
                }
            };

            const style = styles[type];

            // Criar elemento da notificação
            const notification = document.createElement('div');
            notification.id = notificationId;
            notification.className = `
                2-96 ${style.bg} border-2 ${style.text} rounded-xl shadow-xl
                transform transition-all duration-300 ease-in-out translate-x-full opacity-0
            `;

            notification.innerHTML = `
                <div class="p-5">
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            ${style.icon}
                        </div>
                        <div class="ml-4 flex-1">
                            <p class="text-sm font-semibold">${style.title}</p>
                            <p class="mt-1 text-sm leading-relaxed">${message}</p>
                        </div>
                        <div class="ml-4 flex-shrink-0 flex">
                            <button onclick="removeNotification('${notificationId}')" class="rounded-md inline-flex text-gray-400 hover:text-gray-600 focus:outline-none">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"/>
                                </svg>
                            </button>
                        </div>
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

            if (!tipoSentimento) {
                showNotification('Por favor, selecione como você está se sentindo', 'warning');
                return;
            }
            if (!descricao) {
                showNotification('Por favor, adicione uma descrição para continuar', 'warning');
                return;
            }

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
                    showNotification(sentimentoId ? 'Sentimento atualizado com sucesso!' : 'Sentimento registrado com sucesso!', 'success');
                    closeSentimentModal();
                    if (typeof atualizarSentimentoCard === 'function') {
                        atualizarSentimentoCard();
                    } else {
                        location.reload();
                    }
                } else {
                    const error = await response.json();
                    showNotification('Erro ao salvar: ' + (error.message || 'Algo deu errado'), 'error');
                }
            } catch (error) {
                console.error('Erro:', error);
                showNotification('Erro de conexão. Verifique sua internet e tente novamente.', 'error');
            }
        }
        function closeSentimentModal(){
            const modalSentiment = document.getElementById('sentimentModal');
            const sentimentContent = document.getElementById('sentimentModalContent');

            modalSentiment.classList.add('hidden');
            modalSentiment.classList.remove('flex');

            setTimeout(() => {
                sentimentContent.classList.add('scale-95', 'opacity-0');
                sentimentContent.classList.remove('scale-100', 'opacity-100');
            }, 10);
        }

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
