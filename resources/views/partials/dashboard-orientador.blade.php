<main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <header class="mb-8 flex flex-wrap items-center justify-between gap-4">
        <div class="flex items-center gap-3">
            <div class="w-12 h-12 rounded-full fig-gradient flex items-center justify-center text-white">
                <span class="material-symbols-outlined">person</span>
            </div>
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Bem-vindo de volta, {{ session('user_name', 'Orientador') }}!</h1>
            </div>
        </div>
        <a href="{{ route('estagios.index') }}" class="inline-flex items-center gap-2 rounded-lg bg-[#0077fc] px-4 py-2 text-sm font-medium text-white transition-colors hover:bg-[#0056c9]">
            <span class="material-symbols-outlined text-[18px]">edit_note</span>
            Gerenciar estágios
        </a>
    </header>

    <section class="mb-8 grid grid-cols-1 gap-6 sm:grid-cols-2" aria-label="Resumo dos estágios">
        <div class="rounded-xl border border-gray-200 bg-white p-6 text-gray-900 shadow-sm">
            <div class="mb-3 flex items-center justify-between">
                <span class="text-sm text-gray-600">Estágios vinculados</span>
                <span class="material-symbols-outlined text-blue-700">business</span>
            </div>
            <p class="text-3xl font-bold text-gray-900">{{ $estagios->count() }}</p>
        </div>
        <div class="rounded-xl border border-gray-200 bg-white p-6 text-gray-900 shadow-sm">
            <div class="mb-3 flex items-center justify-between">
                <span class="text-sm text-gray-600">Frequências aguardando análise</span>
                <span class="material-symbols-outlined text-amber-600">pending_actions</span>
            </div>
            <p class="text-3xl font-bold text-gray-900">{{ $frequenciasPendentes }}</p>
        </div>
    </section>

    <section class="rounded-xl border border-gray-200 bg-white p-6 text-gray-900 shadow-sm">
        <div class="mb-5 flex flex-wrap items-center justify-between gap-3">
            <h2 class="text-xl font-semibold text-gray-900">Meus estágios</h2>
            <span class="text-sm text-gray-500">{{ $estagios->count() }} {{ $estagios->count() === 1 ? 'estágio' : 'estágios' }}</span>
        </div>

        @if($estagios->isEmpty())
            <p class="rounded-lg bg-gray-50 px-4 py-5 text-sm text-gray-700">Você ainda não possui estágios vinculados. Inicialize um estágio para um aluno.</p>
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-left text-sm">
                    <thead class="text-xs uppercase text-gray-500">
                        <tr>
                            <th scope="col" class="py-3 pr-4 font-medium">Aluno</th>
                            <th scope="col" class="py-3 pr-4 font-medium">Local</th>
                            <th scope="col" class="py-3 pr-4 font-medium">Período</th>
                            <th scope="col" class="py-3 pr-4 font-medium">Status</th>
                            <th scope="col" class="py-3 pr-4 font-medium">Pendentes</th>
                            <th scope="col" class="py-3 font-medium">Ação</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($estagios as $estagio)
                            @php($statusExibido = $estagio->status === 'ativo' && $estagio->data_fim->toDateString() < today()->toDateString() ? 'finalizado' : $estagio->status)
                            <tr>
                                <td class="py-3 pr-4 font-medium text-gray-900">{{ $estagio->aluno->nome }}</td>
                                <td class="py-3 pr-4 text-gray-600">{{ $estagio->local }}</td>
                                <td class="py-3 pr-4 text-gray-600">{{ $estagio->data_inicio->format('d/m/Y') }} a {{ $estagio->data_fim->format('d/m/Y') }}</td>
                                <td class="py-3 pr-4">
                                    <span class="inline-flex rounded-full bg-blue-50 px-2.5 py-1 text-xs font-semibold capitalize text-blue-800">{{ $statusExibido }}</span>
                                </td>
                                <td class="py-3 pr-4 text-gray-600">{{ $estagio->presencas->where('status', 'pendente')->count() }}</td>
                                <td class="py-3"><a href="{{ route('estagios.show', $estagio) }}" class="font-medium text-[#0056c9] hover:underline">Acompanhar</a></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </section>
</main>
