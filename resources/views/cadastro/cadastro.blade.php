<x-layout>
    <!-- Layout Mobile-First -->
    <div class="min-h-[calc(100vh-4rem)] flex flex-col lg:flex-row">

        <!-- Formulário - Mobile: Primeiro, Desktop: Segundo -->
        <div class="flex-1 flex items-center justify-center p-4 sm:p-8 order-1 lg:order-2">
            <div class="max-w-sm w-full">
                <div class="text-center mb-6 sm:mb-8">
                    @if(request('convite'))
                        <div class="bg-emerald-50 border border-emerald-200 rounded-lg p-4 mb-4">
                            <div class="flex items-center justify-center mb-2">
                                <svg class="w-5 h-5 text-emerald-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"></path>
                                </svg>
                                <span class="text-emerald-800 font-semibold">Você foi convidado!</span>
                            </div>
                            <p class="text-emerald-700 text-sm">Complete seu cadastro para aceitar o convite e começar a usar o Relatópia.</p>
                        </div>
                    @endif
                    <h1 class="text-2xl sm:text-3xl font-bold text-gray-800 mb-2">
                        {{ request('convite') ? 'Aceitar Convite' : 'Criar Conta' }}
                    </h1>
                    <p class="text-gray-600 text-sm sm:text-base">
                        {{ request('convite') ? 'Finalize seu cadastro' : 'Junte-se ao Relatopia' }}
                    </p>
                </div>

                <form method="POST" action="{{ route('cadastro') }}" class="space-y-4 sm:space-y-5">
                    @csrf

                    @if(request('convite'))
                        <input type="hidden" name="convite_id" value="{{ request('convite') }}">
                    @endif

                    <!-- Campo Nome -->
                    <div>
                        <input type="text" name="name" class="w-full px-4 py-3 border @error('name') border-red-500 @else border-gray-300 @enderror rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all duration-200 text-sm sm:text-base" placeholder="Nome completo" value="{{ old('name') }}">
                        @error('name')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Campo Email -->
                    <div>
                        <input
                            type="email"
                            name="email"
                            class="w-full px-4 py-3 border @error('email') border-red-500 @else border-gray-300 @enderror rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all duration-200 text-sm sm:text-base @if(request('email')) bg-gray-50 @endif"
                            placeholder="Digite seu email"
                            value="{{ old('email', request('email')) }}"
                            autocomplete="email"
                            spellcheck="false"
                            @if(request('email')) readonly @endif
                        >
                        @error('email')
                            <p class="text-red-500 text-sm mt-1 flex items-center">
                                <svg class="w-4 h-4 mr-1 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                </svg>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <!-- Campo Senha -->
                    <div>
                        <input type="password" name="password" class="w-full px-4 py-3 border @error('password') border-red-500 @else border-gray-300 @enderror rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all duration-200 text-sm sm:text-base" placeholder="Senha">
                        @error('password')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Campo Confirmar Senha -->
                    <div>
                        <input type="password" name="password_confirmation" class="w-full px-4 py-3 border @error('password') border-red-500 @else border-gray-300 @enderror rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all duration-200 text-sm sm:text-base" placeholder="Confirmar senha">
                    </div>

                    <button type="submit" class="w-full bg-gradient-to-r from-emerald-600 to-teal-600 text-white py-3 rounded-lg font-semibold shadow-md hover:shadow-lg transform hover:-translate-y-0.5 transition-all duration-200 hover:from-emerald-700 hover:to-teal-700 text-sm sm:text-base">
                        Criar Conta
                    </button>
                </form>

                <p class="text-center mt-4 sm:mt-6 text-gray-600 text-sm sm:text-base">
                    Já tem uma conta? <a href="/login" class="text-emerald-600 hover:text-emerald-700 font-medium">Entrar</a>
                </p>
            </div>
        </div>

        <!-- Branding - Mobile: Segundo, Desktop: Primeiro -->
        <div class="flex-none lg:flex-1 bg-gradient-to-br from-emerald-500 to-teal-600 flex items-center justify-center p-4 sm:p-8 relative order-2 lg:order-1 min-h-[200px] lg:min-h-0">
            <!-- Pattern sutil -->
            <div class="absolute inset-0 opacity-10">
                <div class="grid grid-cols-8 gap-2 sm:gap-4 h-full">
                    <div class="bg-white rounded-full w-1 h-1 sm:w-2 sm:h-2 animate-pulse"></div>
                    <div class="bg-white rounded-full w-1 h-1 sm:w-2 sm:h-2 animate-pulse delay-100"></div>
                    <div class="bg-white rounded-full w-1 h-1 sm:w-2 sm:h-2 animate-pulse delay-200"></div>
                    <div class="bg-white rounded-full w-1 h-1 sm:w-2 sm:h-2 animate-pulse delay-300"></div>
                    <div class="bg-white rounded-full w-1 h-1 sm:w-2 sm:h-2 animate-pulse delay-400"></div>
                    <div class="bg-white rounded-full w-1 h-1 sm:w-2 sm:h-2 animate-pulse delay-500"></div>
                    <div class="bg-white rounded-full w-1 h-1 sm:w-2 sm:h-2 animate-pulse delay-600"></div>
                    <div class="bg-white rounded-full w-1 h-1 sm:w-2 sm:h-2 animate-pulse delay-700"></div>
                </div>
            </div>

            <div class="text-center text-white max-w-lg z-10">
                <h2 class="text-2xl sm:text-3xl lg:text-4xl font-bold mb-3 sm:mb-6 leading-tight">
                    Comece sua jornada de
                    <span class="text-emerald-200">transformação</span>
                </h2>
                <p class="text-emerald-100 text-sm sm:text-lg lg:text-xl leading-relaxed">
                    Crie sua conta e desbloqueie todo o potencial dos seus objetivos
                </p>
            </div>
        </div>
    </div>

    <!-- Script permanece igual -->
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const emailInput = document.querySelector('input[name="email"]');

        emailInput.addEventListener('blur', function() {
            const email = this.value;
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

            // Remove mensagens de erro anteriores do JS
            const existingError = this.parentNode.querySelector('.js-email-error');
            if (existingError) existingError.remove();

            if (email && !emailRegex.test(email)) {
                this.classList.add('border-red-500');
                this.classList.remove('border-gray-300');

                const errorMsg = document.createElement('p');
                errorMsg.className = 'text-red-500 text-sm mt-1 js-email-error';
                errorMsg.innerHTML = `
                    <svg class="w-4 h-4 mr-1 inline" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                    </svg>
                    Por favor, digite um email válido
                `;
                this.parentNode.appendChild(errorMsg);
            } else if (email) {
                this.classList.remove('border-red-500');
                this.classList.add('border-gray-300');
            }
        });
    });
    </script>
</x-layout>
