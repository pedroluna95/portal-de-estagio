<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    {{-- Saudação --}}
    <div class="flex items-center gap-3 mb-8">
        <div class="w-12 h-12 rounded-full fig-gradient flex items-center justify-center text-white">
            <span class="material-symbols-outlined">person</span>
        </div>
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Bem-vindo de volta, {{ session('user_name', 'Aluno') }}!</h1>
            <p class="text-gray-500 text-sm">Aqui está o resumo do seu estágio.</p>
        </div>
    </div>

    {{-- Cards de resumo --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-6 mb-8">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-3">
                <span class="text-sm text-gray-600">Horas de Estágio</span>
                <span class="material-symbols-outlined text-purple-600">schedule</span>
            </div>
            @if($estagioAtivo && $metricas)
                <p class="text-3xl font-bold text-gray-900">{{ $metricas['aprovadas_formatadas'] }}<span class="text-lg text-gray-400 font-medium">/{{ $metricas['meta_horas'] }}h</span></p>
            @else
                <p class="text-lg font-semibold text-gray-700">Sem estágio ativo</p>
            @endif
            <progress class="mt-3 h-2 w-full accent-purple-500" value="{{ $metricas['percentual'] ?? 0 }}" max="100" aria-label="Progresso das horas mínimas"></progress>
            <p class="text-xs text-gray-500 mt-2">{{ $metricas['percentual'] ?? 0 }}% concluído</p>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-3">
                <span class="text-sm text-gray-600">Frequências pendentes</span>
                <span class="material-symbols-outlined text-orange-500">pending_actions</span>
            </div>
            <p class="text-3xl font-bold text-gray-900">{{ $metricas['pendentes_formatadas'] ?? '0h' }}</p>
            <p class="text-xs text-gray-500 mt-2">aguardando aprovação do orientador</p>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-3">
                <span class="text-sm text-gray-600">Tutorial</span>
                <span class="material-symbols-outlined text-blue-600">menu_book</span>
            </div>
            <p class="text-3xl font-bold text-gray-900">5<span class="text-lg text-gray-400 font-medium">/9</span></p>
            <p class="text-xs text-gray-500 mt-2">etapas concluídas da trilha</p>
        </div>
    </div>

    {{-- Trilha do Estágio --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-8">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-semibold">Trilha do Estágio</h2>
            <span class="px-2.5 py-1 bg-blue-50 text-blue-700 text-xs font-semibold rounded-full">50% concluído</span>
        </div>
        <div class="space-y-3">
            @foreach([
                ['check', 'Conceito de Estágio', true],
                ['check', 'Tipos de Estágio', true],
                ['check', 'Horas Obrigatórias', true],
                ['check', 'Empresas Conveniadas', true],
                ['check', 'Currículo e Candidatura', true],
                ['circle', 'Documentação Necessária', false],
                ['circle', 'Busca por Vagas', false],
                ['circle', 'Registro e Controle de Horas', false],
                ['circle', 'Conclusão do Estágio', false],
            ] as $step)
            <div class="flex items-center gap-3">
                @if($step[2])
                    <span class="w-6 h-6 rounded-full bg-green-100 flex items-center justify-center shrink-0">
                        <span class="material-symbols-outlined text-green-600 text-[16px]">check</span>
                    </span>
                @else
                    <span class="w-6 h-6 rounded-full bg-gray-100 flex items-center justify-center shrink-0">
                        <span class="material-symbols-outlined text-gray-400 text-[16px]">{{ $step[0] }}</span>
                    </span>
                @endif
                <p class="text-sm {{ $step[2] ? 'text-gray-500 line-through' : 'text-gray-700 font-medium' }}">{{ $step[1] }}</p>
            </div>
            @endforeach
        </div>
        <a href="/tutorial" class="inline-flex items-center gap-2 mt-5 text-[#0077fc] text-sm font-medium hover:underline">
            <span class="material-symbols-outlined text-[18px]">menu_book</span>
            Continuar tutorial
        </a>
    </div>

    {{-- Estágio Atual + Documentos --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h2 class="text-xl font-semibold mb-4">Estágio Atual</h2>
            <div class="flex items-start gap-4">
                <div class="w-12 h-12 rounded-xl bg-blue-50 flex items-center justify-center shrink-0">
                    <span class="material-symbols-outlined text-[#0077fc]">business</span>
                </div>
                <div class="flex-1">
                    @if($estagioAtivo && $metricas)
                        <div class="flex items-start justify-between gap-2">
                            <div>
                                <h3 class="font-semibold text-gray-900">{{ $estagioAtivo->local }}</h3>
                                <p class="text-sm text-gray-500">Orientador: {{ $estagioAtivo->orientador->usuario->Nome }}</p>
                            </div>
                            <span class="px-2.5 py-1 bg-green-100 text-green-700 text-xs font-semibold rounded-full">Ativo</span>
                        </div>
                        <p class="text-xs text-gray-500 mt-3">
                            <span class="material-symbols-outlined text-[14px] align-middle">calendar_today</span>
                            {{ $estagioAtivo->data_inicio->format('d/m/Y') }} – {{ $estagioAtivo->data_fim->format('d/m/Y') }}
                        </p>
                        <progress class="mt-3 h-2 w-full accent-blue-600" value="{{ $metricas['percentual'] }}" max="100" aria-label="Progresso das horas mínimas"></progress>
                        <p class="text-xs text-gray-500 mt-1.5">{{ $metricas['aprovadas_formatadas'] }} de {{ $metricas['meta_horas'] }}h mínimas</p>
                        <a href="{{ route('estagios.show', $estagioAtivo) }}" class="mt-3 inline-flex text-sm font-medium text-[#0056c9] hover:underline">Acessar estágio</a>
                    @else
                        <p class="text-sm text-gray-600">Você ainda não possui um estágio ativo. Aguarde a inicialização pelo orientador.</p>
                    @endif
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h2 class="text-xl font-semibold mb-4">Documentos</h2>
            <div class="space-y-3">
                <div class="flex items-center justify-between p-3 border border-gray-200 rounded-lg">
                    <div class="flex items-center gap-3">
                        <span class="material-symbols-outlined text-[#0077fc]">description</span>
                        <div>
                            <p class="text-sm font-medium text-gray-900">Termo de Compromisso</p>
                            <p class="text-xs text-green-600 font-medium">Assinado</p>
                        </div>
                    </div>
                    <span class="material-symbols-outlined text-green-500 text-[20px]">check_circle</span>
                </div>
                <div class="flex items-center justify-between p-3 border border-gray-200 rounded-lg">
                    <div class="flex items-center gap-3">
                        <span class="material-symbols-outlined text-[#0077fc]">description</span>
                        <div>
                            <p class="text-sm font-medium text-gray-900">Plano de Atividades</p>
                            <p class="text-xs text-orange-500 font-medium">Pendente</p>
                        </div>
                    </div>
                    <span class="material-symbols-outlined text-orange-500 text-[20px]">pending</span>
                </div>
            </div>
        </div>
    </div>
</div>
