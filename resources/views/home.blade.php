<x-layout title="Home">
    <div class="min-h-[calc(100vh-4rem)] flex">
        <div class="flex-1 flex items-center justify-center p-8">
            <div class="max-w-lg">
                <!-- Card com gradiente verde e sombra -->
                <div class="bg-gradient-to-br from-emerald-50 to-teal-100 p-8 rounded-2xl shadow-xl border border-emerald-200 backdrop-blur-sm">
                    <!-- Título principal -->
                    <h1 class="text-3xl font-bold text-gray-800 mb-4 leading-tight">
                        Desvende os seus
                        <span class="text-transparent bg-clip-text bg-gradient-to-r from-emerald-600 to-teal-600">
                            próximos passos
                        </span>
                    </h1>

                    <!-- Subtexto -->
                    <p class="text-gray-600 mb-4 leading-relaxed">
                        Prepare-se para uma experiência única, onde cada momento é registrado,
                        cada aprendizado valorizado e cada sonho projetado.
                    </p>

                    <!-- Frase de impacto -->
                    <p class="text-lg font-medium text-emerald-700 mb-6">
                        O que você vai encontrar aqui? Um universo de possibilidades, só seu.
                    </p>

                    <!-- Botão de call-to-action -->
                    <a href="/cadastro" class="inline-block bg-gradient-to-r from-emerald-600 to-teal-600 text-white px-6 py-3 rounded-lg font-semibold shadow-lg hover:shadow-xl transform hover:-translate-y-1 transition-all duration-300 hover:from-emerald-700 hover:to-teal-700 text-center">
                        Começar Jornada
                    </a>
                </div>

                <!-- Elementos decorativos verdes -->
                <div class="absolute -z-10 top-1/4 left-1/4 w-32 h-32 bg-emerald-200 rounded-full opacity-20 animate-pulse"></div>
                <div class="absolute -z-10 bottom-1/4 left-1/2 w-24 h-24 bg-teal-200 rounded-full opacity-30 animate-pulse delay-1000"></div>
            </div>
        </div>
        <div class="flex-1 relative p-8">
            <div class="h-full relative overflow-hidden rounded-2xl shadow-2xl">
                <img class="h-full w-full object-cover" src="tela.png">
                <!-- Overlay sutil -->
                <div class="absolute inset-0 bg-gradient-to-t from-emerald-900/20 to-transparent"></div>
                <!-- Borda interna elegante -->
                <div class="absolute inset-0 border-2 border-white/20 rounded-2xl"></div>
            </div>
        </div>
    </div>
</x-layout>
