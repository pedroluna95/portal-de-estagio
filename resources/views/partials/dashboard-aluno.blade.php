<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    {{-- Saudação --}}
    <div class="flex items-center gap-3 mb-8">
        <div class="w-12 h-12 rounded-full fig-gradient flex items-center justify-center text-white">
            <span class="material-symbols-outlined">person</span>
        </div>
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Bem-vindo de volta, {{ session('user_name', 'Aluno') }}!</h1>
        </div>
    </div>

    {{-- Cards de resumo --}}
    <div class="grid grid-cols-1 gap-6 mb-8">
        <div class="rounded-xl border border-gray-200 bg-white p-6 text-gray-900 shadow-sm">
            <div class="flex items-center justify-between mb-3">
                <span class="text-sm text-gray-600">Horas contabilizadas</span>
                <span class="material-symbols-outlined text-purple-600">schedule</span>
            </div>
            @if($estagioAtivo && $metricas)
                <p class="text-3xl font-bold text-gray-900">{{ $metricas['horas_progresso_formatadas'] }}<span class="text-lg font-medium text-gray-500">/{{ $metricas['meta_horas'] }}h</span></p>
            @else
                <p class="text-lg font-semibold text-gray-700">Sem estágio ativo</p>
            @endif
            @if($estagioAtivo && $metricas)
                <div class="mt-3 h-5 w-full overflow-hidden rounded-full bg-gray-200" role="progressbar" aria-label="Progresso das horas mínimas" aria-valuemin="0" aria-valuemax="100" aria-valuenow="{{ $metricas['percentual'] }}">
                    <div class="flex h-full overflow-hidden rounded-full" style="width: {{ $metricas['percentual'] }}%; min-width: {{ $metricas['percentual'] > 0 ? '12px' : '0' }}; max-width: 100%">
                        @if($metricas['horas_progresso_minutos'] > 0)
                            <div class="h-full shrink-0 bg-green-500" style="width: {{ $metricas['aprovadas_minutos'] / $metricas['horas_progresso_minutos'] * 100 }}%"></div>
                            <div class="h-full shrink-0 bg-blue-500" style="width: {{ $metricas['pessoais_contabilizadas_minutos'] / $metricas['horas_progresso_minutos'] * 100 }}%"></div>
                        @endif
                    </div>
                </div>
                <div class="mt-2 flex flex-wrap gap-x-4 gap-y-1 text-xs text-gray-700">
                    <span class="font-medium text-green-700">{{ $metricas['aprovadas_formatadas'] }} aprovadas</span>
                    <span class="font-medium text-blue-700">{{ $metricas['pessoais_contabilizadas_formatadas'] }} pessoais contabilizadas</span>
                </div>
                <p class="mt-1 text-xs font-medium text-gray-700">{{ $metricas['percentual'] }}% concluído</p>
                <p class="mt-2 text-xs text-gray-600">O registro oficial aprovado substitui o pessoal; se for recusado, o dia não conta.</p>
            @endif
        </div>

        <div class="rounded-xl border border-gray-200 bg-white p-6 text-gray-900 shadow-sm">
            <div class="flex items-center justify-between mb-3">
                <span class="text-sm text-gray-600">Frequências pendentes</span>
                <span class="material-symbols-outlined text-orange-500">pending_actions</span>
            </div>
            <p class="text-3xl font-bold text-gray-900">{{ $metricas['pendentes_formatadas'] ?? '0h' }}</p>
            <p class="mt-2 text-xs text-gray-500">aguardando aprovação do supervisor</p>
        </div>
    </div>

    {{-- Estágio Atual--}}
    <div class="grid grid-cols-1 gap-6">
        <div class="rounded-xl border border-gray-200 bg-white p-6 text-gray-900 shadow-sm">
            <h2 class="mb-4 text-xl font-semibold text-gray-900">Estágio Atual</h2>
            <div class="flex items-start gap-4">
                <div class="w-12 h-12 rounded-xl bg-blue-50 flex items-center justify-center shrink-0">
                    <span class="material-symbols-outlined text-[#0077fc]">business</span>
                </div>
                <div class="flex-1">
                    @if($estagioAtivo && $metricas)
                        <div class="flex items-start justify-between gap-2">
                            <div>
                                <h3 class="font-semibold text-gray-900">{{ $estagioAtivo->local }}</h3>
                                <p class="text-sm text-gray-600">Orientador: {{ $estagioAtivo->orientador->usuario->Nome }}</p>
                            </div>
                            <span class="px-2.5 py-1 bg-green-100 text-green-700 text-xs font-semibold rounded-full">Ativo</span>
                        </div>
                        <p class="mt-3 text-xs text-gray-500">
                            <span class="material-symbols-outlined text-[14px] align-middle">calendar_today</span>
                            {{ $estagioAtivo->data_inicio->format('d/m/Y') }} – {{ $estagioAtivo->data_fim->format('d/m/Y') }}
                        </p>
                        <div class="mt-3 h-5 w-full overflow-hidden rounded-full bg-gray-200" role="progressbar" aria-label="Progresso das horas mínimas" aria-valuemin="0" aria-valuemax="100" aria-valuenow="{{ $metricas['percentual'] }}">
                            <div class="flex h-full overflow-hidden rounded-full" style="width: {{ $metricas['percentual'] }}%; min-width: {{ $metricas['percentual'] > 0 ? '12px' : '0' }}; max-width: 100%">
                                @if($metricas['horas_progresso_minutos'] > 0)
                                    <div class="h-full shrink-0 bg-green-500" style="width: {{ $metricas['aprovadas_minutos'] / $metricas['horas_progresso_minutos'] * 100 }}%"></div>
                                    <div class="h-full shrink-0 bg-blue-500" style="width: {{ $metricas['pessoais_contabilizadas_minutos'] / $metricas['horas_progresso_minutos'] * 100 }}%"></div>
                                @endif
                            </div>
                        </div>
                        <p class="mt-1.5 text-xs text-gray-500">{{ $metricas['horas_progresso_formatadas'] }} de {{ $metricas['meta_horas'] }}h mínimas · inclui {{ $metricas['pessoais_contabilizadas_formatadas'] }} em registros pessoais válidos</p>
                        <a href="{{ route('estagios.show', $estagioAtivo) }}" class="mt-3 inline-flex text-sm font-medium text-[#0056c9] hover:underline">Acessar estágio</a>
                    @else
                        <p class="text-sm text-gray-600">Você ainda não possui um estágio ativo. Aguarde a inicialização pelo orientador.</p>
                    @endif
                </div>
            </div>
        </div>

        
    </div>
</div>
