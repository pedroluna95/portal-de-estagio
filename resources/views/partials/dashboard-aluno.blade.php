<main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8">
    {{-- Saudação moderna --}}
    <div class="flex items-center gap-4 border-b border-slate-200 dark:border-slate-800 pb-5">
        <div class="w-14 h-14 rounded-2xl bg-gradient-to-tr from-blue-600 to-indigo-600 flex items-center justify-center text-white shadow-md shadow-blue-500/20 shrink-0">
            <span class="material-symbols-outlined text-[28px]">school</span>
        </div>
        <div>
            <span class="text-xs font-semibold text-blue-600 dark:text-blue-400 uppercase tracking-wider">Área do Aluno</span>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 dark:text-slate-100 tracking-tight">
                Olá, {{ session('user_name', 'Aluno') }}!
            </h1>
            <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 mt-0.5">
                Acompanhe o cumprimento das horas e o andamento do seu estágio na UFCI-RJ.
            </p>
        </div>
    </div>

    {{-- Cards de Resumo de Horas --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
        {{-- Card de Horas Contabilizadas --}}
        <div class="rounded-2xl border border-slate-200/80 dark:border-slate-800 bg-white dark:bg-slate-900 p-6 shadow-sm flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between mb-4">
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">Horas Contabilizadas</span>
                    <div class="w-10 h-10 rounded-xl bg-purple-50 dark:bg-purple-950/60 text-purple-600 dark:text-purple-400 flex items-center justify-center">
                        <span class="material-symbols-outlined text-[22px]">schedule</span>
                    </div>
                </div>

                @if($estagioAtivo && $metricas)
                    <div class="flex items-baseline gap-2">
                        <span class="text-4xl font-black text-slate-900 dark:text-slate-100 tracking-tight">{{ $metricas['horas_progresso_formatadas'] }}</span>
                        <span class="text-base font-semibold text-slate-400">/ {{ $metricas['meta_horas'] }}h meta</span>
                    </div>

                    {{-- Barra de Progresso com Multi-cores --}}
                    <div class="mt-4 h-4 w-full overflow-hidden rounded-full bg-slate-100 dark:bg-slate-800" role="progressbar" aria-label="Progresso das horas mínimas" aria-valuemin="0" aria-valuemax="100" aria-valuenow="{{ $metricas['percentual'] }}">
                        <div class="flex h-full overflow-hidden rounded-full" style="width: {{ $metricas['percentual'] }}%; min-width: {{ $metricas['percentual'] > 0 ? '8px' : '0' }}; max-width: 100%">
                            @if($metricas['horas_progresso_minutos'] > 0)
                                <div class="h-full shrink-0 bg-emerald-500" style="width: {{ $metricas['aprovadas_minutos'] / $metricas['horas_progresso_minutos'] * 100 }}%"></div>
                                <div class="h-full shrink-0 bg-blue-500" style="width: {{ $metricas['pessoais_contabilizadas_minutos'] / $metricas['horas_progresso_minutos'] * 100 }}%"></div>
                            @endif
                        </div>
                    </div>

                    <div class="mt-3 flex flex-wrap gap-x-4 gap-y-1 text-xs">
                        <span class="inline-flex items-center gap-1.5 font-semibold text-emerald-600 dark:text-emerald-400">
                            <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                            {{ $metricas['aprovadas_formatadas'] }} oficiais aprovadas
                        </span>
                        <span class="inline-flex items-center gap-1.5 font-semibold text-blue-600 dark:text-blue-400">
                            <span class="w-2 h-2 rounded-full bg-blue-500"></span>
                            {{ $metricas['pessoais_contabilizadas_formatadas'] }} pessoais contabilizadas
                        </span>
                    </div>

                    <div class="mt-4 pt-3 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between text-xs text-slate-500 dark:text-slate-400">
                        <span>{{ $metricas['percentual'] }}% concluído</span>
                        <a href="/controle-horas" class="font-semibold text-blue-600 dark:text-blue-400 hover:underline">Registrar frequência &rarr;</a>
                    </div>
                @else
                    <p class="text-sm text-slate-500 dark:text-slate-400 py-4">Nenhum estágio ativo no momento. Aguarde a vinculação do seu orientador.</p>
                @endif
            </div>
        </div>

        {{-- Card de Frequências Pendentes --}}
        <div class="rounded-2xl border border-slate-200/80 dark:border-slate-800 bg-white dark:bg-slate-900 p-6 shadow-sm flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between mb-4">
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">Validações Pendentes</span>
                    <div class="w-10 h-10 rounded-xl bg-amber-50 dark:bg-amber-950/60 text-amber-600 dark:text-amber-400 flex items-center justify-center">
                        <span class="material-symbols-outlined text-[22px]">pending_actions</span>
                    </div>
                </div>

                <p class="text-4xl font-black text-slate-900 dark:text-slate-100 tracking-tight">{{ $metricas['pendentes_formatadas'] ?? '0h' }}</p>
                <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">Aguardando parecer do supervisor responsável na empresa.</p>

                <div class="mt-6 p-3.5 rounded-xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200/60 dark:border-slate-700/60 text-xs text-slate-600 dark:text-slate-400 leading-relaxed">
                    Presenças aprovadas são convertidas oficialmente no histórico. Registros pendentes não afetam sua contagem final até aprovação.
                </div>
            </div>

            <div class="mt-4 pt-3 border-t border-slate-100 dark:border-slate-800 text-right">
                <a href="/controle-horas" class="text-xs font-semibold text-blue-600 dark:text-blue-400 hover:underline">Ver histórico completo &rarr;</a>
            </div>
        </div>
    </div>

    {{-- Detalhes do Estágio Atual --}}
    <div class="rounded-2xl border border-slate-200/80 dark:border-slate-800 bg-white dark:bg-slate-900 p-6 sm:p-7 shadow-sm">
        <h2 class="text-base font-bold text-slate-900 dark:text-slate-100 mb-4 flex items-center gap-2">
            <span class="material-symbols-outlined text-blue-600 dark:text-blue-400 text-[20px]">work</span>
            Estágio Atual em Andamento
        </h2>

        @if($estagioAtivo)
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 p-5 rounded-2xl bg-slate-50 dark:bg-slate-800/50 border border-slate-200/60 dark:border-slate-700/60">
                <div class="flex items-start gap-4">
                    <div class="w-12 h-12 rounded-xl bg-blue-100 dark:bg-blue-900/60 text-blue-700 dark:text-blue-300 flex items-center justify-center shrink-0">
                        <span class="material-symbols-outlined text-[24px]">domain</span>
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <h3 class="font-bold text-base text-slate-900 dark:text-slate-100">{{ $estagioAtivo->local }}</h3>
                            <span class="px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-400 border border-emerald-200/60">
                                Ativo
                            </span>
                        </div>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                            Professor Orientador: <strong class="text-slate-700 dark:text-slate-300">{{ $estagioAtivo->orientador->usuario->Nome ?? '—' }}</strong>
                        </p>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                            Período: <span class="font-medium text-slate-700 dark:text-slate-300">{{ $estagioAtivo->data_inicio->format('d/m/Y') }}</span> até <span class="font-medium text-slate-700 dark:text-slate-300">{{ $estagioAtivo->data_fim->format('d/m/Y') }}</span>
                        </p>
                    </div>
                </div>

                <div>
                    <a href="/controle-horas" class="inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold shadow-sm transition-colors">
                        <span class="material-symbols-outlined text-[18px]">calendar_month</span>
                        Abrir Folha de Ponto
                    </a>
                </div>
            </div>
        @else
            <div class="p-8 text-center rounded-2xl bg-slate-50 dark:bg-slate-800/40 border border-slate-200/60 dark:border-slate-700/60">
                <span class="material-symbols-outlined text-slate-300 dark:text-slate-600 text-[48px]">assignment_late</span>
                <p class="text-sm font-semibold text-slate-700 dark:text-slate-300 mt-2">Você ainda não possui um estágio vinculado</p>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Consulte o tutorial ou contate seu professor orientador para formalizar o início.</p>
                <div class="mt-4 flex justify-center gap-3">
                    <a href="/tutorial" class="px-4 py-2 rounded-xl bg-blue-600 text-white text-xs font-semibold hover:bg-blue-700 transition-colors">
                        Ver Tutorial do Estágio
                    </a>
                    <a href="/empresas" class="px-4 py-2 rounded-xl border border-slate-300 dark:border-slate-700 text-xs font-semibold text-slate-700 dark:text-slate-300 hover:bg-slate-100 transition-colors">
                        Empresas Conveniadas
                    </a>
                </div>
            </div>
        @endif
    </div>
</main>
