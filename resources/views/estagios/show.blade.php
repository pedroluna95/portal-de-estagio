@extends('layouts.app')

@section('content')
<main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">
    <div class="flex items-center justify-between">
        <a href="{{ route('estagios.index') }}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-blue-600 dark:text-blue-400 hover:underline">
            <span class="material-symbols-outlined text-[16px]">arrow_back</span>
            Voltar aos estágios
        </a>
        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold
            {{ $estagio->status === 'ativo' ? 'bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-400 border border-emerald-200/60' : 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300' }}">
            Estágio {{ ucfirst($estagio->status) }}
        </span>
    </div>

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

    {{-- Visão Geral do Estágio --}}
    <section class="rounded-2xl border border-slate-200/80 dark:border-slate-800 bg-white dark:bg-slate-900 p-6 sm:p-7 shadow-sm">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-100 dark:border-slate-800 pb-5">
            <div>
                <span class="text-xs font-bold uppercase tracking-wider text-blue-600 dark:text-blue-400">Dados do Estágio</span>
                <h1 class="text-2xl font-extrabold text-slate-900 dark:text-slate-100 mt-1">{{ $estagio->local }}</h1>
                <div class="mt-2 flex flex-wrap gap-x-4 gap-y-1 text-xs text-slate-500 dark:text-slate-400">
                    <span>Aluno: <strong class="text-slate-700 dark:text-slate-200">{{ $estagio->aluno->nome }}</strong> ({{ $estagio->aluno->matricula }})</span>
                    <span>Orientador: <strong class="text-slate-700 dark:text-slate-200">{{ $estagio->orientador->usuario->Nome }}</strong></span>
                    <span>Supervisor: <strong class="text-slate-700 dark:text-slate-200">{{ $estagio->supervisor?->usuario?->Nome ?? 'Não vinculado' }}</strong></span>
                    <span>Período: <strong class="text-slate-700 dark:text-slate-200">{{ $estagio->data_inicio->format('d/m/Y') }} a {{ $estagio->data_fim->format('d/m/Y') }}</strong></span>
                </div>
            </div>
            @if($papel === 'orientador' && $estagio->status === 'ativo')
                <form method="POST" action="{{ route('estagios.finalizar', $estagio) }}" onsubmit="return confirm('Deseja realmente finalizar este estágio? O histórico de presenças será mantido.');">
                    @csrf
                    <button class="px-4 py-2 rounded-xl text-xs font-semibold text-rose-600 border border-rose-200 dark:border-rose-900/60 hover:bg-rose-50 dark:hover:bg-rose-950/30 transition-colors">
                        Finalizar Estágio
                    </button>
                </form>
            @endif
        </div>

        {{-- Cards de Metrificação --}}
        <div class="grid grid-cols-2 sm:grid-cols-5 gap-3 mt-5">
            <div class="p-4 rounded-xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200/60 dark:border-slate-700/60">
                <span class="text-[11px] font-bold uppercase tracking-wider text-slate-500">Carga Mínima</span>
                <p class="text-2xl font-black text-slate-900 dark:text-slate-100 mt-1">{{ $metricas['meta_horas'] }}h</p>
            </div>
            <div class="p-4 rounded-xl bg-emerald-50/60 dark:bg-emerald-950/30 border border-emerald-200/60 dark:border-emerald-900/40">
                <span class="text-[11px] font-bold uppercase tracking-wider text-emerald-700 dark:text-emerald-400">Aprovadas</span>
                <p class="text-2xl font-black text-emerald-700 dark:text-emerald-300 mt-1">{{ $metricas['aprovadas_formatadas'] }}</p>
            </div>
            <div class="p-4 rounded-xl bg-blue-50/60 dark:bg-blue-950/30 border border-blue-200/60 dark:border-blue-900/40">
                <span class="text-[11px] font-bold uppercase tracking-wider text-blue-700 dark:text-blue-400">Pessoais</span>
                <p class="text-2xl font-black text-blue-700 dark:text-blue-300 mt-1">{{ $metricas['pessoais_contabilizadas_formatadas'] }}</p>
            </div>
            <div class="p-4 rounded-xl bg-amber-50/60 dark:bg-amber-950/30 border border-amber-200/60 dark:border-amber-900/40">
                <span class="text-[11px] font-bold uppercase tracking-wider text-amber-700 dark:text-amber-400">Pendentes</span>
                <p class="text-2xl font-black text-amber-700 dark:text-amber-300 mt-1">{{ $metricas['pendentes_formatadas'] }}</p>
            </div>
            <div class="p-4 rounded-xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200/60 dark:border-slate-700/60">
                <span class="text-[11px] font-bold uppercase tracking-wider text-slate-500">Restantes</span>
                <p class="text-2xl font-black text-slate-900 dark:text-slate-100 mt-1">{{ $metricas['restantes_formatadas'] }}</p>
            </div>
        </div>

        {{-- Barra de Progresso --}}
        <div class="mt-5 pt-4 border-t border-slate-100 dark:border-slate-800">
            <div class="flex justify-between items-center text-xs font-semibold text-slate-600 dark:text-slate-300 mb-2">
                <span>Progresso das horas mínimas · {{ $metricas['horas_progresso_formatadas'] }} de {{ $metricas['meta_horas'] }}h</span>
                <span class="text-blue-600 dark:text-blue-400 font-bold">{{ $metricas['percentual'] }}%</span>
            </div>
            <div class="h-4 w-full overflow-hidden rounded-full bg-slate-100 dark:bg-slate-800" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="{{ $metricas['percentual'] }}">
                <div class="flex h-full overflow-hidden rounded-full" style="width: {{ $metricas['percentual'] }}%; min-width: {{ $metricas['percentual'] > 0 ? '8px' : '0' }}; max-width: 100%">
                    @if($metricas['horas_progresso_minutos'] > 0)
                        <div class="h-full shrink-0 bg-emerald-500" style="width: {{ $metricas['aprovadas_minutos'] / $metricas['horas_progresso_minutos'] * 100 }}%"></div>
                        <div class="h-full shrink-0 bg-blue-500" style="width: {{ $metricas['pessoais_contabilizadas_minutos'] / $metricas['horas_progresso_minutos'] * 100 }}%"></div>
                    @endif
                </div>
            </div>
        </div>
    </section>

    {{-- Seção de Vinculação de Supervisor pelo Orientador --}}
    @if($papel === 'orientador' && $estagio->status === 'ativo' && empty($estagio->supervisor))
        <section class="rounded-2xl border border-slate-200/80 dark:border-slate-800 bg-white dark:bg-slate-900 p-6 shadow-sm">
            <h2 class="text-base font-bold text-slate-900 dark:text-slate-100 mb-1">Vincular Supervisor da Empresa</h2>
            <p class="text-xs text-slate-500 dark:text-slate-400 mb-4">Necessário para permitir envio e validação oficial de presenças.</p>
            <form method="POST" action="{{ route('estagios.supervisor.store', $estagio) }}" class="flex flex-col sm:flex-row gap-3 max-w-xl">
                @csrf
                <select name="supervisor_id" required class="flex-1 rounded-xl border-slate-300 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-100 py-2.5 px-3 text-sm focus:border-blue-500 focus:ring-blue-500">
                    <option value="">Selecione um supervisor</option>
                    @foreach($supervisoresDisponiveis as $supervisorDisponivel)
                        <option value="{{ $supervisorDisponivel->supervisor_ID }}">{{ $supervisorDisponivel->usuario->Nome }}{{ $supervisorDisponivel->empresa ? ' · ' . $supervisorDisponivel->empresa : '' }}</option>
                    @endforeach
                </select>
                <button type="submit" class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-xs font-semibold shadow-sm transition-colors">
                    Vincular
                </button>
            </form>
        </section>
    @endif

    {{-- Lista de Frequências e Presenças --}}
    <section class="rounded-2xl border border-slate-200/80 dark:border-slate-800 bg-white dark:bg-slate-900 overflow-hidden shadow-sm">
        <div class="p-5 border-b border-slate-200 dark:border-slate-800 flex items-center justify-between">
            <h2 class="text-base font-bold text-slate-900 dark:text-slate-100 flex items-center gap-2">
                <span class="material-symbols-outlined text-blue-600 dark:text-blue-400 text-[20px]">fact_check</span>
                Registros de Frequência do Aluno
            </h2>
            <span class="text-xs font-medium text-slate-500">{{ $estagio->presencas->count() }} lançamento(s)</span>
        </div>

        @if($estagio->presencas->isEmpty())
            <div class="p-10 text-center text-slate-500 dark:text-slate-400 text-sm">
                Nenhum registro de frequência lançado para este estágio até o momento.
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200 dark:divide-slate-800 text-sm">
                    <thead class="bg-slate-50 dark:bg-slate-800/80">
                        <tr>
                            <th class="px-5 py-3.5 text-left text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">Data</th>
                            <th class="px-5 py-3.5 text-left text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">Horas</th>
                            <th class="px-5 py-3.5 text-left text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">Tipo</th>
                            <th class="px-5 py-3.5 text-left text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">Status</th>
                            @if($papel === 'supervisor')
                                <th class="px-5 py-3.5 text-right text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">Ações de Validação</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                        @foreach($estagio->presencas as $presenca)
                            <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-800/40 transition-colors">
                                <td class="px-5 py-3.5 font-semibold text-slate-900 dark:text-slate-100">{{ $presenca->data->format('d/m/Y') }}</td>
                                <td class="px-5 py-3.5 font-mono text-xs text-slate-700 dark:text-slate-300">{{ $presenca->horas_formatadas ?? '—' }}</td>
                                <td class="px-5 py-3.5 text-xs text-slate-500 capitalize">{{ $presenca->modo }}</td>
                                <td class="px-5 py-3.5">
                                    <span class="inline-flex px-2.5 py-0.5 rounded-full text-xs font-semibold
                                        {{ $presenca->status === 'aprovada'
                                            ? 'bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-400'
                                            : ($presenca->status === 'recusada'
                                                ? 'bg-rose-50 dark:bg-rose-950/40 text-rose-700 dark:text-rose-400'
                                                : 'bg-amber-50 dark:bg-amber-950/40 text-amber-700 dark:text-amber-400') }}">
                                        {{ ucfirst($presenca->status) }}
                                    </span>
                                </td>
                                @if($papel === 'supervisor')
                                    <td class="px-5 py-3.5 text-right">
                                        @if($presenca->status === 'pendente')
                                            <div class="inline-flex items-center gap-1.5">
                                                <form method="POST" action="{{ route('presencas.aprovar', $presenca) }}" class="inline">
                                                    @csrf
                                                    <button type="submit" class="px-3 py-1 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold shadow-sm transition-colors">
                                                        Aprovar
                                                    </button>
                                                </form>
                                                <form method="POST" action="{{ route('presencas.recusar', $presenca) }}" class="inline">
                                                    @csrf
                                                    <button type="submit" class="px-3 py-1 rounded-lg bg-rose-600 hover:bg-rose-700 text-white text-xs font-semibold shadow-sm transition-colors">
                                                        Recusar
                                                    </button>
                                                </form>
                                            </div>
                                        @else
                                            <span class="text-xs text-slate-400">Avaliado</span>
                                        @endif
                                    </td>
                                @endif
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </section>
</main>
@endsection
