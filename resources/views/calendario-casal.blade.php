<x-dashboard-layout title="Calendário do Casal">
    <link rel="stylesheet" href="{{ asset('css/calendario-animations.css') }}">

    <div class="min-h-screen bg-gradient-to-br from-purple-50 via-white to-pink-50 py-4 lg:py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header - Mobile Responsive -->
            <div class="mb-6 lg:mb-8 fade-in">
                <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                    <div>
                        <h1 class="text-2xl lg:text-3xl font-bold text-gray-800 flex items-center">
                            <svg class="w-6 h-6 lg:w-8 lg:h-8 text-purple-600 mr-2 lg:mr-3 easter-egg" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                            </svg>
                            Calendário do Casal
                        </h1>
                        <p class="text-gray-600 mt-1 text-sm lg:text-base">Eventos compartilhados com seu(sua) parceiro(a)</p>
                    </div>

                    <div class="flex flex-col sm:flex-row gap-3">
                        <a href="{{ route('calendario.individual') }}"
                           class="px-4 py-2.5 bg-emerald-100 text-emerald-700 rounded-xl hover:bg-emerald-200 transition-all duration-200 font-medium text-center touch-manipulation active:scale-95 btn-bounce touch-feedback">
                            <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                            Meu Calendário
                        </a>
                        <button onclick="openEventoModal()"
                                class="px-6 py-2.5 bg-gradient-to-r from-purple-500 to-pink-600 text-white rounded-xl font-semibold hover:from-purple-600 hover:to-pink-700 transition-all duration-200 transform hover:scale-105 active:scale-95 shadow-lg hover:shadow-xl touch-manipulation ripple btn-bounce">
                            <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                            <span class="hidden sm:inline">Novo Evento do Casal</span>
                            <span class="sm:hidden">Novo Evento</span>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Calendário será incluído aqui -->
            <div id="calendario-container" class="fade-in calendario-scroll"></div>
        </div>
    </div>

    <script>
        // Configuração específica para calendário do casal
        const tipoCalendario = 'casal';
        const apiEndpoint = '/api/eventos/compartilhados';
        const tipoEventoFixo = 'compartilhado';
    </script>
    <script src="{{ asset('js/calendario-base.js') }}"></script>
</x-dashboard-layout>
