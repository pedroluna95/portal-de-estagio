@extends('layouts.app')

@section('content')
<main class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8 space-y-8">

    {{-- Topo da Dashboard com Boas-Vindas e Ações Rápidas --}}
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 border-b border-slate-200 dark:border-slate-800 pb-6">
        <div>
            <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-blue-50 dark:bg-blue-950/60 text-blue-600 dark:text-blue-400 border border-blue-200/60 dark:border-blue-900/40 mb-2">
                <span class="material-symbols-outlined text-[14px]">shield</span>
                Painel Administrativo Geral
            </div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 dark:text-slate-100 tracking-tight">
                Dashboard de Controle
            </h1>
            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                Gerencie usuários, convênios de empresas, estágios e parametrizações do portal NEXO.
            </p>
        </div>

        <div class="flex flex-wrap items-center gap-2.5">
            <a href="{{ route('admin.usuarios.create') }}"
               class="inline-flex items-center gap-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white px-4 py-2.5 text-sm font-semibold shadow-sm shadow-blue-500/20 transition-all hover:shadow-md">
                <span class="material-symbols-outlined text-[20px]">person_add</span>
                Cadastrar Usuário
            </a>
            <a href="{{ route('admin.empresas') }}"
               class="inline-flex items-center gap-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 hover:bg-slate-50 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 px-4 py-2.5 text-sm font-semibold shadow-sm transition-all">
                <span class="material-symbols-outlined text-[20px] text-emerald-600">upload_file</span>
                Importar Convênios
            </a>
            <a href="{{ route('admin.tables') }}"
               class="inline-flex items-center gap-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 hover:bg-slate-50 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 px-3.5 py-2.5 text-sm font-semibold shadow-sm transition-all"
               title="Visualizador de dados do sistema">
                <span class="material-symbols-outlined text-[20px] text-amber-500">database</span>
            </a>
        </div>
    </div>

    {{-- Cards de Métricas Principais --}}
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3 sm:gap-4">
        @php
            $metricas = [
                ['icon'=>'groups', 'label'=>'Total Usuários', 'valor'=>$contadores['total_usuarios'], 'cor'=>'blue', 'aba'=>'usuarios', 'bg'=>'bg-blue-50 dark:bg-blue-950/40', 'text'=>'text-blue-600 dark:text-blue-400'],
                ['icon'=>'school', 'label'=>'Alunos Ativos', 'valor'=>$contadores['total_alunos'], 'cor'=>'indigo', 'aba'=>'alunos', 'bg'=>'bg-indigo-50 dark:bg-indigo-950/40', 'text'=>'text-indigo-600 dark:text-indigo-400'],
                ['icon'=>'person_pin', 'label'=>'Orientadores', 'valor'=>$contadores['total_orientadores'], 'cor'=>'violet', 'aba'=>'orientadores', 'bg'=>'bg-violet-50 dark:bg-violet-950/40', 'text'=>'text-violet-600 dark:text-violet-400'],
                ['icon'=>'badge', 'label'=>'Supervisores', 'valor'=>$contadores['total_supervisores'], 'cor'=>'purple', 'aba'=>'supervisores', 'bg'=>'bg-purple-50 dark:bg-purple-950/40', 'text'=>'text-purple-600 dark:text-purple-400'],
                ['icon'=>'domain', 'label'=>'Empresas', 'valor'=>$contadores['total_empresas'], 'cor'=>'emerald', 'aba'=>'empresas', 'bg'=>'bg-emerald-50 dark:bg-emerald-950/40', 'text'=>'text-emerald-600 dark:text-emerald-400'],
                ['icon'=>'work', 'label'=>'Estágios', 'valor'=>$contadores['total_estagios'], 'cor'=>'amber', 'aba'=>'estagios', 'bg'=>'bg-amber-50 dark:bg-amber-950/40', 'text'=>'text-amber-600 dark:text-amber-400'],
            ];
        @endphp

        @foreach($metricas as $m)
        <a href="?aba={{ $m['aba'] }}"
           class="group relative rounded-2xl p-4 sm:p-5 border transition-all duration-200 flex flex-col justify-between
                  {{ $abaAtiva === $m['aba']
                      ? 'bg-white dark:bg-slate-800 border-blue-500 ring-2 ring-blue-500/20 shadow-md'
                      : 'bg-white dark:bg-slate-900 border-slate-200/80 dark:border-slate-800 hover:border-slate-300 dark:hover:border-slate-700 hover:shadow-sm' }}">
            <div class="flex items-center justify-between mb-3">
                <div class="w-10 h-10 rounded-xl {{ $m['bg'] }} {{ $m['text'] }} flex items-center justify-center transition-transform group-hover:scale-105">
                    <span class="material-symbols-outlined text-[22px]">{{ $m['icon'] }}</span>
                </div>
                @if($abaAtiva === $m['aba'])
                    <span class="w-2 h-2 rounded-full bg-blue-500"></span>
                @endif
            </div>
            <div>
                <span class="text-2xl sm:text-3xl font-black text-slate-900 dark:text-slate-100 tracking-tight block">
                    {{ number_format($m['valor'], 0, ',', '.') }}
                </span>
                <span class="text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider mt-1 block">
                    {{ $m['label'] }}
                </span>
            </div>
        </a>
        @endforeach
    </div>

    {{-- Barra de Navegação por Abas (Submenu Administrativo) --}}
    <div class="border-b border-slate-200 dark:border-slate-800">
        <nav class="flex gap-2 overflow-x-auto pb-px" aria-label="Abas de Administração">
            @php
                $abas = [
                    'visao-geral' => ['icon'=>'dashboard', 'label'=>'Visão Geral'],
                    'usuarios'    => ['icon'=>'groups', 'label'=>'Usuários'],
                    'alunos'      => ['icon'=>'school', 'label'=>'Alunos'],
                    'orientadores'=> ['icon'=>'person_pin', 'label'=>'Orientadores'],
                    'supervisores'=> ['icon'=>'badge', 'label'=>'Supervisores'],
                    'empresas'    => ['icon'=>'domain', 'label'=>'Empresas Conveniadas'],
                    'estagios'    => ['icon'=>'work', 'label'=>'Estágios'],
                ];
            @endphp
            @foreach($abas as $chave => $aba)
                <a href="?aba={{ $chave }}"
                   class="group flex items-center gap-2 whitespace-nowrap px-4 py-3 text-sm font-semibold transition-all border-b-2 -mb-px rounded-t-xl
                          {{ $abaAtiva === $chave
                              ? 'border-blue-600 text-blue-600 dark:text-blue-400 dark:border-blue-400 bg-blue-50/50 dark:bg-blue-950/30'
                              : 'border-transparent text-slate-600 hover:text-slate-900 hover:border-slate-300 dark:text-slate-400 dark:hover:text-slate-200 dark:hover:border-slate-700' }}">
                    <span class="material-symbols-outlined text-[20px] {{ $abaAtiva === $chave ? 'text-blue-600 dark:text-blue-400' : 'text-slate-400 group-hover:text-slate-600 dark:text-slate-500 dark:group-hover:text-slate-300' }}">
                        {{ $aba['icon'] }}
                    </span>
                    {{ $aba['label'] }}
                </a>
            @endforeach
        </nav>
    </div>

    {{-- CONTEÚDO DAS ABAS --}}

    @if($abaAtiva === 'visao-geral')
        {{-- VISÃO GERAL --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            {{-- Coluna 1 e 2: Resumo de Estágios e Atividades Recentes --}}
            <div class="lg:col-span-2 space-y-6">

                {{-- Status Geral dos Estágios --}}
                <div class="rounded-2xl border border-slate-200/80 dark:border-slate-800 bg-white dark:bg-slate-900 p-6 shadow-sm">
                    <div class="flex items-center justify-between mb-5">
                        <div class="flex items-center gap-2.5">
                            <span class="material-symbols-outlined text-blue-600 dark:text-blue-400">donut_large</span>
                            <h2 class="text-base font-bold text-slate-900 dark:text-slate-100">Situação Atual dos Estágios</h2>
                        </div>
                        <a href="?aba=estagios" class="text-xs font-semibold text-blue-600 dark:text-blue-400 hover:underline">Ver todos os estágios &rarr;</a>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div class="p-4 rounded-xl bg-emerald-50/60 dark:bg-emerald-950/30 border border-emerald-200/60 dark:border-emerald-900/40">
                            <div class="flex items-center justify-between">
                                <span class="text-xs font-semibold uppercase tracking-wider text-emerald-700 dark:text-emerald-400">Ativos em Andamento</span>
                                <span class="material-symbols-outlined text-emerald-600 text-[20px]">play_circle</span>
                            </div>
                            <p class="text-3xl font-extrabold text-emerald-800 dark:text-emerald-300 mt-2">{{ $contadores['estagios_ativos'] }}</p>
                            <p class="text-xs text-emerald-600/80 dark:text-emerald-400/80 mt-1">Alunos cumprindo jornada</p>
                        </div>

                        <div class="p-4 rounded-xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200/60 dark:border-slate-700/60">
                            <div class="flex items-center justify-between">
                                <span class="text-xs font-semibold uppercase tracking-wider text-slate-700 dark:text-slate-300">Concluídos / Finalizados</span>
                                <span class="material-symbols-outlined text-slate-500 text-[20px]">check_circle</span>
                            </div>
                            <p class="text-3xl font-extrabold text-slate-900 dark:text-slate-100 mt-2">{{ max(0, $contadores['total_estagios'] - $contadores['estagios_ativos']) }}</p>
                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Horas entregues e validadas</p>
                        </div>

                        <div class="p-4 rounded-xl bg-blue-50/60 dark:bg-blue-950/30 border border-blue-200/60 dark:border-blue-900/40">
                            <div class="flex items-center justify-between">
                                <span class="text-xs font-semibold uppercase tracking-wider text-blue-700 dark:text-blue-400">Presenças Lançadas</span>
                                <span class="material-symbols-outlined text-blue-600 text-[20px]">event_available</span>
                            </div>
                            <p class="text-3xl font-extrabold text-blue-800 dark:text-blue-300 mt-2">{{ number_format($contadores['presencas_totais'], 0, ',', '.') }}</p>
                            <p class="text-xs text-blue-600/80 dark:text-blue-400/80 mt-1">Registros totais de ponto</p>
                        </div>
                    </div>
                </div>

                {{-- Últimos Estágios Cadastrados --}}
                <div class="rounded-2xl border border-slate-200/80 dark:border-slate-800 bg-white dark:bg-slate-900 p-6 shadow-sm">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center gap-2">
                            <span class="material-symbols-outlined text-indigo-600 dark:text-indigo-400">history_edu</span>
                            <h2 class="text-base font-bold text-slate-900 dark:text-slate-100">Estágios Recentes</h2>
                        </div>
                        <a href="?aba=estagios" class="text-xs font-semibold text-blue-600 dark:text-blue-400 hover:underline">Ver lista completa</a>
                    </div>

                    <div class="divide-y divide-slate-100 dark:divide-slate-800">
                        @forelse($ultimos_estagios ?? [] as $e)
                        <div class="py-3.5 flex items-center justify-between gap-4">
                            <div class="flex items-center gap-3 min-w-0">
                                <div class="w-9 h-9 rounded-xl bg-indigo-50 dark:bg-indigo-950/60 text-indigo-600 dark:text-indigo-400 flex items-center justify-center shrink-0">
                                    <span class="material-symbols-outlined text-[20px]">person</span>
                                </div>
                                <div class="min-w-0">
                                    <p class="text-sm font-semibold text-slate-900 dark:text-slate-100 truncate">
                                        {{ $e->aluno->nome ?? 'Aluno não identificado' }}
                                    </p>
                                    <p class="text-xs text-slate-500 dark:text-slate-400 truncate">
                                        {{ $e->local }} · Orientador: {{ $e->orientador->usuario->Nome ?? '—' }}
                                    </p>
                                </div>
                            </div>
                            <span class="shrink-0 px-2.5 py-1 rounded-full text-xs font-semibold
                                {{ $e->status === 'ativo'
                                    ? 'bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-400 border border-emerald-200/60 dark:border-emerald-900/40'
                                    : 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300' }}">
                                {{ ucfirst($e->status) }}
                            </span>
                        </div>
                        @empty
                        <div class="py-8 text-center text-sm text-slate-500 dark:text-slate-400">
                            Nenhum estágio registrado até o momento.
                        </div>
                        @endforelse
                    </div>
                </div>
            </div>

            {{-- Coluna 3: Ações Administrativas e Usuários Recentes --}}
            <div class="space-y-6">

                {{-- Módulos Rápidos do Sistema --}}
                <div class="rounded-2xl border border-slate-200/80 dark:border-slate-800 bg-white dark:bg-slate-900 p-6 shadow-sm">
                    <h2 class="text-base font-bold text-slate-900 dark:text-slate-100 mb-4 flex items-center gap-2">
                        <span class="material-symbols-outlined text-blue-600 dark:text-blue-400">bolt</span>
                        Módulos de Gestão
                    </h2>
                    <div class="space-y-2.5">
                        <a href="{{ route('admin.usuarios') }}"
                           class="flex items-center gap-3.5 p-3 rounded-xl border border-slate-200/80 dark:border-slate-800 hover:border-blue-300 dark:hover:border-blue-900/60 hover:bg-blue-50/50 dark:hover:bg-blue-950/20 transition-all group">
                            <div class="w-9 h-9 rounded-lg bg-blue-100 dark:bg-blue-900/50 text-blue-600 dark:text-blue-400 flex items-center justify-center shrink-0 group-hover:scale-105 transition-transform">
                                <span class="material-symbols-outlined text-[20px]">manage_accounts</span>
                            </div>
                            <div>
                                <p class="text-sm font-semibold text-slate-900 dark:text-slate-100">Gerenciador de Usuários</p>
                                <p class="text-xs text-slate-500 dark:text-slate-400">CRUD completo, permissões e busca</p>
                            </div>
                        </a>

                        <a href="{{ route('admin.empresas') }}"
                           class="flex items-center gap-3.5 p-3 rounded-xl border border-slate-200/80 dark:border-slate-800 hover:border-emerald-300 dark:hover:border-emerald-900/60 hover:bg-emerald-50/50 dark:hover:bg-emerald-950/20 transition-all group">
                            <div class="w-9 h-9 rounded-lg bg-emerald-100 dark:bg-emerald-900/50 text-emerald-600 dark:text-emerald-400 flex items-center justify-center shrink-0 group-hover:scale-105 transition-transform">
                                <span class="material-symbols-outlined text-[20px]">domain_add</span>
                            </div>
                            <div>
                                <p class="text-sm font-semibold text-slate-900 dark:text-slate-100">Convênios e Empresas</p>
                                <p class="text-xs text-slate-500 dark:text-slate-400">Importação em lote de PDF oficial</p>
                            </div>
                        </a>

                        <a href="{{ route('admin.tables') }}"
                           class="flex items-center gap-3.5 p-3 rounded-xl border border-slate-200/80 dark:border-slate-800 hover:border-amber-300 dark:hover:border-amber-900/60 hover:bg-amber-50/50 dark:hover:bg-amber-950/20 transition-all group">
                            <div class="w-9 h-9 rounded-lg bg-amber-100 dark:bg-amber-900/50 text-amber-600 dark:text-amber-400 flex items-center justify-center shrink-0 group-hover:scale-105 transition-transform">
                                <span class="material-symbols-outlined text-[20px]">table_view</span>
                            </div>
                            <div>
                                <p class="text-sm font-semibold text-slate-900 dark:text-slate-100">Inspetor de Banco de Dados</p>
                                <p class="text-xs text-slate-500 dark:text-slate-400">Consultar tabelas e registros brutos</p>
                            </div>
                        </a>
                    </div>
                </div>

                {{-- Últimos Usuários Cadastrados --}}
                <div class="rounded-2xl border border-slate-200/80 dark:border-slate-800 bg-white dark:bg-slate-900 p-6 shadow-sm">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-base font-bold text-slate-900 dark:text-slate-100 flex items-center gap-2">
                            <span class="material-symbols-outlined text-purple-600 dark:text-purple-400">recent_actors</span>
                            Novos Usuários
                        </h2>
                        <a href="?aba=usuarios" class="text-xs font-semibold text-blue-600 dark:text-blue-400 hover:underline">Ver todos</a>
                    </div>

                    <div class="divide-y divide-slate-100 dark:divide-slate-800">
                        @forelse($ultimos_usuarios ?? [] as $u)
                        <div class="py-3 flex items-center justify-between">
                            <div class="flex items-center gap-2.5 min-w-0">
                                <div class="w-8 h-8 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 flex items-center justify-center text-xs font-bold shrink-0">
                                    {{ strtoupper(substr($u->Nome, 0, 1)) }}
                                </div>
                                <div class="min-w-0">
                                    <p class="text-sm font-medium text-slate-900 dark:text-slate-100 truncate">{{ $u->Nome }}</p>
                                    <p class="text-xs text-slate-500 dark:text-slate-400 capitalize">{{ $u->tipo }}</p>
                                </div>
                            </div>
                            <span class="text-xs font-mono text-slate-400">#{{ $u->user_ID }}</span>
                        </div>
                        @empty
                        <p class="py-4 text-center text-sm text-slate-500 dark:text-slate-400">Nenhum usuário cadastrado.</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

    @elseif($abaAtiva === 'usuarios')
        {{-- TAB: USUÁRIOS --}}
        <div class="space-y-4">
            <form method="GET" action="" class="rounded-2xl border border-slate-200/80 dark:border-slate-800 bg-white dark:bg-slate-900 p-4 shadow-sm">
                <input type="hidden" name="aba" value="usuarios">
                <div class="grid gap-3 sm:grid-cols-[1fr_200px_180px_auto]">
                    <div class="relative">
                        <span class="material-symbols-outlined absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-[20px]">search</span>
                        <input type="search" name="q" value="{{ request('q') }}" placeholder="Buscar por nome, e-mail, CPF, matrícula..."
                               class="w-full rounded-xl border-slate-300 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-100 py-2.5 pl-10 pr-4 text-sm focus:border-blue-500 focus:ring-blue-500" />
                    </div>
                    <select name="tipo" class="rounded-xl border-slate-300 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-100 py-2.5 text-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="">Todos os perfis</option>
                        <option value="aluno" {{ request('tipo') === 'aluno' ? 'selected' : '' }}>Aluno</option>
                        <option value="orientador" {{ request('tipo') === 'orientador' ? 'selected' : '' }}>Orientador</option>
                        <option value="supervisor" {{ request('tipo') === 'supervisor' ? 'selected' : '' }}>Supervisor</option>
                        <option value="administrador" {{ request('tipo') === 'administrador' ? 'selected' : '' }}>Administrador</option>
                    </select>
                    <select name="status" class="rounded-xl border-slate-300 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-100 py-2.5 text-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="">Todos os status</option>
                        <option value="ativo" {{ request('status') === 'ativo' ? 'selected' : '' }}>Ativos</option>
                        <option value="inativo" {{ request('status') === 'inativo' ? 'selected' : '' }}>Inativos</option>
                    </select>
                    <button type="submit" class="inline-flex items-center justify-center gap-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 text-sm font-semibold shadow-sm transition-colors">
                        <span class="material-symbols-outlined text-[18px]">filter_alt</span>
                        Filtrar
                    </button>
                </div>
            </form>

            <div class="rounded-2xl border border-slate-200/80 dark:border-slate-800 bg-white dark:bg-slate-900 overflow-hidden shadow-sm">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200 dark:divide-slate-800 text-sm">
                        <thead class="bg-slate-50 dark:bg-slate-800/80">
                            <tr>
                                <th class="px-5 py-3.5 text-left text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">ID</th>
                                <th class="px-5 py-3.5 text-left text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">Usuário</th>
                                <th class="px-5 py-3.5 text-left text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400 hidden sm:table-cell">E-mail</th>
                                <th class="px-5 py-3.5 text-left text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">Perfil</th>
                                <th class="px-5 py-3.5 text-left text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">Status</th>
                                <th class="px-5 py-3.5 text-right text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">Ações</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                            @forelse($usuarios ?? [] as $u)
                            <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-800/40 transition-colors">
                                <td class="px-5 py-3.5 font-mono text-xs text-slate-400">#{{ $u->user_ID }}</td>
                                <td class="px-5 py-3.5">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-full bg-blue-100 dark:bg-blue-900/60 text-blue-700 dark:text-blue-300 font-bold text-xs flex items-center justify-center shrink-0">
                                            {{ strtoupper(substr($u->Nome, 0, 1)) }}
                                        </div>
                                        <span class="font-semibold text-slate-900 dark:text-slate-100">{{ $u->Nome }}</span>
                                    </div>
                                </td>
                                <td class="px-5 py-3.5 text-slate-600 dark:text-slate-300 hidden sm:table-cell">{{ $u->Email }}</td>
                                <td class="px-5 py-3.5">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold capitalize
                                        {{ $u->tipo === 'administrador' ? 'bg-purple-50 dark:bg-purple-950/40 text-purple-700 dark:text-purple-300' : 'bg-blue-50 dark:bg-blue-950/40 text-blue-700 dark:text-blue-300' }}">
                                        {{ $u->tipo }}
                                    </span>
                                </td>
                                <td class="px-5 py-3.5">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $u->ativo_badge_class }}">
                                        {{ $u->status_texto }}
                                    </span>
                                </td>
                                <td class="px-5 py-3.5 text-right">
                                    <a href="/admin/usuarios/{{ $u->user_ID }}/edit"
                                       class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-xs font-semibold text-blue-600 dark:text-blue-400 bg-blue-50 dark:bg-blue-950/40 hover:bg-blue-100 dark:hover:bg-blue-900/60 transition-colors">
                                        <span class="material-symbols-outlined text-[16px]">edit</span>
                                        Editar
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="px-5 py-12 text-center text-slate-500 dark:text-slate-400">
                                    Nenhum usuário corresponde aos critérios pesquisados.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if(isset($usuarios) && $usuarios->hasPages())
                <div class="border-t border-slate-200 dark:border-slate-800 px-5 py-4">
                    {{ $usuarios->links() }}
                </div>
                @endif
            </div>

            <div class="flex justify-between items-center text-sm">
                <span class="text-slate-500 dark:text-slate-400">Visualizando lista rápida de usuários.</span>
                <a href="{{ route('admin.usuarios') }}" class="font-semibold text-blue-600 dark:text-blue-400 hover:underline flex items-center gap-1">
                    Gerenciador Avançado (CRUD) &rarr;
                </a>
            </div>
        </div>

    @elseif($abaAtiva === 'alunos')
        {{-- TAB: ALUNOS --}}
        <div class="space-y-4">
            <form method="GET" action="" class="rounded-2xl border border-slate-200/80 dark:border-slate-800 bg-white dark:bg-slate-900 p-4 shadow-sm">
                <input type="hidden" name="aba" value="alunos">
                <div class="grid gap-3 sm:grid-cols-[1fr_220px_180px_auto]">
                    <div class="relative">
                        <span class="material-symbols-outlined absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-[20px]">search</span>
                        <input type="search" name="q" value="{{ request('q') }}" placeholder="Nome, matrícula ou CPF do aluno..."
                               class="w-full rounded-xl border-slate-300 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-100 py-2.5 pl-10 pr-4 text-sm focus:border-blue-500 focus:ring-blue-500" />
                    </div>
                    <select name="curso" class="rounded-xl border-slate-300 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-100 py-2.5 text-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="">Todos os cursos</option>
                        @foreach($cursosDisponiveis as $c)
                        <option value="{{ $c }}" {{ request('curso') === $c ? 'selected' : '' }}>{{ $c }}</option>
                        @endforeach
                    </select>
                    <select name="status" class="rounded-xl border-slate-300 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-100 py-2.5 text-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="">Qualquer status</option>
                        <option value="ativo" {{ request('status') === 'ativo' ? 'selected' : '' }}>Ativo</option>
                        <option value="inativo" {{ request('status') === 'inativo' ? 'selected' : '' }}>Inativo</option>
                    </select>
                    <button type="submit" class="inline-flex items-center justify-center gap-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 text-sm font-semibold shadow-sm transition-colors">
                        <span class="material-symbols-outlined text-[18px]">filter_alt</span>
                        Filtrar
                    </button>
                </div>
            </form>

            <div class="rounded-2xl border border-slate-200/80 dark:border-slate-800 bg-white dark:bg-slate-900 overflow-hidden shadow-sm">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200 dark:divide-slate-800 text-sm">
                        <thead class="bg-slate-50 dark:bg-slate-800/80">
                            <tr>
                                <th class="px-5 py-3.5 text-left text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">Aluno</th>
                                <th class="px-5 py-3.5 text-left text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">Matrícula</th>
                                <th class="px-5 py-3.5 text-left text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">Curso</th>
                                <th class="px-5 py-3.5 text-left text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">Estágios Vinculados</th>
                                <th class="px-5 py-3.5 text-right text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">Ações</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                            @forelse($alunos ?? [] as $a)
                            <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-800/40 transition-colors">
                                <td class="px-5 py-3.5">
                                    <p class="font-semibold text-slate-900 dark:text-slate-100">{{ $a->nome }}</p>
                                    <p class="text-xs text-slate-500 dark:text-slate-400">{{ $a->usuario?->Email ?? '—' }}</p>
                                </td>
                                <td class="px-5 py-3.5 font-mono text-xs text-slate-600 dark:text-slate-300">{{ $a->matricula ?? '—' }}</td>
                                <td class="px-5 py-3.5 text-xs text-slate-600 dark:text-slate-300 font-medium">{{ $a->curso ?? '—' }}</td>
                                <td class="px-5 py-3.5">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300">
                                        {{ $a->estagios->count() }} estágio(s)
                                    </span>
                                </td>
                                <td class="px-5 py-3.5 text-right">
                                    <a href="/admin/usuarios/{{ $a->user_id }}/edit" class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-xs font-semibold text-blue-600 dark:text-blue-400 bg-blue-50 dark:bg-blue-950/40 hover:bg-blue-100 transition-colors">
                                        <span class="material-symbols-outlined text-[16px]">edit</span>
                                        Editar
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="px-5 py-12 text-center text-slate-500 dark:text-slate-400">
                                    Nenhum aluno cadastrado ou encontrado com os filtros atuais.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if(isset($alunos) && $alunos->hasPages())
                <div class="border-t border-slate-200 dark:border-slate-800 px-5 py-4">
                    {{ $alunos->links() }}
                </div>
                @endif
            </div>
        </div>

    @elseif($abaAtiva === 'orientadores')
        {{-- TAB: ORIENTADORES --}}
        <div class="space-y-4">
            <form method="GET" action="" class="rounded-2xl border border-slate-200/80 dark:border-slate-800 bg-white dark:bg-slate-900 p-4 shadow-sm">
                <input type="hidden" name="aba" value="orientadores">
                <div class="flex gap-3">
                    <div class="relative flex-1">
                        <span class="material-symbols-outlined absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-[20px]">search</span>
                        <input type="search" name="q" value="{{ request('q') }}" placeholder="Buscar orientador por nome, SIAPE ou e-mail..."
                               class="w-full rounded-xl border-slate-300 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-100 py-2.5 pl-10 pr-4 text-sm focus:border-blue-500 focus:ring-blue-500" />
                    </div>
                    <button type="submit" class="inline-flex items-center gap-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 text-sm font-semibold shadow-sm transition-colors">
                        <span class="material-symbols-outlined text-[18px]">filter_alt</span>
                        Filtrar
                    </button>
                </div>
            </form>

            <div class="rounded-2xl border border-slate-200/80 dark:border-slate-800 bg-white dark:bg-slate-900 overflow-hidden shadow-sm">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200 dark:divide-slate-800 text-sm">
                        <thead class="bg-slate-50 dark:bg-slate-800/80">
                            <tr>
                                <th class="px-5 py-3.5 text-left text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">Professor Orientador</th>
                                <th class="px-5 py-3.5 text-left text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">SIAPE</th>
                                <th class="px-5 py-3.5 text-left text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">Estágios sob Orientação</th>
                                <th class="px-5 py-3.5 text-right text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">Ações</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                            @forelse($orientadores ?? [] as $o)
                            <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-800/40 transition-colors">
                                <td class="px-5 py-3.5">
                                    <p class="font-semibold text-slate-900 dark:text-slate-100">{{ $o->usuario?->Nome ?? '—' }}</p>
                                    <p class="text-xs text-slate-500 dark:text-slate-400">{{ $o->usuario?->Email ?? '' }}</p>
                                </td>
                                <td class="px-5 py-3.5 font-mono text-xs text-slate-600 dark:text-slate-300">{{ $o->siape ?? '—' }}</td>
                                <td class="px-5 py-3.5">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-violet-50 dark:bg-violet-950/40 text-violet-700 dark:text-violet-300">
                                        {{ $o->estagios->count() }} estágio(s)
                                    </span>
                                </td>
                                <td class="px-5 py-3.5 text-right">
                                    <a href="/admin/usuarios/{{ $o->user_ID }}/edit" class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-xs font-semibold text-blue-600 dark:text-blue-400 bg-blue-50 dark:bg-blue-950/40 hover:bg-blue-100 transition-colors">
                                        <span class="material-symbols-outlined text-[16px]">edit</span>
                                        Editar
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="px-5 py-12 text-center text-slate-500 dark:text-slate-400">
                                    Nenhum orientador encontrado.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if(isset($orientadores) && $orientadores->hasPages())
                <div class="border-t border-slate-200 dark:border-slate-800 px-5 py-4">
                    {{ $orientadores->links() }}
                </div>
                @endif
            </div>
        </div>

    @elseif($abaAtiva === 'supervisores')
        {{-- TAB: SUPERVISORES --}}
        <div class="space-y-4">
            <form method="GET" action="" class="rounded-2xl border border-slate-200/80 dark:border-slate-800 bg-white dark:bg-slate-900 p-4 shadow-sm">
                <input type="hidden" name="aba" value="supervisores">
                <div class="grid gap-3 sm:grid-cols-[1fr_220px_auto]">
                    <div class="relative">
                        <span class="material-symbols-outlined absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-[20px]">search</span>
                        <input type="search" name="q" value="{{ request('q') }}" placeholder="Nome, e-mail ou cargo..."
                               class="w-full rounded-xl border-slate-300 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-100 py-2.5 pl-10 pr-4 text-sm focus:border-blue-500 focus:ring-blue-500" />
                    </div>
                    <input type="text" name="empresa" value="{{ request('empresa') }}" placeholder="Filtrar por empresa..."
                           class="rounded-xl border-slate-300 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-100 py-2.5 px-3 text-sm focus:border-blue-500 focus:ring-blue-500" />
                    <button type="submit" class="inline-flex items-center gap-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 text-sm font-semibold shadow-sm transition-colors">
                        <span class="material-symbols-outlined text-[18px]">filter_alt</span>
                        Filtrar
                    </button>
                </div>
            </form>

            <div class="rounded-2xl border border-slate-200/80 dark:border-slate-800 bg-white dark:bg-slate-900 overflow-hidden shadow-sm">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200 dark:divide-slate-800 text-sm">
                        <thead class="bg-slate-50 dark:bg-slate-800/80">
                            <tr>
                                <th class="px-5 py-3.5 text-left text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">Supervisor</th>
                                <th class="px-5 py-3.5 text-left text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">Empresa</th>
                                <th class="px-5 py-3.5 text-left text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">Cargo</th>
                                <th class="px-5 py-3.5 text-left text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">Estágios Atribuídos</th>
                                <th class="px-5 py-3.5 text-right text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">Ações</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                            @forelse($supervisores ?? [] as $s)
                            <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-800/40 transition-colors">
                                <td class="px-5 py-3.5">
                                    <p class="font-semibold text-slate-900 dark:text-slate-100">{{ $s->usuario?->Nome ?? '—' }}</p>
                                    <p class="text-xs text-slate-500 dark:text-slate-400">{{ $s->usuario?->Email ?? '' }}</p>
                                </td>
                                <td class="px-5 py-3.5 text-xs font-medium text-slate-700 dark:text-slate-300">{{ $s->empresa ?? '—' }}</td>
                                <td class="px-5 py-3.5 text-xs text-slate-500 dark:text-slate-400">{{ $s->cargo ?? '—' }}</td>
                                <td class="px-5 py-3.5">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-purple-50 dark:bg-purple-950/40 text-purple-700 dark:text-purple-300">
                                        {{ $s->estagios->count() }} estágio(s)
                                    </span>
                                </td>
                                <td class="px-5 py-3.5 text-right">
                                    <a href="/admin/usuarios/{{ $s->user_ID }}/edit" class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-xs font-semibold text-blue-600 dark:text-blue-400 bg-blue-50 dark:bg-blue-950/40 hover:bg-blue-100 transition-colors">
                                        <span class="material-symbols-outlined text-[16px]">edit</span>
                                        Editar
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="px-5 py-12 text-center text-slate-500 dark:text-slate-400">
                                    Nenhum supervisor encontrado.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if(isset($supervisores) && $supervisores->hasPages())
                <div class="border-t border-slate-200 dark:border-slate-800 px-5 py-4">
                    {{ $supervisores->links() }}
                </div>
                @endif
            </div>
        </div>

    @elseif($abaAtiva === 'empresas')
        {{-- TAB: EMPRESAS CONVENIADAS --}}
        <div class="space-y-4">
            <form method="GET" action="" class="rounded-2xl border border-slate-200/80 dark:border-slate-800 bg-white dark:bg-slate-900 p-4 shadow-sm">
                <input type="hidden" name="aba" value="empresas">
                <div class="grid gap-3 sm:grid-cols-[1fr_180px_180px_auto]">
                    <div class="relative">
                        <span class="material-symbols-outlined absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-[20px]">search</span>
                        <input type="search" name="q" value="{{ request('q') }}" placeholder="Nome, CNPJ ou município da empresa..."
                               class="w-full rounded-xl border-slate-300 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-100 py-2.5 pl-10 pr-4 text-sm focus:border-blue-500 focus:ring-blue-500" />
                    </div>
                    <select name="status" class="rounded-xl border-slate-300 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-100 py-2.5 text-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="">Todos os convênios</option>
                        <option value="vigente" {{ request('status') === 'vigente' ? 'selected' : '' }}>Somente Vigentes</option>
                        <option value="expirado" {{ request('status') === 'expirado' ? 'selected' : '' }}>Somente Expirados</option>
                    </select>
                    <select name="uf" class="rounded-xl border-slate-300 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-100 py-2.5 text-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="">Todos os estados</option>
                        @foreach($ufsDisponiveis as $uf)
                        <option value="{{ $uf }}" {{ request('uf') === $uf ? 'selected' : '' }}>{{ $uf }}</option>
                        @endforeach
                    </select>
                    <button type="submit" class="inline-flex items-center gap-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 text-sm font-semibold shadow-sm transition-colors">
                        <span class="material-symbols-outlined text-[18px]">filter_alt</span>
                        Filtrar
                    </button>
                </div>
            </form>

            <div class="flex items-center justify-between gap-4">
                <p class="text-sm text-slate-500 dark:text-slate-400">
                    @if(isset($empresas)) Foram encontradas <strong class="text-slate-700 dark:text-slate-200">{{ $empresas->total() }}</strong> empresa(s). @endif
                </p>
                <a href="{{ route('admin.empresas') }}" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl text-xs font-semibold text-emerald-700 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200/60 dark:border-emerald-900/40 hover:bg-emerald-100 transition-colors">
                    <span class="material-symbols-outlined text-[18px]">upload_file</span>
                    Importar / Atualizar Planilha
                </a>
            </div>

            <div class="rounded-2xl border border-slate-200/80 dark:border-slate-800 bg-white dark:bg-slate-900 overflow-hidden shadow-sm">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200 dark:divide-slate-800 text-sm">
                        <thead class="bg-slate-50 dark:bg-slate-800/80">
                            <tr>
                                <th class="px-5 py-3.5 text-left text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">Razão Social</th>
                                <th class="px-5 py-3.5 text-left text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">CNPJ</th>
                                <th class="px-5 py-3.5 text-left text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400 hidden md:table-cell">Período de Vigência</th>
                                <th class="px-5 py-3.5 text-left text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400 hidden md:table-cell">Localização</th>
                                <th class="px-5 py-3.5 text-right text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">Situação</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                            @forelse($empresas ?? [] as $emp)
                            <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-800/40 transition-colors">
                                <td class="px-5 py-3.5">
                                    <p class="font-semibold text-slate-900 dark:text-slate-100">{{ $emp->empresa }}</p>
                                </td>
                                <td class="px-5 py-3.5 font-mono text-xs text-slate-600 dark:text-slate-300">{{ $emp->cnpj ?? '—' }}</td>
                                <td class="px-5 py-3.5 text-xs text-slate-600 dark:text-slate-400 hidden md:table-cell">
                                    {{ $emp->vig_inicial?->format('d/m/Y') ?? '—' }}
                                    @if($emp->vig_final) &rarr; {{ $emp->vig_final->format('d/m/Y') }} @endif
                                </td>
                                <td class="px-5 py-3.5 text-xs text-slate-600 dark:text-slate-400 hidden md:table-cell">
                                    {{ $emp->uf ? $emp->uf . ' · ' . ($emp->municipio ?? '') : '—' }}
                                </td>
                                <td class="px-5 py-3.5 text-right">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold
                                        {{ $emp->isVigente()
                                            ? 'bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-400 border border-emerald-200/60'
                                            : 'bg-rose-50 dark:bg-rose-950/40 text-rose-700 dark:text-rose-400 border border-rose-200/60' }}">
                                        {{ $emp->isVigente() ? 'Vigente' : 'Expirado' }}
                                    </span>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="px-5 py-12 text-center text-slate-500 dark:text-slate-400">
                                    Nenhuma empresa conveniada encontrada para os filtros aplicados.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if(isset($empresas) && $empresas->hasPages())
                <div class="border-t border-slate-200 dark:border-slate-800 px-5 py-4">
                    {{ $empresas->links() }}
                </div>
                @endif
            </div>
        </div>

    @elseif($abaAtiva === 'estagios')
        {{-- TAB: ESTÁGIOS --}}
        <div class="space-y-4">
            <form method="GET" action="" class="rounded-2xl border border-slate-200/80 dark:border-slate-800 bg-white dark:bg-slate-900 p-4 shadow-sm">
                <input type="hidden" name="aba" value="estagios">
                <div class="grid gap-3 sm:grid-cols-[1fr_180px_auto]">
                    <div class="relative">
                        <span class="material-symbols-outlined absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-[20px]">search</span>
                        <input type="search" name="q" value="{{ request('q') }}" placeholder="Buscar por aluno ou local do estágio..."
                               class="w-full rounded-xl border-slate-300 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-100 py-2.5 pl-10 pr-4 text-sm focus:border-blue-500 focus:ring-blue-500" />
                    </div>
                    <select name="status" class="rounded-xl border-slate-300 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-100 py-2.5 text-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="">Qualquer status</option>
                        <option value="ativo" {{ request('status') === 'ativo' ? 'selected' : '' }}>Ativos</option>
                        <option value="finalizado" {{ request('status') === 'finalizado' ? 'selected' : '' }}>Finalizados</option>
                    </select>
                    <button type="submit" class="inline-flex items-center gap-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 text-sm font-semibold shadow-sm transition-colors">
                        <span class="material-symbols-outlined text-[18px]">filter_alt</span>
                        Filtrar
                    </button>
                </div>
            </form>

            <div class="rounded-2xl border border-slate-200/80 dark:border-slate-800 bg-white dark:bg-slate-900 overflow-hidden shadow-sm">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200 dark:divide-slate-800 text-sm">
                        <thead class="bg-slate-50 dark:bg-slate-800/80">
                            <tr>
                                <th class="px-5 py-3.5 text-left text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">Aluno</th>
                                <th class="px-5 py-3.5 text-left text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400 hidden md:table-cell">Orientador</th>
                                <th class="px-5 py-3.5 text-left text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400 hidden lg:table-cell">Empresa / Local</th>
                                <th class="px-5 py-3.5 text-left text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400 hidden lg:table-cell">Período</th>
                                <th class="px-5 py-3.5 text-right text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                            @forelse($estagios ?? [] as $e)
                            <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-800/40 transition-colors">
                                <td class="px-5 py-3.5">
                                    <p class="font-semibold text-slate-900 dark:text-slate-100">{{ $e->aluno?->nome ?? '—' }}</p>
                                    <p class="text-xs text-slate-500 dark:text-slate-400 font-mono">{{ $e->aluno?->matricula ?? '' }}</p>
                                </td>
                                <td class="px-5 py-3.5 text-xs text-slate-700 dark:text-slate-300 hidden md:table-cell">
                                    {{ $e->orientador?->usuario?->Nome ?? '—' }}
                                </td>
                                <td class="px-5 py-3.5 text-xs text-slate-600 dark:text-slate-300 hidden lg:table-cell">
                                    {{ $e->local }}
                                </td>
                                <td class="px-5 py-3.5 text-xs text-slate-500 dark:text-slate-400 hidden lg:table-cell">
                                    {{ $e->data_inicio?->format('d/m/Y') }} &rarr; {{ $e->data_fim?->format('d/m/Y') }}
                                </td>
                                <td class="px-5 py-3.5 text-right">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold
                                        {{ $e->status === 'ativo'
                                            ? 'bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-400 border border-emerald-200/60'
                                            : 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300' }}">
                                        {{ ucfirst($e->status) }}
                                    </span>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="px-5 py-12 text-center text-slate-500 dark:text-slate-400">
                                    Nenhum estágio registrado com os critérios especificados.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if(isset($estagios) && $estagios->hasPages())
                <div class="border-t border-slate-200 dark:border-slate-800 px-5 py-4">
                    {{ $estagios->links() }}
                </div>
                @endif
            </div>
        </div>
    @endif

</main>
@endsection
