# 💕 Relatópia

<div align="center">

![Relatópia Logo](public/relatopia.png)

**A plataforma definitiva para fortalecer relacionamentos e promover crescimento pessoal a dois**

[![Laravel](https://img.shields.io/badge/Laravel-11.x-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)](https://laravel.com)
[![PHP](https://img.shields.io/badge/PHP-8.2+-777BB4?style=for-the-badge&logo=php&logoColor=white)](https://php.net)
[![Tailwind CSS](https://img.shields.io/badge/Tailwind_CSS-3.x-38B2AC?style=for-the-badge&logo=tailwind-css&logoColor=white)](https://tailwindcss.com)
[![MySQL](https://img.shields.io/badge/MySQL-8.0+-4479A1?style=for-the-badge&logo=mysql&logoColor=white)](https://mysql.com)

[🌟 Demo](#demo) • [🚀 Funcionalidades](#funcionalidades) • [💻 Instalação](#instalação) • [📱 Screenshots](#screenshots) • [🤝 Contribuindo](#contribuindo)

</div>

---

## 🎯 **Sobre o Projeto**

**Relatópia** é uma plataforma inovadora desenvolvida para casais que desejam fortalecer seus relacionamentos através do autoconhecimento, comunicação efetiva e crescimento conjunto.

Com uma interface moderna e intuitiva, a plataforma oferece ferramentas para registrar aspectos positivos, identificar pontos de melhoria, acompanhar sentimentos diários e definir metas compartilhadas.

### 🌟 **Por que Relatópia?**

- **💝 Crescimento a dois**: Ferramentas para evolução pessoal e do relacionamento
- **📊 Insights valiosos**: Estatísticas e análises do progresso
- **🎨 Design moderno**: Interface responsiva e acessível
- **🔒 Dados seguros**: Sistema robusto de autenticação e privacidade
- **📱 Mobile-first**: Experiência otimizada para todos os dispositivos

---

## 🚀 **Funcionalidades Principais**

<div align="center">

| Categoria | Funcionalidades |
|-----------|----------------|
| **🎯 Onboarding** | Sistema obrigatório de configuração inicial, Coleta de dados do relacionamento, Convites automáticos para parceiro(a) |
| **💕 Gestão do Relacionamento** | Registros de aspectos positivos, Identificação de pontos de melhoria, Definição de metas e sonhos compartilhados |
| **😊 Monitoramento Emocional** | Registro diário de sentimentos, Histórico completo de humor, Análises e estatísticas emocionais |
| **📊 Dashboard Inteligente** | Estatísticas em tempo real, Cards interativos e animados, Progresso visual com barras dinâmicas |
| **👥 Sistema de Convites** | Convites para usuários cadastrados, Emails automáticos para não cadastrados, Vinculação automática após cadastro |
| **🔐 Segurança** | Autenticação robusta, Verificação de email obrigatória, Criptografia de senhas, Middleware de proteção |

</div>

---

## 📱 **Screenshots**

<div align="center">

### 🎨 **Interface Principal**
<img src="public/tela.png" alt="Dashboard Principal" width="800">

### 📊 **Dashboard Responsivo**
![Dashboard](https://img.shields.io/badge/Dashboard-Responsivo-success?style=for-the-badge)
![Cards](https://img.shields.io/badge/Cards-Interativos-blue?style=for-the-badge)
![Animações](https://img.shields.io/badge/Animações-Suaves-purple?style=for-the-badge)

</div>

---

## 🏗️ **Arquitetura e Tecnologias**

### **Backend**
- **🐘 PHP 8.2+** - Linguagem moderna e performática
- **🚀 Laravel 11.x** - Framework robusto e elegante
- **🗄️ MySQL 8.0+** - Banco de dados confiável
- **📧 Sistema de Email** - Notificações e convites automáticos

### **Frontend**
- **🎨 Tailwind CSS 3.x** - Design system moderno
- **⚡ JavaScript Vanilla** - Performance otimizada
- **📱 Design Responsivo** - Mobile-first approach
- **🎭 Animações CSS** - Microinterações suaves

### **Recursos Avançados**
- **🔐 Middleware personalizado** - Controle de acesso granular
- **📊 API REST** - Endpoints para dados em tempo real
- **💌 Sistema de emails** - Templates responsivos
- **🎯 Onboarding guiado** - Experiência de primeiro uso excepcional

---

## 💻 **Instalação e Configuração**

### **Pré-requisitos**
```bash
PHP >= 8.2
Composer >= 2.0
Node.js >= 18.0
MySQL >= 8.0
```

### **Instalação Rápida**

```bash
# 1. Clone o repositório
git clone https://github.com/Rupelio/relatopia.git
cd relatopia

# 2. Instale as dependências
composer install
npm install

# 3. Configure o ambiente
cp .env.example .env
php artisan key:generate

# 4. Configure o banco de dados no .env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=relatopia
DB_USERNAME=seu_usuario
DB_PASSWORD=sua_senha

# 5. Execute as migrações
php artisan migrate

# 6. Compile os assets
npm run build

# 7. Inicie o servidor
php artisan serve
```

> 🔒 **Dica de Segurança**: Nunca commite o arquivo `.env` no Git. Ele já está no `.gitignore` por padrão.

### **⚙️ Configuração de Email**

Para funcionalidade completa de convites, configure SMTP no `.env`:

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com          # Exemplo: Gmail
MAIL_PORT=587
MAIL_USERNAME=seu-email@gmail.com
MAIL_PASSWORD=sua-senha-app       # Use App Password, não senha normal
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@seudominio.com
MAIL_FROM_NAME="Relatópia"
```

> ⚠️ **IMPORTANTE**:
> - Nunca compartilhe credenciais reais no código
> - Use variáveis de ambiente para dados sensíveis
> - Para Gmail, use [App Passwords](https://support.google.com/accounts/answer/185833)
> - Para produção, considere serviços como SendGrid, Mailgun ou AWS SES

### **📧 Provedores de Email Recomendados**

| Provedor | Tipo | Limite Gratuito |
|----------|------|-----------------|
| **Gmail SMTP** | Gratuito | 500 emails/dia |
| **SendGrid** | Freemium | 100 emails/dia |
| **Mailgun** | Freemium | 5.000 emails/mês |
| **AWS SES** | Pago | $0.10/1000 emails |

---

## 🎯 **Fluxo de Usuário**

### **1. 🔑 Cadastro e Autenticação**
```
Cadastro → Verificação de Email → Onboarding Obrigatório → Dashboard
```

### **2. 🎨 Onboarding (3 Passos)**
- **Passo 1**: Configuração do relacionamento (data + status)
- **Passo 2**: Convite do parceiro(a) (opcional)
- **Passo 3**: Finalização e próximos passos

### **3. 📊 Uso Diário**
- Registro de sentimentos e humor
- Adição de aspectos positivos
- Identificação de melhorias
- Acompanhamento de estatísticas

---

## 🛠️ **Estrutura do Projeto**

```
relatopia/
├── app/
│   ├── Http/
│   │   ├── Controllers/         # Controladores da aplicação
│   │   └── Middleware/          # Middleware personalizado
│   ├── Mail/                    # Classes de email
│   └── Models/                  # Modelos Eloquent
├── database/
│   ├── migrations/              # Migrações do banco
│   └── seeders/                 # Dados iniciais
├── resources/
│   ├── css/                     # Estilos personalizados
│   ├── js/                      # JavaScript
│   └── views/                   # Templates Blade
├── routes/
│   ├── web.php                  # Rotas web
│   └── api.php                  # API endpoints
└── public/                      # Assets públicos
```

---

## 🎨 **Principais Funcionalidades Técnicas**

### **Sistema de Onboarding**
- ✅ Middleware de verificação obrigatória
- ✅ Interface progressiva com 3 etapas
- ✅ Validações em tempo real
- ✅ Redirecionamento inteligente

### **Sistema de Convites**
- ✅ Detecção automática de usuários existentes
- ✅ Emails responsivos para não cadastrados
- ✅ Vinculação automática pós-cadastro
- ✅ Validação contra auto-convite

### **Dashboard Dinâmico**
- ✅ Estatísticas em tempo real
- ✅ Atualizações automáticas via AJAX
- ✅ Cards com animações CSS
- ✅ Barras de progresso dinâmicas

### **Segurança**
- ✅ Criptografia de senhas com bcrypt
- ✅ Verificação obrigatória de email
- ✅ Middleware de autenticação
- ✅ Validação CSRF em formulários

---

## 📊 **APIs Disponíveis**

### **Endpoints Principais**

| Método | Endpoint | Descrição |
|--------|----------|-----------|
| `GET` | `/api/estatisticas` | Estatísticas do relacionamento |
| `POST` | `/api/relacionamento-itens` | Criar novo item |
| `PUT` | `/api/relacionamento-itens/{id}/toggle` | Marcar como resolvido |
| `DELETE` | `/api/relacionamento-itens/{id}` | Remover item |
| `POST` | `/api/sentimento` | Registrar sentimento |
| `GET` | `/api/sentimento` | Listar sentimentos |
| `POST` | `/api/vincular-coparticipante` | Enviar convite |

---

## 🎨 **Design System**

### **Paleta de Cores**
- **Primary**: `emerald-500` (#10B981)
- **Secondary**: `pink-500` (#EC4899)
- **Success**: `green-500` (#22C55E)
- **Warning**: `yellow-500` (#EAB308)
- **Error**: `red-500` (#EF4444)

### **Tipografia**
- **Font Family**: Inter, system-ui, sans-serif
- **Weights**: 400 (regular), 500 (medium), 600 (semibold), 700 (bold)

### **Componentes**
- Cards com sombras suaves e bordas arredondadas
- Botões com gradientes e efeitos hover
- Inputs com foco destacado
- Animações de transição suaves

---

## 🚀 **Deploy e Produção**

### **Checklist de Deploy**
- [ ] Configurar variáveis de ambiente
- [ ] Executar migrações
- [ ] Configurar SMTP para emails
- [ ] Otimizar assets (`npm run build`)
- [ ] Configurar cache (`php artisan config:cache`)
- [ ] SSL/HTTPS obrigatório

### **Recomendações de Hospedagem**
- **Shared Hosting**: cPanel com PHP 8.2+
- **VPS**: Ubuntu 22.04 + Nginx + PHP-FPM
- **Cloud**: AWS, DigitalOcean, Vercel
- **Database**: MySQL 8.0+ ou MariaDB 10.6+

---

## 🤝 **Contribuindo**

Contribuições são sempre bem-vindas! Veja como você pode ajudar:

### **Como Contribuir**
1. 🍴 Fork o projeto
2. 🌿 Crie uma branch para sua feature (`git checkout -b feature/nova-funcionalidade`)
3. 📝 Commit suas mudanças (`git commit -m 'Adiciona nova funcionalidade'`)
4. 🚀 Push para a branch (`git push origin feature/nova-funcionalidade`)
5. 🔄 Abra um Pull Request

### **Tipos de Contribuição**
- 🐛 Correção de bugs
- ✨ Novas funcionalidades
- 📚 Melhorias na documentação
- 🎨 Melhorias de UI/UX
- ⚡ Otimizações de performance

---

## 📄 **Licença**

Este projeto está licenciado sob a **MIT License** - veja o arquivo [LICENSE](LICENSE) para detalhes.

---

## 👨‍💻 **Autor**

<div align="center">

**Desenvolvido com 💜 por [Rupelio](https://github.com/Rupelio)**

[![GitHub](https://img.shields.io/badge/GitHub-Rupelio-181717?style=for-the-badge&logo=github)](https://github.com/Rupelio)
[![LinkedIn](https://img.shields.io/badge/LinkedIn-Perfil-0A66C2?style=for-the-badge&logo=linkedin)](https://linkedin.com/in/seu-perfil)

---

### ⭐ **Se este projeto te ajudou, considere deixar uma estrela!**

</div>

---

## 📞 **Suporte**

Encontrou algum problema ou tem alguma sugestão?

- 🐛 [Reporte um bug](https://github.com/Rupelio/relatopia/issues)
- 💡 [Sugira uma funcionalidade](https://github.com/Rupelio/relatopia/issues)
- 📧 Entre em contato: [seu-email@exemplo.com](mailto:seu-email@exemplo.com)

---

<div align="center">

**Feito com 💕 para casais que querem crescer juntos**

`Última atualização: Julho 2025`

</div>
