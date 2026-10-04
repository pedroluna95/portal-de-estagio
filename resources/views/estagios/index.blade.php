@extends('layouts.app')

@section('content')
<main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">
    <header>
        <h1 class="text-2xl font-semibold text-gray-900">{{ $papel === 'orientador' ? 'Meus estágios' : ($papel === 'supervisor' ? 'Estágios sob supervisão' : 'Controle de horas') }}</h1>
        <p class="mt-1 text-sm text-gray-600">Acompanhe períodos, jornadas e frequências do estágio.</p>
    </header>

    @if(session('success'))
        <div role="status" class="rounded border border-green-200 bg-green-50 p-3 text-sm text-green-800">{{ session('success') }}</div>
    @endif
    @if($errors->any())
        <div role="alert" class="rounded border border-red-200 bg-red-50 p-3 text-sm text-red-800">
            <ul class="list-disc space-y-1 pl-5">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
        </div>
    @endif

    @if($papel === 'orientador')
        <section class="border-b border-gray-200 pb-8">
            <h2 class="text-lg font-semibold text-gray-900">Inicializar estágio</h2>
            <form method="POST" action="{{ route('estagios.store') }}" class="mt-4 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                @csrf
                <label class="text-sm font-medium text-gray-700">
                    Aluno
                    <select name="aluno_id" required class="mt-1 block w-full rounded border-gray-300">
                        <option value="">Selecione um aluno</option>
                        @foreach($alunosDisponiveis as $aluno)
                            <option value="{{ $aluno->user_id }}" @selected(old('aluno_id') == $aluno->user_id)>{{ $aluno->nome }} · {{ $aluno->matricula }}</option>
                        @endforeach
                    </select>
                </label>
                <label class="text-sm font-medium text-gray-700">
                    Local do estágio
                    <input name="local" value="{{ old('local') }}" maxlength="150" required class="mt-1 block w-full rounded border-gray-300" placeholder="Empresa ou setor" />
                </label>
                <label class="text-sm font-medium text-gray-700">
                    Data inicial
                    <input type="date" name="data_inicio" value="{{ old('data_inicio') }}" required class="mt-1 block w-full rounded border-gray-300" />
                </label>
                <label class="text-sm font-medium text-gray-700">
                    Data final
                    <input type="date" name="data_fim" value="{{ old('data_fim') }}" required class="mt-1 block w-full rounded border-gray-300" />
                </label>
                <label class="text-sm font-medium text-gray-700">
                    Supervisor responsável pela validação
                    <select name="supervisor_id" class="mt-1 block w-full rounded border-gray-300">
                        <option value="">Não definido (somente registros pessoais)</option>
                        @foreach($supervisores as $supervisor)
                            <option value="{{ $supervisor->supervisor_ID }}" @selected(old('supervisor_id') == $supervisor->supervisor_ID)>
                                {{ $supervisor->usuario->Nome }}{{ $supervisor->empresa ? ' · ' . $supervisor->empresa : '' }}
                            </option>
                        @endforeach
                    </select>
                </label>
                <div class="sm:col-span-2 lg:col-span-4">
                    @if($alunosDisponiveis->isEmpty())
                        <p class="text-sm text-gray-600">Todos os alunos já possuem um estágio ativo ou ainda não há alunos cadastrados.</p>
                    @else
                        <button class="rounded bg-[#0077fc] px-4 py-2 text-sm font-medium text-white hover:bg-[#0056c9]">Inicializar estágio</button>
                    @endif
                </div>
            </form>
        </section>

        <section>
            <h2 class="mb-3 text-lg font-semibold text-gray-900">Estágios sob minha orientação</h2>
            @if($estagios->isEmpty())
                <p class="rounded border border-gray-200 bg-white p-5 text-sm text-gray-600">Você ainda não possui estágios vinculados.</p>
            @else
                <div class="overflow-x-auto rounded border border-gray-200 bg-white">
                    <table class="min-w-full divide-y divide-gray-200 text-left text-sm">
                        <thead class="bg-gray-50 text-xs uppercase text-gray-600">
                            <tr><th class="px-4 py-3">Aluno</th><th class="px-4 py-3">Local</th><th class="px-4 py-3">Supervisor</th><th class="px-4 py-3">Período</th><th class="px-4 py-3">Status</th><th class="px-4 py-3">Ação</th></tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($estagios as $estagio)
                                @php($statusExibido = $estagio->status === 'ativo' && $estagio->data_fim->toDateString() < today()->toDateString() ? 'finalizado' : $estagio->status)
                                <tr>
                                    <td class="px-4 py-3 font-medium text-gray-900">{{ $estagio->aluno->nome }}</td>
                                    <td class="px-4 py-3">{{ $estagio->local }}</td>
                                    <td class="px-4 py-3">{{ $estagio->supervisor?->usuario?->Nome ?? 'Não definido' }}</td>
                                    <td class="px-4 py-3">{{ $estagio->data_inicio->format('d/m/Y') }} a {{ $estagio->data_fim->format('d/m/Y') }}</td>
                                    <td class="px-4 py-3 capitalize">{{ $statusExibido }}</td>
                                    <td class="px-4 py-3"><a class="font-medium text-[#0056c9] hover:underline" href="{{ route('estagios.show', $estagio) }}">Acompanhar</a></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </section>
    @elseif($papel === 'supervisor')
        <section class="grid gap-4 sm:grid-cols-2">
            <div class="border-b border-gray-200 py-4">
                <p class="text-sm text-gray-600">Estágios atribuídos</p>
                <p class="mt-1 text-2xl font-semibold text-gray-900">{{ $estagios->count() }}</p>
            </div>
            <div class="border-b border-gray-200 py-4">
                <p class="text-sm text-gray-600">Presenças oficiais aguardando validação</p>
                <p class="mt-1 text-2xl font-semibold text-gray-900">{{ $frequenciasPendentes }}</p>
            </div>
        </section>
        <section>
            <h2 class="mb-3 text-lg font-semibold text-gray-900">Meus estágios</h2>
            @if($estagios->isEmpty())
                <p class="rounded border border-gray-200 bg-white p-5 text-sm text-gray-600">Nenhum estágio foi atribuído a você.</p>
            @else
                <div class="overflow-x-auto rounded border border-gray-200 bg-white">
                    <table class="min-w-full divide-y divide-gray-200 text-left text-sm">
                        <thead class="bg-gray-50 text-xs uppercase text-gray-600"><tr><th class="px-4 py-3">Aluno</th><th class="px-4 py-3">Local</th><th class="px-4 py-3">Orientador</th><th class="px-4 py-3">Pendentes</th><th class="px-4 py-3">Ação</th></tr></thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($estagios as $estagio)
                                <tr>
                                    <td class="px-4 py-3 font-medium text-gray-900">{{ $estagio->aluno->nome }}</td>
                                    <td class="px-4 py-3">{{ $estagio->local }}</td>
                                    <td class="px-4 py-3">{{ $estagio->orientador->usuario->Nome }}</td>
                                    <td class="px-4 py-3">{{ $estagio->presencas->where('status', 'pendente')->count() }}</td>
                                    <td class="px-4 py-3"><a href="{{ route('estagios.show', $estagio) }}" class="font-medium text-[#0056c9] hover:underline">Validar presenças</a></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </section>
    @else
        @if(! $estagioAtivo)
            <section class="border-l-4 border-blue-600 bg-white p-5">
                <h2 class="font-semibold text-gray-900">Você não possui um estágio ativo.</h2>
                <p class="mt-1 text-sm text-gray-600">Aguarde a inicialização do seu estágio pelo professor orientador.</p>
            </section>
        @else
            @php($resumoAtivo = $metricas[$estagioAtivo->id])
            <section class="border-b border-gray-200 pb-6">
                <div class="flex flex-wrap items-start justify-between gap-4">
                    <div>
                        <p class="text-xs font-semibold uppercase text-green-700">Estágio ativo</p>
                        <h2 class="mt-1 text-xl font-semibold text-gray-900">{{ $estagioAtivo->local }}</h2>
                        <p class="mt-1 text-sm text-gray-600">Orientador: {{ $estagioAtivo->orientador->usuario->Nome }}</p>
                        <p class="mt-1 text-sm text-gray-600">{{ $estagioAtivo->data_inicio->format('d/m/Y') }} a {{ $estagioAtivo->data_fim->format('d/m/Y') }}</p>
                    </div>
                    <a href="{{ route('estagios.show', $estagioAtivo) }}" class="rounded bg-[#0077fc] px-4 py-2 text-sm font-medium text-white hover:bg-[#0056c9]">Acessar estágio</a>
                </div>
                @if($estagioAtivo->jornada)
                    <p class="mt-4 text-sm text-gray-700">{{ $estagioAtivo->jornada->horas_diarias }}h por dia · {{ $estagioAtivo->jornada->cargaSemanal() }}h por semana · {{ $resumoAtivo['aprovadas_formatadas'] }} aprovadas</p>
                @else
                    <p class="mt-4 text-sm text-amber-800">Configure sua jornada para começar a registrar frequências.</p>
                @endif
            </section>
        @endif

        @if($estagios->isNotEmpty())
            <section>
                <h2 class="mb-3 text-lg font-semibold text-gray-900">Histórico de estágios</h2>
                <div class="divide-y divide-gray-200 border-y border-gray-200">
                    @foreach($estagios as $estagio)
                        @php($statusExibido = $estagio->status === 'ativo' && $estagio->data_fim->toDateString() < today()->toDateString() ? 'finalizado' : $estagio->status)
                        <div class="flex flex-wrap items-center justify-between gap-3 py-4">
                            <div>
                                <p class="font-medium text-gray-900">{{ $estagio->local }} <span class="ml-2 text-xs capitalize text-gray-500">{{ $statusExibido }}</span></p>
                                <p class="text-sm text-gray-600">{{ $estagio->data_inicio->format('d/m/Y') }} a {{ $estagio->data_fim->format('d/m/Y') }} · {{ $estagio->orientador->usuario->Nome }}</p>
                            </div>
                            <a href="{{ route('estagios.show', $estagio) }}" class="text-sm font-medium text-[#0056c9] hover:underline">Ver ficha</a>
                        </div>
                    @endforeach
                </div>
            </section>
        @endif
    @endif
</main>
@endsection