@extends('layouts.app')

@section('content')
<main class="mx-auto max-w-7xl space-y-6 px-4 py-8 sm:px-6 lg:px-8">

    {{-- Cabeçalho --}}
    <div class="mb-6">
        <p class="text-sm font-medium text-[#0056c9] dark:text-blue-400">Área Administrativa</p>
        <h1 class="mt-1 text-2xl font-bold text-gray-900 dark:text-gray-100">Dashboard</h1>
        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Gerencie todo o sistema de estágios do portal NEXO.</p>
    </div>

    {{-- Cards de contadores rápidos --}}
    <div class="grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-6">
        @php
            $metricas = [
                ['icon'=>'groups','label'=>'Usuários','valor'=>$contadores['total_usuarios'],'cor'=>'blue','aba'=>'usuarios'],
                ['icon'=>'school','label'=>'Alunos','valor'=>$contadores['total_alunos'],'cor'=>'indigo','aba'=>'alunos'],
                ['icon'=>'person_pin','label'=>'Orientadores','valor'=>$contadores['total_orientadores'],'cor'=>'violet','aba'=>'orientadores'],
                ['icon'=>'badge','label'=>'Supervisores','valor'=>$contadores['total_supervisores'],'cor'=>'purple','aba'=>'supervisores'],
                ['icon'=>'business','label'=>'Empresas','valor'=>$contadores['total_empresas'],'cor'=>'green','aba'=>'empresas'],
                ['icon'=>'work','label'=>'Estágios','valor'=>$contadores['total_estagios'],'cor'=>'orange','aba'=>'estagios'],
            ];
        @endphp
        @foreach($metricas as $m)
        <a href="?aba={{ $m['aba'] }}"
           class="card-figma rounded-xl p-4 flex flex-col gap-2 group transition-all hover:border-[#0077fc]
                  {{ $abaAtiva === $m['aba'] ? 'border-[#0077fc] ring-2 ring-[#0077fc]/20' : '' }}">
            <span class="material-symbols-outlined text-{{ $m['cor'] }}-500 text-2xl">{{ $m['icon'] }}</span>
            <span class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ number_format($m['valor'], 0, ',', '.') }}</span>
            <span class="text-xs text-gray-500 dark:text-gray-400">{{ $m['label'] }}</span>
        </a>
        @endforeach
    </div>

    {{-- Tabs de navegação --}}
    <div class="border-b border-gray-200 dark:border-gray-700">
        <nav class="flex gap-1 overflow-x-auto pb-px" aria-label="Abas do painel">
            @php
                $abas = [
                    'visao-geral' => ['icon'=>'dashboard','label'=>'Visão Geral'],
                    'usuarios'    => ['icon'=>'groups','label'=>'Usuários'],
                    'alunos'      => ['icon'=>'school','label'=>'Alunos'],
                    'orientadores'=> ['icon'=>'person_pin','label'=>'Orientadores'],
                    'supervisores'=> ['icon'=>'badge','label'=>'Supervisores'],
                    'empresas'    => ['icon'=>'business','label'=>'Empresas'],
                    'estagios'    => ['icon'=>'work','label'=>'Estágios'],
                ];
            @endphp
            @foreach($abas as $chave => $aba)
                <a href="?aba={{ $chave }}"
                   class="flex items-center gap-1.5 whitespace-nowrap px-3 py-2.5 text-sm font-medium transition-colors border-b-2 -mb-px
                          {{ $abaAtiva === $chave
                              ? 'border-[#0077fc] text-[#0077fc] dark:text-blue-400 dark:border-blue-400'
                              : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-200' }}">
                    <span class="material-symbols-outlined text-[18px]">{{ $aba['icon'] }}</span>
                    {{ $aba['label'] }}
                </a>
            @endforeach
        </nav>
    </div>

    {{-- Conteúdo das abas --}}

    @if($abaAtiva === 'visao-geral')
        {{-- Visão Geral --}}
        <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">

            {{-- Links rápidos admin --}}
            <div class="card-figma rounded-xl p-6">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Ações Rápidas</h2>
                <div class="space-y-2">
                    <a href="{{ route('admin.usuarios') }}" class="flex items-center gap-3 p-3 rounded-lg border border-gray-200 dark:border-gray-700 hover:border-[#0077fc] hover:bg-blue-50 dark:hover:bg-blue-950/40 transition-all">
                        <span class="material-symbols-outlined text-[#0077fc]">person_add</span>
                        <div>
                            <p class="text-sm font-medium text-gray-900 dark:text-gray-100">Gerenciar Usuários (CRUD)</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Cadastrar, editar e remover usuários</p>
                        </div>
                    </a>
                    <a href="{{ route('admin.empresas') }}" class="flex items-center gap-3 p-3 rounded-lg border border-gray-200 dark:border-gray-700 hover:border-[#0077fc] hover:bg-blue-50 dark:hover:bg-blue-950/40 transition-all">
                        <span class="material-symbols-outlined text-green-600">upload_file</span>
                        <div>
                            <p class="text-sm font-medium text-gray-900 dark:text-gray-100">Importar Empresas Conveniadas</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Upload de PDF, geração CSV e enriquecimento</p>
                        </div>
                    </a>
                    <a href="{{ route('admin.tables') }}" class="flex items-center gap-3 p-3 rounded-lg border border-gray-200 dark:border-gray-700 hover:border-[#0077fc] hover:bg-blue-50 dark:hover:bg-blue-950/40 transition-all">
                        <span class="material-symbols-outlined text-orange-600">table_chart</span>
                        <div>
                            <p class="text-sm font-medium text-gray-900 dark:text-gray-100">Visualizador de Tabelas</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Inspecionar dados brutos do banco</p>
                        </div>
                    </a>
                </div>
            </div>

            {{-- Estágios ativos --}}
            <div class="card-figma rounded-xl p-6">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Estágios Ativos</h2>
                    <a href="?aba=estagios&status=ativo" class="text-sm text-[#0077fc] hover:underline">Ver todos</a>
                </div>
                <div class="flex items-center gap-4">
                    <div class="flex-1 bg-green-50 dark:bg-green-950/40 rounded-lg p-4 text-center">
                        <p class="text-3xl font-bold text-green-700 dark:text-green-400">{{ $contadores['estagios_ativos'] }}</p>
                        <p class="text-xs text-green-600 dark:text-green-500 mt-1">Ativos</p>
                    </div>
                    <div class="flex-1 bg-gray-50 dark:bg-gray-800 rounded-lg p-4 text-center">
                        <p class="text-3xl font-bold text-gray-700 dark:text-gray-300">{{ $contadores['total_estagios'] - $contadores['estagios_ativos'] }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Finalizados</p>
                    </div>
                </div>
                <p class="mt-3 text-xs text-gray-400 dark:text-gray-500">
                    Total de presenças registradas: <strong class="text-gray-600 dark:text-gray-300">{{ number_format($contadores['presencas_totais'], 0, ',', '.') }}</strong>
                </p>
            </div>

            {{-- Últimos usuários --}}
            <div class="card-figma rounded-xl p-6">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Últimos Usuários Cadastrados</h2>
                    <a href="?aba=usuarios" class="text-sm text-[#0077fc] hover:underline">Ver todos</a>
                </div>
                <div class="space-y-2">
                    @forelse($ultimos_usuarios ?? [] as $u)
                    <div class="flex items-center justify-between py-2 border-b border-gray-100 dark:border-gray-700 last:border-0">
                        <div class="flex items-center gap-2">
                            <div class="w-7 h-7 rounded-full bg-blue-100 dark:bg-blue-900 flex items-center justify-center text-xs font-bold text-blue-700 dark:text-blue-300">
                                {{ strtoupper(substr($u->Nome, 0, 1)) }}
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $u->Nome }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400 capitalize">{{ $u->tipo }}</p>
                            </div>
                        </div>
                        <span class="text-xs text-gray-400 dark:text-gray-500">#{{ $u->user_ID }}</span>
                    </div>
                    @empty
                    <p class="text-sm text-gray-500 dark:text-gray-400">Nenhum usuário cadastrado ainda.</p>
                    @endforelse
                </div>
            </div>

            {{-- Últimos estágios --}}
            <div class="card-figma rounded-xl p-6">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Últimos Estágios</h2>
                    <a href="?aba=estagios" class="text-sm text-[#0077fc] hover:underline">Ver todos</a>
                </div>
                <div class="space-y-2">
                    @forelse($ultimos_estagios ?? [] as $e)
                    <div class="flex items-center justify-between py-2 border-b border-gray-100 dark:border-gray-700 last:border-0">
                        <div>
                            <p class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $e->aluno->nome ?? '—' }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">{{ $e->local }}</p>
                        </div>
                        <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium
                            {{ $e->status === 'ativo' ? 'bg-green-100 text-green-700 dark:bg-green-950 dark:text-green-400' : 'bg-gray-100 text-gray-600 dark:bg-gray-800 dark:text-gray-400' }}">
                            {{ ucfirst($e->status) }}
                        </span>
                    </div>
                    @empty
                    <p class="text-sm text-gray-500 dark:text-gray-400">Nenhum estágio registrado ainda.</p>
                    @endforelse
                </div>
            </div>
        </div>

    @elseif($abaAtiva === 'usuarios')
        {{-- Tab: Usuários --}}
        <form method="GET" action="" class="rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-4 shadow-sm">
            <input type="hidden" name="aba" value="usuarios">
            <div class="grid gap-3 sm:grid-cols-[1fr_200px_180px_auto]">
                <div class="relative">
                    <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-[18px]">search</span>
                    <input type="search" name="q" value="{{ request('q') }}" placeholder="Nome, e-mail, CPF, matrícula ou SIAPE"
                           class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 py-2.5 pl-9 pr-3 text-sm focus:border-[#0077fc] focus:ring-[#0077fc]" />
                </div>
                <select name="tipo" class="rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 py-2.5 text-sm focus:border-[#0077fc] focus:ring-[#0077fc]">
                    <option value="">Todos os perfis</option>
                    <option value="aluno" {{ request('tipo') === 'aluno' ? 'selected' : '' }}>Aluno</option>
                    <option value="orientador" {{ request('tipo') === 'orientador' ? 'selected' : '' }}>Orientador</option>
                    <option value="supervisor" {{ request('tipo') === 'supervisor' ? 'selected' : '' }}>Supervisor</option>
                    <option value="administrador" {{ request('tipo') === 'administrador' ? 'selected' : '' }}>Administrador</option>
                </select>
                <select name="status" class="rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 py-2.5 text-sm focus:border-[#0077fc] focus:ring-[#0077fc]">
                    <option value="">Qualquer status</option>
                    <option value="ativo" {{ request('status') === 'ativo' ? 'selected' : '' }}>Ativo</option>
                    <option value="inativo" {{ request('status') === 'inativo' ? 'selected' : '' }}>Inativo</option>
                </select>
                <button type="submit" class="inline-flex items-center gap-1.5 rounded-lg bg-[#0077fc] px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-[#0056c9]">
                    <span class="material-symbols-outlined text-[18px]">filter_list</span>Filtrar
                </button>
            </div>
        </form>

        <div class="rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden shadow-sm">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 text-sm">
                    <thead class="bg-gray-50 dark:bg-gray-800">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">ID</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Nome</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400 hidden sm:table-cell">E-mail</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Perfil</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Status</th>
                            <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Ações</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700 bg-white dark:bg-gray-800">
                        @forelse($usuarios ?? [] as $u)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                            <td class="px-4 py-3 text-gray-500 dark:text-gray-400 font-mono text-xs">{{ $u->user_ID }}</td>
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-2">
                                    <div class="w-7 h-7 rounded-full bg-blue-100 dark:bg-blue-900 flex items-center justify-center text-xs font-bold text-blue-700 dark:text-blue-300 flex-shrink-0">
                                        {{ strtoupper(substr($u->Nome, 0, 1)) }}
                                    </div>
                                    <span class="font-medium text-gray-900 dark:text-gray-100">{{ $u->Nome }}</span>
                                </div>
                            </td>
                            <td class="px-4 py-3 text-gray-600 dark:text-gray-300 hidden sm:table-cell">{{ $u->Email }}</td>
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center rounded-full bg-blue-50 dark:bg-blue-900/40 px-2 py-0.5 text-xs font-medium text-blue-700 dark:text-blue-300 capitalize">
                                    {{ $u->tipo }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium {{ $u->ativo_badge_class }}">
                                    {{ $u->status_texto }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-right">
                                <div class="flex items-center justify-end gap-1">
                                    <a href="/admin/usuarios/{{ $u->user_ID }}/edit"
                                       class="p-1.5 rounded-lg text-gray-500 dark:text-gray-400 hover:bg-blue-50 dark:hover:bg-blue-900/40 hover:text-[#0077fc]"
                                       title="Editar">
                                        <span class="material-symbols-outlined text-[18px]">edit</span>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-4 py-10 text-center text-sm text-gray-500 dark:text-gray-400">
                                Nenhum usuário encontrado com os filtros aplicados.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if(isset($usuarios) && $usuarios->hasPages())
            <div class="border-t border-gray-200 dark:border-gray-700 px-4 py-3 bg-white dark:bg-gray-800">
                {{ $usuarios->links() }}
            </div>
            @endif
        </div>

        <div class="text-right">
            <a href="{{ route('admin.usuarios') }}" class="inline-flex items-center gap-1.5 text-sm text-[#0077fc] hover:underline">
                <span class="material-symbols-outlined text-[16px]">open_in_new</span>
                Abrir CRUD completo de usuários
            </a>
        </div>

    @elseif($abaAtiva === 'alunos')
        {{-- Tab: Alunos --}}
        <form method="GET" action="" class="rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-4 shadow-sm">
            <input type="hidden" name="aba" value="alunos">
            <div class="grid gap-3 sm:grid-cols-[1fr_220px_180px_auto]">
                <div class="relative">
                    <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-[18px]">search</span>
                    <input type="search" name="q" value="{{ request('q') }}" placeholder="Nome ou matrícula"
                           class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 py-2.5 pl-9 pr-3 text-sm focus:border-[#0077fc] focus:ring-[#0077fc]" />
                </div>
                <select name="curso" class="rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 py-2.5 text-sm focus:border-[#0077fc] focus:ring-[#0077fc]">
                    <option value="">Todos os cursos</option>
                    @foreach($cursosDisponiveis as $c)
                    <option value="{{ $c }}" {{ request('curso') === $c ? 'selected' : '' }}>{{ $c }}</option>
                    @endforeach
                </select>
                <select name="status" class="rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 py-2.5 text-sm focus:border-[#0077fc] focus:ring-[#0077fc]">
                    <option value="">Qualquer status</option>
                    <option value="ativo" {{ request('status') === 'ativo' ? 'selected' : '' }}>Ativo</option>
                    <option value="inativo" {{ request('status') === 'inativo' ? 'selected' : '' }}>Inativo</option>
                </select>
                <button type="submit" class="inline-flex items-center gap-1.5 rounded-lg bg-[#0077fc] px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-[#0056c9]">
                    <span class="material-symbols-outlined text-[18px]">filter_list</span>Filtrar
                </button>
            </div>
        </form>

        <div class="rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden shadow-sm">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 text-sm">
                    <thead class="bg-gray-50 dark:bg-gray-800">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Nome</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Matrícula</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Curso</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Estágios</th>
                            <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Ações</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700 bg-white dark:bg-gray-800">
                        @forelse($alunos ?? [] as $a)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                            <td class="px-4 py-3">
                                <p class="font-medium text-gray-900 dark:text-gray-100">{{ $a->nome }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ $a->usuario?->Email ?? '' }}</p>
                            </td>
                            <td class="px-4 py-3 font-mono text-xs text-gray-700 dark:text-gray-300">{{ $a->matricula ?? '—' }}</td>
                            <td class="px-4 py-3 text-gray-600 dark:text-gray-300 text-xs">{{ $a->curso ?? '—' }}</td>
                            <td class="px-4 py-3 text-gray-600 dark:text-gray-300">
                                <span class="text-xs font-medium">{{ $a->estagios->count() }}</span>
                            </td>
                            <td class="px-4 py-3 text-right">
                                <a href="/admin/usuarios/{{ $a->user_id }}/edit"
                                   class="p-1.5 rounded-lg text-gray-500 dark:text-gray-400 hover:bg-blue-50 dark:hover:bg-blue-900/40 hover:text-[#0077fc]"
                                   title="Editar">
                                    <span class="material-symbols-outlined text-[18px]">edit</span>
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-4 py-10 text-center text-sm text-gray-500 dark:text-gray-400">
                                Nenhum aluno encontrado com os filtros aplicados.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if(isset($alunos) && $alunos->hasPages())
            <div class="border-t border-gray-200 dark:border-gray-700 px-4 py-3 bg-white dark:bg-gray-800">
                {{ $alunos->links() }}
            </div>
            @endif
        </div>

    @elseif($abaAtiva === 'orientadores')
        {{-- Tab: Orientadores --}}
        <form method="GET" action="" class="rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-4 shadow-sm">
            <input type="hidden" name="aba" value="orientadores">
            <div class="flex gap-3">
                <div class="relative flex-1">
                    <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-[18px]">search</span>
                    <input type="search" name="q" value="{{ request('q') }}" placeholder="Nome, e-mail ou SIAPE"
                           class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 py-2.5 pl-9 pr-3 text-sm focus:border-[#0077fc] focus:ring-[#0077fc]" />
                </div>
                <button type="submit" class="inline-flex items-center gap-1.5 rounded-lg bg-[#0077fc] px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-[#0056c9]">
                    <span class="material-symbols-outlined text-[18px]">filter_list</span>Filtrar
                </button>
            </div>
        </form>

        <div class="rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden shadow-sm">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 text-sm">
                    <thead class="bg-gray-50 dark:bg-gray-800">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Nome</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">SIAPE</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Estágios sob orientação</th>
                            <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Ações</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700 bg-white dark:bg-gray-800">
                        @forelse($orientadores ?? [] as $o)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                            <td class="px-4 py-3">
                                <p class="font-medium text-gray-900 dark:text-gray-100">{{ $o->usuario?->Nome ?? '—' }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ $o->usuario?->Email ?? '' }}</p>
                            </td>
                            <td class="px-4 py-3 font-mono text-xs text-gray-700 dark:text-gray-300">{{ $o->siape ?? '—' }}</td>
                            <td class="px-4 py-3 text-gray-600 dark:text-gray-300">{{ $o->estagios->count() }}</td>
                            <td class="px-4 py-3 text-right">
                                <a href="/admin/usuarios/{{ $o->user_ID }}/edit"
                                   class="p-1.5 rounded-lg text-gray-500 dark:text-gray-400 hover:bg-blue-50 dark:hover:bg-blue-900/40 hover:text-[#0077fc]"
                                   title="Editar">
                                    <span class="material-symbols-outlined text-[18px]">edit</span>
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="px-4 py-10 text-center text-sm text-gray-500 dark:text-gray-400">
                                Nenhum orientador encontrado com os filtros aplicados.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if(isset($orientadores) && $orientadores->hasPages())
            <div class="border-t border-gray-200 dark:border-gray-700 px-4 py-3 bg-white dark:bg-gray-800">
                {{ $orientadores->links() }}
            </div>
            @endif
        </div>

    @elseif($abaAtiva === 'supervisores')
        {{-- Tab: Supervisores --}}
        <form method="GET" action="" class="rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-4 shadow-sm">
            <input type="hidden" name="aba" value="supervisores">
            <div class="grid gap-3 sm:grid-cols-[1fr_220px_auto]">
                <div class="relative">
                    <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-[18px]">search</span>
                    <input type="search" name="q" value="{{ request('q') }}" placeholder="Nome, e-mail ou empresa"
                           class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 py-2.5 pl-9 pr-3 text-sm focus:border-[#0077fc] focus:ring-[#0077fc]" />
                </div>
                <input type="text" name="empresa" value="{{ request('empresa') }}" placeholder="Filtrar por empresa"
                       class="rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 py-2.5 text-sm focus:border-[#0077fc] focus:ring-[#0077fc]" />
                <button type="submit" class="inline-flex items-center gap-1.5 rounded-lg bg-[#0077fc] px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-[#0056c9]">
                    <span class="material-symbols-outlined text-[18px]">filter_list</span>Filtrar
                </button>
            </div>
        </form>

        <div class="rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden shadow-sm">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 text-sm">
                    <thead class="bg-gray-50 dark:bg-gray-800">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Nome</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Empresa</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Cargo</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Estágios</th>
                            <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Ações</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700 bg-white dark:bg-gray-800">
                        @forelse($supervisores ?? [] as $s)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                            <td class="px-4 py-3">
                                <p class="font-medium text-gray-900 dark:text-gray-100">{{ $s->usuario?->Nome ?? '—' }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ $s->usuario?->Email ?? '' }}</p>
                            </td>
                            <td class="px-4 py-3 text-gray-700 dark:text-gray-300 text-xs">{{ $s->empresa ?? '—' }}</td>
                            <td class="px-4 py-3 text-gray-600 dark:text-gray-400 text-xs">{{ $s->cargo ?? '—' }}</td>
                            <td class="px-4 py-3 text-gray-600 dark:text-gray-300">{{ $s->estagios->count() }}</td>
                            <td class="px-4 py-3 text-right">
                                <a href="/admin/usuarios/{{ $s->user_ID }}/edit"
                                   class="p-1.5 rounded-lg text-gray-500 dark:text-gray-400 hover:bg-blue-50 dark:hover:bg-blue-900/40 hover:text-[#0077fc]"
                                   title="Editar">
                                    <span class="material-symbols-outlined text-[18px]">edit</span>
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-4 py-10 text-center text-sm text-gray-500 dark:text-gray-400">
                                Nenhum supervisor encontrado com os filtros aplicados.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if(isset($supervisores) && $supervisores->hasPages())
            <div class="border-t border-gray-200 dark:border-gray-700 px-4 py-3 bg-white dark:bg-gray-800">
                {{ $supervisores->links() }}
            </div>
            @endif
        </div>

    @elseif($abaAtiva === 'empresas')
        {{-- Tab: Empresas Conveniadas --}}
        <form method="GET" action="" class="rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-4 shadow-sm">
            <input type="hidden" name="aba" value="empresas">
            <div class="grid gap-3 sm:grid-cols-[1fr_180px_180px_auto]">
                <div class="relative">
                    <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-[18px]">search</span>
                    <input type="search" name="q" value="{{ request('q') }}" placeholder="Nome, CNPJ ou cidade"
                           class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 py-2.5 pl-9 pr-3 text-sm focus:border-[#0077fc] focus:ring-[#0077fc]" />
                </div>
                <select name="status" class="rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 py-2.5 text-sm focus:border-[#0077fc] focus:ring-[#0077fc]">
                    <option value="">Qualquer convênio</option>
                    <option value="vigente" {{ request('status') === 'vigente' ? 'selected' : '' }}>Vigente</option>
                    <option value="expirado" {{ request('status') === 'expirado' ? 'selected' : '' }}>Expirado</option>
                </select>
                <select name="uf" class="rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 py-2.5 text-sm focus:border-[#0077fc] focus:ring-[#0077fc]">
                    <option value="">Todos os estados</option>
                    @foreach($ufsDisponiveis as $uf)
                    <option value="{{ $uf }}" {{ request('uf') === $uf ? 'selected' : '' }}>{{ $uf }}</option>
                    @endforeach
                </select>
                <button type="submit" class="inline-flex items-center gap-1.5 rounded-lg bg-[#0077fc] px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-[#0056c9]">
                    <span class="material-symbols-outlined text-[18px]">filter_list</span>Filtrar
                </button>
            </div>
        </form>

        <div class="flex gap-3 flex-wrap items-center justify-between">
            <p class="text-sm text-gray-500 dark:text-gray-400">
                @if(isset($empresas)) {{ $empresas->total() }} empresa(s) encontrada(s). @endif
            </p>
            <a href="{{ route('admin.empresas') }}" class="inline-flex items-center gap-1.5 rounded-lg border border-[#0077fc] text-[#0077fc] px-3 py-1.5 text-sm font-medium hover:bg-blue-50 dark:hover:bg-blue-900/40 transition-colors">
                <span class="material-symbols-outlined text-[16px]">upload_file</span>
                Importar / Gerenciar Convênios
            </a>
        </div>

        <div class="rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden shadow-sm">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 text-sm">
                    <thead class="bg-gray-50 dark:bg-gray-800">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Empresa</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">CNPJ</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400 hidden md:table-cell">Vigência</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400 hidden md:table-cell">UF / Cidade</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700 bg-white dark:bg-gray-800">
                        @forelse($empresas ?? [] as $emp)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                            <td class="px-4 py-3">
                                <p class="font-medium text-gray-900 dark:text-gray-100 text-xs">{{ $emp->empresa }}</p>
                            </td>
                            <td class="px-4 py-3 font-mono text-xs text-gray-600 dark:text-gray-400">{{ $emp->cnpj ?? '—' }}</td>
                            <td class="px-4 py-3 text-xs text-gray-600 dark:text-gray-400 hidden md:table-cell">
                                {{ $emp->vig_inicial?->format('d/m/Y') ?? '—' }}
                                @if($emp->vig_final) → {{ $emp->vig_final->format('d/m/Y') }} @endif
                            </td>
                            <td class="px-4 py-3 text-xs text-gray-600 dark:text-gray-400 hidden md:table-cell">
                                {{ $emp->uf ? $emp->uf . ' / ' . ($emp->municipio ?? '') : '—' }}
                            </td>
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium
                                    {{ $emp->isVigente() ? 'bg-green-100 text-green-700 dark:bg-green-950 dark:text-green-400' : 'bg-red-100 text-red-700 dark:bg-red-950 dark:text-red-400' }}">
                                    {{ $emp->isVigente() ? 'Vigente' : 'Expirado' }}
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-4 py-10 text-center text-sm text-gray-500 dark:text-gray-400">
                                Nenhuma empresa encontrada com os filtros aplicados.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if(isset($empresas) && $empresas->hasPages())
            <div class="border-t border-gray-200 dark:border-gray-700 px-4 py-3 bg-white dark:bg-gray-800">
                {{ $empresas->links() }}
            </div>
            @endif
        </div>

    @elseif($abaAtiva === 'estagios')
        {{-- Tab: Estágios --}}
        <form method="GET" action="" class="rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-4 shadow-sm">
            <input type="hidden" name="aba" value="estagios">
            <div class="grid gap-3 sm:grid-cols-[1fr_180px_auto]">
                <div class="relative">
                    <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-[18px]">search</span>
                    <input type="search" name="q" value="{{ request('q') }}" placeholder="Aluno ou local"
                           class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 py-2.5 pl-9 pr-3 text-sm focus:border-[#0077fc] focus:ring-[#0077fc]" />
                </div>
                <select name="status" class="rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 py-2.5 text-sm focus:border-[#0077fc] focus:ring-[#0077fc]">
                    <option value="">Qualquer status</option>
                    <option value="ativo" {{ request('status') === 'ativo' ? 'selected' : '' }}>Ativo</option>
                    <option value="finalizado" {{ request('status') === 'finalizado' ? 'selected' : '' }}>Finalizado</option>
                </select>
                <button type="submit" class="inline-flex items-center gap-1.5 rounded-lg bg-[#0077fc] px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-[#0056c9]">
                    <span class="material-symbols-outlined text-[18px]">filter_list</span>Filtrar
                </button>
            </div>
        </form>

        <div class="rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden shadow-sm">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 text-sm">
                    <thead class="bg-gray-50 dark:bg-gray-800">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Aluno</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400 hidden md:table-cell">Orientador</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400 hidden lg:table-cell">Local</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400 hidden lg:table-cell">Período</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700 bg-white dark:bg-gray-800">
                        @forelse($estagios ?? [] as $e)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                            <td class="px-4 py-3">
                                <p class="font-medium text-gray-900 dark:text-gray-100">{{ $e->aluno?->nome ?? '—' }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ $e->aluno?->matricula ?? '' }}</p>
                            </td>
                            <td class="px-4 py-3 text-xs text-gray-600 dark:text-gray-400 hidden md:table-cell">
                                {{ $e->orientador?->usuario?->Nome ?? '—' }}
                            </td>
                            <td class="px-4 py-3 text-xs text-gray-600 dark:text-gray-400 hidden lg:table-cell max-w-[180px] truncate">
                                {{ $e->local }}
                            </td>
                            <td class="px-4 py-3 text-xs text-gray-600 dark:text-gray-400 hidden lg:table-cell">
                                {{ $e->data_inicio?->format('d/m/Y') }} – {{ $e->data_fim?->format('d/m/Y') }}
                            </td>
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium
                                    {{ $e->status === 'ativo' ? 'bg-green-100 text-green-700 dark:bg-green-950 dark:text-green-400' : 'bg-gray-100 text-gray-600 dark:bg-gray-800 dark:text-gray-400' }}">
                                    {{ ucfirst($e->status) }}
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-4 py-10 text-center text-sm text-gray-500 dark:text-gray-400">
                                Nenhum estágio encontrado com os filtros aplicados.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if(isset($estagios) && $estagios->hasPages())
            <div class="border-t border-gray-200 dark:border-gray-700 px-4 py-3 bg-white dark:bg-gray-800">
                {{ $estagios->links() }}
            </div>
            @endif
        </div>
    @endif

</main>
@endsection
