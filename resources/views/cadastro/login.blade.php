<x-layout>
    <div class="min-h-[calc(100vh-4rem)] flex flex-col lg:flex-row">
        <!-- Formulário - Mobile: Primeiro -->
        <div class="flex-1 flex items-center justify-center p-4 sm:p-8 order-1">
            <div class="max-w-sm w-full">
                <div class="text-center mb-6 sm:mb-8">
                    <h1 class="text-2xl sm:text-3xl font-bold text-gray-800 mb-2">Bem-vindo de volta!</h1>
                    <p class="text-gray-600 text-sm sm:text-base">Entre na sua conta</p>
                </div>

                <form action="{{ route('login') }}" method="POST" class="space-y-4 sm:space-y-5">
                    @csrf

                    <!-- Campo Email -->
                    <div>
                        <input type="email" name="email" class="w-full px-4 py-3 border @error('email') border-red-500 @else border-gray-300 @enderror rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all duration-200 text-sm sm:text-base" placeholder="Email" value="{{ old('email') }}" required>
                        @error('email')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Campo Senha -->
                    <div>
                        <input type="password" name="password" class="w-full px-4 py-3 border @error('password') border-red-500 @else border-gray-300 @enderror rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all duration-200 text-sm sm:text-base" placeholder="Senha" required>
                        @error('password')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <button type="submit" class="w-full bg-gradient-to-r from-emerald-600 to-teal-600 text-white py-3 rounded-lg font-semibold shadow-md hover:shadow-lg transform hover:-translate-y-0.5 transition-all duration-200 text-sm sm:text-base">
                        Entrar
                    </button>
                </form>

                <p class="text-center mt-4 sm:mt-6 text-gray-600 text-sm sm:text-base">
                    Não tem conta? <a href="/cadastro" class="text-emerald-600 hover:text-emerald-700 font-medium">Cadastre-se</a>
                </p>
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
                    O futuro dos seus
                    <span class="text-emerald-200">sonhos</span>
                    começa aqui
                </h2>
                <p class="text-emerald-100 text-sm sm:text-lg lg:text-xl leading-relaxed">
                    Cada login é um passo mais próximo dos seus objetivos
                </p>
            </div>
        </div>
    </div>
</x-layout>
