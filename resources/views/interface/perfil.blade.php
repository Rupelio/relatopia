<x-dashboard-layout title="Perfil">
    <div class="min-h-[calc(100vh-4rem)] flex items-center justify-center py-12 px-4">
        <div class="max-w-2xl w-full">
            <!-- Card principal -->
            <div class="bg-white rounded-2xl shadow-xl border border-emerald-200 overflow-hidden">
                <!-- Header -->
                <div class="bg-gradient-to-r from-emerald-500 to-teal-600 px-8 py-6">
                    <h1 class="text-2xl font-bold text-white">Meu Perfil</h1>
                    <p class="text-emerald-100 mt-1">Gerencie suas informações e configurações</p>
                </div>

                <div class="p-8 space-y-8">
                    <!-- Seção: Segurança da Conta -->
                    <div>
                        <div class="flex items-center mb-4">
                            <div class="p-2 bg-emerald-100 rounded-lg mr-3">
                                <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                                </svg>
                            </div>
                            <h2 class="text-lg font-semibold text-gray-800">Segurança da Conta</h2>
                        </div>

                        <div class="bg-gray-50 rounded-lg p-6 space-y-4">
                            <!-- Email atual -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Email atual</label>
                                <div class="bg-white border border-gray-300 rounded-lg px-4 py-3 text-gray-600">
                                    {{ auth()->user()->email }}
                                </div>
                            </div>

                            <!-- Alterar senha -->
                            <form id="changePasswordForm" class="space-y-4">
                                @csrf
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Senha atual</label>
                                    <input type="password"
                                        name="current_password"
                                        id="current_password"
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all duration-200"
                                        placeholder="Digite sua senha atual"
                                        required>
                                    <div id="current_password_error" class="text-red-500 text-sm mt-1 hidden"></div>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Nova senha</label>
                                    <input type="password" name="new_password" id="new_password" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all duration-200" placeholder="Digite sua nova senha" required>
                                    <div id="new_password_error" class="text-red-500 text-sm mt-1 hidden"></div>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Confirmar nova senha</label>
                                    <input type="password"
                                        name="new_password_confirmation"
                                        id="new_password_confirmation"
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all duration-200"
                                        placeholder="Confirme sua nova senha"
                                        required>
                                    <div id="confirm_password_error" class="text-red-500 text-sm mt-1 hidden"></div>
                                </div>

                                <button type="submit"
                                        id="changePasswordBtn"
                                        class="bg-emerald-600 text-white px-6 py-2 rounded-lg hover:bg-emerald-700 transition-colors duration-200 font-medium disabled:opacity-50 disabled:cursor-not-allowed">
                                    <span id="btnText">Alterar Senha</span>
                                    <span id="btnLoading" class="hidden">
                                        <svg class="animate-spin -ml-1 mr-3 h-4 w-4 text-white inline" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                        </svg>
                                        Alterando...
                                    </span>
                                </button>
                            </form>
                        </div>
                    </div>

                    <!-- Divisor -->
                    <div class="border-t border-gray-200"></div>

                    <!-- Seção: Relacionamento -->
                    <div>
                        <div class="flex items-center mb-4">
                            <div class="p-2 bg-pink-100 rounded-lg mr-3">
                                <svg class="w-5 h-5 text-pink-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                                </svg>
                            </div>
                            <h2 class="text-lg font-semibold text-gray-800">Relacionamento</h2>
                        </div>

                        <div class="bg-gray-50 rounded-lg p-6 space-y-4">
                            <!-- Data de início -->
                            <form id="relationshipForm" class="space-y-4">
                                @csrf
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Data de início do relacionamento</label>
                                    <input type="date"
                                        name="data_inicio"
                                        value="{{ auth()->user()->data_inicio_relacionamento?->format('Y-m-d') }}"
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all duration-200">
                                </div>

                                <!-- Status do relacionamento -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                                    <select name="status"
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all duration-200">
                                        <option value="">Selecione o status</option>
                                        <option value="namoro" {{ auth()->user()->status_relacionamento == 'namoro' ? 'selected' : '' }}>Namoro</option>
                                        <option value="noivado" {{ auth()->user()->status_relacionamento == 'noivado' ? 'selected' : '' }}>Noivado</option>
                                        <option value="casamento" {{ auth()->user()->status_relacionamento == 'casamento' ? 'selected' : '' }}>Casamento</option>
                                        <option value="uniao-estavel" {{ auth()->user()->status_relacionamento == 'uniao-estavel' ? 'selected' : '' }}>União Estável</option>
                                    </select>
                                </div>

                                <button type="submit" class="bg-pink-600 text-white px-6 py-2 rounded-lg hover:bg-pink-700 transition-colors duration-200 font-medium">
                                    Salvar Informações
                                </button>
                            </form>
                        </div>
                    </div>

                    <!-- Divisor -->
                    <div class="border-t border-gray-200"></div>

                    <!-- Seção: Preferências -->
                    <div>
                        <div class="flex items-center mb-4">
                            <div class="p-2 bg-yellow-100 rounded-lg mr-3">
                                <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                </svg>
                            </div>
                            <h2 class="text-lg font-semibold text-gray-800">Preferências</h2>
                        </div>

                        <div class="bg-gray-50 rounded-lg p-6">
                            <!-- Zona de perigo -->
                            <div class="border-2 border-red-200 rounded-lg p-4 bg-red-50">
                                <h3 class="text-red-800 font-medium mb-2">Zona de Perigo</h3>
                                <p class="text-red-600 text-sm mb-4">Esta ação não pode ser desfeita. Todos os seus dados serão permanentemente removidos.</p>
                                <button onclick="confirmDelete()" class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 transition-colors duration-200 font-medium">
                                    Excluir Conta
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('changePasswordForm');
            const currentPasswordInput = document.getElementById('current_password');
            const newPasswordInput = document.getElementById('new_password');
            const confirmPasswordInput = document.getElementById('new_password_confirmation');
            const submitBtn = document.getElementById('changePasswordBtn');

            newPasswordInput.addEventListener('input', validateNewPassword);
            confirmPasswordInput.addEventListener('input', validatePasswordConfirmation);

            function validateNewPassword(){
                const password = newPasswordInput.value;
                const errorDiv = document.getElementById('new_password_error');

                if(password.length > 0 && password.length < 6){
                    showError('new_password_error', 'A senha deve ter pelo menos 6 caracteres');
                    return false;
                } else {
                    hideError('new_password_error');
                    return true;
                }
            }

            function validatePasswordConfirmation(){
                const password = newPasswordInput.value;
                const confirmation = confirmPasswordInput.value;

                if(confirmation.length > 0 && password !== confirmation){
                    showError('confirm_password_error', 'As senhas não coincidem');
                    return false;
                } else {
                    hideError('confirm_password_error');
                    return true;
                }
            }

            function showError(elementId, message){
                const errorDiv = document.getElementById(elementId);
                errorDiv.textContent = message;
                errorDiv.classList.remove('hidden');

                const input = errorDiv.previousElementSibling;
                input.classList.add('border-red-500');
                input.classList.remove('border-gray-300');
            }

            function hideError(elementId){
                const errorDiv = document.getElementById(elementId);
                errorDiv.classList.add('hidden');
                errorDiv.textContent = '';

                const input = errorDiv.previousElementSibling;
                input.classList.remove('border-red-500');
                input.classList.add('border-gray-300');
            }

            form.addEventListener('submit', async function (e) {
                e.preventDefault();

                if(!validateNewPassword() || !validatePasswordConfirmation()){
                    return;
                }

                const currentPassword = currentPasswordInput.value;
                const newPassword = newPasswordInput.value;
                const confirmPassword = confirmPasswordInput.value;

                if(!currentPassword || !newPassword || !confirmPassword){
                    showNotification('Por favor, preencha todos os campos', 'warning');
                    return;
                }

                setLoading(true);

                try{
                    const response = await fetch('/api/alterar-senha', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify({
                            current_password: currentPassword,
                            new_password: newPassword,
                            new_password_confirmation: confirmPassword
                        })
                    });

                    const data = await response.json();

                    if(response.ok){
                        showNotification('Senha alterada com sucesso!', 'success');
                        form.reset();
                        hideError('current_password_error');
                        hideError('new_password_error');
                        hideError('confirm_password_error');
                    } else {
                        if(data.errors){
                            if(data.errors.current_password){
                                showError('current_password_error', data.errors.current_password[0])
                            }
                            if (data.errors.new_password) {
                                showError('new_password_error', data.errors.new_password[0]);
                            }
                        } else if(data.message){
                            showNotification(data.message, 'error');
                        } else {
                            showNotification('Erro ao alterar senha. Tente novamente.', 'error');
                        }
                    }
                } catch(error){
                    console.error('Erro:', error);
                    showNotification('Erro de conexão. Verifique sua internet e tente novamente.', 'error')
                } finally{
                    setLoading(false);
                }
            });
            function setLoading(loading){
                const btnText = document.getElementById('btnText');
                const btnLoading = document.getElementById('btnLoading');

                if (loading) {
                    btnText.classList.add('hidden');
                    btnLoading.classList.remove('hidden');
                    submitBtn.disabled = true;
                } else {
                    btnText.classList.remove('hidden');
                    btnLoading.classList.add('hidden');
                    submitBtn.disabled = false;
                }
            }
        })
        document.getElementById('relationshipForm').addEventListener('submit', async function (e) {
            e.preventDefault();
            const formData = new FormData(this);
            try{
                const response = await fetch('/api/relacionamento',{
                    method: 'POST',
                    headers:{
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: formData
                });
                if(response.ok){
                    showNotification('Informações salvas com sucesso!', 'success');
                } else {
                    const data = await response.json();
                    showNotification(data.message || 'Erro ao salvar', 'error');
                }
            } catch(error){
                showNotification('Erro ao salvar informações', 'error')
            }
        });
        function confirmDelete() {
            if (confirm('Tem certeza que deseja excluir sua conta? Esta ação não pode ser desfeita e todos os seus dados serão perdidos.')) {
                if (confirm('Última confirmação: Realmente deseja excluir sua conta permanentemente?')) {
                    // Aqui faria a requisição para excluir a conta
                    showNotification('Ainda está sendo desenvolvida', 'warning');
                }
            }
        }
    </script>
</x-dashboard-layout>
