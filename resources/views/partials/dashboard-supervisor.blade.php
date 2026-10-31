@if(isset($estagios) && isset($frequenciasPendentes))
<main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">
    <header>
        <h1 class="text-2xl font-semibold text-gray-900">Painel do supervisor</h1>
        <p class="mt-1 text-sm text-gray-600">Valide somente as presenças oficiais dos estágios atribuídos a você.</p>
    </header>
    <section class="grid grid-cols-1 gap-4">
        <div class="border-b border-gray-200 py-4"><p class="text-sm text-gray-600">Estágios atribuídos</p><p class="mt-1 text-2xl font-semibold">{{ $estagios->count() }}</p></div>
        <div class="border-b border-gray-200 py-4"><p class="text-sm text-gray-600">Presenças aguardando validação</p><p class="mt-1 text-2xl font-semibold">{{ $frequenciasPendentes }}</p></div>
    </section>
    <section>
        <h2 class="mb-3 text-lg font-semibold text-gray-900">Estágios sob minha supervisão</h2>
        @if($estagios->isEmpty())
            <p class="border-y border-gray-200 py-5 text-sm text-gray-600">Nenhum estágio foi atribuído a você.</p>
        @else
            <div class="overflow-x-auto border-y border-gray-200">
                <table class="min-w-full divide-y divide-gray-200 text-left text-sm">
                    <thead class="text-xs uppercase text-gray-600"><tr><th class="py-3 pr-4">Aluno</th><th class="py-3 pr-4">Local</th><th class="py-3 pr-4">Orientador</th><th class="py-3 pr-4">Pendentes</th><th class="py-3">Ação</th></tr></thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($estagios as $estagio)
                            <tr>
                                <td class="py-3 pr-4 font-medium text-gray-900">{{ $estagio->aluno->nome }}</td>
                                <td class="py-3 pr-4">{{ $estagio->local }}</td>
                                <td class="py-3 pr-4">{{ $estagio->orientador->usuario->Nome }}</td>
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
@else
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="mb-6">
        <div class="flex items-start gap-3">
            <div class="p-2 bg-green-100 rounded-lg mt-0.5">
                <span class="material-symbols-outlined text-green-700 text-[24px]">school</span>
            </div>
            <div>
                <h1 class="text-3xl font-bold text-gray-900 mb-1">Supervisor de Estágio Interno</h1>
                <p class="text-gray-500">Valide as horas dos estagiários internos do CEFET</p>
            </div>
        </div>
    </div>

    {{-- Banner de responsabilidade --}}
    <div class="flex items-start gap-3 bg-green-50 border border-green-200 rounded-xl px-4 py-3 mb-8">
        <span class="material-symbols-outlined text-green-600 text-[20px] mt-0.5">verified</span>
        <p class="text-sm text-green-800">
            Você é o responsável pela <strong>validação de horas</strong> dos estagiários internos
            (Monitoria e Projetos de Extensão).
        </p>
    </div>

    {{-- Stats --}}
    <div class="grid grid-cols-1 gap-6 mb-8">
        <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-200">
            <div class="inline-flex p-3 rounded-lg bg-blue-50 text-blue-600 mb-4"><span class="material-symbols-outlined">groups</span></div>
            <h3 class="text-sm text-gray-600 mb-1">Estagiários Ativos</h3>
            <p class="text-2xl font-bold text-gray-900">3</p>
        </div>
        <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-200">
            <div class="inline-flex p-3 rounded-lg bg-orange-50 text-orange-600 mb-4"><span class="material-symbols-outlined">warning</span></div>
            <h3 class="text-sm text-gray-600 mb-1">Presenças Pendentes</h3>
            <p class="text-2xl font-bold text-gray-900">2</p>
        </div>
        <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-200">
            <div class="inline-flex p-3 rounded-lg bg-green-50 text-green-600 mb-4"><span class="material-symbols-outlined">check_circle</span></div>
            <h3 class="text-sm text-gray-600 mb-1">Validações Este Mês</h3>
            <p class="text-2xl font-bold text-gray-900">48</p>
        </div>
        <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-200">
            <div class="inline-flex p-3 rounded-lg bg-purple-50 text-purple-600 mb-4"><span class="material-symbols-outlined">schedule</span></div>
            <h3 class="text-sm text-gray-600 mb-1">Total de Horas</h3>
            <p class="text-2xl font-bold text-gray-900">356h</p>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-6">
        {{-- Validação de presenças --}}
        <div class="lg:col-span-2 bg-white rounded-xl p-6 shadow-sm border border-gray-200">
            <div class="flex items-center justify-between mb-5">
                <h2 class="text-xl font-semibold">Presenças Pendentes de Validação</h2>
                <span class="px-2.5 py-1 bg-orange-100 text-orange-700 text-xs font-semibold rounded-full">2 pendentes</span>
            </div>
            <div class="space-y-3">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                    <div>
                        <div class="flex items-center gap-2 mb-0.5">
                            <p class="font-medium text-gray-900">João Silva</p>
                            <span class="text-xs bg-green-100 text-green-700 px-2 py-0.5 rounded-full">Interno</span>
                        </div>
                        <p class="text-sm text-gray-500">02/06/2026 • 09:00 – 17:00</p>
                        <p class="text-xs text-gray-400 mt-0.5">8h registradas</p>
                    </div>
                    <div class="flex gap-2 shrink-0">
                        <button class="px-4 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600 transition-colors text-sm font-medium">Validar</button>
                        <button class="px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 transition-colors text-sm font-medium">Rejeitar</button>
                    </div>
                </div>
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                    <div>
                        <div class="flex items-center gap-2 mb-0.5">
                            <p class="font-medium text-gray-900">Maria Santos</p>
                            <span class="text-xs bg-green-100 text-green-700 px-2 py-0.5 rounded-full">Interno</span>
                        </div>
                        <p class="text-sm text-gray-500">03/06/2026 • 14:00 – 18:00</p>
                        <p class="text-xs text-gray-400 mt-0.5">4h registradas</p>
                    </div>
                    <div class="flex gap-2 shrink-0">
                        <button class="px-4 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600 transition-colors text-sm font-medium">Validar</button>
                        <button class="px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 transition-colors text-sm font-medium">Rejeitar</button>
                    </div>
                </div>
            </div>
        </div>

        {{-- Estagiários --}}
        <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-200">
            <h2 class="text-xl font-semibold mb-4">Meus Estagiários</h2>
            <div class="space-y-3">
                <div class="p-4 border border-gray-200 rounded-lg">
                    <p class="font-medium text-gray-900">João Silva</p>
                    <p class="text-xs text-gray-400 mb-0.5">Mat: 20231234</p>
                    <p class="text-xs text-[#0077fc] font-medium mb-3">Monitor de Cálculo I</p>
                    <div class="space-y-1.5">
                        <div class="flex justify-between text-sm"><span class="text-gray-500">Validadas</span><span class="font-semibold text-green-600">120h</span></div>
                        <div class="flex justify-between text-sm"><span class="text-gray-500">Pendentes</span><span class="font-semibold text-orange-500">8h</span></div>
                    </div>
                    <button class="mt-3 w-full py-1.5 bg-[#0077fc] text-white rounded-lg hover:bg-[#0056c9] transition-colors text-sm">Ver Detalhes</button>
                </div>
                <div class="p-4 border border-gray-200 rounded-lg">
                    <p class="font-medium text-gray-900">Maria Santos</p>
                    <p class="text-xs text-gray-400 mb-0.5">Mat: 20231235</p>
                    <p class="text-xs text-[#0077fc] font-medium mb-3">Proj. Extensão — Inclusão Digital</p>
                    <div class="space-y-1.5">
                        <div class="flex justify-between text-sm"><span class="text-gray-500">Validadas</span><span class="font-semibold text-green-600">96h</span></div>
                        <div class="flex justify-between text-sm"><span class="text-gray-500">Pendentes</span><span class="font-semibold text-orange-500">4h</span></div>
                    </div>
                    <button class="mt-3 w-full py-1.5 bg-[#0077fc] text-white rounded-lg hover:bg-[#0056c9] transition-colors text-sm">Ver Detalhes</button>
                </div>
                <div class="p-4 border border-gray-200 rounded-lg">
                    <p class="font-medium text-gray-900">Pedro Costa</p>
                    <p class="text-xs text-gray-400 mb-0.5">Mat: 20231236</p>
                    <p class="text-xs text-[#0077fc] font-medium mb-3">Monitor de Cálculo I</p>
                    <div class="space-y-1.5">
                        <div class="flex justify-between text-sm"><span class="text-gray-500">Validadas</span><span class="font-semibold text-green-600">140h</span></div>
                    </div>
                    <button class="mt-3 w-full py-1.5 bg-[#0077fc] text-white rounded-lg hover:bg-[#0056c9] transition-colors text-sm">Ver Detalhes</button>
                </div>
            </div>
            <button class="mt-4 w-full flex items-center justify-center gap-2 py-2.5 border-2 border-[#0077fc] text-[#0077fc] rounded-lg hover:bg-blue-50 transition-colors font-medium text-sm">
                <span class="material-symbols-outlined text-[18px]">description</span>
                Gerar Fichas
            </button>
        </div>
    </div>
</div>
@endif
