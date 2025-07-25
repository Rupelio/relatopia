<x-dashboard-layout title="Configurar Relacionamento">
    <div class="min-h-[calc(100vh-4rem)] flex items-center justify-center py-12 px-4">
        <div class="max-w-3xl w-full animate-bounce-in">
            <!-- Card principal -->
            <div class="bg-white rounded-2xl shadow-2xl border-2 border-emerald-200 overflow-hidden">
                <!-- Header -->
                <div class="bg-gradient-to-br from-emerald-500 via-emerald-600 to-teal-600 px-8 py-8 relative overflow-hidden">
                    <div class="absolute inset-0 bg-gradient-to-r from-white/10 to-transparent animate-pulse-gentle"></div>
                    <div class="relative text-center">
                        <div class="inline-flex items-center justify-center w-20 h-20 bg-white/20 backdrop-blur rounded-full mb-4">
                            <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                            </svg>
                        </div>
                        <h1 class="text-4xl font-bold text-white mb-2">Bem-vindo ao Relatópia!</h1>
                        <p class="text-emerald-100 text-lg">Vamos configurar seu relacionamento em apenas alguns passos</p>
                    </div>
                </div>

                <div class="p-8">
                    <!-- Progress Bar -->
                    <div class="mb-8">
                        <div class="flex items-center justify-between mb-4">
                            <span class="text-sm font-medium text-gray-600">Progresso</span>
                            <span class="text-sm font-medium text-emerald-600" id="progress-text">Passo 1 de 3</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-3">
                            <div class="bg-gradient-to-r from-emerald-400 to-teal-500 h-3 rounded-full transition-all duration-500"
                                 id="progress-bar" style="width: 33%">
                            </div>
                        </div>
                    </div>

                    <!-- Step 1: Informações do Relacionamento -->
                    <div id="step-1" class="step">
                        <div class="text-center mb-8">
                            <div class="inline-flex items-center justify-center w-16 h-16 bg-pink-100 rounded-full mb-4">
                                <svg class="w-8 h-8 text-pink-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                                </svg>
                            </div>
                            <h2 class="text-2xl font-bold text-gray-800 mb-2">Conte-nos sobre seu relacionamento</h2>
                            <p class="text-gray-600">Essas informações nos ajudam a personalizar sua experiência</p>
                        </div>

                        <form id="relationship-form" class="space-y-6">
                            @csrf
                            <div class="grid md:grid-cols-2 gap-6">
                                <!-- Data de início -->
                                <div>
                                    <label class="block text-sm font-bold text-gray-700 mb-3">
                                        <svg class="w-4 h-4 inline mr-2 text-pink-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                        </svg>
                                        Quando começaram?
                                    </label>
                                    <input type="date"
                                           name="data_inicio"
                                           id="data_inicio"
                                           class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all duration-200"
                                           max="{{ date('Y-m-d') }}"
                                           required>
                                    <p class="text-xs text-gray-500 mt-1">A data que vocês oficializaram o relacionamento</p>
                                </div>

                                <!-- Status do relacionamento -->
                                <div>
                                    <label class="block text-sm font-bold text-gray-700 mb-3">
                                        <svg class="w-4 h-4 inline mr-2 text-pink-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                        </svg>
                                        Status atual
                                    </label>
                                    <select name="status"
                                            id="status"
                                            class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all duration-200"
                                            required>
                                        <option value="">Selecione o status</option>
                                        <option value="namoro">💕 Namoro</option>
                                        <option value="noivado">💍 Noivado</option>
                                        <option value="casamento">👰‍♀️ Casamento</option>
                                        <option value="uniao-estavel">🏠 União Estável</option>
                                    </select>
                                    <p class="text-xs text-gray-500 mt-1">Você pode alterar isso depois</p>
                                </div>
                            </div>

                            <div class="flex justify-end">
                                <button type="button"
                                        onclick="nextStep(2)"
                                        class="px-8 py-3 bg-gradient-to-r from-emerald-500 to-emerald-600 text-white rounded-xl font-semibold hover:from-emerald-600 hover:to-emerald-700 transition-all duration-200 transform hover:scale-105 shadow-lg hover:shadow-xl">
                                    Continuar
                                    <svg class="w-4 h-4 inline ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                    </svg>
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- Step 2: Convitar Parceiro -->
                    <div id="step-2" class="step hidden">
                        <div class="text-center mb-8">
                            <div class="inline-flex items-center justify-center w-16 h-16 bg-blue-100 rounded-full mb-4">
                                <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                            </div>
                            <h2 class="text-2xl font-bold text-gray-800 mb-2">Convide seu parceiro(a)</h2>
                            <p class="text-gray-600">Compartilhem juntos essa jornada de crescimento no relacionamento</p>
                        </div>

                        <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-2xl p-6 border-2 border-blue-200 mb-6">
                            <div class="flex items-start space-x-4">
                                <div class="flex-shrink-0">
                                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="text-lg font-semibold text-blue-800 mb-2">Como funciona o convite?</h3>
                                    <ul class="text-blue-700 text-sm space-y-1">
                                        <li>• Se a pessoa já tem conta, receberá um convite imediato</li>
                                        <li>• Se não tem conta, receberá um email para se cadastrar</li>
                                        <li>• Vocês compartilharão os mesmos dados do relacionamento</li>
                                        <li>• Ambos podem adicionar e visualizar informações</li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <form id="invite-form" class="space-y-6">
                            @csrf
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-3">
                                    <svg class="w-4 h-4 inline mr-2 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"></path>
                                    </svg>
                                    Email do seu parceiro(a)
                                </label>
                                <input type="email"
                                       name="email_parceiro"
                                       id="email_parceiro"
                                       class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all duration-200"
                                       placeholder="exemplo@email.com">
                                <p class="text-xs text-gray-500 mt-1">Digite o email da pessoa que você quer convidar</p>
                                <div id="email-error" class="text-red-500 text-sm mt-1 hidden"></div>
                            </div>

                            <div class="flex justify-between">
                                <button type="button"
                                        onclick="prevStep(1)"
                                        class="px-6 py-3 border-2 border-gray-200 text-gray-600 rounded-xl font-semibold hover:bg-gray-50 transition-all duration-200">
                                    <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                                    </svg>
                                    Voltar
                                </button>

                                <div class="space-x-3">
                                    <button type="button"
                                            onclick="skipInvite()"
                                            class="px-6 py-3 border-2 border-gray-200 text-gray-600 rounded-xl font-semibold hover:bg-gray-50 transition-all duration-200">
                                        Pular por agora
                                    </button>
                                    <button type="button"
                                            onclick="sendInvite()"
                                            class="px-8 py-3 bg-gradient-to-r from-blue-500 to-blue-600 text-white rounded-xl font-semibold hover:from-blue-600 hover:to-blue-700 transition-all duration-200 transform hover:scale-105 shadow-lg hover:shadow-xl">
                                        Enviar Convite
                                        <svg class="w-4 h-4 inline ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>

                    <!-- Step 3: Finalização -->
                    <div id="step-3" class="step hidden">
                        <div class="text-center">
                            <div class="inline-flex items-center justify-center w-20 h-20 bg-green-100 rounded-full mb-6">
                                <svg class="w-10 h-10 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                            </div>
                            <h2 class="text-3xl font-bold text-gray-800 mb-4">Tudo pronto! 🎉</h2>
                            <p class="text-gray-600 text-lg mb-8">Seu relacionamento foi configurado com sucesso!</p>

                            <div class="bg-gradient-to-br from-emerald-50 to-emerald-100 rounded-2xl p-6 border-2 border-emerald-200 mb-8">
                                <h3 class="text-lg font-semibold text-emerald-800 mb-4">Próximos passos:</h3>
                                <div class="grid md:grid-cols-2 gap-4 text-left">
                                    <div class="flex items-start space-x-3">
                                        <div class="w-6 h-6 bg-emerald-500 rounded-full flex items-center justify-center flex-shrink-0 mt-0.5">
                                            <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                            </svg>
                                        </div>
                                        <span class="text-emerald-700 text-sm">Explore o dashboard e comece a registrar aspectos do relacionamento</span>
                                    </div>
                                    <div class="flex items-start space-x-3">
                                        <div class="w-6 h-6 bg-emerald-500 rounded-full flex items-center justify-center flex-shrink-0 mt-0.5">
                                            <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                            </svg>
                                        </div>
                                        <span class="text-emerald-700 text-sm">Registre seus sentimentos diários</span>
                                    </div>
                                    <div class="flex items-start space-x-3">
                                        <div class="w-6 h-6 bg-emerald-500 rounded-full flex items-center justify-center flex-shrink-0 mt-0.5">
                                            <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                            </svg>
                                        </div>
                                        <span class="text-emerald-700 text-sm">Adicione pontos positivos e aspectos a melhorar</span>
                                    </div>
                                    <div class="flex items-start space-x-3">
                                        <div class="w-6 h-6 bg-emerald-500 rounded-full flex items-center justify-center flex-shrink-0 mt-0.5">
                                            <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                            </svg>
                                        </div>
                                        <span class="text-emerald-700 text-sm">Defina sonhos e desejos juntos</span>
                                    </div>
                                </div>
                            </div>

                            <button type="button"
                                    onclick="finishOnboarding()"
                                    class="px-12 py-4 bg-gradient-to-r from-emerald-500 to-emerald-600 text-white rounded-xl text-lg font-semibold hover:from-emerald-600 hover:to-emerald-700 transition-all duration-200 transform hover:scale-105 shadow-lg hover:shadow-xl">
                                Começar a usar o Relatópia
                                <svg class="w-5 h-5 inline ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        let currentStep = 1;
        let relationshipData = {};

        function updateProgress(step) {
            const progressBar = document.getElementById('progress-bar');
            const progressText = document.getElementById('progress-text');
            const percentage = (step / 3) * 100;

            progressBar.style.width = percentage + '%';
            progressText.textContent = `Passo ${step} de 3`;
        }

        function showStep(step) {
            // Esconder todos os steps
            document.querySelectorAll('.step').forEach(el => el.classList.add('hidden'));
            // Mostrar step atual
            document.getElementById(`step-${step}`).classList.remove('hidden');
            // Atualizar progresso
            updateProgress(step);
            currentStep = step;
        }

        function nextStep(step) {
            if (step === 2) {
                // Validar step 1
                const dataInicio = document.getElementById('data_inicio').value;
                const status = document.getElementById('status').value;

                if (!dataInicio || !status) {
                    showNotification('Por favor, preencha todos os campos obrigatórios', 'warning');
                    return;
                }

                // Salvar dados do relacionamento
                relationshipData.data_inicio = dataInicio;
                relationshipData.status = status;
            }

            showStep(step);
        }

        function prevStep(step) {
            showStep(step);
        }

        function validateEmail(email) {
            // Verificar se não é o próprio email
            const userEmail = '{{ auth()->user()->email }}';
            if (email.toLowerCase() === userEmail.toLowerCase()) {
                document.getElementById('email-error').textContent = 'Você não pode convidar a si mesmo';
                document.getElementById('email-error').classList.remove('hidden');
                return false;
            }

            document.getElementById('email-error').classList.add('hidden');
            return true;
        }

        async function sendInvite() {
            const email = document.getElementById('email_parceiro').value.trim();

            if (!email) {
                showNotification('Por favor, digite um email', 'warning');
                return;
            }

            if (!validateEmail(email)) {
                return;
            }

            try {
                setButtonLoading('send-invite-btn', true);

                // Primeiro salvar dados do relacionamento
                await saveRelationshipData();

                // Depois enviar convite
                const response = await fetch('/api/vincular-coparticipante', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        email: email,
                        from_onboarding: true
                    })
                });

                const data = await response.json();

                if (response.ok) {
                    showNotification(data.message || 'Convite enviado com sucesso!', 'success');
                    nextStep(3);
                } else {
                    showNotification(data.message || 'Erro ao enviar convite', 'error');
                }
            } catch (error) {
                console.error('Erro:', error);
                showNotification('Erro ao enviar convite', 'error');
            } finally {
                setButtonLoading('send-invite-btn', false);
            }
        }

        async function skipInvite() {
            try {
                await saveRelationshipData();
                nextStep(3);
            } catch (error) {
                console.error('Erro:', error);
                showNotification('Erro ao salvar dados', 'error');
            }
        }

        async function saveRelationshipData() {
            const formData = new FormData();
            formData.append('data_inicio', relationshipData.data_inicio);
            formData.append('status', relationshipData.status);
            formData.append('_token', document.querySelector('meta[name="csrf-token"]').content);

            const response = await fetch('/api/relacionamento', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: formData
            });

            if (!response.ok) {
                throw new Error('Erro ao salvar dados do relacionamento');
            }
        }

        async function finishOnboarding() {
            try {
                const response = await fetch('/api/finalizar-onboarding', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });

                if (response.ok) {
                    window.location.href = '/dashboard';
                } else {
                    showNotification('Erro ao finalizar configuração', 'error');
                }
            } catch (error) {
                console.error('Erro:', error);
                showNotification('Erro ao finalizar configuração', 'error');
            }
        }

        // Validação de email em tempo real
        document.getElementById('email_parceiro').addEventListener('input', function() {
            const email = this.value.trim();
            if (email) {
                validateEmail(email);
            }
        });

        function setButtonLoading(buttonId, loading) {
            // Helper function for button loading state
            // This would need the actual button implementation
            console.log('Loading state:', loading);
        }

        // Function to show notifications (placeholder - you should implement this)
        function showNotification(message, type) {
            // Create a simple notification
            const notification = document.createElement('div');
            notification.className = `fixed top-4 right-4 px-6 py-3 rounded-lg text-white z-50 ${
                type === 'success' ? 'bg-green-500' :
                type === 'error' ? 'bg-red-500' :
                type === 'warning' ? 'bg-yellow-500' : 'bg-blue-500'
            }`;
            notification.textContent = message;

            document.body.appendChild(notification);

            setTimeout(() => {
                notification.remove();
            }, 5000);
        }
    </script>
</x-dashboard-layout>
