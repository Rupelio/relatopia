<x-dashboard-layout title="Dashboard">
    <script>
        let currentCategory = '';
        const somenteLeitura = @json(!empty($somenteLeitura));
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
                    // Atualiza só a lista do card e as estatísticas
                    setTimeout(() => {
                        atualizarCardList(currentCategory);
                        atualizarEstatisticas();
                    }, 500);
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
                    const response = await fetch(`/api/relacionamento-itens?categoria=${category}`);
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
            try {
                const response = await fetch(`/api/relacionamento-itens/${itemId}/toggle`, {
                    method: 'PUT',
                    headers:{
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });
                if(response.ok){
                    const card = btn.closest('.bg-white');
                    const openList = card.querySelector('.card-list');
                    if(openList){
                        const button = card.closest('.bg-white').querySelector('button[onclick*="toggleCardList"]');
                        button.click();
                        setTimeout(() => button.click(), 100);
                    }
                    showNotification('Status alterado com sucesso!', 'success');
                    setTimeout(() => {
                        atualizarCardList(currentCategory);
                        atualizarEstatisticas();
                    }, 500);
                }
            } catch (error) {
                console.error('Erro ao alterar status:', error);
                showNotification('Erro ao alterar status. Tente novamente.', 'error');
            }
        }
        // Função para remover item da lista
        async function removeItem(itemId) {
            if (confirm('Tem certeza que deseja remover este item?')) {
                try {
                    const response = await fetch(`/api/relacionamento-itens/${itemId}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        }
                    });

                    if (response.ok) {
                        // Recarregar a lista atual
                        const openList = document.querySelector('.card-list');
                        if (openList) {
                            const button = openList.closest('.bg-white').querySelector('button[onclick*="toggleCardList"]');
                            button.click(); // Fechar
                            setTimeout(() => button.click(), 100); // Reabrir
                        }

                        // Atualizar estatísticas
                        showNotification('Item removido com sucesso!', 'success');
                        setTimeout(() => {
                            atualizarCardList(currentCategory);
                            atualizarEstatisticas();
                        }, 500);
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
                const response = await fetch('/api/estatisticas');
                if (response.ok) {
                    const stats = await response.json();
                    // Atualize os elementos na tela conforme os dados retornados
                    document.querySelector('[data-stat="reclamacoes"]').textContent = stats.reclamacoes;
                    document.querySelector('[data-stat="positivos"]').textContent = stats.positivos;
                    document.querySelector('[data-stat="meus_desejos"]').textContent = stats.meus_desejos;
                    document.querySelector('[data-stat="nossos_desejos"]').textContent = stats.nossos_desejos;
                    document.querySelector('[data-stat="melhorar_mim"]').textContent = stats.melhorar_mim;
                    document.querySelector('[data-stat="melhorar_juntos"]').textContent = stats.melhorar_juntos;
                    document.querySelector('[data-stat="total_itens"]').textContent = stats.total_itens;
                    document.querySelector('[data-stat="total_melhorias"]').textContent = stats.total_melhorias;
                    document.querySelector('[data-stat="total_desejos"]').textContent = stats.total_desejos;
                    // ...adicione outros campos conforme necessário
                }
            } catch (error) {
                console.error('Erro ao atualizar estatísticas:', error);
            }
        }
    </script>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-800">Meu Relacionamento</h1>
            <p class="text-gray-600 mt-2">Acompanhe e desenvolva todos os aspectos do seu relacionamento</p>
        </div>

        <!-- Grid de cards do dashboard -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">

            <!-- Card 1: Reclamações -->
            <div class="bg-white rounded-xl shadow-lg border border-red-100 hover:shadow-xl transition-shadow duration-300">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center">
                            <div class="p-3 bg-red-100 rounded-lg">
                                <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 15.5c-.77.833.192 2.5 1.732 2.5z"></path>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <h3 class="text-lg font-semibold text-gray-800">Reclamações</h3>
                                <p class="text-sm text-gray-500">
                                    <span data-stat="reclamacoes">{{ $estatisticas['relacionamento']['reclamacoes'] }}</span> pontos de atenção
                                </p>
                            </div>
                        </div>
                    </div>
                    <p class="text-gray-600 text-sm mb-4">Identifique e trabalhe os pontos de atrito no relacionamento</p>
                    <div class="flex space-x-2">
                        <button onclick="toggleCardList('reclamacoes')" class="flex-1 bg-red-50 text-red-700 px-4 py-2 rounded-lg text-sm font-medium hover:bg-red-100 transition-colors duration-200">
                            Ver Lista
                        </button>
                        @if(empty($somenteLeitura))
                        <button onclick="openModal('reclamacoes', 'Reclamações', 'red')" class="px-4 py-2 bg-red-600 text-white rounded-lg text-sm font-medium hover:bg-red-700 transition-colors duration-200">
                            + Adicionar
                        </button>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Card 2: Pontos Positivos -->
            <div class="bg-white rounded-xl shadow-lg border border-green-100 hover:shadow-xl transition-shadow duration-300">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center">
                            <div class="p-3 bg-green-100 rounded-lg">
                                <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h1.01M15 10h1.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <h3 class="text-lg font-semibold text-gray-800">Pontos Positivos</h3>
                                <p class="text-sm text-gray-500">
                                    <span data-stat="positivos">{{ $estatisticas['relacionamento']['positivos'] }}</span> pontos de atenção
                                </p>
                            </div>
                        </div>
                    </div>
                    <p class="text-gray-600 text-sm mb-4">Celebre e valorize os aspectos positivos do relacionamento</p>
                    <div class="flex space-x-2">
                        <button onclick="toggleCardList('positivos')" class="flex-1 bg-green-50 text-green-700 px-4 py-2 rounded-lg text-sm font-medium hover:bg-green-100 transition-colors duration-200">
                            Ver Lista
                        </button>
                        @if(empty($somenteLeitura))
                        <button onclick="openModal('positivos', 'Ponto Positivo', 'green')" class="px-4 py-2 bg-green-600 text-white rounded-lg text-sm font-medium hover:bg-green-700 transition-colors duration-200">
                            + Adicionar
                        </button>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Card 3: Meus Desejos -->
            <div class="bg-white rounded-xl shadow-lg border border-blue-100 hover:shadow-xl transition-shadow duration-300">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center">
                            <div class="p-3 bg-blue-100 rounded-lg">
                                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <h3 class="text-lg font-semibold text-gray-800">Meus Desejos</h3>
                                <p class="text-sm text-gray-500">
                                    <span data-stat="meus_desejos">{{ $estatisticas['relacionamento']['meus_desejos'] }}</span> pontos de atenção
                                </p>
                            </div>
                        </div>
                    </div>
                    <p class="text-gray-600 text-sm mb-4">Seus objetivos e aspirações pessoais</p>
                    <div class="flex space-x-2">
                        <button onclick="toggleCardList('meus_desejos')" class="flex-1 bg-blue-50 text-blue-700 px-4 py-2 rounded-lg text-sm font-medium hover:bg-blue-100 transition-colors duration-200">
                            Ver Lista
                        </button>
                        @if(empty($somenteLeitura))
                        <button onclick="openModal('meus_desejos', 'Meus Desejos', 'blue')" class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700 transition-colors duration-200">
                            + Adicionar
                        </button>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Card 4: Nossos Desejos -->
            <div class="bg-white rounded-xl shadow-lg border border-purple-100 hover:shadow-xl transition-shadow duration-300">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center">
                            <div class="p-3 bg-purple-100 rounded-lg">
                                <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <h3 class="text-lg font-semibold text-gray-800">Nossos Desejos</h3>
                                <p class="text-sm text-gray-500">
                                    <span data-stat="nossos_desejos">{{ $estatisticas['relacionamento']['nossos_desejos'] }}</span> pontos de atenção
                                </p>
                            </div>
                        </div>
                    </div>
                    <p class="text-gray-600 text-sm mb-4">Planos e sonhos que vocês querem realizar juntos</p>
                    <div class="flex space-x-2">
                        <button onclick="toggleCardList('nossos_desejos')" class="flex-1 bg-purple-50 text-purple-700 px-4 py-2 rounded-lg text-sm font-medium hover:bg-purple-100 transition-colors duration-200">
                            Ver Lista
                        </button>
                        @if(empty($somenteLeitura))
                        <button onclick="openModal('nossos_desejos', 'Nossos Desejos', 'purple')" class="px-4 py-2 bg-purple-600 text-white rounded-lg text-sm font-medium hover:bg-purple-700 transition-colors duration-200">
                            + Adicionar
                        </button>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Card 5: Melhorar em Mim -->
            <div class="bg-white rounded-xl shadow-lg border border-yellow-100 hover:shadow-xl transition-shadow duration-300">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center">
                            <div class="p-3 bg-yellow-100 rounded-lg">
                                <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <h3 class="text-lg font-semibold text-gray-800">Melhorar em Mim</h3>
                                <p class="text-sm text-gray-500">
                                    <span data-stat="melhorar_mim">{{ $estatisticas['relacionamento']['melhorar_mim'] }}</span> pontos de atenção
                                </p>
                            </div>
                        </div>
                    </div>
                    <p class="text-gray-600 text-sm mb-4">Aspectos pessoais que você quer desenvolver</p>
                    <div class="flex space-x-2">
                        <button onclick="toggleCardList('melhorar_mim')" class="flex-1 bg-yellow-50 text-yellow-700 px-4 py-2 rounded-lg text-sm font-medium hover:bg-yellow-100 transition-colors duration-200">
                            Ver Lista
                        </button>
                        @if(empty($somenteLeitura))
                        <button onclick="openModal('melhorar_mim', 'Melhorar em Mim', 'green')" class="px-4 py-2 bg-yellow-600 text-white rounded-lg text-sm font-medium hover:bg-yellow-700 transition-colors duration-200">
                            + Adicionar
                        </button>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Card 6: Melhorar Juntos -->
            <div class="bg-white rounded-xl shadow-lg border border-emerald-100 hover:shadow-xl transition-shadow duration-300">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center">
                            <div class="p-3 bg-emerald-100 rounded-lg">
                                <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <h3 class="text-lg font-semibold text-gray-800">Melhorar Juntos</h3>
                                <p class="text-sm text-gray-500">
                                    <span data-stat="melhorar_juntos">{{ $estatisticas['relacionamento']['melhorar_juntos'] }}</span> pontos de atenção
                                </p>
                            </div>
                        </div>
                    </div>
                    <p class="text-gray-600 text-sm mb-4">Aspectos do relacionamento para desenvolver em conjunto</p>
                    <div class="flex space-x-2">
                        <button onclick="toggleCardList('melhorar_juntos')" class="flex-1 bg-emerald-50 text-emerald-700 px-4 py-2 rounded-lg text-sm font-medium hover:bg-emerald-100 transition-colors duration-200">
                            Ver Lista
                        </button>
                        @if(empty($somenteLeitura))
                        <button onclick="openModal('melhorar_juntos', 'Melhorar Juntos', 'green')" class="px-4 py-2 bg-emerald-600 text-white rounded-lg text-sm font-medium hover:bg-emerald-700 transition-colors duration-200">
                            + Adicionar
                        </button>
                        @endif
                    </div>
                </div>
            </div>
            <!-- Card 7: Registro de Sentimentos -->
            <div class="bg-white rounded-xl shadow-lg border border-orange-100 hover:shadow-xl transition-shadow duration-300">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center">
                            <div class="p-3 bg-orange-100 rounded-lg">
                                <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h1.01M15 10h1.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <h3 class="text-lg font-semibold text-gray-800">Meus Sentimentos</h3>
                                <p class="text-sm text-gray-500">
                                    <span data-stat="sentimentos">{{ $estatisticas['sentimentos']['total'] }}</span> registros hoje
                                </p>
                            </div>
                        </div>
                    </div>
                    <p class="text-gray-600 text-sm mb-4">Registre como você se sente no momento e acompanhe seus padrões emocionais</p>
                    <div class="mb-3">
                        <div class="text-xs text-gray-500 mb-1">Último sentimento:</div>
                        <div class="text-sm font-medium text-orange-700" id="ultimoSentimento" data-stat="ultimo_sentimento">
                            {{ $estatisticas['sentimentos']['ultimo'] ? ucfirst($estatisticas['sentimentos']['ultimo']->tipo_sentimento) . " • Intensidade " . $estatisticas['sentimentos']['ultimo']->nivel_intensidade . "/10" : 'Nenhum registro ainda' }}
                        </div>
                    </div>
                    <div class="flex space-x-2">
                        <a href="{{ route('historico') }}" class="flex-1 text-center bg-orange-50 text-orange-700 px-4 py-2 rounded-lg text-sm font-medium hover:bg-orange-100 transition-colors duration-200">
                            Ver Histórico
                        </a>
                        @if(empty($somenteLeitura))
                        <button onclick="openSentimentModal()" class="px-4 py-2 bg-orange-600 text-white rounded-lg text-sm font-medium hover:bg-orange-700 transition-colors duration-200">
                            Registrar Agora
                        </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Seção de estatísticas rápidas -->
        <div class="mt-12 bg-gradient-to-br from-emerald-50 to-teal-100 rounded-2xl p-8 border border-emerald-200">
            <h2 class="text-2xl font-bold text-gray-800 mb-4">Resumo do Relacionamento</h2>
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                <div class="text-center">
                    <span class="text-3xl font-bold text-emerald-600" data-stat="total_itens">{{ $estatisticas['relacionamento']['total_itens'] }}</span>
                    <div class="text-sm text-gray-600">Total de itens</div>
                </div>
                <div class="text-center">
                    <span class="text-3xl font-bold text-green-600" data-stat="positivos">{{ $estatisticas['relacionamento']['positivos'] }}</span>
                    <div class="text-sm text-gray-600">Pontos positivos</div>
                </div>
                <div class="text-center">
                    <span class="text-3xl font-bold text-yellow-600" data-stat="total_melhorias">{{ $estatisticas['relacionamento']['total_melhorias'] }}</span>
                    <div class="text-sm text-gray-600">Para melhorar</div>
                </div>
                <div class="text-center">
                    <span class="text-3xl font-bold text-purple-600" data-stat="total_desejos">{{ $estatisticas['relacionamento']['total_desejos'] }}</span>
                    <div class="text-sm text-gray-600">Desejos totais</div>
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

