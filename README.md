<div align="center">

  <h1>Amazon Picture — Dashboard Trabalhe Conosco</h1>
  
  <p>
    Painel administrativo interno para gestão de candidaturas e solicitações de orçamento recebidas pelo site institucional da Amazon Picture.
  </p>

  <h4>
</div>

<br />

<!-- Table of Contents -->
# Índice

- [Sobre o Projeto](#sobre-o-projeto)
  * [Tecnologias Utilizadas](#tecnologias-utilizadas)
  * [Funcionalidades](#funcionalidades)
  * [Paleta de Cores](#paleta-de-cores)
  * [Variáveis de Ambiente](#variaveis-de-ambiente)
- [Primeiros Passos](#primeiros-passos)
  * [Pré-requisitos](#pre-requisitos)
  * [Instalação](#instalacao)
  * [Rodando Localmente](#rodando-localmente)
- [Como Usar](#como-usar)
- [Manutenção](#manutencao)
- [Próximos Passos](#proximos-passos)
- [Como Contribuir](#como-contribuir)
  * [Código de Conduta](#codigo-de-conduta)
- [Licença](#licenca)
- [Contato](#contato)
- [Agradecimentos](#agradecimentos)


<!-- About the Project -->
## Sobre o Projeto

A Amazon Picture é uma empresa de produção audiovisual, clipping eletrônico, marketing digital e automação com IA. Com o crescimento do site institucional da empresa, duas necessidades de negócio surgiram: (1) receber e organizar as candidaturas de novos profissionais que desejam entrar na rede de freelancers da empresa (cinegrafistas, operadores de drone, editores de vídeo, programadores) e (2) capturar solicitações de orçamento de potenciais clientes interessados nos serviços.

Este projeto é o **painel administrativo interno (dashboard)** que resolve esse problema: toda vez que alguém preenche o formulário de "Trabalhe Conosco" ou o formulário de "Orçamento" no site público, os dados chegam automaticamente aqui e ficam disponíveis para a equipe da Amazon Picture consultar, filtrar por área de atuação ou cidade, e entrar em contato diretamente pelo e-mail ou WhatsApp cadastrado — sem precisar de nenhum conhecimento técnico. O acesso ao painel é restrito por login, garantindo que apenas pessoas autorizadas da empresa vejam os dados dos candidatos e clientes em potencial.

Em resumo: é a "central de recebimento" que transforma o interesse gerado pelo site em uma lista organizada e acionável para o time comercial e de recrutamento da empresa.

<!-- TechStack -->
### Tecnologias Utilizadas

<details>
  <summary>Server</summary>
  <ul>
    <li><a href="https://www.php.net/">PHP</a> (MVC próprio, sem framework externo)</li>
    <li><a href="https://www.php.net/manual/en/book.pdo.php">PDO</a> (camada de acesso ao banco de dados)</li>
  </ul>
</details>

<details>
<summary>Database</summary>
  <ul>
    <li><a href="https://www.mysql.com/">MySQL / MariaDB</a></li>
  </ul>
</details>

<details>
<summary>Frontend (das telas do próprio painel)</summary>
  <ul>
    <li><a href="https://tailwindcss.com/">Tailwind CSS</a> (via CDN, usado na tela de login)</li>
  </ul>
</details>

<details>
<summary>DevOps & Tools</summary>
  <ul>
    <li><a href="https://httpd.apache.org/">Apache</a> com <code>mod_rewrite</code> (roteamento via <code>.htaccess</code>)</li>
    <li><a href="https://git-scm.com/">Git</a></li>
  </ul>
</details>

<!-- Features -->
### Funcionalidades

- Autenticação de administrador via sessão PHP, com senha protegida por hash (`password_hash` / `password_verify`)
- Roteamento próprio (front controller `App.php`) que mapeia URLs para Controllers e Métodos
- Listagem paginada de candidatos cadastrados, com filtros por área de atuação e por localização
- Endpoints de recebimento de formulários do site institucional:
  - `POST /orcamento/enviar` — grava solicitações de orçamento
  - `POST /trabalheConosco/receberFormulario` — grava candidaturas de profissionais
- Endpoints legados equivalentes em `app/API/` (`candidatos.php` e `orcamentos.php`) para integração direta via `$_POST`
- Script de migração (`app/database/migrate.php`) que cria as tabelas do banco e o usuário administrador inicial automaticamente
- Sistema de logs para depuração de envios de formulário (`/logs/orcamento_*.log` e `debug.log` por model)
- Cabeçalhos CORS liberados nos endpoints públicos para permitir chamadas do site institucional (hospedado separadamente)

<!-- Color Reference -->
### Paleta de Cores

| Color             | Hex                                                                  |
| ----------------- | -------------------------------------------------------------------- |
| Primary Color | ![#4F46E5](https://via.placeholder.com/10/4F46E5?text=+) #4F46E5 (indigo — tela de login) |
| Secondary Color | ![#334155](https://via.placeholder.com/10/334155?text=+) #334155 (slate — painel) |

<!-- Env Variables -->
### Variáveis de Ambiente

Para rodar este projeto, crie um arquivo `.env` na raiz do projeto (mesmo nível da pasta `app/`) com as seguintes variáveis:

```
DB_HOST=localhost
DB_NAME=nome_do_banco
DB_USER=usuario_do_banco
DB_PASS=senha_do_banco
DB_PORT=3306
```

> ⚠️ O arquivo `.env` já está listado no `.gitignore` e **nunca deve ser commitado**. Se credenciais reais já foram expostas em algum momento (por exemplo, em um `.zip` ou commit antigo), rotacione a senha do banco imediatamente.

<!-- Getting Started -->
## Primeiros Passos

<!-- Prerequisites -->
### Pré-requisitos

- PHP 7.4 ou superior (com extensão PDO e PDO_MySQL habilitadas)
- Servidor MySQL ou MariaDB
- Apache com `mod_rewrite` habilitado (ou outro servidor compatível com reescrita de URL)
- Git

<!-- Installation -->
### Instalação

Clone o repositório:

```bash
git clone https://github.com/gabeRN1/amazonclipback.git
cd amazonclipback
```

Crie o arquivo `.env` na raiz do projeto com as credenciais do seu banco (veja [Variáveis de Ambiente](#variaveis-de-ambiente)).

Ajuste a constante `BASE_URL` em `public/index.php` para o domínio/caminho onde o projeto será servido:

```php
define('BASE_URL', 'https://seu-dominio.com.br/caminho-do-dashboard');
```

Execute o script de migração para criar as tabelas (`usuarios`, `candidatos_trabalhe_conosco`, `orcamentos`) e o usuário administrador inicial:

```bash
php app/database/migrate.php
```

<!-- Run Locally -->
### Rodando Localmente

Usando o servidor embutido do PHP (aponte o document root para a pasta `public`):

```bash
php -S localhost:8000 -t public
```

Em produção (Apache), aponte o `DocumentRoot` do VirtualHost para a pasta `public/`; o arquivo `.htaccess` já contido nesta pasta cuida do redirecionamento de todas as rotas para `index.php`.

Acesse `http://localhost:8000/login` e utilize as credenciais criadas pela migration para entrar no painel.

<!-- Usage -->
## Como Usar

Principais rotas expostas pela aplicação:

| Método | Rota | Descrição |
|---|---|---|
| GET | `/` | Painel principal (protegido por login), lista candidatos com filtros e paginação |
| GET | `/login` | Tela de login |
| POST | `/login/autenticar` | Processa o login |
| GET | `/login/logout` | Encerra a sessão |
| POST | `/orcamento/enviar` | Recebe solicitações de orçamento vindas do site institucional |
| POST | `/trabalheConosco/receberFormulario` | Recebe candidaturas de profissionais vindas do site institucional |

Exemplo de chamada ao endpoint de orçamento a partir do frontend:

```javascript
fetch('https://amazonpicture.com.br/dash-trabalheconosco/orcamento/enviar', {
  method: 'POST',
  headers: { 'Content-Type': 'application/json' },
  body: JSON.stringify({
    nome: 'Fulano de Tal',
    email: 'fulano@email.com',
    telefone: '92999990000',
    servicos: ['Vídeo', 'Marketing'],
    objetivo: 'Preciso de um vídeo institucional.'
  })
});
```

<!-- Maintenance -->
## Manutenção

- **Rodar a migration novamente:** o script `app/database/migrate.php` é idempotente (usa `CREATE TABLE IF NOT EXISTS` e checa se o admin já existe antes de inserir), então pode ser executado novamente sem duplicar dados.
- **Trocar a senha do administrador:** a senha inicial é definida diretamente no script de migração. Após o primeiro acesso, é recomendado implementar uma tela de troca de senha ou atualizar o hash diretamente na tabela `usuarios`.
- **Logs de depuração:** falhas no recebimento de orçamentos e candidaturas são gravadas em `/logs/orcamento_*.log` (criado automaticamente) e em `debug.log` dentro de `app/Models/`. Consulte esses arquivos ao investigar formulários que não gravaram no banco.
- **Backup do banco de dados:** faça backups periódicos das tabelas `usuarios`, `candidatos_trabalhe_conosco` e `orcamentos` antes de qualquer alteração de schema.
- **Atualização de dependências:** o projeto não usa Composer nem pacotes externos no backend — a única dependência de terceiros é o Tailwind CSS via CDN na tela de login, que pode ser atualizado trocando a versão no `<script>` da view.
- **Deploy:** publique o conteúdo do repositório no servidor, garanta que o `DocumentRoot` aponte para `public/`, confirme que o `.env` está presente (fora do controle de versão) e rode a migration uma única vez no ambiente novo.
- **Troubleshooting comum:**
  - Erro de conexão com o banco → verifique as credenciais em `.env` e se o serviço MySQL está no ar.
  - Rotas retornando 404 → confirme que `mod_rewrite` está ativo e que o `.htaccess` está sendo lido (`AllowOverride All`).
  - Login não funciona → confirme que o usuário existe na tabela `usuarios` com `is_admin = 1` e que a senha foi hasheada com `password_hash`.

<!-- Roadmap -->
## RoadMap

* [ ] Testes automatizados (unitários e de integração)
* [ ] Gerenciador de dependências (Composer) e autoload PSR-4
* [ ] Notificação por e-mail ao receber novo orçamento ou candidatura
* [ ] Rate limiting nos endpoints públicos de formulário
