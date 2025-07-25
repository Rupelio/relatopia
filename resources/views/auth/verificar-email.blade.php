<x-layout>
    <div class="min-h-[calc(100vh-4rem)] flex flex-col lg:flex-row">
        <!-- Formulário - Mobile: Primeiro -->
        <div class="flex-1 flex items-center justify-center p-4 sm:p-8 order-1">
            <div class="max-w-md w-full">
                <div class="text-center mb-6 sm:mb-8">
                    <div class="mx-auto w-16 h-16 bg-gradient-to-r from-emerald-500 to-teal-600 rounded-full flex items-center justify-center mb-4">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.94a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                    <h1 class="text-2xl sm:text-3xl font-bold text-gray-800 mb-2">Verificar Email</h1>
                    <p class="text-gray-600 text-sm sm:text-base">Enviamos um link de verificação para seu email</p>
                </div>

                @if(session('success'))
                    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                        {{ session('success') }}
                    </div>
                @endif

                @if(session('error'))
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                        {{ session('error') }}
                    </div>
                @endif

                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-6">
                    <div class="flex items-start">
                        <svg class="w-5 h-5 text-yellow-600 mt-0.5 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                        </svg>
                        <div>
                            <h3 class="text-sm font-medium text-yellow-800 mb-1">Email não verificado</h3>
                            <p class="text-sm text-yellow-700">
                                Você precisa verificar seu email antes de acessar o sistema.
                                Verifique sua caixa de entrada e pasta de spam.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="space-y-4">
                    <form action="{{ route('verification.send') }}" method="POST">
                        @csrf
                        <button type="submit" class="w-full bg-gradient-to-r from-emerald-600 to-teal-600 text-white py-3 rounded-lg font-semibold shadow-md hover:shadow-lg transform hover:-translate-y-0.5 transition-all duration-200 text-sm sm:text-base">
                            📧 Reenviar Email de Verificação
                        </button>
                    </form>

                    <div class="text-center">
                        <p class="text-gray-600 text-sm mb-2">Email incorreto?</p>
                        <form action="{{ route('logout') }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" class="text-emerald-600 hover:text-emerald-700 font-medium text-sm">
                                Fazer logout e corrigir
                            </button>
                        </form>
                    </div>
                </div>

                <div class="mt-8 p-4 bg-gray-50 rounded-lg">
                    <h4 class="font-medium text-gray-800 mb-2">📋 Checklist:</h4>
                    <ul class="text-sm text-gray-600 space-y-1">
                        <li>✅ Verifique sua caixa de entrada</li>
                        <li>✅ Verifique a pasta de spam/lixo eletrônico</li>
                        <li>✅ O link expira em 60 minutos</li>
                        <li>✅ Certifique-se de clicar no link mais recente</li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Branding - Mobile: Segundo -->
        <div class="flex-none lg:flex-1 bg-gradient-to-br from-emerald-500 to-teal-600 flex items-center justify-center p-4 sm:p-8 relative order-2 min-h-[200px] lg:min-h-0">
            <!-- Pattern sutil -->
            <div class="absolute inset-0 opacity-10">
                <div class="grid grid-cols-6 sm:grid-cols-8 gap-2 sm:gap-4 h-full">
                    <div class="bg-white rounded-full w-1 h-1 sm:w-2 sm:h-2 animate-pulse"></div>
                    <div class="bg-white rounded-full w-1 h-1 sm:w-2 sm:h-2 animate-pulse delay-100"></div>
                    <div class="bg-white rounded-full w-1 h-1 sm:w-2 sm:h-2 animate-pulse delay-200"></div>
                </div>
            </div>

            <div class="text-center text-white max-w-lg z-10">
                <h2 class="text-2xl sm:text-3xl lg:text-4xl font-bold mb-3 sm:mb-6 leading-tight">
                    Falta
                    <span class="text-emerald-200">pouco!</span>
                </h2>
                <p class="text-emerald-100 text-sm sm:text-lg lg:text-xl leading-relaxed">
                    Verifique seu email e comece a usar o Relatopia
                </p>
            </div>
        </div>
    </div>
</x-layout>
