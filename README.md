# 🧩 Systex Clientes (CRM)

Sistema de **gestão de clientes e recorrência financeira** desenvolvido pela **Systex Sistemas Inteligentes**, voltado para controle de contratos, planos, faturamento recorrente, suporte técnico e acompanhamento de indicadores estratégicos de performance.

---

## 🚀 Visão Geral

O **Systex Clientes** é o módulo **CRM** da Systex, criado para **centralizar e automatizar o relacionamento com os clientes**, oferecendo uma visão completa das operações comerciais e financeiras.  
Com ele, é possível:

- Cadastrar clientes e responsáveis com informações completas  
- Gerenciar planos e assinaturas mensais ou anuais  
- Gerar e atualizar faturas recorrentes automaticamente  
- Controlar pagamentos e inadimplência  
- Registrar tickets de suporte e histórico de interações  
- Visualizar dashboards estratégicos com indicadores de receita e churn  

O sistema foi desenvolvido em **Laravel 11**, com **arquitetura limpa (Clean Architecture)** e **Filament Admin** como painel administrativo moderno e responsivo.

---

## 🏗️ Estrutura de Módulos

| Módulo | Descrição |
|:--|:--|
| **Clientes** | Cadastro completo com dados empresariais, responsáveis, status e canal de aquisição |
| **Planos** | Gerenciamento de planos e periodicidades (mensal, anual, etc.) |
| **Assinaturas** | Controle de assinaturas ativas, trials, pausadas ou canceladas |
| **Faturas (Invoices)** | Geração automática de cobranças recorrentes |
| **Pagamentos** | Registro de pagamentos manuais e automáticos (via integração com gateways) |
| **Tickets** | Sistema de suporte com controle de prioridade e SLA |
| **Interações** | Histórico de contatos, ligações, reuniões e follow-ups |
| **Dashboard** | Indicadores e gráficos de performance (MRR, ARR, churn, ARPU, LTV) |

---

## ⚙️ Tecnologias Utilizadas

- **Laravel 11** (PHP 8.2+)  
- **Filament Admin Panel**  
- **MySQL / MariaDB**  
- **Chart.js** (gráficos e projeções)  
- **Maatwebsite/Excel** (exportações)  
- **DomPDF** (relatórios em PDF)  
- **Mercado Pago / Asaas API** *(integração financeira opcional)*  

---

## 🧠 Conceitos-Chave do CRM

| Indicador | Descrição |
|:--|:--|
| **MRR (Monthly Recurring Revenue)** | Receita recorrente mensal total |
| **ARR (Annual Recurring Revenue)** | Receita recorrente anual |
| **Churn Rate** | Percentual de clientes que cancelaram no período |
| **ARPU (Average Revenue per User)** | Receita média por cliente |
| **LTV (Lifetime Value)** | Valor médio de vida útil de um cliente |
| **Inadimplência** | Total de faturas vencidas e não pagas |

---

## 🧱 Estrutura de Diretórios

```
app/
 ├── Console/Commands/       # Comandos automáticos de cobrança
 ├── Http/Controllers/       # Controllers do CRM e Dashboard
 ├── Models/                 # Models principais (Client, Plan, Subscription, etc.)
database/
 ├── migrations/             # Estrutura das tabelas
resources/
 ├── views/crm/              # Views do dashboard e relatórios
```

---

## 🔁 Automação de Cobranças

O sistema conta com **comandos automáticos (Artisan)** para geração e atualização de faturas:

| Comando | Função |
|:--|:--|
| `php artisan crm:gerar-faturas` | Gera faturas mensais automaticamente |
| `php artisan crm:atualizar-inad` | Atualiza status de faturas vencidas |

Esses comandos podem ser executados via **CRON**:

```
* * * * * php /caminho/do/projeto/artisan schedule:run >> /dev/null 2>&1
```

---

## 📊 Dashboard Financeiro

O painel `/crm/dashboard` apresenta indicadores e projeções como:

- Clientes ativos  
- Receita recorrente mensal (MRR)  
- Receita anual (ARR)  
- Inadimplência atual  
- Taxa de churn  
- ARPU e LTV estimado  
- Gráficos dos últimos 6 meses e projeções futuras  

---

## 🧰 Instalação e Setup

1. Clone o repositório:
   ```bash
   git clone https://github.com/manoelfilhodev/systex-clientes.git
   ```

2. Instale as dependências:
   ```bash
   composer install
   npm install && npm run build
   ```

3. Configure o arquivo `.env`:
   ```env
   APP_NAME="Systex Clientes"
   APP_URL=https://localhost
   DB_CONNECTION=mysql
   DB_DATABASE=systex_clientes
   DB_USERNAME=root
   DB_PASSWORD=
   ```

4. Execute as migrations e seeders:
   ```bash
   php artisan migrate --seed
   ```

5. Inicie o servidor:
   ```bash
   php artisan serve
   ```

---

## 🔒 Acesso e Permissões

- Autenticação padrão do Laravel  
- Perfis disponíveis: **Admin**, **Financeiro**, **Suporte**  
- Permissões de acesso controladas pelo painel administrativo (Filament)

---

## 💡 Autor

**Systex Sistemas Inteligentes**  
Desenvolvido por [Manoel Filho](https://systex.com.br)  
📧 E-mail: financeiro@systex.com.br  
🌐 [https://systex.com.br](https://systex.com.br)

---

## 🧾 Licença MIT

Este projeto é de propriedade da **Systex Sistemas Inteligentes**.  
A reprodução, redistribuição ou uso sem autorização expressa é proibida.
