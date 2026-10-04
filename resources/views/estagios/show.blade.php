@extends('layouts.app')

@section('content')
<main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">
    <a href="{{ route('estagios.index') }}" class="text-sm font-medium text-[#0056c9] hover:underline">← Voltar aos estágios</a>

    @if(session('success'))
        <div role="status" class="rounded border border-green-200 bg-green-50 p-3 text-sm text-green-800">{{ session('success') }}</div>
    @endif
    @if($errors->any())
        <div role="alert" class="rounded border border-red-200 bg-red-50 p-3 text-sm text-red-800">
            <ul class="list-disc space-y-1 pl-5">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
        </div>
    @endif

    <section class="border-b border-gray-200 pb-6">
        <div class="flex flex-wrap items-start justify-between gap-4">
            <div>
                <p class="text-xs font-semibold uppercase text-gray-500">Estágio {{ $estagio->status }}</p>
                <h1 class="mt-1 text-2xl font-semibold text-gray-900">{{ $estagio->local }}</h1>
                <p class="mt-2 text-sm text-gray-700">Aluno: {{ $estagio->aluno->nome }} · {{ $estagio->aluno->matricula }}</p>
                <p class="mt-1 text-sm text-gray-700">Orientador: {{ $estagio->orientador->usuario->Nome }}</p>
                <p class="mt-1 text-sm text-gray-700">Supervisor: {{ $estagio->supervisor?->usuario?->Nome ?? 'Não definido' }}</p>
                <p class="mt-1 text-sm text-gray-600">Período: {{ $estagio->data_inicio->format('d/m/Y') }} a {{ $estagio->data_fim->format('d/m/Y') }}</p>
            </div>
            @if($papel === 'orientador' && $estagio->status === 'ativo')
                <form method="POST" action="{{ route('estagios.finalizar', $estagio) }}" onsubmit="return confirm('Finalizar este estágio? O histórico será mantido.');">
                    @csrf
                    <button class="rounded border border-gray-300 px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">Finalizar estágio</button>
                </form>
            @endif
        </div>
    </section>

    @if($papel === 'orientador' && $estagio->status === 'ativo')
        <section class="border-b border-gray-200 pb-5">
            <h2 class="text-lg font-semibold text-gray-900">Supervisor responsável</h2>
            @if($estagio->supervisor)
                <p class="mt-2 text-sm text-gray-700">{{ $estagio->supervisor->usuario->Nome }}{{ $estagio->supervisor->empresa ? ' · ' . $estagio->supervisor->empresa : '' }}</p>
                <p class="mt-1 text-xs text-gray-500">O supervisor valida as presenças oficiais. Registros pessoais do aluno não são enviados a ele.</p>
            @else
                <p class="mt-1 text-sm text-gray-600">Vincule um supervisor para habilitar registros oficiais.</p>
                <form method="POST" action="{{ route('estagios.supervisor.store', $estagio) }}" class="mt-3 flex flex-col gap-3 sm:flex-row sm:items-end">
                    @csrf
                    <label class="block w-full max-w-md text-sm font-medium text-gray-700">
                        Supervisor
                        <select name="supervisor_id" required class="mt-1 block w-full rounded border-gray-300">
                            <option value="">Selecione o supervisor</option>
                            @foreach($supervisoresDisponiveis as $supervisorDisponivel)
                                <option value="{{ $supervisorDisponivel->supervisor_ID }}">{{ $supervisorDisponivel->usuario->Nome }}{{ $supervisorDisponivel->empresa ? ' · ' . $supervisorDisponivel->empresa : '' }}</option>
                            @endforeach
                        </select>
                    </label>
                    <button class="rounded bg-[#0077fc] px-4 py-2 text-sm font-medium text-white hover:bg-[#0056c9]">Vincular supervisor</button>
                </form>
            @endif
        </section>
    @endif

    <section class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4" aria-label="Resumo de frequência">
        <div class="border-b border-gray-200 py-3"><p class="text-sm text-gray-600">Carga mínima</p><p class="mt-1 text-xl font-semibold">{{ $metricas['meta_horas'] }}h</p></div>
        <div class="border-b border-gray-200 py-3"><p class="text-sm text-gray-600">Horas aprovadas</p><p class="mt-1 text-xl font-semibold">{{ $metricas['aprovadas_formatadas'] }}</p></div>
        <div class="border-b border-gray-200 py-3"><p class="text-sm text-gray-600">Horas pendentes</p><p class="mt-1 text-xl font-semibold">{{ $metricas['pendentes_formatadas'] }}</p></div>
        <div class="border-b border-gray-200 py-3"><p class="text-sm text-gray-600">Horas restantes</p><p class="mt-1 text-xl font-semibold">{{ $metricas['restantes_formatadas'] }}</p></div>
    </section>
    <div>
        <div class="mb-1 flex justify-between text-sm"><span class="text-gray-600">Progresso das horas mínimas</span><span class="font-medium">{{ $metricas['percentual'] }}%</span></div>
        <progress class="h-2 w-full accent-blue-600" value="{{ $metricas['percentual'] }}" max="100" aria-label="Progresso das horas mínimas"></progress>
    </div>

    <section class="border-b border-gray-200 pb-5">
        <h2 class="text-lg font-semibold text-gray-900">Comparativo semanal</h2>
        <p class="mt-1 text-xs text-gray-500">{{ $metricas['semana_inicio'] }} a {{ $metricas['semana_fim'] }}</p>
        <div class="mt-3 grid gap-4 sm:grid-cols-3">
            <div><p class="text-sm text-gray-600">Carga prevista</p><p class="font-semibold">{{ $metricas['previstas_semana_formatadas'] }}</p></div>
            <div><p class="text-sm text-gray-600">Carga aprovada</p><p class="font-semibold">{{ $metricas['realizadas_semana_formatadas'] }}</p></div>
            <div><p class="text-sm text-gray-600">Diferença</p><p class="font-semibold">{{ $metricas['diferenca_semana_formatada'] }}</p></div>
        </div>
    </section>

    <section class="border-y border-gray-200 py-6">
        <h2 class="text-lg font-semibold text-gray-900">Jornada do estágio</h2>
        @if($estagio->jornada)
            @php($diasSelecionados = collect(['segunda' => 'Segunda', 'terca' => 'Terça', 'quarta' => 'Quarta', 'quinta' => 'Quinta', 'sexta' => 'Sexta', 'sabado' => 'Sábado', 'domingo' => 'Domingo'])->filter(fn ($nome, $dia) => $estagio->jornada->{$dia})->values())
            <p class="mt-2 text-sm text-gray-700">{{ $estagio->jornada->horas_diarias }}h por dia · {{ $estagio->jornada->cargaSemanal() }}h por semana</p>
            <p class="mt-1 text-sm text-gray-600">{{ $diasSelecionados->implode(', ') }}</p>
        @elseif($papel === 'aluno' && $estagio->status === 'ativo')
            <form method="POST" action="{{ route('estagios.jornada.store', $estagio) }}" class="mt-4 space-y-4">
                @csrf
                <label class="block max-w-xs text-sm font-medium text-gray-700">
                    Horas por dia
                    <input type="number" name="horas_diarias" min="0.25" max="24" step="0.25" value="{{ old('horas_diarias', 6) }}" required class="mt-1 block w-full rounded border-gray-300" />
                </label>
                <fieldset>
                    <legend class="mb-2 text-sm font-medium text-gray-700">Dias da semana</legend>
                    <div class="flex flex-wrap gap-x-5 gap-y-2">
                        @foreach(['segunda' => 'Segunda', 'terca' => 'Terça', 'quarta' => 'Quarta', 'quinta' => 'Quinta', 'sexta' => 'Sexta', 'sabado' => 'Sábado', 'domingo' => 'Domingo'] as $campo => $dia)
                            <label class="inline-flex items-center gap-2 text-sm text-gray-700"><input type="checkbox" name="{{ $campo }}" value="1" @checked(old('horas_diarias') === null ? in_array($campo, ['segunda', 'terca', 'quarta', 'quinta', 'sexta'], true) : old($campo, false)) class="rounded border-gray-300 text-[#0077fc]" />{{ $dia }}</label>
                        @endforeach
                    </div>
                </fieldset>
                <p id="carga-semanal" class="text-sm text-gray-700" aria-live="polite">Carga semanal: 30h</p>
                <button class="rounded bg-[#0077fc] px-4 py-2 text-sm font-medium text-white hover:bg-[#0056c9]">Salvar jornada</button>
            </form>
        @else
            <p class="mt-2 text-sm text-gray-600">A jornada ainda não foi configurada pelo aluno.</p>
        @endif
    </section>

    @if($papel === 'aluno' && $estagio->jornada && $estagio->status === 'ativo')
        <section class="grid gap-6 lg:grid-cols-3" aria-labelledby="titulo-calendario-presencas">
            <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm sm:p-6 lg:col-span-2">
                <div class="mb-5 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h2 id="titulo-calendario-presencas" class="text-xl font-semibold text-gray-900">Calendário de presenças</h2>
                        <p class="mt-1 text-sm text-gray-600">Consulte os dias da jornada e registre períodos encerrados.</p>
                    </div>
                    <button id="abrir-modal-presenca" type="button" class="inline-flex items-center justify-center gap-2 rounded-lg bg-[#0077fc] px-4 py-2.5 text-sm font-medium text-white hover:bg-[#0056c9]">
                        <span class="material-symbols-outlined text-lg">add</span> Registrar presença
                    </button>
                </div>
                <div class="mb-4 flex items-center justify-between">
                    <p id="mes-calendario" class="min-w-40 text-base font-semibold text-gray-900" aria-live="polite"></p>
                    <div class="flex items-center gap-1">
                        <button id="mes-anterior" type="button" class="rounded-lg p-2 text-gray-600 hover:bg-gray-100 disabled:cursor-not-allowed" aria-label="Mês anterior" title="Mês anterior"><span class="material-symbols-outlined block">chevron_left</span></button>
                        <button id="proximo-mes" type="button" class="rounded-lg p-2 text-gray-600 hover:bg-gray-100 disabled:cursor-not-allowed" aria-label="Próximo mês" title="Próximo mês"><span class="material-symbols-outlined block">chevron_right</span></button>
                    </div>
                </div>
                <div class="grid grid-cols-7 gap-1 text-center text-xs font-semibold text-gray-500" aria-hidden="true">
                    <span class="py-2">Dom</span><span class="py-2">Seg</span><span class="py-2">Ter</span><span class="py-2">Qua</span><span class="py-2">Qui</span><span class="py-2">Sex</span><span class="py-2">Sáb</span>
                </div>
                <div id="calendario-presencas" class="grid grid-cols-7 gap-1.5 sm:gap-2" role="grid" aria-label="Calendário de presenças"></div>
                <div class="mt-5 flex flex-wrap gap-x-5 gap-y-2 border-t border-gray-100 pt-4 text-xs text-gray-600" aria-label="Legenda do calendário">
                    <span class="inline-flex items-center gap-2"><span class="h-3.5 w-3.5 rounded border border-amber-300 bg-amber-200"></span>Pendente preencher</span>
                    <span class="inline-flex items-center gap-2"><span class="h-3.5 w-3.5 rounded border border-blue-300 bg-blue-200"></span>Registro pessoal</span>
                    <span class="inline-flex items-center gap-2"><span class="h-3.5 w-3.5 rounded border border-green-300 bg-green-200"></span>Presença registrada</span>
                    <span class="inline-flex items-center gap-2"><span class="h-3.5 w-3.5 rounded border border-red-300 bg-red-200"></span>Falta ou oficial recusada</span>
                </div>
                <script id="dados-calendario-presencas" type="application/json">@json($calendarData)</script>
            </div>

            <aside class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm sm:p-6">
                <h2 class="text-lg font-semibold text-gray-900">Histórico recente</h2>
                <div class="mt-4 space-y-3">
                    @forelse($estagio->presencas->take(5) as $presencaRecente)
                        @php($corRegistro = $presencaRecente->modo === 'pessoal' ? 'text-blue-800' : ($presencaRecente->status === 'aprovada' ? 'text-green-700' : ($presencaRecente->status === 'recusada' ? 'text-red-700' : 'text-amber-700')))
                        <div class="border-b border-gray-100 pb-3 last:border-0">
                            <div class="flex items-start justify-between gap-2">
                                <p class="text-sm font-medium text-gray-900">{{ $presencaRecente->data->format('d/m/Y') }}</p>
                                <span class="text-xs font-medium {{ $corRegistro }}">{{ $presencaRecente->modo === 'pessoal' ? 'Pessoal' : ($presencaRecente->status === 'pendente' ? 'Aguardando supervisor' : ucfirst($presencaRecente->status)) }}</span>
                            </div>
                            <p class="mt-1 text-xs text-gray-600">{{ substr($presencaRecente->entrada, 0, 5) }} – {{ substr($presencaRecente->saida, 0, 5) }} · {{ $estagioService->formatarMinutos($presencaRecente->minutos) }}</p>
                        </div>
                    @empty
                        <p class="border-y border-gray-100 py-4 text-sm text-gray-600">Nenhuma presença registrada ainda.</p>
                    @endforelse
                </div>
                <div class="mt-5 space-y-2 border-t border-gray-200 pt-4">
                    <div class="flex items-center justify-between rounded-lg bg-green-50 px-3 py-2.5">
                        <span class="text-sm font-medium text-green-900">Aprovadas</span>
                        <span class="text-sm text-green-800">{{ $estagio->presencas->where('status', 'aprovada')->count() }}</span>
                    </div>
                    <div class="flex items-center justify-between rounded-lg bg-amber-50 px-3 py-2.5">
                        <span class="text-sm font-medium text-amber-900">Aguardando aprovação</span>
                        <span class="text-sm text-amber-800">{{ $estagio->presencas->where('status', 'pendente')->count() }}</span>
                    </div>
                    <div class="flex items-center justify-between rounded-lg bg-blue-50 px-3 py-2.5">
                        <span class="text-sm font-medium text-blue-900">Registros pessoais</span>
                        <span class="text-sm text-blue-800">{{ $estagio->presencas->where('modo', 'pessoal')->count() }}</span>
                    </div>
                </div>
            </aside>
        </section>

        <div id="modal-presenca" data-aberto-por-erro="{{ $errors->hasAny(['modo', 'data', 'entrada', 'saida', 'observacao']) ? 'true' : 'false' }}" class="fixed inset-0 z-50 hidden items-center justify-center bg-gray-900/50 p-4" role="presentation">
            <section class="w-full max-w-lg rounded-xl bg-white p-5 shadow-xl sm:p-6" role="dialog" aria-modal="true" aria-labelledby="titulo-modal-presenca">
                <div class="mb-5 flex items-start justify-between gap-4">
                    <div>
                        <h2 id="titulo-modal-presenca" class="text-xl font-semibold text-gray-900">Registrar presença</h2>
                        <p class="mt-1 text-sm text-gray-600">{{ $estagio->local }}</p>
                    </div>
                    <button id="fechar-modal-presenca" type="button" class="rounded-lg p-2 text-gray-500 hover:bg-gray-100" aria-label="Fechar"><span class="material-symbols-outlined block">close</span></button>
                </div>
                <p class="mb-5 rounded-lg border border-amber-200 bg-amber-50 px-3 py-2.5 text-xs text-amber-900">Registre somente períodos já encerrados. Hoje e datas futuras não são permitidos.</p>
                <form id="form-presenca" method="POST" action="{{ route('estagios.presencas.store', $estagio) }}" class="space-y-4">
                    @csrf
                    <fieldset>
                        <legend class="mb-2 text-sm font-medium text-gray-700">Tipo de registro</legend>
                        <div class="grid gap-3 sm:grid-cols-2">
                            <label class="flex cursor-pointer items-start gap-2 rounded-lg border border-blue-200 bg-blue-50 p-3 text-sm text-blue-950">
                                <input type="radio" name="modo" value="pessoal" @checked(old('modo', $estagio->supervisor_id ? 'oficial' : 'pessoal') === 'pessoal') required class="mt-0.5 border-blue-300 text-blue-700" />
                                <span><strong class="block">Não oficial</strong><span class="text-xs text-blue-800">Somente para seu controle. Não é enviado ao supervisor.</span></span>
                            </label>
                            <label class="flex items-start gap-2 rounded-lg border border-gray-200 p-3 text-sm {{ $estagio->supervisor_id ? 'cursor-pointer text-gray-900' : 'bg-gray-50 text-gray-500' }}">
                                <input type="radio" name="modo" value="oficial" @checked(old('modo', $estagio->supervisor_id ? 'oficial' : 'pessoal') === 'oficial') @disabled(! $estagio->supervisor_id) required class="mt-0.5 border-gray-300 text-[#0077fc]" />
                                <span><strong class="block">Oficial</strong><span class="text-xs text-gray-600">Enviado ao supervisor para validar ou recusar.</span>
                                    @unless($estagio->supervisor_id)<span class="mt-1 block text-xs text-amber-800">O orientador precisa atribuir um supervisor ao estágio.</span>@endunless
                                </span>
                            </label>
                        </div>
                    </fieldset>
                    <label class="block text-sm font-medium text-gray-700">Data do período<input id="data-presenca" type="date" name="data" min="{{ $estagio->data_inicio->toDateString() }}" max="{{ min($estagio->data_fim->toDateString(), today()->subDay()->toDateString()) }}" value="{{ old('data') }}" required class="mt-1 block w-full rounded-lg border-gray-300" /><span class="mt-1 block text-xs font-normal text-gray-500">Escolha um dia anterior a hoje que faça parte da jornada.</span></label>
                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <label class="block text-sm font-medium text-gray-700">Hora de entrada<input type="time" name="entrada" value="{{ old('entrada') }}" required class="mt-1 block w-full rounded-lg border-gray-300" /></label>
                        <label class="block text-sm font-medium text-gray-700">Hora de saída<input type="time" name="saida" value="{{ old('saida') }}" required class="mt-1 block w-full rounded-lg border-gray-300" /></label>
                    </div>
                    <label class="block text-sm font-medium text-gray-700">Observação (opcional)<textarea name="observacao" maxlength="500" rows="3" class="mt-1 block w-full rounded-lg border-gray-300">{{ old('observacao') }}</textarea></label>
                    <div class="flex flex-col-reverse gap-3 pt-2 sm:flex-row sm:justify-end">
                        <button id="cancelar-modal-presenca" type="button" class="rounded-lg border border-gray-300 px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50">Cancelar</button>
                        <button type="submit" class="rounded-lg bg-[#0077fc] px-4 py-2.5 text-sm font-medium text-white hover:bg-[#0056c9]">Enviar para aprovação</button>
                    </div>
                </form>
            </section>
        </div>
    @endif

    <section>
        <h2 class="mb-3 text-lg font-semibold text-gray-900">Frequências</h2>
        @if($estagio->presencas->isEmpty())
            <p class="border-y border-gray-200 py-4 text-sm text-gray-600">Nenhuma frequência registrada.</p>
        @else
            <div class="overflow-x-auto border-y border-gray-200">
                <table class="min-w-full divide-y divide-gray-200 text-left text-sm">
                    <thead class="text-xs uppercase text-gray-600"><tr><th class="py-3 pr-4">Data</th><th class="py-3 pr-4">Entrada</th><th class="py-3 pr-4">Saída</th><th class="py-3 pr-4">Horas</th><th class="py-3 pr-4">Status</th><th class="py-3">Observação / decisão</th></tr></thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($estagio->presencas as $presenca)
                            <tr class="align-top">
                                <td class="py-3 pr-4">{{ $presenca->data->format('d/m/Y') }}</td>
                                <td class="py-3 pr-4">{{ substr($presenca->entrada, 0, 5) }}</td>
                                <td class="py-3 pr-4">{{ substr($presenca->saida, 0, 5) }}</td>
                                <td class="py-3 pr-4">{{ $estagioService->formatarMinutos($presenca->minutos) }}</td>
                                <td class="py-3 pr-4">{{ $presenca->modo === 'pessoal' ? 'Pessoal' : ($presenca->status === 'pendente' ? 'Aguardando supervisor' : ucfirst($presenca->status)) }}</td>
                                <td class="min-w-56 py-3">
                                    @if($presenca->observacao)<p>{{ $presenca->observacao }}</p>@endif
                                    @if($presenca->parecer_supervisor)<p class="mt-1 text-gray-600">Supervisor: {{ $presenca->parecer_supervisor }}</p>@endif
                                    @if($papel === 'supervisor' && $presenca->modo === 'oficial' && $presenca->status === 'pendente')
                                        <div class="mt-2 flex flex-wrap gap-2">
                                            <form method="POST" action="{{ route('presencas.aprovar', $presenca) }}">@csrf<button class="rounded bg-green-700 px-3 py-1.5 text-xs font-medium text-white">Aprovar</button></form>
                                            <form method="POST" action="{{ route('presencas.recusar', $presenca) }}" class="flex flex-wrap gap-2">
                                                @csrf
                                                <input name="parecer_supervisor" maxlength="500" required placeholder="Motivo da recusa" class="w-40 rounded border-gray-300 text-xs" />
                                                <button class="rounded border border-red-300 px-3 py-1.5 text-xs font-medium text-red-700">Recusar</button>
                                            </form>
                                        </div>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </section>
</main>

@if($papel === 'aluno' && ! $estagio->jornada && $estagio->status === 'ativo')
<script>
    const horasDiarias = document.querySelector('input[name="horas_diarias"]');
    const checkboxesDias = document.querySelectorAll('input[type="checkbox"][name]');
    const cargaSemanal = document.getElementById('carga-semanal');
    const atualizarCarga = () => {
        const diasSelecionados = [...checkboxesDias].filter((checkbox) => checkbox.checked).length;
        cargaSemanal.textContent = `Carga semanal: ${(Number(horasDiarias.value || 0) * diasSelecionados).toFixed(2).replace(/\.00$/, '')}h`;
    };
    horasDiarias.addEventListener('input', atualizarCarga);
    checkboxesDias.forEach((checkbox) => checkbox.addEventListener('change', atualizarCarga));
</script>
@endif
@if($papel === 'aluno' && $estagio->jornada && $estagio->status === 'ativo')
    <script src="/js/calendario-presencas.js?v=1" defer></script>
@endif
@endsection