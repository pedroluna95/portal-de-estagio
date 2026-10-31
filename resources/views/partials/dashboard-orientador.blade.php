<main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <header class="mb-6 flex flex-wrap items-end justify-between gap-4">
        <div>
            <h1 class="text-2xl font-semibold text-gray-900">Dashboard do orientador</h1>
            <p class="mt-1 text-sm text-gray-600">Acompanhe seus estágios e frequências enviadas pelos alunos.</p>
        </div>
        <a href="{{ route('estagios.index') }}" class="rounded bg-[#0077fc] px-4 py-2 text-sm font-medium text-white hover:bg-[#0056c9]">Gerenciar estágios</a>
    </header>

    <section class="mb-8 grid grid-cols-1 gap-4">
        <div class="border-b border-gray-200 py-4">
            <p class="text-sm text-gray-600">Estágios vinculados</p>
            <p class="mt-1 text-2xl font-semibold text-gray-900">{{ $estagios->count() }}</p>
        </div>
        <div class="border-b border-gray-200 py-4">
            <p class="text-sm text-gray-600">Frequências aguardando análise</p>
            <p class="mt-1 text-2xl font-semibold text-gray-900">{{ $frequenciasPendentes }}</p>
        </div>
    </section>

    <section>
        <h2 class="mb-3 text-lg font-semibold text-gray-900">Meus estágios</h2>
        @if($estagios->isEmpty())
            <p class="border-y border-gray-200 py-5 text-sm text-gray-600">Você ainda não possui estágios vinculados. Inicialize um estágio para um aluno.</p>
        @else
            <div class="overflow-x-auto border-y border-gray-200">
                <table class="min-w-full divide-y divide-gray-200 text-left text-sm">
                    <thead class="text-xs uppercase text-gray-600">
                        <tr><th class="py-3 pr-4">Aluno</th><th class="py-3 pr-4">Local</th><th class="py-3 pr-4">Período</th><th class="py-3 pr-4">Status</th><th class="py-3 pr-4">Pendentes</th><th class="py-3">Ação</th></tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($estagios as $estagio)
                            @php($statusExibido = $estagio->status === 'ativo' && $estagio->data_fim->toDateString() < today()->toDateString() ? 'finalizado' : $estagio->status)
                            <tr>
                                <td class="py-3 pr-4 font-medium text-gray-900">{{ $estagio->aluno->nome }}</td>
                                <td class="py-3 pr-4">{{ $estagio->local }}</td>
                                <td class="py-3 pr-4">{{ $estagio->data_inicio->format('d/m/Y') }} a {{ $estagio->data_fim->format('d/m/Y') }}</td>
                                <td class="py-3 pr-4 capitalize">{{ $statusExibido }}</td>
                                <td class="py-3 pr-4">{{ $estagio->presencas->where('status', 'pendente')->count() }}</td>
                                <td class="py-3"><a href="{{ route('estagios.show', $estagio) }}" class="font-medium text-[#0056c9] hover:underline">Acompanhar</a></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </section>
</main>
</div>
