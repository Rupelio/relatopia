<x-layout>
    <div class="min-h-[calc(100vh-4rem)] flex">
        <!-- Lado esquerdo - Branding -->
        <div class="flex-1 bg-gradient-to-br from-emerald-500 to-teal-600 flex items-center justify-center p-8 relative">
            <!-- Pattern sutil -->
            <div class="absolute inset-0 opacity-10">
                <div class="grid grid-cols-8 gap-4 h-full">
                    <div class="bg-white rounded-full w-2 h-2 animate-pulse"></div>
                    <div class="bg-white rounded-full w-2 h-2 animate-pulse delay-100"></div>
                    <div class="bg-white rounded-full w-2 h-2 animate-pulse delay-200"></div>
                    <div class="bg-white rounded-full w-2 h-2 animate-pulse delay-300"></div>
                    <div class="bg-white rounded-full w-2 h-2 animate-pulse delay-400"></div>
                    <div class="bg-white rounded-full w-2 h-2 animate-pulse delay-500"></div>
                    <div class="bg-white rounded-full w-2 h-2 animate-pulse delay-600"></div>
                    <div class="bg-white rounded-full w-2 h-2 animate-pulse delay-700"></div>
                </div>
            </div>

            <div class="text-center text-white max-w-lg z-10">
                <h2 class="text-4xl font-bold mb-6 leading-tight">
                    Comece sua jornada de
                    <span class="text-emerald-200">transformação</span>
                </h2>
                <p class="text-emerald-100 text-xl leading-relaxed">
                    Crie sua conta e desbloqueie todo o potencial dos seus objetivos
                </p>
            </div>
        </div>

        <!-- Lado direito - Formulário -->
        <div class="flex-1 flex items-center justify-center p-8">
            <div class="max-w-sm w-full">
                <div class="text-center mb-8">
                    <h1 class="text-3xl font-bold text-gray-800 mb-2">Criar Conta</h1>
                    <p class="text-gray-600">Junte-se ao Relatopia</p>
                </div>

                <form method="POST" action="{{ route('cadastro') }}" class="space-y-5">
                    @csrf

                    <!-- Campo Nome -->
                    <div>
                        <input type="text" name="name" class="w-full px-4 py-3 border @error('name') border-red-500 @else border-gray-300 @enderror rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all duration-200" placeholder="Nome completo" value="{{ old('name') }}">
                        @error('name')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Campo Email -->
                    <div>
                        <input
                            type="email"
                            name="email"
                            class="w-full px-4 py-3 border @error('email') border-red-500 @else border-gray-300 @enderror rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all duration-200"
                            placeholder="Digite seu email (ex: nome@exemplo.com)"
                            value="{{ old('email') }}"
                            autocomplete="email"
                            spellcheck="false"
                        >
                        @error('email')
                            <p class="text-red-500 text-sm mt-1 flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                </svg>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <!-- Campo Senha -->
                    <div>
                        <input type="password" name="password" class="w-full px-4 py-3 border @error('password') border-red-500 @else border-gray-300 @enderror rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all duration-200" placeholder="Senha">
                        @error('password')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Campo Confirmar Senha -->
                    <div>
                        <input type="password" name="password_confirmation" class="w-full px-4 py-3 border @error('password') border-red-500 @else border-gray-300 @enderror rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all duration-200" placeholder="Confirmar senha">
                        @error('password')
                            <p class="text-red-500 text-sm mt-1">As senhas não coincidem</p>
                        @enderror
                    </div>

                    <button type="submit" class="w-full bg-gradient-to-r from-emerald-600 to-teal-600 text-white py-3 rounded-lg font-semibold shadow-md hover:shadow-lg transform hover:-translate-y-0.5 transition-all duration-200 hover:from-emerald-700 hover:to-teal-700">
                        Criar Conta
                    </button>
                </form>

                <p class="text-center mt-6 text-gray-600">
                    Já tem uma conta? <a href="/login" class="text-emerald-600 hover:text-emerald-700 font-medium">Entrar</a>
                </p>
            </div>
        </div>
    </div>
</x-layout>

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
