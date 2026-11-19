<main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8">
    {{-- Header Supervisor --}}
    <div class="flex items-center gap-4 border-b border-slate-200 dark:border-slate-800 pb-5">
        <div class="w-14 h-14 rounded-2xl bg-gradient-to-tr from-purple-600 to-indigo-600 flex items-center justify-center text-white shadow-md shadow-purple-500/20 shrink-0">
            <span class="material-symbols-outlined text-[28px]">verified_user</span>
        </div>
        <div>
            <span class="text-xs font-semibold text-purple-600 dark:text-purple-400 uppercase tracking-wider">Painel da Empresa / Supervisor</span>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 dark:text-slate-100 tracking-tight">
                Supervisor {{ session('user_name', 'Supervisor') }}
            </h1>
            <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 mt-0.5">
                Valide os registros diários de frequência e presenças dos estagiários da sua empresa.
            </p>
        </div>
    </div>

    {{-- Cards de Resumo --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div class="rounded-2xl border border-slate-200/80 dark:border-slate-800 bg-white dark:bg-slate-900 p-6 shadow-sm flex items-center justify-between">
            <div>
                <span class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400 block mb-1">
                    Estágios Atribuídos
                </span>
                <span class="text-3xl font-black text-slate-900 dark:text-slate-100 tracking-tight">
                    {{ $estagios->count() }}
                </span>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Alunos sob sua supervisão direta</p>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-purple-50 dark:bg-purple-950/60 text-purple-600 dark:text-purple-400 flex items-center justify-center shrink-0">
                <span class="material-symbols-outlined text-[28px]">groups</span>
            </div>
        </div>

        <div class="rounded-2xl border border-slate-200/80 dark:border-slate-800 bg-white dark:bg-slate-900 p-6 shadow-sm flex items-center justify-between">
            <div>
                <span class="text-xs font-bold uppercase tracking-wider text-amber-600 dark:text-amber-400 block mb-1">
                    Presenças a Validar
                </span>
                <span class="text-3xl font-black text-amber-700 dark:text-amber-300 tracking-tight">
                    {{ $frequenciasPendentes }}
                </span>
                <p class="text-xs text-amber-600/80 dark:text-amber-400/80 mt-1">Registros aguardando aprovação ou recusa</p>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-amber-50 dark:bg-amber-950/60 text-amber-600 dark:text-amber-400 flex items-center justify-center shrink-0">
                <span class="material-symbols-outlined text-[28px]">fact_check</span>
            </div>
        </div>
    </div>

    {{-- Tabela de Estágios --}}
    <div class="rounded-2xl border border-slate-200/80 dark:border-slate-800 bg-white dark:bg-slate-900 overflow-hidden shadow-sm">
        <div class="p-5 border-b border-slate-200 dark:border-slate-800 flex items-center justify-between">
            <h2 class="text-base font-bold text-slate-900 dark:text-slate-100 flex items-center gap-2">
                <span class="material-symbols-outlined text-purple-600 dark:text-purple-400 text-[20px]">badge</span>
                Estágios Sob Minha Supervisão
            </h2>
            <span class="text-xs font-semibold text-slate-500 dark:text-slate-400">
                {{ $estagios->count() }} {{ $estagios->count() === 1 ? 'estágio' : 'estágios' }}
            </span>
        </div>

        @if($estagios->isEmpty())
            <div class="p-12 text-center text-slate-500 dark:text-slate-400">
                <span class="material-symbols-outlined text-slate-300 dark:text-slate-600 text-[48px]">badge</span>
                <p class="text-sm font-semibold text-slate-700 dark:text-slate-300 mt-2">Nenhum estágio foi atribuído a você ainda</p>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Assim que um orientador vincular sua conta a um estagiário, ele aparecerá aqui.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200 dark:divide-slate-800 text-sm">
                    <thead class="bg-slate-50 dark:bg-slate-800/80">
                        <tr>
                            <th class="px-5 py-3.5 text-left text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">Estagiário</th>
                            <th class="px-5 py-3.5 text-left text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">Empresa / Local</th>
                            <th class="px-5 py-3.5 text-left text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">Orientador UFCI</th>
                            <th class="px-5 py-3.5 text-left text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">Presenças Pendentes</th>
                            <th class="px-5 py-3.5 text-right text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">Ação</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                        @foreach($estagios as $estagio)
                            <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-800/40 transition-colors">
                                <td class="px-5 py-3.5 font-semibold text-slate-900 dark:text-slate-100">{{ $estagio->aluno->nome }}</td>
                                <td class="px-5 py-3.5 text-xs text-slate-600 dark:text-slate-300">{{ $estagio->local }}</td>
                                <td class="px-5 py-3.5 text-xs text-slate-500 dark:text-slate-400">{{ $estagio->orientador->usuario->Nome }}</td>
                                <td class="px-5 py-3.5">
                                    @php $pendentesCount = $estagio->presencas->where('status', 'pendente')->count(); @endphp
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold
                                        {{ $pendentesCount > 0 ? 'bg-amber-50 dark:bg-amber-950/40 text-amber-700 dark:text-amber-300 border border-amber-200/60' : 'bg-slate-100 dark:bg-slate-800 text-slate-500' }}">
                                        @if($pendentesCount > 0)
                                            <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                        @endif
                                        {{ $pendentesCount }} pendente(s)
                                    </span>
                                </td>
                                <td class="px-5 py-3.5 text-right">
                                    <a href="{{ route('estagios.show', $estagio) }}" class="inline-flex items-center gap-1 px-3.5 py-1.5 rounded-lg text-xs font-semibold text-purple-700 dark:text-purple-300 bg-purple-50 dark:bg-purple-950/40 hover:bg-purple-100 transition-colors">
                                        <span class="material-symbols-outlined text-[16px]">fact_check</span>
                                        Validar
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
