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

                <!-- Menu de navegação -->
                <div class="flex items-center space-x-6">
                    <div class="flex items-center space-x-6">
                    <a href="{{ route('dashboard') }}" class="text-gray-700 hover:text-emerald-600 px-3 py-2 rounded-lg text-sm font-medium transition-colors duration-200 hover:bg-emerald-50">
                        Dashboard
                    </a>
                    <a href="{{ route('historico') }}" class="text-gray-700 hover:text-emerald-600 px-3 py-2 rounded-lg text-sm font-medium transition-colors duration-200 hover:bg-emerald-50">
                        Historico
                    </a>
                    <a href="{{ route('perfil') }}" class="text-gray-700 hover:text-emerald-600 px-3 py-2 rounded-lg text-sm font-medium transition-colors duration-200 hover:bg-emerald-50">
                        Perfil
                    </a>

                    <!-- Botão Sair com transição verde → vermelho -->
                    <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <button type="submit" class="bg-emerald-600 hover:bg-red-600 text-white px-4 py-2 rounded-lg text-sm font-medium transition-all duration-300 shadow-md hover:shadow-lg">
                            Sair
                        </button>
                    </form>
                </div>
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
    </script>
</body>
</html>
