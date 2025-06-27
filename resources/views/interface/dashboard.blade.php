<x-dashboard-layout title="Dashboard">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script>
        let currentCategory = '';
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

        // Função para remover notificação
        function removeNotification(notificationId) {
            const notification = document.getElementById(notificationId);
            if (notification) {
                notification.classList.add('translate-x-full', 'opacity-0');
                setTimeout(() => {
                    notification.remove();
                }, 300);
            }
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
                    await atualizarEstatisticas();
                } else {
                    const error = await response.json();
                    showNotification('Erro ao salvar: ' + (error.message || 'Algo deu errado'), 'error');
                }
            } catch(error){
                console.error('Erro:', error);
                showNotification('Erro de conexão. Verifique sua internet e tente novamente.', 'error');
            }
        }
        async function atualizarEstatisticas() {
            try {
                const response = await fetch('/api/estatisticas');
                const estatisticas = await response.json();

                document.querySelector('[data-stat="reclamacoes"]').textContent = estatisticas.reclamacoes;
                document.querySelector('[data-stat="positivos"]').textContent = estatisticas.positivos;
                document.querySelector('[data-stat="meus_desejos"]').textContent = estatisticas.meus_desejos;
                document.querySelector('[data-stat="nossos_desejos"]').textContent = estatisticas.nossos_desejos;
                document.querySelector('[data-stat="melhorar_mim"]').textContent = estatisticas.melhorar_mim;
                document.querySelector('[data-stat="melhorar_juntos"]').textContent = estatisticas.melhorar_juntos;

            } catch (error) {

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
                                        <button onclick="toggleItem(${item.id})" class="${toggleButtonColor} text-xs" title="${toggleTitle}">
                                            ${toggleIcon}
                                        </button>
                                        <button onclick="removeItem(${item.id})" class="text-red-500 hover:text-red-700 text-xs" title="Remover item">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                        </button>
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

        async function toggleItem(itemId) {
            try {
                const response = await fetch(`/api/relacionamento-itens/${itemId}/toggle`, {
                    method: 'PUT',
                    headers:{
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });
                if(response.ok){
                    const openList = document.querySelector('.card-list');
                    if(openList){
                        const button = openList.closest('.bg-white').querySelector('button[onclick*="toggleCardList"]');
                        button.click();
                        setTimeout(() => button.click(), 100);
                    }
                    await atualizarEstatisticas();
                    showNotification('Status alterado com sucesso!', 'success');
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
                        await atualizarEstatisticas();
                        showNotification('Item removido com sucesso!', 'success');
                    }
                } catch (error) {
                    console.error('Erro ao remover item:', error);
                    showNotification('Erro ao remover item. Tente novamente.', 'error');
                }
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
                                    <span data-stat="reclamacoes">{{ $estatisticas['reclamacoes'] }}</span> pontos de atenção
                                </p>
                            </div>
                        </div>
                    </div>
                    <p class="text-gray-600 text-sm mb-4">Identifique e trabalhe os pontos de atrito no relacionamento</p>
                    <div class="flex space-x-2">
                        <button onclick="toggleCardList('reclamacoes')" class="flex-1 bg-red-50 text-red-700 px-4 py-2 rounded-lg text-sm font-medium hover:bg-red-100 transition-colors duration-200">
                            Ver Lista
                        </button>
                        <button onclick="openModal('reclamacoes', 'Reclamações', 'red')" class="px-4 py-2 bg-red-600 text-white rounded-lg text-sm font-medium hover:bg-red-700 transition-colors duration-200">
                            + Adicionar
                        </button>
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
                                    <span data-stat="positivos">{{ $estatisticas['positivos'] }}</span> pontos de atenção
                                </p>
                            </div>
                        </div>
                    </div>
                    <p class="text-gray-600 text-sm mb-4">Celebre e valorize os aspectos positivos do relacionamento</p>
                    <div class="flex space-x-2">
                        <button onclick="toggleCardList('positivos')" class="flex-1 bg-green-50 text-green-700 px-4 py-2 rounded-lg text-sm font-medium hover:bg-green-100 transition-colors duration-200">
                            Ver Lista
                        </button>
                        <button onclick="openModal('positivos', 'Ponto Positivo', 'green')" class="px-4 py-2 bg-green-600 text-white rounded-lg text-sm font-medium hover:bg-green-700 transition-colors duration-200">
                            + Adicionar
                        </button>
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
                                    <span data-stat="meus_desejos">{{ $estatisticas['meus_desejos'] }}</span> pontos de atenção
                                </p>
                            </div>
                        </div>
                    </div>
                    <p class="text-gray-600 text-sm mb-4">Seus objetivos e aspirações pessoais</p>
                    <div class="flex space-x-2">
                        <button onclick="toggleCardList('meus_desejos')" class="flex-1 bg-blue-50 text-blue-700 px-4 py-2 rounded-lg text-sm font-medium hover:bg-blue-100 transition-colors duration-200">
                            Ver Lista
                        </button>
                        <button onclick="openModal('meus_desejos', 'Meus Desejos', 'blue')" class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700 transition-colors duration-200">
                            + Adicionar
                        </button>
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
                                    <span data-stat="nossos_desejos">{{ $estatisticas['nossos_desejos'] }}</span> pontos de atenção
                                </p>
                            </div>
                        </div>
                    </div>
                    <p class="text-gray-600 text-sm mb-4">Planos e sonhos que vocês querem realizar juntos</p>
                    <div class="flex space-x-2">
                        <button onclick="toggleCardList('nossos_desejos')" class="flex-1 bg-purple-50 text-purple-700 px-4 py-2 rounded-lg text-sm font-medium hover:bg-purple-100 transition-colors duration-200">
                            Ver Lista
                        </button>
                        <button onclick="openModal('nossos_desejos', 'Nossos Desejos', 'purple')" class="px-4 py-2 bg-purple-600 text-white rounded-lg text-sm font-medium hover:bg-purple-700 transition-colors duration-200">
                            + Adicionar
                        </button>
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
                                    <span data-stat="melhorar_mim">{{ $estatisticas['melhorar_mim'] }}</span> pontos de atenção
                                </p>
                            </div>
                        </div>
                    </div>
                    <p class="text-gray-600 text-sm mb-4">Aspectos pessoais que você quer desenvolver</p>
                    <div class="flex space-x-2">
                        <button onclick="toggleCardList('melhorar_mim')" class="flex-1 bg-yellow-50 text-yellow-700 px-4 py-2 rounded-lg text-sm font-medium hover:bg-yellow-100 transition-colors duration-200">
                            Ver Lista
                        </button>
                        <button onclick="openModal('melhorar_mim', 'Melhorar em Mim', 'green')" class="px-4 py-2 bg-yellow-600 text-white rounded-lg text-sm font-medium hover:bg-yellow-700 transition-colors duration-200">
                            + Adicionar
                        </button>
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
                                    <span data-stat="melhorar_juntos">{{ $estatisticas['melhorar_juntos'] }}</span> pontos de atenção
                                </p>
                            </div>
                        </div>
                    </div>
                    <p class="text-gray-600 text-sm mb-4">Aspectos do relacionamento para desenvolver em conjunto</p>
                    <div class="flex space-x-2">
                        <button onclick="toggleCardList('melhorar_juntos')" class="flex-1 bg-emerald-50 text-emerald-700 px-4 py-2 rounded-lg text-sm font-medium hover:bg-emerald-100 transition-colors duration-200">
                            Ver Lista
                        </button>
                        <button onclick="openModal('melhorar_juntos', 'Melhorar Juntos', 'green')" class="px-4 py-2 bg-emerald-600 text-white rounded-lg text-sm font-medium hover:bg-emerald-700 transition-colors duration-200">
                            + Adicionar
                        </button>
                    </div>
                </div>
            </div>

        </div>

        <!-- Seção de estatísticas rápidas -->
        <div class="mt-12 bg-gradient-to-br from-emerald-50 to-teal-100 rounded-2xl p-8 border border-emerald-200">
            <h2 class="text-2xl font-bold text-gray-800 mb-4">Resumo do Relacionamento</h2>
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                <div class="text-center">
                    <div class="text-3xl font-bold text-emerald-600">{{ $estatisticas['total_itens'] }}</div>
                    <div class="text-sm text-gray-600">Total de itens</div>
                </div>
                <div class="text-center">
                    <div class="text-3xl font-bold text-green-600">{{ $estatisticas['positivos'] }}</div>
                    <div class="text-sm text-gray-600">Pontos positivos</div>
                </div>
                <div class="text-center">
                    <div class="text-3xl font-bold text-yellow-600">{{ $estatisticas['total_melhorias'] }}</div>
                    <div class="text-sm text-gray-600">Para melhorar</div>
                </div>
                <div class="text-center">
                    <div class="text-3xl font-bold text-purple-600">{{ $estatisticas['total_desejos'] }}</div>
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


