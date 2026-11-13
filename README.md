# 🎓 Portal de Estágio — CEFET/RJ

Plataforma integrada para gerenciamento de estágios, controle digital de frequência/horas, validação com supervisores de empresas conveniadas e homologação acadêmica no CEFET/RJ.

---

## 🚀 Como Iniciar Rapidamente (com Docker)

```bash
# 1. Clone o repositório
git clone URL_DO_REPOSITORIO portal-de-estagio
cd portal-de-estagio

# 2. Copie o arquivo de ambiente
cp .env.example .env    # (ou 'copy .env.example .env' no Windows)

# 3. Suba os containers
docker compose up -d --build

# 4. Prepare a aplicação (uma única vez)
docker compose exec app composer install
docker compose exec app php artisan key:generate
docker compose exec app php artisan migrate
docker compose exec app php artisan db:seed
docker compose exec app npm install
docker compose exec app npm run build
```

Acesse no navegador: **[http://localhost:8000](http://localhost:8000)**

- **Administrador padrão:** `admin@cefet.br` | Senha: `admin123`

---

## 📚 Documentação Completa & Tutoriais

Consulte o manual detalhado com a arquitetura completa, explicação didática de como o sistema funciona, diagramas de fluxo de dados, guia de commits e troubleshooting em:

👉 **[Manual do Sistema & Guia do Desenvolvedor (tutoriais/MANUAL_DO_SISTEMA_E_GUIA_DO_DESENVOLVEDOR.md)](tutoriais/MANUAL_DO_SISTEMA_E_GUIA_DO_DESENVOLVEDOR.md)**

---

## 🛠️ Tecnologias Principais

- **PHP 8.2+** / **Laravel 12**
- **MySQL 8.0**
- **Docker** & **Docker Compose**
- **Tailwind CSS** & **Vite**
- **BrasilAPI** (Enriquecimento cadastral de CNPJ via Receita Federal)
- **Python** (Extrator e sanitizador de PDFs oficiais)
