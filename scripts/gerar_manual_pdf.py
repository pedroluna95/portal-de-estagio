import os
import sys
from reportlab.lib.pagesizes import letter, A4
from reportlab.lib import colors
from reportlab.lib.styles import getSampleStyleSheet, ParagraphStyle
from reportlab.platypus import (
    SimpleDocTemplate, Paragraph, Spacer, Table, TableStyle, PageBreak, KeepTogether, HRFlowable
)
from reportlab.pdfgen import canvas

class NumberedCanvas(canvas.Canvas):
    def __init__(self, *args, **kwargs):
        super(NumberedCanvas, self).__init__(*args, **kwargs)
        self._saved_page_states = []

    def showPage(self):
        self._saved_page_states.append(dict(self.__dict__))
        self._startPage()

    def save(self):
        num_pages = len(self._saved_page_states)
        for state in self._saved_page_states:
            self.__dict__.update(state)
            self.draw_page_decorations(num_pages)
            super(NumberedCanvas, self).showPage()
        super(NumberedCanvas, self).save()

    def draw_page_decorations(self, page_count):
        self.saveState()
        self.setFont("Helvetica", 8)
        self.setFillColor(colors.HexColor("#64748b"))

        # Cabeçalho nas páginas seguintes à primeira
        if self._pageNumber > 1:
            self.drawString(40, 810, "Portal de Estágio — CEFET/RJ | Manual do Sistema e Guia do Desenvolvedor")
            self.setStrokeColor(colors.HexColor("#cbd5e1"))
            self.setLineWidth(0.5)
            self.line(40, 804, 555, 804)

        # Rodapé
        self.setStrokeColor(colors.HexColor("#cbd5e1"))
        self.setLineWidth(0.5)
        self.line(40, 42, 555, 42)
        
        texto_rodape = f"Página {self._pageNumber} de {page_count}"
        self.drawRightString(555, 30, texto_rodape)
        self.drawString(40, 30, "Documentação Oficial — Centro Federal de Educação Tecnológica Celso Suckow da Fonseca")
        self.restoreState()


def build_pdf(dest_path):
    doc = SimpleDocTemplate(
        dest_path,
        pagesize=A4,
        leftMargin=40,
        rightMargin=40,
        topMargin=50,
        bottomMargin=55
    )

    styles = getSampleStyleSheet()

    # Estilos customizados
    title_style = ParagraphStyle(
        'DocTitle',
        parent=styles['Normal'],
        fontName='Helvetica-Bold',
        fontSize=20,
        leading=24,
        textColor=colors.HexColor('#0056c9'),
        spaceAfter=4
    )
    
    subtitle_style = ParagraphStyle(
        'DocSubTitle',
        parent=styles['Normal'],
        fontName='Helvetica',
        fontSize=11,
        leading=15,
        textColor=colors.HexColor('#475569'),
        spaceAfter=14
    )

    h1_style = ParagraphStyle(
        'Heading1_Custom',
        parent=styles['Heading1'],
        fontName='Helvetica-Bold',
        fontSize=13,
        leading=17,
        textColor=colors.HexColor('#0056c9'),
        spaceBefore=14,
        spaceAfter=6,
        keepWithNext=True
    )

    h2_style = ParagraphStyle(
        'Heading2_Custom',
        parent=styles['Heading2'],
        fontName='Helvetica-Bold',
        fontSize=10.5,
        leading=14,
        textColor=colors.HexColor('#1e293b'),
        spaceBefore=10,
        spaceAfter=4,
        keepWithNext=True
    )

    body_style = ParagraphStyle(
        'Body_Custom',
        parent=styles['Normal'],
        fontName='Helvetica',
        fontSize=9,
        leading=13,
        textColor=colors.HexColor('#1e293b'),
        spaceAfter=6
    )

    bullet_style = ParagraphStyle(
        'Bullet_Custom',
        parent=body_style,
        leftIndent=14,
        bulletIndent=4,
        spaceAfter=3
    )

    code_style = ParagraphStyle(
        'Code_Custom',
        fontName='Courier',
        fontSize=8,
        leading=11,
        textColor=colors.HexColor('#f8fafc'),
        spaceBefore=0,
        spaceAfter=0
    )

    def make_code_box(code_text):
        p = Paragraph(code_text.strip().replace('\n', '<br/>').replace(' ', '&nbsp;'), code_style)
        t = Table([[p]], colWidths=[515])
        t.setStyle(TableStyle([
            ('BACKGROUND', (0,0), (-1,-1), colors.HexColor('#0f172a')),
            ('PADDING', (0,0), (-1,-1), 8),
            ('BOTTOMPADDING', (0,0), (-1,-1), 8),
            ('TOPPADDING', (0,0), (-1,-1), 8),
            ('LEFTPADDING', (0,0), (-1,-1), 10),
            ('RIGHTPADDING', (0,0), (-1,-1), 10),
            ('BOX', (0,0), (-1,-1), 1, colors.HexColor('#1e293b')),
        ]))
        return t

    def make_alert_box(title, text, bg_hex='#eff6ff', border_hex='#3b82f6', text_hex='#1e40af'):
        html = f"<b>{title}</b><br/>{text}"
        p = Paragraph(html, ParagraphStyle('AlertText', parent=body_style, fontSize=8.5, leading=12, textColor=colors.HexColor(text_hex)))
        t = Table([[p]], colWidths=[515])
        t.setStyle(TableStyle([
            ('BACKGROUND', (0,0), (-1,-1), colors.HexColor(bg_hex)),
            ('BOX', (0,0), (-1,-1), 0.5, colors.HexColor(border_hex)),
            ('LEFTPADDING', (0,0), (-1,-1), 10),
            ('RIGHTPADDING', (0,0), (-1,-1), 10),
            ('TOPPADDING', (0,0), (-1,-1), 6),
            ('BOTTOMPADDING', (0,0), (-1,-1), 6),
        ]))
        return t

    story = []

    # Cabeçalho Principal
    story.append(Paragraph("📘 Manual do Sistema & Guia do Desenvolvedor", title_style))
    story.append(Paragraph("<b>Portal de Estágio — Centro Federal de Educação Tecnológica Celso Suckow da Fonseca (CEFET/RJ)</b>", subtitle_style))
    story.append(HRFlowable(width="100%", thickness=1.5, color=colors.HexColor('#0077fc'), spaceBefore=0, spaceAfter=12))

    # Sumário Box
    sumario_html = """
    <b>📑 Sumário dos Tópicos:</b><br/>
    1. Visão Geral do Projeto &bull; 2. Arquitetura & Tecnologias Utilizadas &bull; 3. Perfis de Usuários & Suas Responsabilidades<br/>
    4. Fluxos de Dados & Regras de Negócio &bull; 5. Tutorial de Instalação (Primeira Vez em uma Máquina)<br/>
    6. Como Rodar o Projeto no Dia a Dia &bull; 7. Guia de Git, Commits & Boas Práticas &bull; 8. Estrutura de Pastas &bull; 9. Troubleshooting
    """
    story.append(Paragraph(sumario_html, ParagraphStyle('Sumario', parent=body_style, fontSize=8.5, leading=12, textColor=colors.HexColor('#334155'))))
    story.append(Spacer(1, 10))

    # 1. Visão Geral
    story.append(Paragraph("1. Visão Geral do Projeto", h1_style))
    story.append(Paragraph(
        "O <b>Portal de Estágio</b> é uma plataforma web criada para modernizar, digitalizar e centralizar todo o processo de gestão de estágios curriculares e extracurriculares do <b>CEFET/RJ</b>.",
        body_style
    ))
    story.append(Paragraph("&bull; <b>Alunos:</b> Formalização ágil de estágios, consulta de convênios ativos e controle digital diário de frequência/horas.", bullet_style))
    story.append(Paragraph("&bull; <b>Supervisores (Empresas):</b> Validação e aprovação rápida de folhas de ponto e justificativas de faltas/atrasos com emissão de parecer.", bullet_style))
    story.append(Paragraph("&bull; <b>Professores Orientadores:</b> Acompanhamento pedagógico das atividades e homologação da carga horária acadêmica para colação de grau.", bullet_style))
    story.append(Paragraph("&bull; <b>Administração / DIPEX:</b> Gestão de convênios oficiais via PDF, enriquecimento automático de cidades via CNPJ na Receita Federal e auditoria de usuários.", bullet_style))

    # 2. Arquitetura
    story.append(Paragraph("2. Arquitetura & Tecnologias Utilizadas", h1_style))
    story.append(Paragraph("O sistema adota o padrão <b>MVC (Model-View-Controller)</b> estruturado com Services desacoplados:", body_style))
    story.append(Paragraph("&bull; <b>Backend:</b> PHP 8.2+ com framework <b>Laravel 12</b>.", bullet_style))
    story.append(Paragraph("&bull; <b>Banco de Dados:</b> <b>MySQL 8.0</b> rodando em container Docker isolado.", bullet_style))
    story.append(Paragraph("&bull; <b>Frontend:</b> Laravel Blade estilizado com <b>Tailwind CSS</b> e empacotado via <b>Vite</b>.", bullet_style))
    story.append(Paragraph("&bull; <b>Ambiente Docker:</b> Containers configurados via Docker Compose para eliminar a necessidade de instalação de dependências locais no host.", bullet_style))
    story.append(Paragraph("&bull; <b>Integração Externa:</b> <b>BrasilAPI</b> para obtenção em tempo real de municípios, estados e endereços oficiais via CNPJ.", bullet_style))
    story.append(Paragraph("&bull; <b>Extrator de Documentos:</b> Script Python inteligente com regex para higienização e correção de anomalias em PDFs de convênios.", bullet_style))

    # 3. Perfis
    story.append(Paragraph("3. Perfis de Usuários & Suas Responsabilidades", h1_style))
    tabela_perfis_data = [
        [Paragraph("<b>Perfil</b>", body_style), Paragraph("<b>Acesso Principal</b>", body_style), Paragraph("<b>Atribuições no Sistema</b>", body_style)],
        [
            Paragraph("🎓 <b>Aluno</b>", body_style),
            Paragraph("<code>/dashboard</code><br/><code>/estagios</code><br/><code>/empresas</code>", body_style),
            Paragraph("Inicia o processo de estágio, cadastra a jornada semanal, bate o ponto diário (presenças), busca empresas parceiras por localização e consulta o progresso das horas.", body_style)
        ],
        [
            Paragraph("🏢 <b>Supervisor</b>", body_style),
            Paragraph("<code>/dashboard</code><br/><code>/estagios</code>", body_style),
            Paragraph("Representante da empresa concedente. Avalia as folhas de ponto diárias do estagiário, aprovando ou recusando justificativas com parecer formal.", body_style)
        ],
        [
            Paragraph("👨‍🏫 <b>Orientador</b>", body_style),
            Paragraph("<code>/dashboard</code><br/><code>/estagios</code>", body_style),
            Paragraph("Professor do CEFET/RJ responsável pedagógico. Acompanha os relatórios e homologa a carga horária acadêmica.", body_style)
        ],
        [
            Paragraph("🛡️ <b>Administrador</b>", body_style),
            Paragraph("<code>/admin/empresas</code><br/><code>/admin/usuarios</code>", body_style),
            Paragraph("Equipe da DIPEX / Coordenação. Importa PDFs de convênios, busca localização via CNPJ, salva atualizações no Seeder e audita o sistema.", body_style)
        ]
    ]
    tabela_perfis = Table(tabela_perfis_data, colWidths=[90, 110, 315])
    tabela_perfis.setStyle(TableStyle([
        ('BACKGROUND', (0,0), (-1,0), colors.HexColor('#f1f5f9')),
        ('BOX', (0,0), (-1,-1), 0.5, colors.HexColor('#cbd5e1')),
        ('INNERGRID', (0,0), (-1,-1), 0.5, colors.HexColor('#e2e8f0')),
        ('TOPPADDING', (0,0), (-1,-1), 5),
        ('BOTTOMPADDING', (0,0), (-1,-1), 5),
    ]))
    story.append(tabela_perfis)

    story.append(PageBreak())

    # 4. Fluxo de Dados
    story.append(Paragraph("4. Fluxos de Dados & Regras de Negócio", h1_style))
    story.append(Paragraph("<b>4.1 Ciclo de Vida do Estágio & Frequência de Horas:</b>", h2_style))
    story.append(Paragraph("1. <b>Início do Estágio:</b> O aluno informa a empresa conveniada, o supervisor e anexa o Termo de Compromisso de Estágio (TCE).", bullet_style))
    story.append(Paragraph("2. <b>Homologação:</b> A coordenação ou orientador confere a documentação e autoriza o início.", bullet_style))
    story.append(Paragraph("3. <b>Jornada Semanal:</b> O aluno cadastra seus horários contratados (ex: Segunda a Sexta, 6h/dia = 30h semanais).", bullet_style))
    story.append(Paragraph("4. <b>Registro de Presença:</b> Diariamente, o aluno bate o ponto (horário de entrada, saída e resumo das atividades realizadas).", bullet_style))
    story.append(Paragraph("5. <b>Validação do Supervisor:</b> O supervisor da empresa analisa o registro e emite parecer (Aprovado / Recusado).", bullet_style))
    story.append(Paragraph("6. <b>Cômputo Acadêmico:</b> As horas aprovadas somam automaticamente no saldo do aluno até a conclusão da meta.", bullet_style))

    story.append(Spacer(1, 6))
    story.append(Paragraph("<b>4.2 Ciclo de Vida das Empresas Conveniadas & Localização via CNPJ:</b>", h2_style))
    story.append(Paragraph("1. <b>Upload do PDF Oficial:</b> O admin envia o documento fornecido pelo CEFET/DIPEX em <code>/admin/empresas</code>.", bullet_style))
    story.append(Paragraph("2. <b>Extração Inteligente:</b> O script Python higieniza erros de digitação (ex: 29/02 em anos não bissextos) e gera o CSV.", bullet_style))
    story.append(Paragraph("3. <b>Persistência sem Duplicidade:</b> O sistema filtra os CNPJs existentes e insere apenas empresas novas no MySQL.", bullet_style))
    story.append(Paragraph("4. <b>Enriquecimento via BrasilAPI:</b> Consulta pública na Receita Federal para preenchimento de Município, UF e Endereço.", bullet_style))
    story.append(Paragraph("5. <b>Base Pré-Embarcada:</b> O arquivo <code>database/seeders/data/empresas_conveniadas.json</code> mantém todas as 1.608 empresas e 1.462 cidades fixadas no repositório, dispensando reconsultas na API em novas máquinas.", bullet_style))

    # 5. Tutorial de Instalação
    story.append(Paragraph("5. Tutorial de Instalação: Primeira Vez em uma Nova Máquina", h1_style))
    story.append(make_alert_box(
        "💡 Pré-Requisitos Necessários:",
        "Instale no computador físico apenas o <b>Docker Desktop</b> e o <b>Git</b>. Não instale PHP, Composer, MySQL ou Node.js!",
        bg_hex='#eff6ff', border_hex='#3b82f6', text_hex='#1e40af'
    ))

    story.append(Paragraph("<b>Passo 1: Clonar o Repositório Git</b>", h2_style))
    story.append(make_code_box("git clone URL_DO_SEU_REPOSITORIO portal-de-estagio\ncd portal-de-estagio"))

    story.append(Paragraph("<b>Passo 2: Configurar o Arquivo .env</b>", h2_style))
    story.append(make_code_box("# No Windows PowerShell:\ncopy .env.example .env\n\n# No Linux ou macOS:\ncp .env.example .env"))
    story.append(Paragraph("Certifique-se de que a conexão do banco no <code>.env</code> esteja configurada com as portas do Docker:", body_style))
    story.append(make_code_box("DB_CONNECTION=mysql\nDB_HOST=db\nDB_PORT=3307\nDB_DATABASE=projeto\nDB_USERNAME=root\nDB_PASSWORD="))

    story.append(Paragraph("<b>Passo 3: Subir os Containers do Docker</b>", h2_style))
    story.append(make_code_box("docker compose up -d --build"))

    story.append(Paragraph("<b>Passo 4: Preparar a Aplicação Laravel e Popular o Banco</b>", h2_style))
    story.append(make_code_box("""# 1. Instalar dependências PHP
docker compose exec app composer install

# 2. Gerar chave da aplicação
docker compose exec app php artisan key:generate

# 3. Criar tabelas no banco de dados
docker compose exec app php artisan migrate

# 4. Popular com dados iniciais (Admin + Empresas + Cidades)
docker compose exec app php artisan db:seed

# 5. Compilar os estilos CSS e scripts Vite
docker compose exec app npm install
docker compose exec app npm run build

# 6. Limpar e otimizar caches
docker compose exec app php artisan optimize:clear"""))

    story.append(Paragraph("<b>Passo 5: Acessar no Navegador</b>", h2_style))
    story.append(Paragraph("Abra o navegador em: <b>http://localhost:8000</b>", body_style))
    story.append(make_alert_box(
        "🔑 Credenciais do Administrador Padrão:",
        "<b>E-mail:</b> admin@cefet.br &bull; <b>Senha:</b> admin123",
        bg_hex='#f0fdf4', border_hex='#22c55e', text_hex='#166534'
    ))

    story.append(PageBreak())

    # 6. Dia a Dia
    story.append(Paragraph("6. Como Rodar o Projeto no Dia a Dia", h1_style))
    story.append(Paragraph("Após a instalação inicial, você <b>não precisa</b> reinstalar nada! O fluxo diário consiste apenas em ligar e desligar:", body_style))
    
    tabela_dia_data = [
        [Paragraph("<b>Objetivo</b>", body_style), Paragraph("<b>Comando no Terminal</b>", body_style)],
        [Paragraph("Ligar o projeto", body_style), Paragraph("<code>docker compose up -d</code>", body_style)],
        [Paragraph("Desligar o projeto", body_style), Paragraph("<code>docker compose down</code>", body_style)],
        [Paragraph("Modo desenvolvimento de CSS/JS (Hot Reload)", body_style), Paragraph("<code>docker compose exec app npm run dev</code>", body_style)],
        [Paragraph("Limpar caches do Laravel", body_style), Paragraph("<code>docker compose exec app php artisan optimize:clear</code>", body_style)],
        [Paragraph("Ver logs em tempo real", body_style), Paragraph("<code>docker compose exec app tail -f storage/logs/laravel.log</code>", body_style)],
        [Paragraph("Salvar base atualizada no Seeder", body_style), Paragraph("<code>docker compose exec app php artisan empresas:salvar-seeder</code>", body_style)],
        [Paragraph("Acessar o terminal bash do container", body_style), Paragraph("<code>docker compose exec app bash</code>", body_style)],
    ]
    tabela_dia = Table(tabela_dia_data, colWidths=[185, 330])
    tabela_dia.setStyle(TableStyle([
        ('BACKGROUND', (0,0), (-1,0), colors.HexColor('#f1f5f9')),
        ('BOX', (0,0), (-1,-1), 0.5, colors.HexColor('#cbd5e1')),
        ('INNERGRID', (0,0), (-1,-1), 0.5, colors.HexColor('#e2e8f0')),
        ('TOPPADDING', (0,0), (-1,-1), 4),
        ('BOTTOMPADDING', (0,0), (-1,-1), 4),
    ]))
    story.append(tabela_dia)

    # 7. Git & Commits
    story.append(Paragraph("7. Guia de Git, Commits & Boas Práticas", h1_style))
    story.append(Paragraph("<b>1. Sincronização Diária:</b> Comece o dia sempre atualizando sua branch principal:", body_style))
    story.append(make_code_box("git checkout main\ngit pull origin main"))

    story.append(Paragraph("<b>2. Crie uma Branch Temática para cada Tarefa:</b>", body_style))
    story.append(make_code_box("git checkout -b feature/nome-da-funcionalidade\n# ou para correções:\ngit checkout -b fix/descricao-do-bug"))

    story.append(Paragraph("<b>3. Padrão de Mensagens Semânticas (Conventional Commits):</b>", body_style))
    story.append(Paragraph("&bull; <code>feat:</code> Nova funcionalidade adicionada ao sistema.", bullet_style))
    story.append(Paragraph("&bull; <code>fix:</code> Correção de um bug ou erro inesperado.", bullet_style))
    story.append(Paragraph("&bull; <code>docs:</code> Alteração em documentações, tutoriais ou README.", bullet_style))
    story.append(Paragraph("&bull; <code>style:</code> Ajustes visuais ou de formatação sem mudança de regras.", bullet_style))
    story.append(Paragraph("&bull; <code>refactor:</code> Refatoração interna de código para melhoria de arquitetura.", bullet_style))
    story.append(make_code_box("""git add .
git commit -m "feat: adicionar busca automatica de localizacao via cnpj"
git push -u origin feature/nome-da-funcionalidade"""))

    story.append(make_alert_box(
        "⚠️ O que NUNCA commitar no Git:",
        "Nunca comite arquivos com senhas reais (<code>.env</code>), pastas de dependências (<code>vendor/</code>, <code>node_modules/</code>) ou arquivos de cache. Todos esses itens já estão protegidos pelo arquivo <code>.gitignore</code>.",
        bg_hex='#fffbeb', border_hex='#f59e0b', text_hex='#92400e'
    ))

    # 8. Estrutura
    story.append(Paragraph("8. Estrutura de Pastas do Projeto", h1_style))
    story.append(make_code_box("""portal-de-estagio/
├── app/
│   ├── Console/Commands/      # Comandos artisan personalizados
│   ├── Http/Controllers/      # Controladores das páginas e regras de rotas
│   ├── Models/                # Entidades Eloquent (banco de dados)
│   └── Services/              # Serviços de negócio (Importação PDF, BrasilAPI)
├── database/
│   ├── migrations/            # Histórico e versionamento de tabelas SQL
│   └── seeders/               # Dados padrão (AdminUserSeeder, EmpresasConveniadasSeeder)
│       └── data/              # Base embarcada: empresas_conveniadas.json
├── resources/views/           # Telas em Blade e componentes Tailwind
├── routes/web.php             # Mapeamento de todas as URLs da aplicação
├── tutoriais/                 # Manuais, guias em PDF e documentação do projeto
├── docker-compose.yml         # Configuração dos containers Docker (app e db)
└── Dockerfile                 # Construção da imagem PHP com extensões e Node"""))

    # 9. Troubleshooting
    story.append(Paragraph("9. Solução de Problemas Comuns (Troubleshooting)", h1_style))
    story.append(Paragraph("&bull; <b>Porta 8000 ou 3307 ocupada:</b> Verifique se você não tem outro container ou MySQL/XAMPP aberto na máquina.", bullet_style))
    story.append(Paragraph("&bull; <b>Telas sem estilização CSS:</b> Execute <code>docker compose exec app npm run build</code> para compilar o Tailwind.", bullet_style))
    story.append(Paragraph("&bull; <b>Erro de permissão no storage:</b> Execute <code>docker compose exec app chmod -R 777 storage bootstrap/cache</code>.", bullet_style))
    story.append(Paragraph("&bull; <b>Resetar e recriar o banco do zero:</b> Execute <code>docker compose exec app php artisan migrate:fresh --seed</code>.", bullet_style))

    doc.build(story, canvasmaker=NumberedCanvas)
    print(f"PDF gerado com sucesso em: {dest_path}")

if __name__ == '__main__':
    saida = sys.argv[1] if len(sys.argv) > 1 else 'MANUAL_DO_SISTEMA_E_GUIA_DO_DESENVOLVEDOR.pdf'
    build_pdf(saida)
