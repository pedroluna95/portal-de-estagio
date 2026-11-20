@extends('layouts.app')

@section('content')
<main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8">
    {{-- Hero com Gradiente Suave --}}
    <div class="relative overflow-hidden rounded-3xl bg-gradient-to-r from-blue-600 via-indigo-600 to-blue-700 text-white p-8 sm:p-10 shadow-xl shadow-blue-500/10">
        <div class="relative z-10 max-w-3xl">
            <div class="inline-flex items-center gap-2 px-3 py-1 bg-white/20 backdrop-blur-md rounded-full text-xs font-semibold text-blue-100 mb-4">
                <span class="material-symbols-outlined text-[16px]">map</span>
                Guia Oficial do Aluno UFCI-RJ
            </div>
            <h1 class="text-3xl sm:text-4xl font-black tracking-tight mb-3">Trilha Completa do Estágio</h1>
            <p class="text-blue-100 text-base sm:text-lg leading-relaxed">
                7 etapas estruturadas para você entender, formalizar, cumprir e concluir seu estágio com tranquilidade.
            </p>

            <div class="flex flex-wrap gap-2.5 mt-6 text-xs sm:text-sm font-semibold">
                <span class="inline-flex items-center gap-1.5 px-3.5 py-1.5 bg-white/15 backdrop-blur-md rounded-xl border border-white/20">
                    <span class="material-symbols-outlined text-[18px]">route</span> 7 Etapas
                </span>
                <span class="inline-flex items-center gap-1.5 px-3.5 py-1.5 bg-white/15 backdrop-blur-md rounded-xl border border-white/20">
                    <span class="material-symbols-outlined text-[18px]">schedule</span> Mínimo 240h a 400h
                </span>
                <span class="inline-flex items-center gap-1.5 px-3.5 py-1.5 bg-white/15 backdrop-blur-md rounded-xl border border-white/20">
                    <span class="material-symbols-outlined text-[18px]">domain</span> Convênios Ativos
                </span>
            </div>
        </div>
        {{-- Detalhe visual de fundo --}}
        <div class="absolute -right-16 -bottom-16 w-80 h-80 bg-white/10 rounded-full blur-3xl pointer-events-none"></div>
    </div>

    {{-- Linha do Tempo e Passos --}}
    <div class="space-y-4">
        {{-- Etapa 1 --}}
        <div class="rounded-2xl border border-slate-200/80 dark:border-slate-800 bg-white dark:bg-slate-900 p-6 sm:p-7 shadow-sm transition-all hover:border-blue-300 dark:hover:border-blue-900/60 flex flex-col sm:flex-row gap-5">
            <div class="w-12 h-12 rounded-2xl bg-blue-50 dark:bg-blue-950/60 text-blue-600 dark:text-blue-400 flex items-center justify-center shrink-0 shadow-sm">
                <span class="material-symbols-outlined text-[26px]">lightbulb</span>
            </div>
            <div>
                <span class="text-xs font-bold uppercase tracking-wider text-blue-600 dark:text-blue-400 mb-1 block">Etapa 01</span>
                <h2 class="text-lg font-bold text-slate-900 dark:text-slate-100 mb-2">Conceito de Estágio</h2>
                <p class="text-sm text-slate-600 dark:text-slate-400 leading-relaxed">
                    O estágio é um ato educativo escolar supervisionado que prepara o estudante para o ambiente produtivo, colocando em prática as competências adquiridas durante as aulas. É regulamentado pela Lei nº 11.788/2008 e faz parte do projeto pedagógico do curso.
                </p>
            </div>
        </div>

        {{-- Etapa 2 --}}
        <div class="rounded-2xl border border-slate-200/80 dark:border-slate-800 bg-white dark:bg-slate-900 p-6 sm:p-7 shadow-sm transition-all hover:border-purple-300 dark:hover:border-purple-900/60 flex flex-col sm:flex-row gap-5">
            <div class="w-12 h-12 rounded-2xl bg-purple-50 dark:bg-purple-950/60 text-purple-600 dark:text-purple-400 flex items-center justify-center shrink-0 shadow-sm">
                <span class="material-symbols-outlined text-[26px]">category</span>
            </div>
            <div>
                <span class="text-xs font-bold uppercase tracking-wider text-purple-600 dark:text-purple-400 mb-1 block">Etapa 02</span>
                <h2 class="text-lg font-bold text-slate-900 dark:text-slate-100 mb-2">Tipos de Estágio</h2>
                <p class="text-sm text-slate-600 dark:text-slate-400 leading-relaxed">
                    Pode ser <strong>obrigatório</strong> (requisito curricular para obtenção de diploma) ou <strong>não obrigatório</strong> (opcional para enriquecimento de currículo). Pode ocorrer de forma interna (dentro de laboratórios, setores ou projetos da própria UFCI) ou externa (em empresas conveniadas).
                </p>
            </div>
        </div>

        {{-- Etapa 3 --}}
        <div class="rounded-2xl border border-slate-200/80 dark:border-slate-800 bg-white dark:bg-slate-900 p-6 sm:p-7 shadow-sm transition-all hover:border-amber-300 dark:hover:border-amber-900/60 flex flex-col sm:flex-row gap-5">
            <div class="w-12 h-12 rounded-2xl bg-amber-50 dark:bg-amber-950/60 text-amber-600 dark:text-amber-400 flex items-center justify-center shrink-0 shadow-sm">
                <span class="material-symbols-outlined text-[26px]">timer</span>
            </div>
            <div>
                <span class="text-xs font-bold uppercase tracking-wider text-amber-600 dark:text-amber-400 mb-1 block">Etapa 03</span>
                <h2 class="text-lg font-bold text-slate-900 dark:text-slate-100 mb-2">Carga Horária e Equivalências</h2>
                <p class="text-sm text-slate-600 dark:text-slate-400 leading-relaxed">
                    A matriz curricular do curso prevê até <strong>400 horas</strong>. Alunos que desempenharem atividades de monitoria ou projetos de extensão podem aproveitar até <strong>160 horas</strong>, reduzindo a meta mínima de estágio prático para <strong>240 horas</strong>.
                </p>
            </div>
        </div>

        {{-- Etapa 4 --}}
        <div class="rounded-2xl border border-slate-200/80 dark:border-slate-800 bg-white dark:bg-slate-900 p-6 sm:p-7 shadow-sm transition-all hover:border-emerald-300 dark:hover:border-emerald-900/60 flex flex-col sm:flex-row gap-5">
            <div class="w-12 h-12 rounded-2xl bg-emerald-50 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 flex items-center justify-center shrink-0 shadow-sm">
                <span class="material-symbols-outlined text-[26px]">domain</span>
            </div>
            <div class="flex-1">
                <span class="text-xs font-bold uppercase tracking-wider text-emerald-600 dark:text-emerald-400 mb-1 block">Etapa 04</span>
                <h2 class="text-lg font-bold text-slate-900 dark:text-slate-100 mb-2">Empresas Parceiras e Convênios</h2>
                <p class="text-sm text-slate-600 dark:text-slate-400 leading-relaxed">
                    A UFCI mantém parceria ativa com dezenas de empresas de tecnologia e indústria. Empresas com convênio vigente dispensam burocracia complexa para o início do contrato de estágio.
                </p>
                <div class="mt-4">
                    <a href="/empresas" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold shadow-sm transition-colors">
                        <span class="material-symbols-outlined text-[18px]">domain</span>
                        Explorar Catálogo de Empresas Conveniadas
                    </a>
                </div>
            </div>
        </div>

        {{-- Etapa 5 --}}
        <div class="rounded-2xl border border-slate-200/80 dark:border-slate-800 bg-white dark:bg-slate-900 p-6 sm:p-7 shadow-sm transition-all hover:border-indigo-300 dark:hover:border-indigo-900/60 flex flex-col sm:flex-row gap-5">
            <div class="w-12 h-12 rounded-2xl bg-indigo-50 dark:bg-indigo-950/60 text-indigo-600 dark:text-indigo-400 flex items-center justify-center shrink-0 shadow-sm">
                <span class="material-symbols-outlined text-[26px]">description</span>
            </div>
            <div>
                <span class="text-xs font-bold uppercase tracking-wider text-indigo-600 dark:text-indigo-400 mb-1 block">Etapa 05</span>
                <h2 class="text-lg font-bold text-slate-900 dark:text-slate-100 mb-2">Documentação Inicial Necessária</h2>
                <p class="text-sm text-slate-600 dark:text-slate-400 leading-relaxed">
                    Antes de iniciar as atividades, é indispensável a emissão e assinatura do <strong>Termo de Compromisso de Estágio (TCE)</strong>, do <strong>Plano de Atividades</strong> assinado pelo orientador e a contratação de apólice de seguro contra acidentes.
                </p>
            </div>
        </div>

        {{-- Etapa 6 --}}
        <div class="rounded-2xl border border-slate-200/80 dark:border-slate-800 bg-white dark:bg-slate-900 p-6 sm:p-7 shadow-sm transition-all hover:border-cyan-300 dark:hover:border-cyan-900/60 flex flex-col sm:flex-row gap-5">
            <div class="w-12 h-12 rounded-2xl bg-cyan-50 dark:bg-cyan-950/60 text-cyan-600 dark:text-cyan-400 flex items-center justify-center shrink-0 shadow-sm">
                <span class="material-symbols-outlined text-[26px]">calendar_month</span>
            </div>
            <div class="flex-1">
                <span class="text-xs font-bold uppercase tracking-wider text-cyan-600 dark:text-cyan-400 mb-1 block">Etapa 06</span>
                <h2 class="text-lg font-bold text-slate-900 dark:text-slate-100 mb-2">Controle e Lançamento de Presenças</h2>
                <p class="text-sm text-slate-600 dark:text-slate-400 leading-relaxed">
                    Utilize o calendário digital do portal NEXO para marcar suas horas trabalhadas conforme a jornada acordada. Presenças oficiais serão enviadas diretamente ao supervisor para validação periódica.
                </p>
                <div class="mt-4">
                    <a href="/controle-horas" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 hover:bg-slate-100 text-slate-700 dark:text-slate-200 text-xs font-semibold transition-colors">
                        <span class="material-symbols-outlined text-[18px]">schedule</span>
                        Ir para Controle de Horas
                    </a>
                </div>
            </div>
        </div>

        {{-- Etapa 7 --}}
        <div class="rounded-2xl border border-slate-200/80 dark:border-slate-800 bg-white dark:bg-slate-900 p-6 sm:p-7 shadow-sm transition-all hover:border-emerald-300 dark:hover:border-emerald-900/60 flex flex-col sm:flex-row gap-5">
            <div class="w-12 h-12 rounded-2xl bg-emerald-50 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 flex items-center justify-center shrink-0 shadow-sm">
                <span class="material-symbols-outlined text-[26px]">task_alt</span>
            </div>
            <div class="flex-1">
                <span class="text-xs font-bold uppercase tracking-wider text-emerald-600 dark:text-emerald-400 mb-1 block">Etapa 07</span>
                <h2 class="text-lg font-bold text-slate-900 dark:text-slate-100 mb-2">Conclusão e Relatório Final</h2>
                <p class="text-sm text-slate-600 dark:text-slate-400 leading-relaxed">
                    Ao atingir a carga horária estabelecida, preencha a Ficha Individual de Frequência e elabore o Relatório de Estágio com supervisão do professor orientador para deferimento acadêmico.
                </p>
                <div class="mt-4 flex flex-wrap gap-2.5">
                    <a href="{{ route('ficha.download') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 text-xs font-semibold transition-colors">
                        <span class="material-symbols-outlined text-[18px] text-emerald-600">file_download</span>
                        Baixar Ficha de Frequência (.xlsx)
                    </a>
                    <a href="{{ asset('documents/CEFET -Relatorio de estagio modelo 5.docx') }}" download class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 text-xs font-semibold transition-colors">
                        <span class="material-symbols-outlined text-[18px] text-blue-600">file_download</span>
                        Baixar Modelo de Relatório (.docx)
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- Banner de Ação Final --}}
    <div class="rounded-3xl bg-slate-900 text-white p-8 sm:p-10 text-center relative overflow-hidden shadow-lg">
        <div class="relative z-10 max-w-2xl mx-auto">
            <h2 class="text-2xl sm:text-3xl font-black mb-3">Tudo pronto para dar o próximo passo?</h2>
            <p class="text-slate-300 text-sm sm:text-base mb-6">
                Consulte as empresas conveniadas para encontrar oportunidades ou inicie seu cadastro de frequência.
            </p>
            <div class="flex flex-wrap justify-center gap-3">
                <a href="/empresas" class="px-6 py-3 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold shadow-sm transition-colors">
                    Ver Empresas Conveniadas
                </a>
                <a href="/dashboard" class="px-6 py-3 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200 text-sm font-semibold transition-colors">
                    Acessar Minha Dashboard
                </a>
            </div>
        </div>
    </div>
</main>
@endsection
