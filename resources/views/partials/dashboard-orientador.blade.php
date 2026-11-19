<main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8">
    {{-- Header Orientador --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 border-b border-slate-200 dark:border-slate-800 pb-5">
        <div class="flex items-center gap-4">
            <div class="w-14 h-14 rounded-2xl bg-gradient-to-tr from-indigo-600 to-violet-600 flex items-center justify-center text-white shadow-md shadow-indigo-500/20 shrink-0">
                <span class="material-symbols-outlined text-[28px]">person_pin</span>
            </div>
            <div>
                <span class="text-xs font-semibold text-indigo-600 dark:text-indigo-400 uppercase tracking-wider">Painel do Orientador</span>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 dark:text-slate-100 tracking-tight">
                    Prof. {{ session('user_name', 'Orientador') }}
                </h1>
                <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 mt-0.5">
                    Gerencie os alunos sob sua orientação, inicialize novos estágios e acompanhe as frequências.
                </p>
            </div>
        </div>

        <div>
            <a href="{{ route('estagios.index') }}" class="inline-flex items-center gap-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white px-4 py-2.5 text-xs font-semibold shadow-sm shadow-blue-500/20 transition-all">
                <span class="material-symbols-outlined text-[18px]">add_circle</span>
                Inicializar Novo Estágio
            </a>
        </div>
    </div>

    {{-- Cards de Resumo --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div class="rounded-2xl border border-slate-200/80 dark:border-slate-800 bg-white dark:bg-slate-900 p-6 shadow-sm flex items-center justify-between">
            <div>
                <span class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400 block mb-1">
                    Estágios Vinculados
                </span>
                <span class="text-3xl font-black text-slate-900 dark:text-slate-100 tracking-tight">
                    {{ $estagios->count() }}
                </span>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Alunos atualmente orientados</p>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-indigo-50 dark:bg-indigo-950/60 text-indigo-600 dark:text-indigo-400 flex items-center justify-center shrink-0">
                <span class="material-symbols-outlined text-[28px]">groups</span>
            </div>
        </div>

        <div class="rounded-2xl border border-slate-200/80 dark:border-slate-800 bg-white dark:bg-slate-900 p-6 shadow-sm flex items-center justify-between">
            <div>
                <span class="text-xs font-bold uppercase tracking-wider text-amber-600 dark:text-amber-400 block mb-1">
                    Presenças Aguardando Análise
                </span>
                <span class="text-3xl font-black text-amber-700 dark:text-amber-300 tracking-tight">
                    {{ $frequenciasPendentes }}
                </span>
                <p class="text-xs text-amber-600/80 dark:text-amber-400/80 mt-1">Validações pendentes pelos supervisores</p>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-amber-50 dark:bg-amber-950/60 text-amber-600 dark:text-amber-400 flex items-center justify-center shrink-0">
                <span class="material-symbols-outlined text-[28px]">pending_actions</span>
            </div>
        </div>
    </div>

    {{-- Tabela de Estágios --}}
    <div class="rounded-2xl border border-slate-200/80 dark:border-slate-800 bg-white dark:bg-slate-900 overflow-hidden shadow-sm">
        <div class="p-5 border-b border-slate-200 dark:border-slate-800 flex items-center justify-between">
            <h2 class="text-base font-bold text-slate-900 dark:text-slate-100 flex items-center gap-2">
                <span class="material-symbols-outlined text-blue-600 dark:text-blue-400 text-[20px]">assignment</span>
                Meus Estágios Orientados
            </h2>
            <span class="text-xs font-semibold text-slate-500 dark:text-slate-400">
                {{ $estagios->count() }} {{ $estagios->count() === 1 ? 'estágio' : 'estágios' }}
            </span>
        </div>

        @if($estagios->isEmpty())
            <div class="p-12 text-center text-slate-500 dark:text-slate-400">
                <span class="material-symbols-outlined text-slate-300 dark:text-slate-600 text-[48px]">school</span>
                <p class="text-sm font-semibold text-slate-700 dark:text-slate-300 mt-2">Você ainda não possui estágios vinculados</p>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Clique no botão acima para inicializar o estágio de um aluno.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200 dark:divide-slate-800 text-sm">
                    <thead class="bg-slate-50 dark:bg-slate-800/80">
                        <tr>
                            <th class="px-5 py-3.5 text-left text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">Aluno</th>
                            <th class="px-5 py-3.5 text-left text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">Local / Setor</th>
                            <th class="px-5 py-3.5 text-left text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">Período</th>
                            <th class="px-5 py-3.5 text-left text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">Status</th>
                            <th class="px-5 py-3.5 text-left text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">Pendências</th>
                            <th class="px-5 py-3.5 text-right text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">Ação</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                        @foreach($estagios as $estagio)
                            <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-800/40 transition-colors">
                                <td class="px-5 py-3.5 font-semibold text-slate-900 dark:text-slate-100">{{ $estagio->aluno->nome }}</td>
                                <td class="px-5 py-3.5 text-xs text-slate-600 dark:text-slate-300">{{ $estagio->local }}</td>
                                <td class="px-5 py-3.5 text-xs text-slate-500 dark:text-slate-400">{{ $estagio->data_inicio->format('d/m/Y') }} a {{ $estagio->data_fim->format('d/m/Y') }}</td>
                                <td class="px-5 py-3.5">
                                    <span class="inline-flex rounded-full bg-blue-50 dark:bg-blue-950/40 px-2.5 py-0.5 text-xs font-semibold capitalize text-blue-700 dark:text-blue-300 border border-blue-200/60 dark:border-blue-900/40">
                                        {{ $estagio->status_efetivo }}
                                    </span>
                                </td>
                                <td class="px-5 py-3.5 text-xs font-medium {{ $estagio->presencas->where('status', 'pendente')->count() > 0 ? 'text-amber-600 dark:text-amber-400' : 'text-slate-400' }}">
                                    {{ $estagio->presencas->where('status', 'pendente')->count() }} pendente(s)
                                </td>
                                <td class="px-5 py-3.5 text-right">
                                    <a href="{{ route('estagios.show', $estagio) }}" class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-xs font-semibold text-blue-600 dark:text-blue-400 bg-blue-50 dark:bg-blue-950/40 hover:bg-blue-100 transition-colors">
                                        <span class="material-symbols-outlined text-[16px]">visibility</span>
                                        Acompanhar
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</main>
