// Variáveis globais
let dataAtual = new Date();
let eventos = [];
let eventoSelecionado = null;
let visualizacaoAtual = 'mes';

// Configurações específicas definidas em cada view
// tipoCalendario, apiEndpoint, tipoEventoFixo

// Inicialização
document.addEventListener('DOMContentLoaded', function() {
    inicializarCalendario();
    carregarEventos();
    configurarEventListeners();
});

function inicializarCalendario() {
    renderizarEstrutura();
    atualizarMesAtual();
    renderizarCalendario();
}

function renderizarEstrutura() {
    const container = document.getElementById('calendario-container');

    container.innerHTML = `
        <!-- Controles do Calendário - Mobile Responsive -->
        <div class="bg-white rounded-2xl shadow-lg border border-emerald-100 mb-6">
            <div class="p-4 lg:p-6">
                <!-- Mobile First Layout -->
                <div class="space-y-4 lg:space-y-0 lg:flex lg:items-center lg:justify-between">
                    <!-- Navegação do Mês - Mobile Optimized -->
                    <div class="flex items-center justify-center lg:justify-start space-x-2 lg:space-x-4">
                        <button onclick="anteriorMes()" class="p-2 text-emerald-600 hover:bg-emerald-50 rounded-lg transition-all duration-200 active:scale-95 touch-manipulation">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                            </svg>
                        </button>

                        <h2 id="mesAtual" class="text-lg lg:text-xl font-bold text-gray-800 min-w-[150px] lg:min-w-[200px] text-center"></h2>

                        <button onclick="proximoMes()" class="p-2 text-emerald-600 hover:bg-emerald-50 rounded-lg transition-all duration-200 active:scale-95 touch-manipulation">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </button>

                        <button onclick="voltarHoje()" class="px-3 py-1.5 lg:px-4 lg:py-2 bg-emerald-100 text-emerald-700 rounded-lg hover:bg-emerald-200 transition-all duration-200 font-medium text-sm lg:text-base active:scale-95 touch-manipulation">
                            Hoje
                        </button>
                    </div>

                    <!-- Filtros - Mobile Optimized -->
                    <div class="flex flex-col sm:flex-row gap-3 lg:gap-4">
                        <!-- Toggle para Mobile -->
                        <button id="toggleFiltros" class="sm:hidden flex items-center justify-center px-4 py-2 bg-emerald-100 text-emerald-700 rounded-lg font-medium transition-all duration-200 active:scale-95 touch-manipulation">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.414A1 1 0 013 6.707V4z"></path>
                            </svg>
                            Filtros
                        </button>

                        <!-- Container de Filtros -->
                        <div id="filtrosContainer" class="hidden sm:flex flex-col sm:flex-row gap-3 lg:gap-4 w-full sm:w-auto">
                            <div class="flex flex-col sm:flex-row items-start sm:items-center gap-2">
                                <label class="text-sm font-medium text-gray-700 min-w-fit">Visualização:</label>
                                <select id="tipoVisualizacao" onchange="alterarVisualizacao()"
                                        class="w-full sm:w-auto px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-emerald-500 transition-all duration-200 touch-manipulation">
                                    <option value="mes">Mês</option>
                                    <option value="semana">Semana</option>
                                    <option value="lista">Lista</option>
                                </select>
                            </div>

                            <div class="flex flex-col sm:flex-row items-start sm:items-center gap-2">
                                <label class="text-sm font-medium text-gray-700 min-w-fit">Categoria:</label>
                                <select id="filtroCategoria" onchange="filtrarEventos()"
                                        class="w-full sm:w-auto px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-emerald-500 transition-all duration-200 touch-manipulation">
                                    <option value="todas">Todas</option>
                                    <option value="aniversario">Aniversário</option>
                                    <option value="encontro">Encontro</option>
                                    <option value="viagem">Viagem</option>
                                    <option value="comemoração">Comemoração</option>
                                    <option value="compromisso">Compromisso</option>
                                    <option value="outro">Outro</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Grid do Calendário - Mobile Responsive -->
        <div class="bg-white rounded-2xl shadow-lg border border-emerald-100">
            <!-- Cabeçalho dos dias da semana - Mobile Hidden -->
            <div class="hidden md:grid grid-cols-7 bg-emerald-50 rounded-t-2xl">
                <div class="p-3 lg:p-4 text-center font-semibold text-emerald-700 text-sm lg:text-base">Dom</div>
                <div class="p-3 lg:p-4 text-center font-semibold text-emerald-700 text-sm lg:text-base">Seg</div>
                <div class="p-3 lg:p-4 text-center font-semibold text-emerald-700 text-sm lg:text-base">Ter</div>
                <div class="p-3 lg:p-4 text-center font-semibold text-emerald-700 text-sm lg:text-base">Qua</div>
                <div class="p-3 lg:p-4 text-center font-semibold text-emerald-700 text-sm lg:text-base">Qui</div>
                <div class="p-3 lg:p-4 text-center font-semibold text-emerald-700 text-sm lg:text-base">Sex</div>
                <div class="p-3 lg:p-4 text-center font-semibold text-emerald-700 text-sm lg:text-base">Sáb</div>
            </div>

            <!-- Grid das datas - Mobile Responsive -->
            <div id="calendario-grid" class="grid grid-cols-7 md:grid-cols-7 gap-px md:gap-0">
                <!-- Será preenchido via JavaScript -->
            </div>
        </div>

        <!-- Visualização Mobile Semana -->
        <div id="semana-mobile" class="md:hidden bg-white rounded-2xl shadow-lg border border-emerald-100 mt-6" style="display: none;">
            <div class="p-4">
                <h3 class="text-lg font-bold text-gray-800 mb-4">Visão Semanal</h3>
                <div id="semana-mobile-container">
                    <!-- Será preenchido via JavaScript -->
                </div>
            </div>
        </div>

        <!-- Visualização de Lista (inicialmente oculta) -->
        <div id="lista-eventos" class="bg-white rounded-2xl shadow-lg border border-emerald-100 mt-6" style="display: none;">
            <div class="p-6">
                <h3 class="text-lg font-bold text-gray-800 mb-4">Lista de Eventos</h3>
                <div id="eventos-lista-container">
                    <!-- Será preenchido via JavaScript -->
                </div>
            </div>
        </div>

        <!-- Modal para Criar/Editar Evento - Mobile Optimized -->
        <div id="eventoModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50 transition-opacity duration-300 p-4">
            <div id="eventoModalContent" class="bg-white rounded-2xl w-full max-w-lg mx-auto transform scale-95 opacity-0 transition-all duration-300 shadow-2xl border border-emerald-200 max-h-[90vh] overflow-y-auto">
                <div class="sticky top-0 bg-white rounded-t-2xl border-b border-gray-100 p-4 lg:p-6">
                    <div class="flex justify-between items-center">
                        <div class="flex items-center">
                            <div class="p-2 bg-emerald-100 rounded-lg mr-3">
                                <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                            </div>
                            <h3 id="modalTitulo" class="text-lg lg:text-xl font-bold text-gray-800">Novo Evento</h3>
                        </div>
                        <button onclick="closeEventoModal()" class="text-gray-400 hover:text-gray-600 transition-colors p-2 hover:bg-gray-100 rounded-lg touch-manipulation">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                </div>

                <form id="eventoForm" class="p-4 lg:p-6">
                    <input type="hidden" id="eventoId" name="evento_id">

                    <div class="space-y-4 lg:space-y-5">
                        <!-- Título -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Título do Evento</label>
                            <input type="text" id="eventoTitulo" name="titulo" required
                                   class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-all duration-200 text-base touch-manipulation"
                                   placeholder="Ex: Jantar romântico">
                        </div>

                        <!-- Descrição -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Descrição (opcional)</label>
                            <textarea id="eventoDescricao" name="descricao" rows="3"
                                      class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-all duration-200 resize-none text-base touch-manipulation"
                                      placeholder="Detalhes do evento..."></textarea>
                        </div>

                        <!-- Data e Hora - Mobile Responsive -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Data</label>
                                <input type="date" id="eventoData" name="data" required
                                       class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-all duration-200 text-base touch-manipulation">
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Hora</label>
                                <input type="time" id="eventoHora" name="hora" required
                                       class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-all duration-200 text-base touch-manipulation">
                            </div>
                        </div>

                        <!-- Tipo e Categoria - Mobile Responsive -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div id="tipoContainer">
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Tipo</label>
                                <select id="eventoTipo" name="tipo" required
                                        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-all duration-200 text-base touch-manipulation">
                                    <option value="pessoal">Pessoal</option>
                                    <option value="compartilhado">Compartilhado</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Categoria</label>
                                <select id="eventoCategoria" name="categoria" required
                                        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-all duration-200 text-base touch-manipulation">
                                    <option value="aniversario">Aniversário</option>
                                    <option value="encontro">Encontro</option>
                                    <option value="viagem">Viagem</option>
                                    <option value="comemoração">Comemoração</option>
                                    <option value="compromisso">Compromisso</option>
                                    <option value="outro">Outro</option>
                                </select>
                            </div>
                        </div>

                        <!-- Notificações -->
                        <div>
                            <div class="flex items-center mb-3">
                                <input type="checkbox" id="eventoNotificar" name="notificar_email" checked
                                       class="w-5 h-5 text-emerald-600 bg-gray-100 border-gray-300 rounded focus:ring-emerald-500 focus:ring-2 touch-manipulation">
                                <label for="eventoNotificar" class="ml-3 text-sm font-semibold text-gray-700">Receber notificação por email</label>
                            </div>
                            <div id="notificacaoTempo">
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Notificar</label>
                                <select id="eventoNotificarMinutos" name="notificar_minutos_antes"
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-all duration-200 text-base touch-manipulation">
                                    <option value="5">5 minutos antes</option>
                                    <option value="10">10 minutos antes</option>
                                    <option value="15">15 minutos antes</option>
                                    <option value="30">30 minutos antes</option>
                                    <option value="60" selected>1 hora antes</option>
                                    <option value="120">2 horas antes</option>
                                    <option value="1440">1 dia antes</option>
                                    <option value="2880">2 dias antes</option>
                                    <option value="10080">1 semana antes</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Botões - Mobile Optimized -->
                    <div class="flex flex-col sm:flex-row gap-3 mt-6 pt-4 border-t border-gray-100">
                        <button type="button" onclick="closeEventoModal()"
                                class="flex-1 px-4 py-3 border border-gray-300 text-gray-700 rounded-xl hover:bg-gray-50 transition-all duration-200 font-medium touch-manipulation active:scale-95">
                            Cancelar
                        </button>
                        <button type="submit" id="salvarEventoBtn"
                                class="flex-1 px-4 py-3 bg-gradient-to-r from-emerald-500 to-emerald-600 text-white rounded-xl hover:from-emerald-600 hover:to-emerald-700 transition-all duration-200 transform hover:scale-105 active:scale-95 shadow-lg font-medium touch-manipulation">
                            <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                            Salvar Evento
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Modal para Visualizar Detalhes do Evento -->
        <div id="eventoDetalhesModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50 transition-opacity duration-300">
            <div class="bg-white rounded-2xl p-6 w-full max-w-md mx-4 shadow-2xl border border-emerald-200">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-xl font-bold text-gray-800">Detalhes do Evento</h3>
                    <button onclick="closeEventoDetalhesModal()" class="text-gray-400 hover:text-gray-600 transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <div id="eventoDetalhesConteudo">
                    <!-- Será preenchido via JavaScript -->
                </div>

                <div class="flex space-x-3 mt-6">
                    <button onclick="closeEventoDetalhesModal()"
                            class="flex-1 px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors duration-200">
                        Fechar
                    </button>
                    <button onclick="editarEventoDetalhes()" id="editarEventoBtn"
                            class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors duration-200">
                        Editar
                    </button>
                    <button onclick="removerEventoDetalhes()" id="removerEventoBtn"
                            class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors duration-200">
                        Remover
                    </button>
                </div>
            </div>
        </div>

        <!-- Modal para Visualizar Todos os Eventos do Dia -->
        <div id="eventosDiaModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50 transition-opacity duration-300 p-4">
            <div class="bg-white rounded-2xl w-full max-w-lg mx-auto shadow-2xl border border-emerald-200 max-h-[80vh] overflow-y-auto">
                <div class="sticky top-0 bg-white rounded-t-2xl border-b border-gray-100 p-4 lg:p-6">
                    <div class="flex justify-between items-center">
                        <div class="flex items-center">
                            <div class="p-2 bg-emerald-100 rounded-lg mr-3">
                                <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                            </div>
                            <h3 id="eventosDiaModalTitulo" class="text-lg lg:text-xl font-bold text-gray-800">Eventos do Dia</h3>
                        </div>
                        <button onclick="closeEventosDiaModal()" class="text-gray-400 hover:text-gray-600 transition-colors p-2 hover:bg-gray-100 rounded-lg touch-manipulation">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                </div>

                <div id="eventosDiaConteudo" class="p-4 lg:p-6">
                    <!-- Será preenchido via JavaScript -->
                </div>

                <div class="sticky bottom-0 bg-white rounded-b-2xl border-t border-gray-100 p-4 lg:p-6">
                    <button onclick="adicionarEventoNoDia()"
                            class="w-full px-4 py-3 bg-gradient-to-r from-emerald-500 to-emerald-600 text-white rounded-xl hover:from-emerald-600 hover:to-emerald-700 transition-all duration-200 transform hover:scale-105 active:scale-95 shadow-lg font-medium touch-manipulation">
                        <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        Adicionar Evento Neste Dia
                    </button>
                </div>
            </div>
        </div>
    `;

    // Configurar tipo fixo se especificado
    if (typeof tipoEventoFixo !== 'undefined') {
        const tipoSelect = document.getElementById('eventoTipo');
        const tipoContainer = document.getElementById('tipoContainer');

        tipoSelect.value = tipoEventoFixo;
        tipoSelect.disabled = true;
        tipoContainer.style.display = 'none';
    }
}

function atualizarMesAtual() {
    const meses = [
        'Janeiro', 'Fevereiro', 'Março', 'Abril', 'Maio', 'Junho',
        'Julho', 'Agosto', 'Setembro', 'Outubro', 'Novembro', 'Dezembro'
    ];

    const elementoTitulo = document.getElementById('mesAtual');

    if (visualizacaoAtual === 'semana') {
        // Para visualização semanal, mostrar o período da semana
        const inicioSemana = new Date(dataAtual);
        inicioSemana.setDate(dataAtual.getDate() - dataAtual.getDay());

        const fimSemana = new Date(inicioSemana);
        fimSemana.setDate(inicioSemana.getDate() + 6);

        if (inicioSemana.getMonth() === fimSemana.getMonth()) {
            // Mesma semana, mesmo mês
            elementoTitulo.textContent =
                `${inicioSemana.getDate()} - ${fimSemana.getDate()} ${meses[inicioSemana.getMonth()]} ${inicioSemana.getFullYear()}`;
        } else {
            // Semana que cruza meses
            elementoTitulo.textContent =
                `${inicioSemana.getDate()} ${meses[inicioSemana.getMonth()]} - ${fimSemana.getDate()} ${meses[fimSemana.getMonth()]} ${fimSemana.getFullYear()}`;
        }
    } else {
        // Para visualização mensal e lista
        elementoTitulo.textContent =
            `${meses[dataAtual.getMonth()]} ${dataAtual.getFullYear()}`;
    }
}

function renderizarListaEventos() {
    const container = document.getElementById('eventos-lista-container');

    if (eventos.length === 0) {
        container.innerHTML = `
            <div class="text-center py-8">
                <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                </svg>
                <p class="text-gray-500 text-lg">Nenhum evento encontrado</p>
            </div>
        `;
        return;
    }

    // Agrupar eventos por data
    const eventosPorData = {};
    eventos.forEach(evento => {
        const data = new Date(evento.data_evento).toISOString().split('T')[0];
        if (!eventosPorData[data]) {
            eventosPorData[data] = [];
        }
        eventosPorData[data].push(evento);
    });

    let html = '';
    Object.keys(eventosPorData).sort().forEach(data => {
        const dataFormatada = new Date(data + 'T00:00:00').toLocaleDateString('pt-BR', {
            weekday: 'long',
            day: 'numeric',
            month: 'long',
            year: 'numeric'
        });

        html += `
            <div class="mb-6">
                <h4 class="text-lg font-semibold text-gray-800 mb-3 capitalize">${dataFormatada}</h4>
                <div class="space-y-2">
        `;

        eventosPorData[data].forEach(evento => {
            const hora = new Date(evento.data_evento).toLocaleTimeString('pt-BR', {hour: '2-digit', minute: '2-digit'});
            const categoriaColors = {
                'aniversario': 'pink',
                'encontro': 'purple',
                'viagem': 'blue',
                'comemoração': 'yellow',
                'compromisso': 'red',
                'outro': 'gray'
            };
            const cor = categoriaColors[evento.categoria] || 'gray';

            html += `
                <div class="bg-white border border-emerald-200 rounded-lg p-4 hover:shadow-md transition-shadow cursor-pointer"
                     onclick="mostrarDetalhesEvento(${JSON.stringify(evento).replace(/"/g, '&quot;')})">
                    <div class="flex justify-between items-start">
                        <div>
                            <h5 class="font-semibold text-gray-800">${evento.titulo}</h5>
                            <p class="text-sm text-gray-600 mt-1">${evento.descricao || 'Sem descrição'}</p>
                            <span class="inline-block px-2 py-1 text-xs bg-${cor}-100 text-${cor}-700 rounded-full mt-2">
                                ${evento.categoria}
                            </span>
                        </div>
                        <div class="text-right">
                            <span class="text-sm font-medium text-gray-700">${hora}</span>
                            <div class="text-xs text-gray-500 mt-1">${evento.tipo}</div>
                        </div>
                    </div>
                </div>
            `;
        });

        html += `
                </div>
            </div>
        `;
    });

    container.innerHTML = html;
}

function renderizarVisualizacaoSemana() {
    const container = document.getElementById('semana-mobile-container');

    // Encontrar o início da semana (domingo)
    const hoje = new Date();
    const inicioSemana = new Date(dataAtual);
    inicioSemana.setDate(dataAtual.getDate() - dataAtual.getDay());

    // Atualizar título para mostrar a semana
    const fimSemana = new Date(inicioSemana);
    fimSemana.setDate(inicioSemana.getDate() + 6);

    const tituloSemana = document.querySelector('#semana-mobile h3');
    if (tituloSemana) {
        tituloSemana.textContent = `Semana de ${inicioSemana.getDate()}/${inicioSemana.getMonth() + 1} a ${fimSemana.getDate()}/${fimSemana.getMonth() + 1}/${fimSemana.getFullYear()}`;
    }

    let html = '<div class="space-y-3">';

    // Criar 7 dias da semana
    for (let i = 0; i < 7; i++) {
        const data = new Date(inicioSemana);
        data.setDate(inicioSemana.getDate() + i);

        const ehHoje = data.toDateString() === hoje.toDateString();
        const diasSemana = ['Dom', 'Seg', 'Ter', 'Qua', 'Qui', 'Sex', 'Sáb'];

        // Filtrar eventos do dia
        const eventosNoDia = eventos.filter(evento => {
            const dataEvento = new Date(evento.data_evento);
            return dataEvento.toDateString() === data.toDateString();
        });

        html += `
            <div class="border border-gray-200 rounded-xl p-3 ${ehHoje ? 'bg-emerald-50 border-emerald-300' : 'bg-white'} cursor-pointer hover:shadow-md transition-all duration-200"
                 onclick="openEventoModal(new Date('${data.toISOString()}'))">
                <div class="flex justify-between items-center mb-2">
                    <div class="flex items-center space-x-2">
                        <span class="text-sm font-medium text-gray-600">${diasSemana[i]}</span>
                        <span class="text-lg font-bold ${ehHoje ? 'text-emerald-700' : 'text-gray-800'}">${data.getDate()}</span>
                    </div>
                    <span class="text-xs text-gray-500">${eventosNoDia.length} evento${eventosNoDia.length !== 1 ? 's' : ''}</span>
                </div>

                <div class="space-y-1 max-h-20 overflow-y-auto">
                    ${eventosNoDia.slice(0, 3).map(evento => {
                        const hora = new Date(evento.data_evento).toLocaleTimeString('pt-BR', {hour: '2-digit', minute: '2-digit'});
                        return `
                            <div class="text-xs bg-emerald-100 text-emerald-800 px-2 py-1 rounded truncate cursor-pointer hover:bg-emerald-200 transition-colors"
                                 onclick="event.stopPropagation(); mostrarDetalhesEvento(${JSON.stringify(evento).replace(/"/g, '&quot;')})">
                                ${hora} - ${evento.titulo}
                            </div>
                        `;
                    }).join('')}
                    ${eventosNoDia.length > 3 ?
                        `<div class="text-xs text-gray-500 cursor-pointer hover:text-gray-700"
                              onclick="event.stopPropagation(); mostrarTodosEventosDia('${data.toISOString().split('T')[0]}')">
                              +${eventosNoDia.length - 3} mais eventos
                         </div>` : ''
                    }
                </div>
            </div>
        `;
    }

    html += '</div>';
    container.innerHTML = html;
}

async function carregarEventos() {
    try {
        const categoria = document.getElementById('filtroCategoria').value;

        let url = apiEndpoint; // Definido em cada view específica
        const params = new URLSearchParams();

        // Filtro de mês atual
        const mesAno = `${dataAtual.getFullYear()}-${(dataAtual.getMonth() + 1).toString().padStart(2, '0')}`;
        params.append('mes', mesAno);

        if (categoria && categoria !== 'todas') {
            params.append('categoria', categoria);
        }

        if (params.toString()) {
            url += '?' + params.toString();
        }

        const response = await fetch(url, {
            method: 'GET',
            credentials: 'include',
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        });

        if (response.ok) {
            const data = await response.json();
            eventos = data.eventos || [];
            renderizarCalendario();
        } else {
            const error = await response.json();
            console.error('Erro ao carregar eventos:', error);
            mostrarNotificacao('Erro ao carregar eventos: ' + response.status, 'error');
        }
    } catch (error) {
        console.error('Erro:', error);
        mostrarNotificacao('Erro de conexão', 'error');
    }
}

function anteriorMes() {
    if (visualizacaoAtual === 'semana') {
        // Para semana, voltar 7 dias
        dataAtual.setDate(dataAtual.getDate() - 7);
    } else {
        // Para mês e lista, voltar 1 mês
        dataAtual.setMonth(dataAtual.getMonth() - 1);
    }
    atualizarMesAtual();
    carregarEventos();
}

function proximoMes() {
    if (visualizacaoAtual === 'semana') {
        // Para semana, avançar 7 dias
        dataAtual.setDate(dataAtual.getDate() + 7);
    } else {
        // Para mês e lista, avançar 1 mês
        dataAtual.setMonth(dataAtual.getMonth() + 1);
    }
    atualizarMesAtual();
    carregarEventos();
}

function voltarHoje() {
    const hoje = new Date();
    dataAtual = new Date(hoje);

    // Para visualização semanal, ajustar para o início da semana atual
    if (visualizacaoAtual === 'semana') {
        const inicioSemana = new Date(hoje);
        inicioSemana.setDate(hoje.getDate() - hoje.getDay());
        dataAtual = inicioSemana;
    }

    atualizarMesAtual();
    carregarEventos();
}

function alterarVisualizacao() {
    visualizacaoAtual = document.getElementById('tipoVisualizacao').value;
    renderizarCalendario();
}

function filtrarEventos() {
    carregarEventos();
}

function abrirModalNoDia(data) {
    document.getElementById('eventoData').value = data;
    openEventoModal();
}

function closeEventoModal() {
    const modal = document.getElementById('eventoModal');
    const content = document.getElementById('eventoModalContent');

    content.classList.remove('scale-100', 'opacity-100');
    content.classList.add('scale-95', 'opacity-0');

    setTimeout(() => {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        eventoSelecionado = null;
    }, 300);
}

function mostrarDetalhesEvento(evento) {
    eventoSelecionado = evento;
    const modal = document.getElementById('eventoDetalhesModal');
    const conteudo = document.getElementById('eventoDetalhesConteudo');

    const dataEvento = new Date(evento.data_evento);
    const dataFormatada = dataEvento.toLocaleDateString('pt-BR');
    const horaFormatada = dataEvento.toLocaleTimeString('pt-BR', {hour: '2-digit', minute: '2-digit'});

    conteudo.innerHTML = `
        <div class="space-y-4">
            <div>
                <h4 class="font-semibold text-gray-700 mb-1">Título</h4>
                <p class="text-gray-900">${evento.titulo}</p>
            </div>

            ${evento.descricao ? `
            <div>
                <h4 class="font-semibold text-gray-700 mb-1">Descrição</h4>
                <p class="text-gray-900">${evento.descricao}</p>
            </div>
            ` : ''}

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <h4 class="font-semibold text-gray-700 mb-1">Data</h4>
                    <p class="text-gray-900">${dataFormatada}</p>
                </div>
                <div>
                    <h4 class="font-semibold text-gray-700 mb-1">Hora</h4>
                    <p class="text-gray-900">${horaFormatada}</p>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <h4 class="font-semibold text-gray-700 mb-1">Tipo</h4>
                    <span class="inline-block px-2 py-1 text-xs bg-emerald-100 text-emerald-700 rounded-full">
                        ${evento.tipo}
                    </span>
                </div>
                <div>
                    <h4 class="font-semibold text-gray-700 mb-1">Categoria</h4>
                    <span class="inline-block px-2 py-1 text-xs bg-blue-100 text-blue-700 rounded-full">
                        ${evento.categoria}
                    </span>
                </div>
            </div>

            ${evento.notificar_email ? `
            <div>
                <h4 class="font-semibold text-gray-700 mb-1">Notificação</h4>
                <p class="text-gray-900">${evento.notificar_minutos_antes} minutos antes por email</p>
            </div>
            ` : ''}
        </div>
    `;

    modal.classList.remove('hidden');
    modal.classList.add('flex');
}

function closeEventoDetalhesModal() {
    const modal = document.getElementById('eventoDetalhesModal');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
    eventoSelecionado = null;
}

// Variável global para armazenar a data selecionada no modal de eventos do dia
let dataSelecionadaEventos = null;

function mostrarTodosEventosDia(dataISO) {
    dataSelecionadaEventos = dataISO;
    const modal = document.getElementById('eventosDiaModal');
    const titulo = document.getElementById('eventosDiaModalTitulo');
    const conteudo = document.getElementById('eventosDiaConteudo');

    // Formatar data para exibição
    const data = new Date(dataISO + 'T00:00:00');
    const dataFormatada = data.toLocaleDateString('pt-BR', {
        weekday: 'long',
        day: 'numeric',
        month: 'long',
        year: 'numeric'
    });

    titulo.textContent = `Eventos - ${dataFormatada.charAt(0).toUpperCase() + dataFormatada.slice(1)}`;

    // Filtrar eventos do dia
    const eventosNoDia = eventos.filter(evento => {
        const dataEvento = new Date(evento.data_evento);
        return dataEvento.toDateString() === data.toDateString();
    });

    if (eventosNoDia.length === 0) {
        conteudo.innerHTML = `
            <div class="text-center py-8">
                <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                </svg>
                <p class="text-gray-500 text-lg mb-2">Nenhum evento neste dia</p>
                <p class="text-gray-400 text-sm">Clique em "Adicionar Evento" para criar um novo</p>
            </div>
        `;
    } else {
        // Ordenar eventos por horário
        eventosNoDia.sort((a, b) => {
            const horaA = new Date(a.data_evento);
            const horaB = new Date(b.data_evento);
            return horaA - horaB;
        });

        let html = '<div class="space-y-3">';

        eventosNoDia.forEach((evento, index) => {
            const hora = new Date(evento.data_evento).toLocaleTimeString('pt-BR', {hour: '2-digit', minute: '2-digit'});
            const categoriaColors = {
                'aniversario': 'pink',
                'encontro': 'purple',
                'viagem': 'blue',
                'comemoração': 'yellow',
                'compromisso': 'red',
                'outro': 'gray'
            };
            const cor = categoriaColors[evento.categoria] || 'gray';

            html += `
                <div class="bg-white border border-emerald-200 rounded-xl p-4 hover:shadow-md transition-all duration-200 cursor-pointer hover:border-emerald-300 evento-dia-item"
                     data-evento='${JSON.stringify(evento)}'
                     style="animation-delay: ${index * 0.1}s">
                    <div class="flex justify-between items-start">
                        <div class="flex-1">
                            <div class="flex items-center gap-2 mb-2">
                                <span class="text-sm font-medium text-gray-700 bg-gray-100 px-2 py-1 rounded-lg">${hora}</span>
                                <span class="inline-block px-2 py-1 text-xs bg-${cor}-100 text-${cor}-700 rounded-full">
                                    ${evento.categoria}
                                </span>
                            </div>
                            <h5 class="font-semibold text-gray-800 mb-1">${evento.titulo}</h5>
                            ${evento.descricao ? `<p class="text-sm text-gray-600">${evento.descricao}</p>` : ''}
                        </div>
                        <div class="text-right ml-3">
                            <div class="text-xs text-gray-500 bg-emerald-50 px-2 py-1 rounded-full">${evento.tipo}</div>
                        </div>
                    </div>
                </div>
            `;
        });

        html += '</div>';
        conteudo.innerHTML = html;

        // Animar entrada dos eventos
        setTimeout(() => {
            document.querySelectorAll('.evento-dia-item').forEach((item, index) => {
                setTimeout(() => {
                    item.style.opacity = '1';
                    item.style.transform = 'translateY(0)';
                }, index * 50);
            });
        }, 100);
    }

    // Mostrar modal
    modal.classList.remove('hidden');
    modal.classList.add('flex');

    // Adicionar vibração no mobile
    if (navigator.vibrate) {
        navigator.vibrate(15);
    }
}

function closeEventosDiaModal() {
    const modal = document.getElementById('eventosDiaModal');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
    dataSelecionadaEventos = null;
}

function adicionarEventoNoDia() {
    if (dataSelecionadaEventos) {
        closeEventosDiaModal();
        // Abrir modal de criação de evento com a data pré-selecionada
        const data = new Date(dataSelecionadaEventos + 'T00:00:00');
        openEventoModal(data);
    }
}

function editarEventoDetalhes() {
    if (!eventoSelecionado) return;

    closeEventoDetalhesModal();

    // Preencher modal de edição
    document.getElementById('eventoId').value = eventoSelecionado.id;
    document.getElementById('eventoTitulo').value = eventoSelecionado.titulo;
    document.getElementById('eventoDescricao').value = eventoSelecionado.descricao || '';

    const dataEvento = new Date(eventoSelecionado.data_evento);
    // Usar formato local sem conversão UTC
    const ano = dataEvento.getFullYear();
    const mes = String(dataEvento.getMonth() + 1).padStart(2, '0');
    const dia = String(dataEvento.getDate()).padStart(2, '0');
    document.getElementById('eventoData').value = `${ano}-${mes}-${dia}`;
    document.getElementById('eventoHora').value = dataEvento.toTimeString().slice(0, 5);

    document.getElementById('eventoTipo').value = eventoSelecionado.tipo;
    document.getElementById('eventoCategoria').value = eventoSelecionado.categoria;
    document.getElementById('eventoNotificar').checked = eventoSelecionado.notificar_email;
    document.getElementById('eventoNotificarMinutos').value = eventoSelecionado.notificar_minutos_antes;

    document.getElementById('modalTitulo').textContent = 'Editar Evento';
    document.getElementById('salvarEventoBtn').innerHTML = `
        <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
        </svg>
        Atualizar Evento
    `;

    openEventoModal();
}

async function removerEventoDetalhes() {
    if (!eventoSelecionado) return;

    if (!confirm('Tem certeza que deseja remover este evento?')) {
        return;
    }

    try {
        const response = await fetch(`/api/eventos/${eventoSelecionado.id}`, {
            method: 'DELETE',
            credentials: 'include',
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        });

        if (response.ok) {
            const result = await response.json();
            mostrarNotificacao(result.message || 'Evento removido com sucesso!', 'success');
            closeEventoDetalhesModal();
            carregarEventos();
        } else {
            const error = await response.json();
            mostrarNotificacao(error.message || 'Erro ao remover evento', 'error');
        }
    } catch (error) {
        console.error('Erro:', error);
        mostrarNotificacao('Erro de conexão. Tente novamente.', 'error');
    }
}

function configurarEventListeners() {
    // Form de evento
    document.getElementById('eventoForm').addEventListener('submit', async function(e) {
        e.preventDefault();

        const formData = new FormData(this);
        const data = Object.fromEntries(formData);

        // Combinar data e hora no formato local (sem conversão UTC) - v3
        const dataEvento = new Date(data.data + 'T' + data.hora);

        // Criar string no formato local com timezone explícito para Brasil
        const ano = dataEvento.getFullYear();
        const mes = String(dataEvento.getMonth() + 1).padStart(2, '0');
        const dia = String(dataEvento.getDate()).padStart(2, '0');
        const hora = String(dataEvento.getHours()).padStart(2, '0');
        const minuto = String(dataEvento.getMinutes()).padStart(2, '0');
        const segundo = String(dataEvento.getSeconds()).padStart(2, '0');

        // Enviar com indicação de timezone Brasil
        data.data_evento = `${ano}-${mes}-${dia} ${hora}:${minuto}:${segundo}`;
        data.timezone = 'America/Sao_Paulo';
        delete data.data;
        delete data.hora;

        // Converter checkbox
        data.notificar_email = document.getElementById('eventoNotificar').checked;

        // Usar tipo fixo se definido
        if (typeof tipoEventoFixo !== 'undefined') {
            data.tipo = tipoEventoFixo;
        }

        const eventoId = document.getElementById('eventoId').value;
        const isEdicao = !!eventoId;

        const submitButton = document.getElementById('salvarEventoBtn');
        const originalText = submitButton.innerHTML;

        submitButton.innerHTML = `
            <div class="flex items-center gap-2">
                <div class="animate-spin w-4 h-4 border-2 border-white border-t-transparent rounded-full"></div>
                <span>${isEdicao ? 'Atualizando...' : 'Criando...'}</span>
            </div>
        `;
        submitButton.disabled = true;

        try {
            const url = isEdicao ? `/api/eventos/${eventoId}` : '/api/eventos';
            const method = isEdicao ? 'PUT' : 'POST';

            const response = await fetch(url, {
                method: method,
                credentials: 'include',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify(data)
            });

            if (response.ok) {
                const result = await response.json();
                mostrarNotificacao(result.message || `Evento ${isEdicao ? 'atualizado' : 'criado'} com sucesso!`, 'success');
                resetarEstadoFormulario(); // Resetar estado antes de fechar
                closeEventoModal();
                carregarEventos();
            } else {
                const error = await response.json();
                mostrarNotificacao(error.message || 'Erro ao salvar evento', 'error');
            }
        } catch (error) {
            console.error('Erro:', error);
            mostrarNotificacao('Erro de conexão. Tente novamente.', 'error');
        } finally {
            submitButton.innerHTML = originalText;
            submitButton.disabled = false;
        }
    });

    // Controlar visibilidade do campo de notificação
    document.getElementById('eventoNotificar').addEventListener('change', function() {
        const notificacaoTempo = document.getElementById('notificacaoTempo');
        notificacaoTempo.style.display = this.checked ? 'block' : 'none';
    });

    // Toggle de filtros mobile
    const toggleFiltros = document.getElementById('toggleFiltros');
    const filtrosContainer = document.getElementById('filtrosContainer');

    if (toggleFiltros) {
        toggleFiltros.addEventListener('click', function() {
            filtrosContainer.classList.toggle('hidden');
            filtrosContainer.classList.toggle('flex');

            // Animar ícone
            const icon = this.querySelector('svg');
            icon.style.transform = filtrosContainer.classList.contains('hidden') ? 'rotate(0deg)' : 'rotate(180deg)';
        });
    }

    // Swipe gestures para mobile
    let startX = 0;
    let startY = 0;

    document.addEventListener('touchstart', function(e) {
        startX = e.touches[0].clientX;
        startY = e.touches[0].clientY;
    });

    document.addEventListener('touchend', function(e) {
        if (!startX || !startY) return;

        const endX = e.changedTouches[0].clientX;
        const endY = e.changedTouches[0].clientY;

        const diffX = startX - endX;
        const diffY = startY - endY;

        // Se o movimento foi mais horizontal que vertical
        if (Math.abs(diffX) > Math.abs(diffY)) {
            if (Math.abs(diffX) > 50) { // Mínimo de 50px de swipe
                if (diffX > 0) {
                    // Swipe para esquerda - próximo mês
                    proximoMes();
                } else {
                    // Swipe para direita - mês anterior
                    anteriorMes();
                }
            }
        }

        startX = 0;
        startY = 0;
    });

    // Detectar mudanças de orientação
    window.addEventListener('orientationchange', function() {
        setTimeout(() => {
            renderizarCalendario();
        }, 100);
    });

    // Fechar modal ao clicar fora (melhorado)
    document.getElementById('eventoModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeEventoModal();
        }
    });

    // Escape key para fechar modal
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            const modalEvento = document.getElementById('eventoModal');
            const modalEventosDia = document.getElementById('eventosDiaModal');
            const modalDetalhes = document.getElementById('eventoDetalhesModal');

            if (!modalEvento.classList.contains('hidden')) {
                closeEventoModal();
            } else if (!modalEventosDia.classList.contains('hidden')) {
                closeEventosDiaModal();
            } else if (!modalDetalhes.classList.contains('hidden')) {
                closeEventoDetalhesModal();
            }
        }
    });

    // Fechar modais ao clicar fora
    document.getElementById('eventosDiaModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeEventosDiaModal();
        }
    });

    document.getElementById('eventoDetalhesModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeEventoDetalhesModal();
        }
    });

    // Adicionar estilos CSS dinâmicos para animações
    const style = document.createElement('style');
    style.textContent = `
        .evento-dia-item {
            opacity: 0;
            transform: translateY(10px);
            transition: all 0.3s ease;
        }

        .evento-dia-item:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
        }

        .calendario-event-card:hover {
            transform: scale(1.02);
        }
    `;
    document.head.appendChild(style);

    // Configurar event listeners para elementos criados dinamicamente
    configurarEventListenersDinamicos();
}

function configurarEventListenersDinamicos() {
    // Event delegation para elementos criados dinamicamente no calendário
    document.addEventListener('click', function(e) {
        // Handler para eventos no modal de eventos do dia
        if (e.target.closest('.evento-dia-item')) {
            const eventoItem = e.target.closest('.evento-dia-item');
            const eventoData = eventoItem.getAttribute('data-evento');
            if (eventoData) {
                try {
                    const evento = JSON.parse(eventoData);
                    mostrarDetalhesEvento(evento);
                    closeEventosDiaModal();
                } catch (error) {
                    console.error('Erro ao parselar dados do evento:', error);
                }
            }
        }
    });
}function mostrarNotificacao(mensagem, tipo) {
    // Criar notificação temporária melhorada
    const notificacao = document.createElement('div');
    notificacao.className = `fixed top-4 right-4 z-50 px-6 py-4 rounded-xl shadow-2xl text-white transform transition-all duration-300 translate-x-full ${
        tipo === 'success' ? 'bg-gradient-to-r from-green-500 to-green-600' : 'bg-gradient-to-r from-red-500 to-red-600'
    }`;

    notificacao.innerHTML = `
        <div class="flex items-center space-x-3">
            ${tipo === 'success' ?
                '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>' :
                '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>'
            }
            <span class="font-medium">${mensagem}</span>
        </div>
    `;

    document.body.appendChild(notificacao);

    // Animar entrada
    setTimeout(() => {
        notificacao.classList.remove('translate-x-full');
    }, 100);

    // Animar saída
    setTimeout(() => {
        notificacao.classList.add('translate-x-full');
        setTimeout(() => notificacao.remove(), 300);
    }, 3000);
}

// Melhorar função renderizarCalendario para mobile
function renderizarCalendario() {
    // Verificar se é visualização em lista
    if (visualizacaoAtual === 'lista') {
        document.querySelector('.grid.grid-cols-7').style.display = 'none';
        document.getElementById('lista-eventos').style.display = 'block';
        document.getElementById('semana-mobile').style.display = 'none';
        renderizarListaEventos();
        return;
    }

    // Verificar se é visualização por semana
    if (visualizacaoAtual === 'semana') {
        document.querySelector('.grid.grid-cols-7').style.display = 'none';
        document.getElementById('lista-eventos').style.display = 'none';
        document.getElementById('semana-mobile').style.display = 'block';
        renderizarVisualizacaoSemana();
        return;
    }

    // Visualização mensal padrão
    document.querySelector('.grid.grid-cols-7').style.display = 'grid';
    document.getElementById('lista-eventos').style.display = 'none';
    document.getElementById('semana-mobile').style.display = 'none';

    const grid = document.getElementById('calendario-grid');
    const hoje = new Date();
    const primeiroDia = new Date(dataAtual.getFullYear(), dataAtual.getMonth(), 1);
    const ultimoDia = new Date(dataAtual.getFullYear(), dataAtual.getMonth() + 1, 0);
    const diaSemanaInicio = primeiroDia.getDay();

    grid.innerHTML = '';

    // Detectar se é mobile
    const isMobile = window.innerWidth < 768;

    // Adicionar dias do mês anterior
    for (let i = diaSemanaInicio - 1; i >= 0; i--) {
        const dataAnterior = new Date(primeiroDia);
        dataAnterior.setDate(dataAnterior.getDate() - (i + 1));

        const dia = criarCelulaDia(dataAnterior, true, isMobile);
        grid.appendChild(dia);
    }

    // Adicionar dias do mês atual
    for (let dia = 1; dia <= ultimoDia.getDate(); dia++) {
        const data = new Date(dataAtual.getFullYear(), dataAtual.getMonth(), dia);
        const celula = criarCelulaDia(data, false, isMobile);
        grid.appendChild(celula);
    }

    // Completar a grade com dias do próximo mês
    const totalCelulas = grid.children.length;
    const celulasRestantes = 42 - totalCelulas; // 6 semanas * 7 dias

    for (let i = 1; i <= celulasRestantes; i++) {
        const dataProxima = new Date(ultimoDia);
        dataProxima.setDate(dataProxima.getDate() + i);

        const dia = criarCelulaDia(dataProxima, true, isMobile);
        grid.appendChild(dia);
    }
}

function criarCelulaDia(data, outroMes, isMobile) {
    const hoje = new Date();
    const ehHoje = data.toDateString() === hoje.toDateString();
    const eventosNoDia = eventos.filter(evento => {
        const dataEvento = new Date(evento.data_evento); // Mudado de data_hora para data_evento
        return dataEvento.toDateString() === data.toDateString();
    });

    const celula = document.createElement('div');
    celula.className = `
        calendar-cell
        ${isMobile ? 'min-h-[50px] p-1' : 'min-h-[100px] p-2'}
        border-b border-r border-gray-100
        cursor-pointer
        transition-all duration-200
        hover:bg-emerald-50
        active:bg-emerald-100
        touch-feedback
        ${outroMes ? 'text-gray-300 bg-gray-50' : 'text-gray-800'}
        ${ehHoje ? 'bg-emerald-100 border-emerald-300 today' : ''}
    `;

    celula.innerHTML = `
        <div class="flex flex-col h-full">
            <div class="${isMobile ? 'text-xs' : 'text-sm'} font-medium mb-1 ${ehHoje ? 'text-emerald-700 font-bold' : ''}">
                ${data.getDate()}
            </div>
            <div class="flex-1 overflow-hidden">
                ${eventosNoDia.slice(0, isMobile ? 1 : 3).map((evento, index) => `
                    <div class="evento-item calendario-event-card ${isMobile ? 'text-xs px-1 py-0.5 mb-0.5' : 'text-xs px-2 py-1 mb-1'} bg-emerald-200 text-emerald-800 rounded truncate cursor-pointer hover:bg-emerald-300 transition-colors relative z-10"
                         style="animation-delay: ${index * 0.1}s; pointer-events: auto;"
                         data-evento='${JSON.stringify(evento)}'>
                        ${evento.titulo}
                    </div>
                `).join('')}
                ${eventosNoDia.length > (isMobile ? 1 : 3) ?
                    `<div class="mais-eventos-btn ${isMobile ? 'text-xs' : 'text-xs'} text-gray-500 font-medium cursor-pointer hover:text-gray-700 transition-colors bg-gray-100 hover:bg-gray-200 rounded px-1 py-0.5 relative z-10"
                          style="pointer-events: auto;"
                          data-date="${data.toISOString().split('T')[0]}"
                          title="Ver todos os eventos deste dia">
                          +${eventosNoDia.length - (isMobile ? 1 : 3)} mais
                     </div>` : ''
                }
            </div>
        </div>
    `;

    // Adicionar eventos de toque melhorados
    celula.addEventListener('click', (e) => {
        // Se clicou em um evento específico, abrir detalhes para edição
        if (e.target.classList.contains('evento-item') || e.target.classList.contains('calendario-event-card')) {
            e.stopPropagation();
            const eventoData = e.target.getAttribute('data-evento');
            if (eventoData) {
                try {
                    const evento = JSON.parse(eventoData);
                    mostrarDetalhesEvento(evento);
                } catch (error) {
                    console.error('Erro ao parselar dados do evento:', error);
                }
            }
            return;
        }

        // Se clicou no botão "+X mais", mostrar modal de eventos do dia
        if (e.target.classList.contains('mais-eventos-btn')) {
            e.stopPropagation();
            const dataISO = e.target.getAttribute('data-date');
            if (dataISO) {
                mostrarTodosEventosDia(dataISO);
            }
            return;
        }

        // Se clicou na área vazia da célula (não em eventos), criar novo evento
        if (!outroMes) {
            openEventoModal(data);
        }
    });

    // Adicionar vibração no mobile (se suportado)
    celula.addEventListener('touchstart', () => {
        if (navigator.vibrate && !outroMes) {
            navigator.vibrate(10); // Vibração sutil de 10ms
        }
    });

    return celula;
}

// Função para carregar eventos com loading state melhorado
// Função duplicada removida - usando apenas a função carregarEventos principal

function mostrarLoadingSkeleton() {
    const grid = document.getElementById('calendario-grid');
    if (!grid) return;

    grid.innerHTML = '';

    // Criar 35 células skeleton (5 semanas)
    for (let i = 0; i < 35; i++) {
        const skeleton = document.createElement('div');
        skeleton.className = 'min-h-[100px] p-2 border-b border-r border-gray-100';
        skeleton.innerHTML = `
            <div class="skeleton w-6 h-4 mb-2 rounded"></div>
            <div class="skeleton w-full h-3 mb-1 rounded"></div>
            <div class="skeleton w-3/4 h-3 rounded"></div>
        `;
        grid.appendChild(skeleton);
    }
}

function removerLoadingSkeleton() {
    document.querySelectorAll('.skeleton').forEach(el => el.remove());
}

// Função de debounce para melhorar performance
function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

// Otimizar renderização em resize
const debouncedRender = debounce(() => {
    renderizarCalendario();
}, 250);

window.addEventListener('resize', debouncedRender);

// Função para adicionar efeitos visuais no modal
function openEventoModal(data = null) {
    const modal = document.getElementById('eventoModal');
    const modalContent = document.getElementById('eventoModalContent');

    // Resetar estado do formulário
    resetarEstadoFormulario();

    // Limpar formulário
    document.getElementById('eventoForm').reset();
    document.getElementById('eventoId').value = '';
    document.getElementById('modalTitulo').textContent = 'Novo Evento';
    document.getElementById('salvarEventoBtn').innerHTML = `
        <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
        </svg>
        Salvar Evento
    `;

    // Adicionar listeners para detectar alterações no formulário
    const form = document.getElementById('eventoForm');
    const inputs = form.querySelectorAll('input, textarea, select');
    inputs.forEach(input => {
        input.addEventListener('input', marcarFormularioAlterado);
        input.addEventListener('change', marcarFormularioAlterado);
    });

    // Configurar data se fornecida, senão usar hoje
    if (data) {
        const dataFormatada = data.toISOString().split('T')[0];
        document.getElementById('eventoData').value = dataFormatada;
    } else if (!document.getElementById('eventoData').value) {
        const hoje = new Date().toISOString().split('T')[0];
        document.getElementById('eventoData').value = hoje;
    }

    // Definir hora atual
    const agora = new Date();
    const hora = agora.getHours().toString().padStart(2, '0');
    const minutos = agora.getMinutes().toString().padStart(2, '0');
    document.getElementById('eventoHora').value = `${hora}:${minutos}`;

    // Configurar tipo se fixo
    if (typeof tipoEventoFixo !== 'undefined') {
        document.getElementById('eventoTipo').value = tipoEventoFixo;
    }

    // Animação de abertura melhorada
    modal.classList.remove('hidden');
    modal.classList.add('flex');

    // Forçar reflow para garantir que as classes sejam aplicadas
    modal.offsetHeight;

    // Aplicar animações
    modal.classList.add('modal-backdrop');
    modalContent.classList.remove('scale-95', 'opacity-0');
    modalContent.classList.add('scale-100', 'opacity-100', 'modal-content');

    // Focus no primeiro campo após animação
    setTimeout(() => {
        document.getElementById('eventoTitulo').focus();
    }, 300);

    // Adicionar vibração no mobile
    if (navigator.vibrate) {
        navigator.vibrate(15);
    }
}

function closeEventoModal() {
    const modal = document.getElementById('eventoModal');
    const modalContent = document.getElementById('eventoModalContent');

    // Resetar estado do formulário
    resetarEstadoFormulario();

    // Animação de fechamento
    modalContent.classList.add('closing');
    modalContent.classList.remove('scale-100', 'opacity-100');
    modalContent.classList.add('scale-95', 'opacity-0');

    setTimeout(() => {
        modal.classList.add('hidden');
        modal.classList.remove('flex', 'modal-backdrop');
        modalContent.classList.remove('closing', 'modal-content');

        // Limpar formulário
        document.getElementById('eventoForm').reset();
        document.getElementById('eventoId').value = '';
        eventoSelecionado = null;
    }, 200);
}

// Função para adicionar micro-interações
function adicionarMicroInteracoes() {
    // Adicionar ripple effect aos botões
    document.querySelectorAll('.ripple').forEach(button => {
        button.addEventListener('click', function(e) {
            const ripple = document.createElement('span');
            const rect = this.getBoundingClientRect();
            const size = Math.max(rect.width, rect.height);
            const x = e.clientX - rect.left - size / 2;
            const y = e.clientY - rect.top - size / 2;

            ripple.style.width = ripple.style.height = size + 'px';
            ripple.style.left = x + 'px';
            ripple.style.top = y + 'px';
            ripple.classList.add('ripple-effect');

            this.appendChild(ripple);

            setTimeout(() => {
                ripple.remove();
            }, 600);
        });
    });

    // Adicionar feedback visual aos form fields
    document.querySelectorAll('input, textarea, select').forEach(field => {
        field.classList.add('form-field');

        field.addEventListener('focus', function() {
            this.style.transform = 'translateY(-2px)';
            this.style.boxShadow = '0 8px 25px rgba(5, 150, 105, 0.15)';
        });

        field.addEventListener('blur', function() {
            this.style.transform = '';
            this.style.boxShadow = '';
        });
    });

    // Adicionar efeito bounce aos botões
    document.querySelectorAll('.btn-bounce').forEach(btn => {
        btn.addEventListener('mousedown', function() {
            this.style.transform = 'scale(0.95)';
        });

        btn.addEventListener('mouseup', function() {
            this.style.transform = '';
        });

        btn.addEventListener('mouseleave', function() {
            this.style.transform = '';
        });
    });
}

// Função para melhorar acessibilidade
function melhorarAcessibilidade() {
    // Adicionar indicadores de foco visíveis
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Tab') {
            document.body.classList.add('keyboard-navigation');
        }
    });

    document.addEventListener('mousedown', function() {
        document.body.classList.remove('keyboard-navigation');
    });

    // Adicionar aria-labels dinâmicos
    document.querySelectorAll('.calendar-cell').forEach((cell, index) => {
        const data = new Date();
        data.setDate(data.getDate() + index);
        cell.setAttribute('aria-label', `Dia ${data.getDate()}`);
        cell.setAttribute('role', 'button');
        cell.setAttribute('tabindex', '0');

        // Adicionar navegação por teclado
        cell.addEventListener('keydown', function(e) {
            if (e.key === 'Enter' || e.key === ' ') {
                e.preventDefault();
                this.click();
            }
        });
    });
}

// Função para lazy loading de eventos (se necessário no futuro)
function implementarLazyLoading() {
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const elemento = entry.target;
                elemento.classList.add('fade-in');
                observer.unobserve(elemento);
            }
        });
    });

    // Observar elementos que precisam de lazy loading
    document.querySelectorAll('.evento-item').forEach(item => {
        observer.observe(item);
    });
}

// Performance: Usar requestAnimationFrame para animações
function animarElemento(elemento, propriedades) {
    let start = null;
    const duration = 300;

    function animate(timestamp) {
        if (!start) start = timestamp;
        const progress = Math.min((timestamp - start) / duration, 1);

        // Aplicar propriedades com easing
        Object.keys(propriedades).forEach(prop => {
            const valor = propriedades[prop];
            if (typeof valor === 'number') {
                elemento.style[prop] = `${valor * progress}px`;
            }
        });

        if (progress < 1) {
            requestAnimationFrame(animate);
        }
    }

    requestAnimationFrame(animate);
}

// Inicializar melhorias quando o DOM estiver pronto
document.addEventListener('DOMContentLoaded', function() {
    setTimeout(() => {
        adicionarMicroInteracoes();
        melhorarAcessibilidade();
        implementarLazyLoading();

        // Adicionar classes de animação aos elementos existentes
        document.querySelectorAll('.fade-in').forEach((el, index) => {
            el.style.animationDelay = `${index * 0.1}s`;
        });
    }, 500);
});

// Adicionar service worker para cache (opcional)
if ('serviceWorker' in navigator) {
    window.addEventListener('load', () => {
        navigator.serviceWorker.register('/sw.js')
            .then(registration => console.log('SW registrado'))
            .catch(registrationError => console.log('SW não registrado'));
    });
}

// Função para detectar modo escuro do sistema
function detectarModoEscuro() {
    if (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches) {
        document.body.classList.add('dark-mode-transition');
    }
}

// Função para analytics de uso (opcional)
function registrarInteracao(tipo, dados = {}) {
    // Se analytics estiver configurado
    if (typeof gtag !== 'undefined') {
        gtag('event', tipo, {
            event_category: 'calendario',
            ...dados
        });
    }
}

// Adicionar função para feedback háptico
function adicionarFeedbackHaptico(tipo = 'light') {
    if (navigator.vibrate) {
        const padroes = {
            light: [10],
            medium: [50],
            heavy: [100],
            success: [50, 50, 50],
            error: [100, 50, 100]
        };

        navigator.vibrate(padroes[tipo] || padroes.light);
    }
}

// Função para backup local de dados
function backupEventosLocal() {
    if (eventos.length > 0) {
        localStorage.setItem('eventos_backup', JSON.stringify(eventos));
        localStorage.setItem('backup_timestamp', new Date().toISOString());
    }
}

function restaurarEventosLocal() {
    const backup = localStorage.getItem('eventos_backup');
    const timestamp = localStorage.getItem('backup_timestamp');

    if (backup && timestamp) {
        const backupDate = new Date(timestamp);
        const agora = new Date();
        const diferencaHoras = (agora - backupDate) / (1000 * 60 * 60);

        // Restaurar backup se for de menos de 24 horas
        if (diferencaHoras < 24) {
            eventos = JSON.parse(backup);
            renderizarCalendario();
            mostrarNotificacao('Eventos restaurados do backup local', 'success');
        }
    }
}

// Adicionar listeners para melhorar UX
window.addEventListener('online', () => {
    mostrarNotificacao('Conexão restaurada!', 'success');
    carregarEventos(); // Recarregar eventos quando voltar online
});

window.addEventListener('offline', () => {
    mostrarNotificacao('Você está offline. Algumas funcionalidades podem estar limitadas.', 'error');
});

// Adicionar prevenção de perda de dados
let formularioAlterado = false;

// Função para marcar formulário como alterado
function marcarFormularioAlterado() {
    formularioAlterado = true;
}

// Função para resetar estado do formulário
function resetarEstadoFormulario() {
    formularioAlterado = false;
}

window.addEventListener('beforeunload', (e) => {
    backupEventosLocal();

    // Se houver alterações não salvas no formulário de evento, avisar o usuário
    if (formularioAlterado && document.querySelector('#eventoModal.active')) {
        e.preventDefault();
        e.returnValue = '';
    }
});

// Inicializar detecção de modo escuro
detectarModoEscuro();

// Adicionar listener para mudanças de tema do sistema
if (window.matchMedia) {
    window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', detectarModoEscuro);
};
