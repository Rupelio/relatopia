<x-dashboard-layout title="Dashboard">
    <script>
        let currentCategory = '';
        const somenteLeitura = @json(!empty($somenteLeitura));
        const relacionamentoId = @json(isset($relacionamento) ? $relacionamento->id : null);
        const isDashboardParceiro = relacionamentoId !== null;

        // URLs das APIs baseadas no contexto
        const apiUrls = {
            itens: isDashboardParceiro ? `/api/parceiro/${relacionamentoId}/relacionamento-itens` : '/api/relacionamento-itens',
            estatisticas: isDashboardParceiro ? `/api/parceiro/${relacionamentoId}/estatisticas` : '/api/estatisticas'
        };

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

            setTimeout(() => {
                sentimentContent.classList.remove('scale-95', 'opacity-0');
                sentimentContent.classList.add('scale-100', 'opacity-100');
            }, 10);
            document.getElementById('nivelIntensidade').addEventListener('input', function() {
                document.getElementById('nivelDisplay').textContent = this.value;
            });
        }

        function openModal(category, title, color){
            currentCategory = category;
            document.getElementById('modalTitle').textContent = `Adicionar ${title}`;
            document.getElementById('submitBtn').className = `flex-1 px-4 py-2 bg-${color}-600 text-white rounded-lg hover:bg-${color}-700 transition-colors duration-200`;

            const modal = document.getElementById('addModal');
            const content = document.getElementById('modalContent');

            modal.classList.remove('hidden');
            modal.classList.add('flex');

            setTimeout(() => {
                content.classList.remove('scale-95', 'opacity-0');
                content.classList.add('scale-100', 'opacity-100');
            }, 10)

            document.getElementById('itemDescription').focus();
        }
        function closeModal() {
            const modal = document.getElementById('addModal');
            const content = document.getElementById('modalContent');

            content.classList.remove('scale-100', 'opacity-100');
            content.classList.add('scale-95', 'opacity-0');
            setTimeout(() => {
                modal.classList.add('hidden');
                modal.classList.remove('flex');
                document.getElementById('addForm').reset();
            }, 300);
        }
        async function submitForm(event) {
            event.preventDefault();
            if (somenteLeitura) {
                showNotification('Você não pode alterar os dados do parceiro.', 'warning');
                return;
            }
            const description = document.getElementById('itemDescription').value.trim();

            if (!description) {
                showNotification('Por favor, adicione uma descrição para continuar', 'warning');
                return;
            }
            try{
                const response = await fetch('/api/relacionamento-itens',{
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        categoria: currentCategory,
                        descricao: description
                    })
                });
                if(response.ok){
                    showNotification('Item adicionado com sucesso!', 'success');
                    closeModal();

                    // CORREÇÃO: Atualizar imediatamente as estatísticas
                    atualizarEstatisticas();
                } else {
                    const error = await response.json();
                    showNotification('Erro ao salvar: ' + (error.message || 'Algo deu errado'), 'error');
                }
            } catch(error){
                console.error('Erro:', error);
                showNotification('Erro de conexão. Verifique sua internet e tente novamente.', 'error');
            }
        }
        document.getElementById('addModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeModal();
            }
        });

        // Fechar modal com ESC
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeModal();
                closeSentimentModal();
            }
        });
        // Função para alternar a exibição da lista
        async function toggleCardList(category, buttonElement = null) {
            const target = buttonElement || event.target;
            const cardElement = target.closest('.bg-white');
            let listElement = cardElement.querySelector('.card-list');

            if (listElement) {
                listElement.remove();
                target.textContent = 'Ver Lista';
            } else {
                try{
                    const response = await fetch(apiUrls.itens + `?categoria=${category}`);
                    const items = await response.json();

                    if (items.length > 0) {
                        listElement = document.createElement('div');
                        listElement.className = 'card-list mt-4 pt-4 border-t border-gray-200';

                        // CORREÇÃO: Limitar itens mostrados inicialmente
                        const maxItemsToShow = 5;
                        const itemsToShow = items.slice(0, maxItemsToShow);
                        const hasMoreItems = items.length > maxItemsToShow;

                        let listHTML = '<ul class="space-y-2">';
                        itemsToShow.forEach(item => {
                            const statusClass = item.resolvido ? 'bg-green-50 text-green-700' : 'bg-gray-50 text-gray-700';
                            const statusIcon = item.resolvido ? '✅' : '⏳';
                            const toggleIcon = item.resolvido
                                ? `<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"></path>
                                </svg>`
                                : `<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>`;

                            const toggleTitle = item.resolvido ? 'Marcar como pendente' : 'Marcar como resolvido';
                            const toggleButtonColor = item.resolvido ? 'text-orange-500 hover:text-orange-700' : 'text-blue-500 hover:text-blue-700';

                            listHTML += `
                                <li class="flex items-center justify-between text-sm ${statusClass} px-3 py-2 rounded-lg">
                                    <div class="flex items-center space-x-2">
                                        <span>${statusIcon}</span>
                                        <span>${item.descricao}</span>
                                    </div>
                                    <div class="flex items-center space-x-2">
                                        ${!somenteLeitura ? `
                                            <button onclick="toggleItem(${item.id}, this)" class="${toggleButtonColor} text-xs" title="${toggleTitle}">
                                                ${toggleIcon}
                                            </button>
                                            <button onclick="removeItem(${item.id})" class="text-red-500 hover:text-red-700 text-xs" title="Remover item">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                </svg>
                                            </button>
                                        ` : ''}
                                    </div>
                                </li>
                            `;
                        });
                        listHTML += '</ul>';

                        // CORREÇÃO: Se tem mais itens, adiciona botão funcional
                        if (hasMoreItems) {
                            const remainingItems = items.length - maxItemsToShow;
                            listHTML += `
                                <div class="mt-3 text-center">
                                    <button onclick="showAllItems('${category}')" class="text-sm text-emerald-600 hover:text-emerald-700 font-medium">
                                        Ver mais ${remainingItems} itens
                                    </button>
                                </div>
                            `;
                        }

                        listElement.innerHTML = listHTML;
                        cardElement.querySelector('.p-6').appendChild(listElement);
                        target.textContent = 'Ocultar Lista';
                    } else {
                        // Se não tem itens, mostra mensagem
                        listElement = document.createElement('div');
                        listElement.className = 'card-list mt-4 pt-4 border-t border-gray-200 text-center text-gray-500 text-sm';
                        listElement.innerHTML = 'Nenhum item adicionado ainda. Clique em "+ Adicionar" para começar!';
                        cardElement.querySelector('.p-6').appendChild(listElement);
                        target.textContent = 'Ocultar Lista';
                    }
                } catch (error) {
                    console.error('Erro ao carregar itens:', error);
                    showNotification('Erro ao carregar itens. Tente novamente.', 'error');
                }
            }
        }

        async function toggleItem(itemId, btn) {
            if (somenteLeitura) {
                showNotification('Você não pode alterar os dados do parceiro.', 'warning');
                return;
            }
            try {
                const response = await fetch(`/api/relacionamento-itens/${itemId}/toggle`, {
                    method: 'PUT',
                    headers:{
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });
                if(response.ok){
                    showNotification('Status alterado com sucesso!', 'success');

                    // CORREÇÃO: Atualizar imediatamente as estatísticas
                    atualizarEstatisticas();

                    // Recarregar a lista atual se estiver aberta
                    const card = btn.closest('.bg-white');
                    const openList = card.querySelector('.card-list');
                    if(openList){
                        const button = card.closest('.bg-white').querySelector('button[onclick*="toggleCardList"]');
                        button.click();
                        setTimeout(() => button.click(), 100);
                    }
                }
            } catch (error) {
                console.error('Erro ao alterar status:', error);
                showNotification('Erro ao alterar status. Tente novamente.', 'error');
            }
        }
        // Função para remover item da lista
        async function removeItem(itemId) {
            if (somenteLeitura) {
                showNotification('Você não pode alterar os dados do parceiro.', 'warning');
                return;
            }
            if (confirm('Tem certeza que deseja remover este item?')) {
                try {
                    const response = await fetch(`/api/relacionamento-itens/${itemId}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        }
                    });

                    if (response.ok) {
                        showNotification('Item removido com sucesso!', 'success');

                        // CORREÇÃO: Atualizar imediatamente as estatísticas
                        atualizarEstatisticas();

                        // Recarregar a lista atual se estiver aberta
                        const openList = document.querySelector('.card-list');
                        if (openList) {
                            const button = openList.closest('.bg-white').querySelector('button[onclick*="toggleCardList"]');
                            button.click(); // Fechar
                            setTimeout(() => button.click(), 100); // Reabrir
                        }
                    }
                } catch (error) {
                    console.error('Erro ao remover item:', error);
                    showNotification('Erro ao remover item. Tente novamente.', 'error');
                }
            }
        }
        async function atualizarCardList(category) {
            // Procura o card correto pelo atributo data-category
            const cardButton = document.querySelector(`button[onclick="toggleCardList('${category}')"]`);
            if (cardButton) {
                // Se a lista estiver aberta, fecha e reabre para atualizar
                if (cardButton.textContent.trim() === 'Ocultar Lista') {
                    cardButton.click();
                    setTimeout(() => cardButton.click(), 200);
                }
            }
        }
        async function atualizarSentimentoCard() {
            try {
                const response = await fetch('/api/estatisticasSentimento');
                if (response.ok) {
                    const ultimo = await response.json();
                    // Atualize o texto do card de sentimentos
                    document.querySelector('[data-stat="sentimentos"]').textContent = ultimo.total;
                    document.querySelector('[data-stat="ultimo_sentimento"]').textContent =
                        ultimo.ultimo
                            ? capitalize(ultimo.ultimo.tipo_sentimento) + " • Intensidade " + ultimo.ultimo.nivel_intensidade + "/10"
                            : 'Nenhum registro ainda';
                }
            } catch (error) {
                // Silencie ou mostre erro se quiser
            }
        }
        function capitalize(str) {
            return str ? str.charAt(0).toUpperCase() + str.slice(1) : '';
        }
        async function atualizarEstatisticas() {
            try {
                const response = await fetch(apiUrls.estatisticas);
                if (response.ok) {
                    const stats = await response.json();

                    // Atualize os elementos dos cards principais
                    const updateElement = (selector, value) => {
                        const element = document.querySelector(selector);
                        if (element) element.textContent = value;
                    };

                    updateElement('[data-stat="reclamacoes"]', stats.reclamacoes);
                    updateElement('[data-stat="positivos"]', stats.positivos);
                    updateElement('[data-stat="meus_desejos"]', stats.meus_desejos);
                    updateElement('[data-stat="nossos_desejos"]', stats.nossos_desejos);
                    updateElement('[data-stat="melhorar_mim"]', stats.melhorar_mim);
                    updateElement('[data-stat="melhorar_juntos"]', stats.melhorar_juntos);
                    updateElement('[data-stat="total_itens"]', stats.total_itens);
                    updateElement('[data-stat="total_melhorias"]', stats.total_melhorias);
                    updateElement('[data-stat="total_desejos"]', stats.total_desejos);

                    // CORREÇÃO: Atualizar seção de estatísticas rápidas (resumo)
                    updateElement('[data-stat="resumo_reclamacoes"]', stats.reclamacoes);
                    updateElement('[data-stat="resumo_positivos"]', stats.positivos);
                    updateElement('[data-stat="resumo_melhorias"]', stats.total_melhorias);
                    updateElement('[data-stat="resumo_desejos"]', stats.total_desejos);
                    updateElement('[data-stat="resumo_total"]', stats.total_itens);

                    // CORREÇÃO: Atualizar barra de progresso geral
                    const progressBar = document.getElementById('progress-bar');
                    const progressPercentage = document.getElementById('progress-percentage');

                    if (progressBar && progressPercentage && stats.total_itens > 0) {
                        const percentage = Math.round((stats.positivos / stats.total_itens) * 100);
                        progressBar.style.width = percentage + '%';
                        progressPercentage.textContent = percentage + '%';
                    } else if (progressBar && progressPercentage) {
                        progressBar.style.width = '0%';
                        progressPercentage.textContent = '0%';
                    }

                    console.log('✅ Estatísticas atualizadas com sucesso:', stats);
                }
            } catch (error) {
                console.error('❌ Erro ao atualizar estatísticas:', error);
            }
        }

        // Função para mostrar todos os itens de uma categoria
        async function showAllItems(category) {
            try {
                const response = await fetch(apiUrls.itens + `?categoria=${category}`);
                const items = await response.json();

                // Encontrar o card correto
                const cardButton = document.querySelector(`button[onclick="toggleCardList('${category}')"]`);
                const cardElement = cardButton.closest('.bg-white');
                let listElement = cardElement.querySelector('.card-list');

                if (listElement && items.length > 0) {
                    // Recriar a lista com todos os itens
                    let listHTML = '<ul class="space-y-2">';
                    items.forEach(item => {
                        const statusClass = item.resolvido ? 'bg-green-50 text-green-700' : 'bg-gray-50 text-gray-700';
                        const statusIcon = item.resolvido ? '✅' : '⏳';
                        const toggleIcon = item.resolvido
                            ? `<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"></path>
                            </svg>`
                            : `<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>`;

                        const toggleTitle = item.resolvido ? 'Marcar como pendente' : 'Marcar como resolvido';
                        const toggleButtonColor = item.resolvido ? 'text-orange-500 hover:text-orange-700' : 'text-blue-500 hover:text-blue-700';

                        listHTML += `
                            <li class="flex items-center justify-between text-sm ${statusClass} px-3 py-2 rounded-lg">
                                <div class="flex items-center space-x-2">
                                    <span>${statusIcon}</span>
                                    <span>${item.descricao}</span>
                                </div>
                                <div class="flex items-center space-x-2">
                                    ${!somenteLeitura ? `
                                        <button onclick="toggleItem(${item.id}, this)" class="${toggleButtonColor} text-xs" title="${toggleTitle}">
                                            ${toggleIcon}
                                        </button>
                                        <button onclick="removeItem(${item.id})" class="text-red-500 hover:text-red-700 text-xs" title="Remover item">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                        </button>
                                    ` : ''}
                                </div>
                            </li>
                        `;
                    });
                    listHTML += '</ul>';

                    // Adicionar botão para reduzir a lista
                    listHTML += `
                        <div class="mt-3 text-center">
                            <button onclick="toggleCardList('${category}'); setTimeout(() => toggleCardList('${category}'), 100);" class="text-sm text-gray-600 hover:text-gray-700 font-medium">
                                Mostrar menos
                            </button>
                        </div>
                    `;

                    listElement.innerHTML = listHTML;
                }
            } catch (error) {
                console.error('Erro ao carregar todos os itens:', error);
                showNotification('Erro ao carregar itens. Tente novamente.', 'error');
            }
        }

        // CORREÇÃO: Inicializar atualização automática quando a página carregar
        document.addEventListener('DOMContentLoaded', function() {
            console.log('🚀 Dashboard carregado - iniciando sistema de atualização automática');

            // Primeira atualização após 1 segundo para garantir que tudo está carregado
            setTimeout(atualizarEstatisticas, 1000);

            // Atualização periódica a cada 30 segundos (opcional)
            setInterval(atualizarEstatisticas, 30000);
        });
    </script>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Header com melhor visual -->
        <div class="mb-8 text-center">
            <div class="inline-flex items-center justify-center w-16 h-16 bg-gradient-to-br from-emerald-400 to-emerald-600 rounded-full mb-4">
                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                </svg>
            </div>
            <h1 class="text-4xl font-bold bg-gradient-to-r from-emerald-600 to-teal-600 bg-clip-text text-transparent mb-2">
                {{ isset($relacionamento) ? 'Dashboard do Parceiro' : 'Meu Relacionamento' }}
            </h1>
            <p class="text-gray-600 text-lg">Acompanhe e desenvolva todos os aspectos do seu relacionamento</p>
            @if(isset($relacionamento))
                <div class="mt-4 inline-flex items-center px-4 py-2 bg-blue-100 text-blue-800 rounded-full text-sm font-medium">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                    </svg>
                    Visualizando dados do parceiro
                </div>
            @endif
        </div>

        <!-- Grid de cards do dashboard -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-2 gap-6">

            <!-- Card 1: Reclamações -->
            <div class="group bg-white rounded-2xl shadow-lg border-2 border-red-100 hover:border-red-200 transition-all duration-300 card-hover overflow-hidden">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center">
                            <div class="p-3 bg-gradient-to-br from-red-100 to-red-200 rounded-xl group-hover:scale-110 transition-transform duration-300">
                                <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 15.5c-.77.833.192 2.5 1.732 2.5z"></path>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <h3 class="text-lg font-bold text-gray-800 group-hover:text-red-700 transition-colors duration-300">Reclamações</h3>
                                <p class="text-sm text-gray-500 flex items-center">
                                    <span class="inline-flex items-center justify-center w-6 h-6 bg-red-100 text-red-600 rounded-full text-xs font-bold mr-2" data-stat="reclamacoes">{{ $estatisticas['relacionamento']['reclamacoes'] }}</span>
                                    pontos de atenção
                                </p>
                            </div>
                        </div>
                        @if($estatisticas['relacionamento']['reclamacoes'] > 0)
                            <div class="w-3 h-3 bg-red-400 rounded-full animate-pulse-gentle"></div>
                        @endif
                    </div>
                    <p class="text-gray-600 text-sm mb-6 leading-relaxed">Identifique e trabalhe os pontos de atrito no relacionamento</p>
                    <div class="flex space-x-3">
                        <button onclick="toggleCardList('reclamacoes')" class="flex-1 bg-red-50 text-red-700 px-4 py-3 rounded-xl text-sm font-semibold hover:bg-red-100 transition-all duration-200 transform hover:scale-105 border-2 border-red-100 hover:border-red-200">
                            <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"></path>
                            </svg>
                            Ver Lista
                        </button>
                        @if(empty($somenteLeitura))
                        <button onclick="openModal('reclamacoes', 'Reclamações', 'red')" class="px-4 py-3 bg-gradient-to-r from-red-500 to-red-600 text-white rounded-xl text-sm font-semibold hover:from-red-600 hover:to-red-700 transition-all duration-200 transform hover:scale-105 shadow-lg hover:shadow-xl">
                            <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                            Adicionar
                        </button>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Card 2: Pontos Positivos -->
            <div class="group bg-white rounded-2xl shadow-lg border-2 border-green-100 hover:border-green-200 transition-all duration-300 card-hover overflow-hidden">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center">
                            <div class="p-3 bg-gradient-to-br from-green-100 to-green-200 rounded-xl group-hover:scale-110 transition-transform duration-300">
                                <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h1.01M15 10h1.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <h3 class="text-lg font-bold text-gray-800 group-hover:text-green-700 transition-colors duration-300">Pontos Positivos</h3>
                                <p class="text-sm text-gray-500 flex items-center">
                                    <span class="inline-flex items-center justify-center w-6 h-6 bg-green-100 text-green-600 rounded-full text-xs font-bold mr-2" data-stat="positivos">{{ $estatisticas['relacionamento']['positivos'] }}</span>
                                    pontos de atenção
                                </p>
                            </div>
                        </div>
                        @if($estatisticas['relacionamento']['positivos'] > 0)
                            <div class="w-3 h-3 bg-green-400 rounded-full animate-pulse-gentle"></div>
                        @endif
                    </div>
                    <p class="text-gray-600 text-sm mb-6 leading-relaxed">Celebre e valorize os aspectos positivos do relacionamento</p>
                    <div class="flex space-x-3">
                        <button onclick="toggleCardList('positivos')" class="flex-1 bg-green-50 text-green-700 px-4 py-3 rounded-xl text-sm font-semibold hover:bg-green-100 transition-all duration-200 transform hover:scale-105 border-2 border-green-100 hover:border-green-200">
                            <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"></path>
                            </svg>
                            Ver Lista
                        </button>
                        @if(empty($somenteLeitura))
                        <button onclick="openModal('positivos', 'Ponto Positivo', 'green')" class="px-4 py-3 bg-gradient-to-r from-green-500 to-green-600 text-white rounded-xl text-sm font-semibold hover:from-green-600 hover:to-green-700 transition-all duration-200 transform hover:scale-105 shadow-lg hover:shadow-xl">
                            <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                            Adicionar
                        </button>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Card 3: Meus Desejos -->
            <div class="group bg-white rounded-2xl shadow-lg border-2 border-blue-100 hover:border-blue-200 transition-all duration-300 card-hover overflow-hidden">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center">
                            <div class="p-3 bg-gradient-to-br from-blue-100 to-blue-200 rounded-xl group-hover:scale-110 transition-transform duration-300">
                                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <h3 class="text-lg font-bold text-gray-800 group-hover:text-blue-700 transition-colors duration-300">Meus Desejos</h3>
                                <p class="text-sm text-gray-500 flex items-center">
                                    <span class="inline-flex items-center justify-center w-6 h-6 bg-blue-100 text-blue-600 rounded-full text-xs font-bold mr-2" data-stat="meus_desejos">{{ $estatisticas['relacionamento']['meus_desejos'] }}</span>
                                    pontos de atenção
                                </p>
                            </div>
                        </div>
                        @if($estatisticas['relacionamento']['meus_desejos'] > 0)
                            <div class="w-3 h-3 bg-blue-400 rounded-full animate-pulse-gentle"></div>
                        @endif
                    </div>
                    <p class="text-gray-600 text-sm mb-6 leading-relaxed">Seus objetivos e aspirações pessoais</p>
                    <div class="flex space-x-3">
                        <button onclick="toggleCardList('meus_desejos')" class="flex-1 bg-blue-50 text-blue-700 px-4 py-3 rounded-xl text-sm font-semibold hover:bg-blue-100 transition-all duration-200 transform hover:scale-105 border-2 border-blue-100 hover:border-blue-200">
                            <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"></path>
                            </svg>
                            Ver Lista
                        </button>
                        @if(empty($somenteLeitura))
                        <button onclick="openModal('meus_desejos', 'Meus Desejos', 'blue')" class="px-4 py-3 bg-gradient-to-r from-blue-500 to-blue-600 text-white rounded-xl text-sm font-semibold hover:from-blue-600 hover:to-blue-700 transition-all duration-200 transform hover:scale-105 shadow-lg hover:shadow-xl">
                            <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                            Adicionar
                        </button>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Card 4: Nossos Desejos -->
            <div class="group bg-white rounded-2xl shadow-lg border-2 border-purple-100 hover:border-purple-200 transition-all duration-300 card-hover overflow-hidden">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center">
                            <div class="p-3 bg-gradient-to-br from-purple-100 to-purple-200 rounded-xl group-hover:scale-110 transition-transform duration-300">
                                <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <h3 class="text-lg font-bold text-gray-800 group-hover:text-purple-700 transition-colors duration-300">Nossos Desejos</h3>
                                <p class="text-sm text-gray-500 flex items-center">
                                    <span class="inline-flex items-center justify-center w-6 h-6 bg-purple-100 text-purple-600 rounded-full text-xs font-bold mr-2" data-stat="nossos_desejos">{{ $estatisticas['relacionamento']['nossos_desejos'] }}</span>
                                    pontos de atenção
                                </p>
                            </div>
                        </div>
                        @if($estatisticas['relacionamento']['nossos_desejos'] > 0)
                            <div class="w-3 h-3 bg-purple-400 rounded-full animate-pulse-gentle"></div>
                        @endif
                    </div>
                    <p class="text-gray-600 text-sm mb-6 leading-relaxed">Planos e sonhos que vocês querem realizar juntos</p>
                    <div class="flex space-x-3">
                        <button onclick="toggleCardList('nossos_desejos')" class="flex-1 bg-purple-50 text-purple-700 px-4 py-3 rounded-xl text-sm font-semibold hover:bg-purple-100 transition-all duration-200 transform hover:scale-105 border-2 border-purple-100 hover:border-purple-200">
                            <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"></path>
                            </svg>
                            Ver Lista
                        </button>
                        @if(empty($somenteLeitura))
                        <button onclick="openModal('nossos_desejos', 'Nossos Desejos', 'purple')" class="px-4 py-3 bg-gradient-to-r from-purple-500 to-purple-600 text-white rounded-xl text-sm font-semibold hover:from-purple-600 hover:to-purple-700 transition-all duration-200 transform hover:scale-105 shadow-lg hover:shadow-xl">
                            <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                            Adicionar
                        </button>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Card 5: Melhorar em Mim -->
            <div class="group bg-white rounded-2xl shadow-lg border-2 border-yellow-100 hover:border-yellow-200 transition-all duration-300 card-hover overflow-hidden">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center">
                            <div class="p-3 bg-gradient-to-br from-yellow-100 to-yellow-200 rounded-xl group-hover:scale-110 transition-transform duration-300">
                                <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <h3 class="text-lg font-bold text-gray-800 group-hover:text-yellow-700 transition-colors duration-300">Melhorar em Mim</h3>
                                <p class="text-sm text-gray-500 flex items-center">
                                    <span class="inline-flex items-center justify-center w-6 h-6 bg-yellow-100 text-yellow-600 rounded-full text-xs font-bold mr-2" data-stat="melhorar_mim">{{ $estatisticas['relacionamento']['melhorar_mim'] }}</span>
                                    pontos de atenção
                                </p>
                            </div>
                        </div>
                        @if($estatisticas['relacionamento']['melhorar_mim'] > 0)
                            <div class="w-3 h-3 bg-yellow-400 rounded-full animate-pulse-gentle"></div>
                        @endif
                    </div>
                    <p class="text-gray-600 text-sm mb-6 leading-relaxed">Aspectos pessoais que você quer desenvolver</p>
                    <div class="flex space-x-3">
                        <button onclick="toggleCardList('melhorar_mim')" class="flex-1 bg-yellow-50 text-yellow-700 px-4 py-3 rounded-xl text-sm font-semibold hover:bg-yellow-100 transition-all duration-200 transform hover:scale-105 border-2 border-yellow-100 hover:border-yellow-200">
                            <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"></path>
                            </svg>
                            Ver Lista
                        </button>
                        @if(empty($somenteLeitura))
                        <button onclick="openModal('melhorar_mim', 'Melhorar em Mim', 'yellow')" class="px-4 py-3 bg-gradient-to-r from-yellow-500 to-yellow-600 text-white rounded-xl text-sm font-semibold hover:from-yellow-600 hover:to-yellow-700 transition-all duration-200 transform hover:scale-105 shadow-lg hover:shadow-xl">
                            <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                            Adicionar
                        </button>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Card 6: Melhorar Juntos -->
            <div class="group bg-white rounded-2xl shadow-lg border-2 border-emerald-100 hover:border-emerald-200 transition-all duration-300 card-hover overflow-hidden">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center">
                            <div class="p-3 bg-gradient-to-br from-emerald-100 to-emerald-200 rounded-xl group-hover:scale-110 transition-transform duration-300">
                                <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <h3 class="text-lg font-bold text-gray-800 group-hover:text-emerald-700 transition-colors duration-300">Melhorar Juntos</h3>
                                <p class="text-sm text-gray-500 flex items-center">
                                    <span class="inline-flex items-center justify-center w-6 h-6 bg-emerald-100 text-emerald-600 rounded-full text-xs font-bold mr-2" data-stat="melhorar_juntos">{{ $estatisticas['relacionamento']['melhorar_juntos'] }}</span>
                                    pontos de atenção
                                </p>
                            </div>
                        </div>
                        @if($estatisticas['relacionamento']['melhorar_juntos'] > 0)
                            <div class="w-3 h-3 bg-emerald-400 rounded-full animate-pulse-gentle"></div>
                        @endif
                    </div>
                    <p class="text-gray-600 text-sm mb-6 leading-relaxed">Aspectos do relacionamento para desenvolver em conjunto</p>
                    <div class="flex space-x-3">
                        <button onclick="toggleCardList('melhorar_juntos')" class="flex-1 bg-emerald-50 text-emerald-700 px-4 py-3 rounded-xl text-sm font-semibold hover:bg-emerald-100 transition-all duration-200 transform hover:scale-105 border-2 border-emerald-100 hover:border-emerald-200">
                            <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"></path>
                            </svg>
                            Ver Lista
                        </button>
                        @if(empty($somenteLeitura))
                        <button onclick="openModal('melhorar_juntos', 'Melhorar Juntos', 'emerald')" class="px-4 py-3 bg-gradient-to-r from-emerald-500 to-emerald-600 text-white rounded-xl text-sm font-semibold hover:from-emerald-600 hover:to-emerald-700 transition-all duration-200 transform hover:scale-105 shadow-lg hover:shadow-xl">
                            <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                            Adicionar
                        </button>
                        @endif
                    </div>
                </div>
            </div>
            <!-- Card 8: Em breve -->
            <div class="group bg-white rounded-2xl shadow-lg border-2 border-pink-100 hover:border-pink-200 transition-all duration-300 card-hover overflow-hidden">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center">
                            <div class="p-3 bg-gradient-to-br from-pink-100 to-pink-200 rounded-xl group-hover:scale-110 transition-transform duration-300">
                                <svg class="w-6 h-6 text-pink-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <circle cx="12" cy="12" r="9" stroke-width="2" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 7v5l3 3" />
                                </svg>
                            </div>
                            <div class="ml-4">
                                <h3 class="text-lg font-bold text-gray-800 group-hover:text-pink-700 transition-colors duration-300">Lista de desejos (em breve)</h3>
                                <p class="text-sm text-gray-500 flex items-center">
                                    <span class="inline-flex items-center justify-center w-6 h-6 bg-pink-100 text-pink-600 rounded-full text-xs font-bold mr-2">0</span>
                                    objetos cadastrados
                                </p>
                            </div>
                        </div>
                        <div class="w-3 h-3 bg-pink-300 rounded-full animate-pulse-gentle opacity-50"></div>
                    </div>
                    <p class="text-gray-600 text-sm mb-6 leading-relaxed">Em breve você poderá criar uma lista com os objetos e experiências que deseja realizar ou conquistar. Fique de olho nas novidades!</p>
                    <div class="flex space-x-3">
                        <button disabled class="flex-1 bg-pink-50 text-pink-400 px-4 py-3 rounded-xl text-sm font-semibold cursor-not-allowed opacity-50 border-2 border-pink-100">
                            <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"></path>
                            </svg>
                            Ver Lista
                        </button>
                        <button disabled class="px-4 py-3 bg-gradient-to-r from-pink-300 to-pink-400 text-white rounded-xl text-sm font-semibold cursor-not-allowed opacity-50 shadow-lg">
                            <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                            Adicionar
                        </button>
                    </div>
                </div>
            </div>
            <!-- Card 7: Registro de Sentimentos -->
            <div class="group bg-white rounded-2xl shadow-lg border-2 border-orange-100 hover:border-orange-200 transition-all duration-300 card-hover overflow-hidden">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center">
                            <div class="p-3 bg-gradient-to-br from-orange-100 to-orange-200 rounded-xl group-hover:scale-110 transition-transform duration-300">
                                <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h1.01M15 10h1.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <h3 class="text-lg font-bold text-gray-800 group-hover:text-orange-700 transition-colors duration-300">Meus Sentimentos</h3>
                                <p class="text-sm text-gray-500 flex items-center">
                                    <span class="inline-flex items-center justify-center w-6 h-6 bg-orange-100 text-orange-600 rounded-full text-xs font-bold mr-2" data-stat="sentimentos">{{ $estatisticas['sentimentos']['total'] }}</span>
                                    registros hoje
                                </p>
                            </div>
                        </div>
                        @if($estatisticas['sentimentos']['total'] > 0)
                            <div class="w-3 h-3 bg-orange-400 rounded-full animate-pulse-gentle"></div>
                        @endif
                    </div>
                    <p class="text-gray-600 text-sm mb-6 leading-relaxed">Registre como você se sente no momento e acompanhe seus padrões emocionais</p>
                    <div class="mb-3">
                        <div class="text-xs text-gray-500 mb-1">Último sentimento:</div>
                        <div class="text-sm font-medium text-orange-700" id="ultimoSentimento" data-stat="ultimo_sentimento">
                            {{ $estatisticas['sentimentos']['ultimo'] ? ucfirst($estatisticas['sentimentos']['ultimo']->tipo_sentimento) . " • Intensidade " . $estatisticas['sentimentos']['ultimo']->nivel_intensidade . "/10" : 'Nenhum registro ainda' }}
                        </div>
                    </div>
                    <div class="flex space-x-3">
                        <a href="{{ route('historico') }}" class="flex-1 text-center bg-orange-50 text-orange-700 px-4 py-3 rounded-xl text-sm font-semibold hover:bg-orange-100 transition-all duration-200 transform hover:scale-105 border-2 border-orange-100 hover:border-orange-200">
                            <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                            </svg>
                            Ver Histórico
                        </a>
                        @if(empty($somenteLeitura))
                        <button onclick="openSentimentModal()" class="px-4 py-3 bg-gradient-to-r from-orange-500 to-orange-600 text-white rounded-xl text-sm font-semibold hover:from-orange-600 hover:to-orange-700 transition-all duration-200 transform hover:scale-105 shadow-lg hover:shadow-xl">
                            <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                            Registrar Agora
                        </button>
                        @endif
                    </div>
                </div>

            </div>

        </div>

        <!-- Seção de estatísticas rápidas melhorada -->
        <div class="mt-12 bg-gradient-to-br from-emerald-50 to-teal-100 rounded-2xl p-8 border border-emerald-200 shadow-lg">
            <div class="flex items-center justify-between mb-6">
                <div class="flex items-center">
                    <div class="p-3 bg-gradient-to-br from-emerald-500 to-teal-600 rounded-xl mr-4">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-2xl font-bold text-gray-800">Resumo do Relacionamento</h2>
                        <p class="text-gray-600 text-sm">Visão geral dos dados principais</p>
                    </div>
                </div>
                <div class="text-xs text-gray-500 bg-white px-3 py-1 rounded-full border">
                    Atualização automática
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-5 gap-6">
                <!-- Estatística 1: Total de Itens -->
                <div class="bg-white rounded-xl p-6 shadow-md border border-emerald-100 hover:shadow-lg transition-all duration-300 group">
                    <div class="flex items-center justify-between mb-3">
                        <div class="p-2 bg-emerald-100 rounded-lg group-hover:scale-110 transition-transform duration-300">
                            <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 9a2 2 0 00-2 2v2m0 0V9a2 2 0 012-2m0 0V7a2 2 0 012-2h10a2 2 0 012 2v2M7 7V6a1 1 0 011-1h8a1 1 0 011 1v1"></path>
                            </svg>
                        </div>
                        <div class="w-2 h-2 bg-emerald-400 rounded-full animate-pulse-gentle"></div>
                    </div>
                    <div class="text-center">
                        <span class="text-3xl font-bold text-emerald-600" data-stat="resumo_total">{{ $estatisticas['relacionamento']['total_itens'] }}</span>
                        <div class="text-xs font-medium text-gray-600 mt-1">Total de itens</div>
                    </div>
                </div>

                <!-- Estatística 2: Reclamações -->
                <div class="bg-white rounded-xl p-6 shadow-md border border-red-100 hover:shadow-lg transition-all duration-300 group">
                    <div class="flex items-center justify-between mb-3">
                        <div class="p-2 bg-red-100 rounded-lg group-hover:scale-110 transition-transform duration-300">
                            <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 15.5c-.77.833.192 2.5 1.732 2.5z"></path>
                            </svg>
                        </div>
                        <div class="w-2 h-2 bg-red-400 rounded-full animate-pulse-gentle"></div>
                    </div>
                    <div class="text-center">
                        <span class="text-3xl font-bold text-red-600" data-stat="resumo_reclamacoes">{{ $estatisticas['relacionamento']['reclamacoes'] }}</span>
                        <div class="text-xs font-medium text-gray-600 mt-1">Reclamações</div>
                    </div>
                </div>

                <!-- Estatística 3: Pontos Positivos -->
                <div class="bg-white rounded-xl p-6 shadow-md border border-green-100 hover:shadow-lg transition-all duration-300 group">
                    <div class="flex items-center justify-between mb-3">
                        <div class="p-2 bg-green-100 rounded-lg group-hover:scale-110 transition-transform duration-300">
                            <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h1.01M15 10h1.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div class="w-2 h-2 bg-green-400 rounded-full animate-pulse-gentle"></div>
                    </div>
                    <div class="text-center">
                        <span class="text-3xl font-bold text-green-600" data-stat="resumo_positivos">{{ $estatisticas['relacionamento']['positivos'] }}</span>
                        <div class="text-xs font-medium text-gray-600 mt-1">Pontos positivos</div>
                    </div>
                </div>

                <!-- Estatística 4: Para Melhorar -->
                <div class="bg-white rounded-xl p-6 shadow-md border border-yellow-100 hover:shadow-lg transition-all duration-300 group">
                    <div class="flex items-center justify-between mb-3">
                        <div class="p-2 bg-yellow-100 rounded-lg group-hover:scale-110 transition-transform duration-300">
                            <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                            </svg>
                        </div>
                        <div class="w-2 h-2 bg-yellow-400 rounded-full animate-pulse-gentle"></div>
                    </div>
                    <div class="text-center">
                        <span class="text-3xl font-bold text-yellow-600" data-stat="resumo_melhorias">{{ $estatisticas['relacionamento']['total_melhorias'] }}</span>
                        <div class="text-xs font-medium text-gray-600 mt-1">Para melhorar</div>
                    </div>
                </div>

                <!-- Estatística 5: Desejos Totais -->
                <div class="bg-white rounded-xl p-6 shadow-md border border-purple-100 hover:shadow-lg transition-all duration-300 group">
                    <div class="flex items-center justify-between mb-3">
                        <div class="p-2 bg-purple-100 rounded-lg group-hover:scale-110 transition-transform duration-300">
                            <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                            </svg>
                        </div>
                        <div class="w-2 h-2 bg-purple-400 rounded-full animate-pulse-gentle"></div>
                    </div>
                    <div class="text-center">
                        <span class="text-3xl font-bold text-purple-600" data-stat="resumo_desejos">{{ $estatisticas['relacionamento']['total_desejos'] }}</span>
                        <div class="text-xs font-medium text-gray-600 mt-1">Desejos totais</div>
                    </div>
                </div>
            </div>

            <!-- Barra de progresso geral -->
            <div class="mt-6 bg-white rounded-xl p-4 border border-emerald-100">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-sm font-medium text-gray-600">Progresso Geral do Relacionamento</span>
                    <span class="text-sm text-emerald-600 font-semibold" id="progress-percentage">
                        {{ $estatisticas['relacionamento']['total_itens'] > 0 ? round(($estatisticas['relacionamento']['positivos'] / $estatisticas['relacionamento']['total_itens']) * 100) : 0 }}%
                    </span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-3">
                    <div class="bg-gradient-to-r from-emerald-400 to-teal-500 h-3 rounded-full transition-all duration-1000"
                         id="progress-bar"
                         style="width: {{ $estatisticas['relacionamento']['total_itens'] > 0 ? round(($estatisticas['relacionamento']['positivos'] / $estatisticas['relacionamento']['total_itens']) * 100) : 0 }}%">
                    </div>
                </div>
                <div class="text-xs text-gray-500 mt-1">
                    Baseado na proporção de pontos positivos em relação ao total de itens
                </div>
            </div>
        </div>
    </div>
</x-dashboard-layout>

<!-- Modal para adicionar itens -->
<div id="addModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 items-center justify-center hidden">
    <div class="bg-white rounded-xl p-6 max-w-md w-full mx-4 transform transition-all duration-300 scale-95 opacity-0" id="modalContent">
        <!-- Header do Modal -->
        <div class="flex justify-between items-center mb-4">
            <h3 id="modalTitle" class="text-lg font-semibold text-gray-800">Adicionar Item</h3>
            <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>

        <!-- Conteúdo do Modal -->
        <form id="addForm" onsubmit="submitForm(event)">
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Descrição</label>
                <textarea id="itemDescription" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 resize-none" rows="3" placeholder="Digite aqui..." required></textarea>
            </div>

            <!-- Botões -->
            <div class="flex space-x-3">
                <button type="button" onclick="closeModal()" class="flex-1 px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors duration-200">
                    Cancelar
                </button>
                <button type="submit" id="submitBtn" class="flex-1 px-4 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 transition-colors duration-200">
                    Adicionar
                </button>
            </div>
        </form>
    </div>
</div>

