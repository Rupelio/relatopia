/**
 * Tour Guiado do Relatópia
 * Sistema inteligente de tutorial para novos usuários
 *
 * @author Rupelio
 * @version 1.0.0
 */

class RelatopiaGuide {
    constructor() {
        this.tour = null;
        this.isInitialized = false;
        this.checkAutoStartDelay = 3000; // 3 segundos após carregamento
        this.userPreferences = this.loadUserPreferences();
    }

    /**
     * Carrega preferências do usuário do localStorage
     */
    loadUserPreferences() {
        try {
            return {
                tourCompleted: localStorage.getItem('relatopia_tour_completed') === 'true',
                tourDate: localStorage.getItem('relatopia_tour_date'),
                autoStartDisabled: localStorage.getItem('relatopia_tour_auto_disabled') === 'true',
                language: localStorage.getItem('relatopia_language') || 'pt-BR'
            };
        } catch (error) {
            console.warn('Erro ao carregar preferências do tour:', error);
            return {
                tourCompleted: false,
                tourDate: null,
                autoStartDisabled: false,
                language: 'pt-BR'
            };
        }
    }

    /**
     * Salva preferências do usuário
     */
    saveUserPreferences(preferences) {
        try {
            Object.keys(preferences).forEach(key => {
                if (key === 'tourCompleted' || key === 'autoStartDisabled') {
                    localStorage.setItem(`relatopia_tour_${key.replace('tour', '').toLowerCase()}`, preferences[key].toString());
                } else {
                    localStorage.setItem(`relatopia_${key}`, preferences[key]);
                }
            });
        } catch (error) {
            console.warn('Erro ao salvar preferências do tour:', error);
        }
    }

    /**
     * Inicializa o tour guiado
     */
    initialize() {
        if (this.isInitialized) return;

        if (!window.Shepherd) {
            console.error('Shepherd.js não está carregado. Tour guiado não disponível.');
            return;
        }

        this.isInitialized = true;

        // Auto-start para novos usuários
        this.checkAutoStart();
    }

    /**
     * Verifica se deve iniciar automaticamente o tour
     */
    checkAutoStart() {
        // Só funciona no dashboard
        if (!window.location.pathname.includes('/dashboard')) return;

        // Verificar se já foi completado ou desabilitado
        if (this.userPreferences.tourCompleted || this.userPreferences.autoStartDisabled) return;

        // Aguardar elementos carregarem
        setTimeout(() => {
            if (this.hasRequiredElements()) {
                this.suggestTour();
            }
        }, this.checkAutoStartDelay);
    }

    /**
     * Verifica se os elementos necessários para o tour estão presentes
     */
    hasRequiredElements() {
        const requiredElements = [
            '[data-tour="sentimentos"]',
            '[data-tour="lista-desejos"]',
            '[data-tour="melhorias"]'
        ];

        return requiredElements.every(selector => document.querySelector(selector) !== null);
    }

    /**
     * Sugere o tour para o usuário
     */
    suggestTour() {
        if (window.showNotification) {
            window.showNotification('Novo aqui? Que tal um tour guiado? 🎯', 'info', 8000);
        }

        setTimeout(() => {
            if (confirm('🎉 Bem-vindo ao Relatópia!\n\nGostaria de fazer um tour guiado para conhecer as principais funcionalidades?\n\n(Você pode fazer isso depois clicando em "Tour" no menu)')) {
                this.startTour();
            } else {
                // Usuário recusou, não sugerir novamente nesta sessão
                this.userPreferences.autoStartDisabled = true;
                this.saveUserPreferences(this.userPreferences);
            }
        }, 2000);
    }

    /**
     * Inicia o tour guiado
     */
    startTour() {
        if (this.tour) {
            this.tour.cancel();
        }

        this.tour = new Shepherd.Tour({
            useModalOverlay: true,
            defaultStepOptions: {
                classes: 'shepherd-theme-arrows',
                scrollTo: { behavior: 'smooth', block: 'center' },
                cancelIcon: {
                    enabled: true
                },
                modalOverlayOpeningPadding: 8,
                modalOverlayOpeningRadius: 8
            }
        });

        this.addTourSteps();
        this.attachTourEvents();

        // Verificar se estamos no dashboard
        if (!window.location.pathname.includes('/dashboard')) {
            if (window.showNotification) {
                window.showNotification('Redirecionando para o dashboard...', 'info');
            }
            setTimeout(() => {
                window.location.href = '/dashboard';
            }, 1500);
            return;
        }

        this.tour.start();
    }

    /**
     * Adiciona os passos do tour
     */
    addTourSteps() {
        // Passo 1: Boas-vindas
        this.tour.addStep({
            title: '🎉 Bem-vindo ao Relatópia!',
            text: this.getStepContent('welcome'),
            attachTo: { element: 'body', on: 'top' },
            buttons: [
                { text: 'Começar Tour', action: this.tour.next, classes: 'shepherd-button' },
                { text: 'Pular', action: this.tour.cancel, classes: 'shepherd-button shepherd-button-secondary' }
            ]
        });

        // Passo 2: Sentimentos
        this.tour.addStep({
            title: '😊 Registre seus Sentimentos',
            text: this.getStepContent('sentimentos'),
            attachTo: { element: '[data-tour="sentimentos"]', on: 'right' },
            buttons: [
                { text: 'Anterior', action: this.tour.back, classes: 'shepherd-button shepherd-button-secondary' },
                { text: 'Próximo', action: this.tour.next, classes: 'shepherd-button' }
            ]
        });

        // Passo 3: Lista de Desejos
        this.tour.addStep({
            title: '🎁 Suas Listas de Desejos',
            text: this.getStepContent('lista-desejos'),
            attachTo: { element: '[data-tour="lista-desejos"]', on: 'left' },
            buttons: [
                { text: 'Anterior', action: this.tour.back, classes: 'shepherd-button shepherd-button-secondary' },
                { text: 'Próximo', action: this.tour.next, classes: 'shepherd-button' }
            ]
        });

        // Passo 4: Melhorias
        this.tour.addStep({
            title: '🌱 Área de Crescimento',
            text: this.getStepContent('melhorias'),
            attachTo: { element: '[data-tour="melhorias"]', on: 'top' },
            buttons: [
                { text: 'Anterior', action: this.tour.back, classes: 'shepherd-button shepherd-button-secondary' },
                { text: 'Próximo', action: this.tour.next, classes: 'shepherd-button' }
            ]
        });

        // Passo 5: Navegação
        this.tour.addStep({
            title: '🧭 Navegação Inteligente',
            text: this.getStepContent('navegacao'),
            attachTo: { element: 'nav', on: 'bottom' },
            buttons: [
                { text: 'Anterior', action: this.tour.back, classes: 'shepherd-button shepherd-button-secondary' },
                { text: 'Próximo', action: this.tour.next, classes: 'shepherd-button' }
            ]
        });

        // Passo 6: Finalização
        this.tour.addStep({
            title: '🎊 Parabéns! Tour Concluído',
            text: this.getStepContent('conclusao'),
            attachTo: { element: 'body', on: 'top' },
            buttons: [
                { text: 'Anterior', action: this.tour.back, classes: 'shepherd-button shepherd-button-secondary' },
                { text: '✨ Começar a Usar!', action: this.tour.complete, classes: 'shepherd-button' }
            ]
        });
    }

    /**
     * Obtém o conteúdo de cada passo do tour
     */
    getStepContent(step) {
        const content = {
            welcome: `
                <div class="space-y-3">
                    <p>Olá! Eu sou seu guia no <strong>Relatópia</strong> - sua plataforma para fortalecer relacionamentos.</p>
                    <p>Vou mostrar as principais funcionalidades em poucos passos. Pronto para começar?</p>
                    <div class="bg-emerald-50 p-3 rounded-lg border border-emerald-200">
                        <p class="text-sm text-emerald-700">💡 <strong>Dica:</strong> Você pode pular o tour a qualquer momento</p>
                    </div>
                </div>
            `,
            sentimentos: `
                <div class="space-y-3">
                    <p>Este é o <strong>coração do Relatópia</strong>! Aqui você registra como está se sentindo todos os dias.</p>
                    <ul class="space-y-1 text-sm">
                        <li>📊 <strong>Acompanhe padrões</strong> emocionais</li>
                        <li>💭 <strong>Reflita</strong> sobre seus sentimentos</li>
                        <li>📈 <strong>Monitore</strong> seu bem-estar</li>
                    </ul>
                    <div class="bg-orange-50 p-3 rounded border border-orange-200">
                        <p class="text-sm"><strong>Experimente:</strong> Clique em "Registrar Agora" depois do tour!</p>
                    </div>
                </div>
            `,
            'lista-desejos': `
                <div class="space-y-3">
                    <p>Organize seus <strong>sonhos e desejos</strong> em categorias especiais:</p>
                    <div class="grid grid-cols-1 gap-2 text-sm">
                        <div class="bg-blue-50 p-2 rounded border border-blue-200">
                            <strong>🔒 Meus Desejos:</strong> Lista pessoal e privada
                        </div>
                        <div class="bg-purple-50 p-2 rounded border border-purple-200">
                            <strong>💕 Nossos Desejos:</strong> Lista compartilhada com seu parceiro
                        </div>
                    </div>
                    <p class="text-xs text-gray-600">Ideal para planejamentos de presentes e objetivos conjuntos!</p>
                </div>
            `,
            melhorias: `
                <div class="space-y-3">
                    <p>Desenvolva-se continuamente com estas <strong>ferramentas de crescimento</strong>:</p>
                    <div class="space-y-2 text-sm">
                        <div class="bg-yellow-50 p-2 rounded border border-yellow-200">
                            <strong>🌟 Melhorar em Mim:</strong> Aspectos pessoais para desenvolver
                        </div>
                        <div class="bg-emerald-50 p-2 rounded border border-emerald-200">
                            <strong>🤝 Melhorar Juntos:</strong> Objetivos como casal
                        </div>
                    </div>
                    <p class="text-xs text-gray-600">Transforme desafios em oportunidades de crescimento!</p>
                </div>
            `,
            navegacao: `
                <div class="space-y-3">
                    <p>Explore todas as funcionalidades através do <strong>menu principal</strong>:</p>
                    <div class="grid grid-cols-1 gap-1 text-sm">
                        <div class="flex items-center space-x-2">
                            <span>🏠</span><span><strong>Dashboard:</strong> Visão geral de tudo</span>
                        </div>
                        <div class="flex items-center space-x-2">
                            <span>📅</span><span><strong>Calendário:</strong> Eventos organizados</span>
                        </div>
                        <div class="flex items-center space-x-2">
                            <span>📊</span><span><strong>Histórico:</strong> Análise dos sentimentos</span>
                        </div>
                        <div class="flex items-center space-x-2">
                            <span>👥</span><span><strong>Parceiro:</strong> Dashboard compartilhado</span>
                        </div>
                        <div class="flex items-center space-x-2">
                            <span>⚙️</span><span><strong>Perfil:</strong> Configurações e convites</span>
                        </div>
                    </div>
                </div>
            `,
            conclusao: `
                <div class="space-y-4">
                    <p class="text-lg">Agora você conhece as principais funcionalidades do <strong>Relatópia</strong>!</p>

                    <div class="bg-gradient-to-r from-emerald-50 to-teal-50 p-4 rounded-lg border border-emerald-200">
                        <h4 class="font-semibold text-emerald-800 mb-2">🚀 Próximos Passos:</h4>
                        <ol class="space-y-1 text-sm text-emerald-700">
                            <li>1. Registre seu primeiro sentimento do dia</li>
                            <li>2. Adicione alguns desejos às suas listas</li>
                            <li>3. Defina objetivos de melhoria pessoal</li>
                            <li>4. Convide seu parceiro se ainda não fez</li>
                        </ol>
                    </div>

                    <div class="bg-blue-50 p-3 rounded border border-blue-200">
                        <p class="text-sm text-blue-700">
                            💡 <strong>Dica:</strong> Você pode refazer este tour clicando em "Tour" no menu!
                        </p>
                    </div>

                    <p class="text-sm text-center text-gray-600">
                        Muito obrigado por usar o Relatópia! 💚
                    </p>
                </div>
            `
        };

        return content[step] || '<p>Conteúdo não encontrado</p>';
    }

    /**
     * Anexa eventos do tour
     */
    attachTourEvents() {
        this.tour.on('complete', () => this.completeTour());
        this.tour.on('cancel', () => this.cancelTour());
    }

    /**
     * Completa o tour
     */
    completeTour() {
        this.userPreferences.tourCompleted = true;
        this.userPreferences.tourDate = new Date().toISOString();
        this.saveUserPreferences(this.userPreferences);

        if (window.showNotification) {
            window.showNotification('Tour concluído! Bem-vindo ao Relatópia! 🎉', 'success', 5000);
        }

        // Analytics (se disponível)
        if (window.gtag) {
            window.gtag('event', 'tour_completed', {
                event_category: 'engagement',
                event_label: 'guided_tour'
            });
        }
    }

    /**
     * Cancela o tour
     */
    cancelTour() {
        if (window.showNotification) {
            window.showNotification('Tour cancelado. Você pode iniciá-lo novamente pelo menu!', 'info');
        }

        // Analytics (se disponível)
        if (window.gtag) {
            window.gtag('event', 'tour_cancelled', {
                event_category: 'engagement',
                event_label: 'guided_tour'
            });
        }
    }

    /**
     * Redefine o tour (para debug ou re-experiência)
     */
    resetTour() {
        this.userPreferences.tourCompleted = false;
        this.userPreferences.autoStartDisabled = false;
        this.userPreferences.tourDate = null;
        this.saveUserPreferences(this.userPreferences);

        if (window.showNotification) {
            window.showNotification('Tour redefinido! Recarregue a página para experimentar novamente.', 'info');
        }
    }

    /**
     * Obtém estatísticas do tour
     */
    getTourStats() {
        return {
            completed: this.userPreferences.tourCompleted,
            completionDate: this.userPreferences.tourDate,
            autoStartDisabled: this.userPreferences.autoStartDisabled,
            isInitialized: this.isInitialized
        };
    }
}

// Instância global
window.relatopiaGuide = new RelatopiaGuide();

// Função global para compatibilidade
window.startGuidedTour = () => {
    window.relatopiaGuide.startTour();
};

// Auto-inicialização quando DOM estiver pronto
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => {
        setTimeout(() => window.relatopiaGuide.initialize(), 500);
    });
} else {
    setTimeout(() => window.relatopiaGuide.initialize(), 500);
}

// Função de debug para desenvolvedores
window.debugTour = {
    reset: () => window.relatopiaGuide.resetTour(),
    stats: () => window.relatopiaGuide.getTourStats(),
    start: () => window.relatopiaGuide.startTour()
};
