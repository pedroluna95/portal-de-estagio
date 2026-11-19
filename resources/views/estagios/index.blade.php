@extends('layouts.app')

@section('content')
<main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8">
    {{-- Topo da Gestão de Estágios --}}
    <header class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 border-b border-slate-200 dark:border-slate-800 pb-5">
        <div class="flex items-center gap-4">
            <div class="w-14 h-14 rounded-2xl bg-gradient-to-tr from-blue-600 to-indigo-600 flex items-center justify-center text-white shadow-md shadow-blue-500/20 shrink-0">
                <span class="material-symbols-outlined text-[28px]">{{ $papel === 'aluno' ? 'schedule' : ($papel === 'supervisor' ? 'verified_user' : 'school') }}</span>
            </div>
            <div>
                <span class="text-xs font-semibold text-blue-600 dark:text-blue-400 uppercase tracking-wider">Módulo de Acompanhamento</span>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 dark:text-slate-100 tracking-tight">
                    {{ $papel === 'orientador' ? 'Meus Estágios Orientados' : ($papel === 'supervisor' ? 'Estágios Sob Supervisão' : 'Controle de Horas e Frequência') }}
                </h1>
                <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 mt-0.5">
                    Acompanhe períodos, jornadas, folha de ponto e validações de horas do estágio.
                </p>
            </div>
        </div>
    </header>

    @if(session('success'))
        <div role="status" class="rounded-2xl border border-emerald-200 dark:border-emerald-900/60 bg-emerald-50 dark:bg-emerald-950/40 p-4 text-sm font-semibold text-emerald-800 dark:text-emerald-300 flex items-center gap-2">
            <span class="material-symbols-outlined text-[20px]">check_circle</span>
            {{ session('success') }}
        </div>
    @endif
    @if($errors->any())
        <div role="alert" class="rounded-2xl border border-rose-200 dark:border-rose-900/60 bg-rose-50 dark:bg-rose-950/40 p-4 text-sm text-rose-800 dark:text-rose-300">
            <ul class="list-disc space-y-1 pl-5">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
        </div>
    @endif

    {{-- Se for Orientador: Formulário de Inicialização e Tabela --}}
    @if($papel === 'orientador')
        <section class="rounded-2xl border border-slate-200/80 dark:border-slate-800 bg-white dark:bg-slate-900 p-6 sm:p-7 shadow-sm">
            <div class="flex items-center gap-2.5 mb-1">
                <span class="material-symbols-outlined text-blue-600 dark:text-blue-400 text-[22px]">add_task</span>
                <h2 class="text-base font-bold text-slate-900 dark:text-slate-100">Inicializar Novo Estágio</h2>
            </div>
            <p class="text-xs text-slate-500 dark:text-slate-400 mb-5">Vincule um estudante, defina local de trabalho e o supervisor corporativo.</p>

            <form method="POST" action="{{ route('estagios.store') }}" class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                @csrf
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1.5">Aluno *</label>
                    <select name="aluno_id" required class="w-full rounded-xl border-slate-300 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-100 py-2.5 px-3 text-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="">Selecione o aluno</option>
                        @foreach($alunosDisponiveis as $aluno)
                            <option value="{{ $aluno->user_id }}" @selected(old('aluno_id') == $aluno->user_id)>{{ $aluno->nome }} · {{ $aluno->matricula }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1.5">Local do Estágio *</label>
                    <input name="local" value="{{ old('local') }}" maxlength="150" required placeholder="Empresa ou Laboratório"
                           class="w-full rounded-xl border-slate-300 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-100 py-2.5 px-3.5 text-sm focus:border-blue-500 focus:ring-blue-500 placeholder-slate-400" />
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1.5">Data Inicial *</label>
                    <input type="date" name="data_inicio" value="{{ old('data_inicio') }}" required
                           class="w-full rounded-xl border-slate-300 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-100 py-2.5 px-3 text-sm focus:border-blue-500 focus:ring-blue-500" />
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1.5">Data Final *</label>
                    <input type="date" name="data_fim" value="{{ old('data_fim') }}" required
                           class="w-full rounded-xl border-slate-300 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-100 py-2.5 px-3 text-sm focus:border-blue-500 focus:ring-blue-500" />
                </div>
                <div class="sm:col-span-2 lg:col-span-3">
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1.5">Supervisor Responsável (Empresa)</label>
                    <select name="supervisor_id" class="w-full rounded-xl border-slate-300 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-100 py-2.5 px-3 text-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="">Não definido (somente registros pessoais)</option>
                        @foreach($supervisores as $supervisor)
                            <option value="{{ $supervisor->supervisor_ID }}" @selected(old('supervisor_id') == $supervisor->supervisor_ID)>
                                {{ $supervisor->usuario->Nome }}{{ $supervisor->empresa ? ' · ' . $supervisor->empresa : '' }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="flex items-end">
                    @if($alunosDisponiveis->isEmpty())
                        <p class="text-xs text-slate-500 dark:text-slate-400">Todos os alunos cadastrados já possuem estágio ativo.</p>
                    @else
                        <button type="submit" class="w-full inline-flex justify-center items-center gap-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 text-xs font-semibold shadow-sm transition-colors">
                            <span class="material-symbols-outlined text-[18px]">play_circle</span>
                            Criar Estágio
                        </button>
                    @endif
                </div>
            </form>
        </section>

        <section class="rounded-2xl border border-slate-200/80 dark:border-slate-800 bg-white dark:bg-slate-900 overflow-hidden shadow-sm">
            <div class="p-5 border-b border-slate-200 dark:border-slate-800">
                <h2 class="text-base font-bold text-slate-900 dark:text-slate-100">Estágios Sob Minha Orientação</h2>
            </div>
            @if($estagios->isEmpty())
                <p class="p-8 text-center text-sm text-slate-500 dark:text-slate-400">Nenhum estágio em andamento.</p>
            @else
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200 dark:divide-slate-800 text-sm">
                        <thead class="bg-slate-50 dark:bg-slate-800/80">
                            <tr>
                                <th class="px-5 py-3.5 text-left text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">Aluno</th>
                                <th class="px-5 py-3.5 text-left text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">Local</th>
                                <th class="px-5 py-3.5 text-left text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">Supervisor</th>
                                <th class="px-5 py-3.5 text-left text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">Período</th>
                                <th class="px-5 py-3.5 text-left text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">Status</th>
                                <th class="px-5 py-3.5 text-right text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">Ação</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                            @foreach($estagios as $estagio)
                                <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-800/40 transition-colors">
                                    <td class="px-5 py-3.5 font-semibold text-slate-900 dark:text-slate-100">{{ $estagio->aluno->nome }}</td>
                                    <td class="px-5 py-3.5 text-xs text-slate-600 dark:text-slate-300">{{ $estagio->local }}</td>
                                    <td class="px-5 py-3.5 text-xs text-slate-500 dark:text-slate-400">{{ $estagio->supervisor?->usuario?->Nome ?? 'Não definido' }}</td>
                                    <td class="px-5 py-3.5 text-xs text-slate-500 dark:text-slate-400">{{ $estagio->data_inicio->format('d/m/Y') }} a {{ $estagio->data_fim->format('d/m/Y') }}</td>
                                    <td class="px-5 py-3.5">
                                        <span class="inline-flex rounded-full bg-blue-50 dark:bg-blue-950/40 px-2.5 py-0.5 text-xs font-semibold capitalize text-blue-700 dark:text-blue-300">
                                            {{ $estagio->status_efetivo }}
                                        </span>
                                    </td>
                                    <td class="px-5 py-3.5 text-right">
                                        <a href="{{ route('estagios.show', $estagio) }}" class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-xs font-semibold text-blue-600 dark:text-blue-400 bg-blue-50 dark:bg-blue-950/40 hover:bg-blue-100 transition-colors">
                                            Visualizar
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </section>
    @endif

    {{-- Se for Aluno ou Supervisor: Grade de Estágios e Calendário --}}
    @if($papel === 'aluno' || $papel === 'supervisor')
        @if($estagio)
            {{-- Detalhes do Estágio e Horas --}}
            <section class="rounded-2xl border border-slate-200/80 dark:border-slate-800 bg-white dark:bg-slate-900 p-6 sm:p-7 shadow-sm">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-100 dark:border-slate-800 pb-5">
                    <div>
                        <span class="text-xs font-semibold uppercase tracking-wider text-blue-600 dark:text-blue-400">Estágio {{ $estagio->status }}</span>
                        <h2 class="text-xl font-bold text-slate-900 dark:text-slate-100 mt-1">{{ $estagio->local }}</h2>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                            Aluno: <strong class="text-slate-700 dark:text-slate-300">{{ $estagio->aluno->nome }}</strong> · Orientador: {{ $estagio->orientador->usuario->Nome }}
                        </p>
                    </div>
                    @if($papel === 'orientador' && $estagio->status === 'ativo')
                        <form method="POST" action="{{ route('estagios.finalizar', $estagio) }}" onsubmit="return confirm('Deseja realmente finalizar este estágio?');">
                            @csrf
                            <button class="px-4 py-2 rounded-xl text-xs font-semibold text-rose-600 border border-rose-200 hover:bg-rose-50 transition-colors">
                                Finalizar Estágio
                            </button>
                        </form>
                    @endif
                </div>

                <div class="grid grid-cols-2 sm:grid-cols-5 gap-3 mt-5">
                    <div class="p-3.5 rounded-xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200/60 dark:border-slate-700/60">
                        <span class="text-[11px] font-semibold uppercase tracking-wider text-slate-500">Meta</span>
                        <p class="text-xl font-bold text-slate-900 dark:text-slate-100 mt-1">{{ $metricas['meta_horas'] }}h</p>
                    </div>
                    <div class="p-3.5 rounded-xl bg-emerald-50/60 dark:bg-emerald-950/30 border border-emerald-200/60 dark:border-emerald-900/40">
                        <span class="text-[11px] font-semibold uppercase tracking-wider text-emerald-700 dark:text-emerald-400">Aprovadas</span>
                        <p class="text-xl font-bold text-emerald-700 dark:text-emerald-300 mt-1">{{ $metricas['aprovadas_formatadas'] }}</p>
                    </div>
                    <div class="p-3.5 rounded-xl bg-blue-50/60 dark:bg-blue-950/30 border border-blue-200/60 dark:border-blue-900/40">
                        <span class="text-[11px] font-semibold uppercase tracking-wider text-blue-700 dark:text-blue-400">Pessoais</span>
                        <p class="text-xl font-bold text-blue-700 dark:text-blue-300 mt-1">{{ $metricas['pessoais_contabilizadas_formatadas'] }}</p>
                    </div>
                    <div class="p-3.5 rounded-xl bg-amber-50/60 dark:bg-amber-950/30 border border-amber-200/60 dark:border-amber-900/40">
                        <span class="text-[11px] font-semibold uppercase tracking-wider text-amber-700 dark:text-amber-400">Pendentes</span>
                        <p class="text-xl font-bold text-amber-700 dark:text-amber-300 mt-1">{{ $metricas['pendentes_formatadas'] }}</p>
                    </div>
                    <div class="p-3.5 rounded-xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200/60 dark:border-slate-700/60">
                        <span class="text-[11px] font-semibold uppercase tracking-wider text-slate-500">Restantes</span>
                        <p class="text-xl font-bold text-slate-900 dark:text-slate-100 mt-1">{{ $metricas['restantes_formatadas'] }}</p>
                    </div>
                </div>
            </section>

            {{-- Calendário de Presenças --}}
            <section class="rounded-2xl border border-slate-200/80 dark:border-slate-800 bg-white dark:bg-slate-900 p-6 sm:p-7 shadow-sm">
                <div class="flex items-center justify-between mb-5">
                    <div>
                        <h2 class="text-base font-bold text-slate-900 dark:text-slate-100">Folha de Frequência Mensal</h2>
                        <p class="text-xs text-slate-500 dark:text-slate-400">Selecione os dias cumpridos para registrar ponto ou validar presenças.</p>
                    </div>
                    <div class="flex items-center gap-2">
                        <button id="mes-anterior" class="p-2 rounded-xl border border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-800 text-slate-600 dark:text-slate-300">
                            <span class="material-symbols-outlined text-[18px]">chevron_left</span>
                        </button>
                        <span id="mes-calendario" class="text-sm font-bold text-slate-800 dark:text-slate-200 px-3"></span>
                        <button id="proximo-mes" class="p-2 rounded-xl border border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-800 text-slate-600 dark:text-slate-300">
                            <span class="material-symbols-outlined text-[18px]">chevron_right</span>
                        </button>
                    </div>
                </div>

                {{-- Grade do Calendário --}}
                <div id="calendario-presencas" class="grid grid-cols-7 gap-2 text-center text-xs">
                    {{-- Dias gerados via JS --}}
                </div>
            </section>

            {{-- Script de dados para o calendário existente --}}
            <script id="dados-calendario-presencas" type="application/json">
                {!! json_encode($calendarioData ?? ['hoje' => date('Y-m-d'), 'inicio' => $estagio->data_inicio->format('Y-m-d'), 'fim' => $estagio->data_fim->format('Y-m-d'), 'presencas' => []]) !!}
            </script>
        @else
            <div class="rounded-2xl border border-slate-200/80 dark:border-slate-800 bg-white dark:bg-slate-900 p-12 text-center shadow-sm">
                <span class="material-symbols-outlined text-slate-300 dark:text-slate-600 text-[56px]">calendar_today</span>
                <p class="text-base font-bold text-slate-900 dark:text-slate-100 mt-3">Nenhum estágio em andamento</p>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Você não possui períodos de estágio ativos vinculados.</p>
            </div>
        @endif
    @endif
</main>
<script src="/js/calendario-presencas.js"></script>
@endsection
