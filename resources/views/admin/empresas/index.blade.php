@extends('layouts.app')

@section('content')
<main class="mx-auto max-w-7xl space-y-6 px-4 py-8 sm:px-6 lg:px-8">
    {{-- Cabeçalho da Página --}}
    <header class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <div class="flex items-center gap-2">
                <a href="/dashboard" class="text-sm font-medium text-gray-500 hover:text-[#0077fc] transition-colors flex items-center gap-1">
                    <span class="material-symbols-outlined text-[18px]">arrow_back</span>
                    Dashboard
                </a>
                <span class="text-gray-300">/</span>
                <p class="text-sm font-medium text-[#0056c9]">Administração</p>
            </div>
            <h1 class="mt-1 text-2xl font-bold text-gray-900">Empresas Conveniadas</h1>
            <p class="mt-1 text-sm text-gray-600">Importação em lote via PDF oficial, geração de planilha .CSV e sincronização com o banco de dados.</p>
        </div>
        <div class="flex flex-wrap items-center gap-3">
            <form action="{{ route('admin.empresas.salvar_seeder') }}" method="POST" class="inline">
                @csrf
                <button type="submit" title="Grava todas as empresas e localizações no arquivo Seeder do projeto para que fiquem salvas no repositório permanentemente"
                   class="inline-flex items-center justify-center gap-2 rounded-lg border border-purple-300 bg-purple-50 px-4 py-2.5 text-sm font-semibold text-purple-700 shadow-sm transition hover:bg-purple-100">
                    <span class="material-symbols-outlined text-lg text-purple-700">save</span>
                    Salvar na Base do Projeto (Seeder)
                </button>
            </form>
            @if(!empty($ultimoCsv))
                <a href="{{ route('admin.empresas.download_csv', ['arquivo' => $ultimoCsv]) }}"
                   class="inline-flex items-center justify-center gap-2 rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-semibold text-gray-700 shadow-sm transition hover:bg-gray-50">
                    <span class="material-symbols-outlined text-lg text-green-600">file_download</span>
                    Baixar Último .CSV ({{ $ultimoCsv }})
                </a>
            @endif
            <a href="{{ route('admin.empresas.exportar_csv') }}"
               class="inline-flex items-center justify-center gap-2 rounded-lg bg-[#0077fc] px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-[#0056c9]">
                <span class="material-symbols-outlined text-lg">download</span>
                Exportar Base Atual (.CSV)
            </a>
        </div>
    </header>

    {{-- Feedback de Sucesso --}}
    @if(session('sucesso'))
        <div class="rounded-xl border border-green-200 bg-green-50 p-5 shadow-sm transition-all" role="alert">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div class="flex items-start gap-3">
                    <span class="material-symbols-outlined text-2xl text-green-600 mt-0.5">check_circle</span>
                    <div>
                        <h2 class="text-sm font-bold text-green-900">Operação concluída com sucesso!</h2>
                        <p class="mt-1 text-sm text-green-700">{{ session('sucesso') }}</p>
                    </div>
                </div>
                @if(session('csv_gerado'))
                    <a href="{{ route('admin.empresas.download_csv', ['arquivo' => session('csv_gerado')]) }}"
                       class="inline-flex items-center justify-center gap-2 rounded-lg bg-green-600 px-4 py-2 text-sm font-semibold text-white shadow hover:bg-green-700 transition">
                        <span class="material-symbols-outlined text-lg">description</span>
                        Baixar .CSV Gerado
                    </a>
                @endif
            </div>
        </div>
    @endif

    {{-- Feedback de Erro --}}
    @if($errors->any())
        <div class="rounded-xl border border-red-200 bg-red-50 p-5 shadow-sm" role="alert">
            <div class="flex items-start gap-3">
                <span class="material-symbols-outlined text-2xl text-red-600 mt-0.5">error</span>
                <div>
                    <h2 class="text-sm font-bold text-red-900">Houve um problema durante a operação</h2>
                    <ul class="mt-1 list-disc list-inside text-sm text-red-700 space-y-1">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    @endif

    {{-- Cards de Estatísticas --}}
    <section class="grid grid-cols-1 sm:grid-cols-3 gap-6" aria-label="Estatísticas de Convênios">
        <div class="card-figma rounded-xl p-6 shadow-sm border border-gray-200 bg-white">
            <div class="inline-flex p-3 rounded-lg bg-blue-50 text-blue-600 mb-4">
                <span class="material-symbols-outlined text-[24px]">business</span>
            </div>
            <h3 class="text-sm text-gray-600 mb-1 font-medium">Total de Empresas Cadastradas</h3>
            <p class="text-3xl font-bold text-gray-900">{{ number_format($stats['total'], 0, ',', '.') }}</p>
            <p class="text-xs text-gray-500 mt-1">Armazenadas na tabela empresas_conveniadas</p>
        </div>

        <div class="card-figma rounded-xl p-6 shadow-sm border border-gray-200 bg-white">
            <div class="inline-flex p-3 rounded-lg bg-green-50 text-green-600 mb-4">
                <span class="material-symbols-outlined text-[24px]">verified</span>
            </div>
            <h3 class="text-sm text-gray-600 mb-1 font-medium">Convênios Vigentes</h3>
            <p class="text-3xl font-bold text-green-700">{{ number_format($stats['vigentes'], 0, ',', '.') }}</p>
            <p class="text-xs text-gray-500 mt-1">Vigência final válida e ativa</p>
        </div>

        <div class="card-figma rounded-xl p-6 shadow-sm border border-gray-200 bg-white">
            <div class="inline-flex p-3 rounded-lg bg-orange-50 text-orange-600 mb-4">
                <span class="material-symbols-outlined text-[24px]">history</span>
            </div>
            <h3 class="text-sm text-gray-600 mb-1 font-medium">Convênios Expirados</h3>
            <p class="text-3xl font-bold text-orange-600">{{ number_format($stats['expiradas'], 0, ',', '.') }}</p>
            <p class="text-xs text-gray-500 mt-1">Necessitam de renovação documental</p>
        </div>
    </section>

    {{-- Card de Localização via CNPJ --}}
    <section class="rounded-xl border border-blue-200 bg-blue-50 p-5 sm:p-6" aria-labelledby="enrich-titulo">
        <div class="flex flex-col sm:flex-row sm:items-start gap-4">
            <div class="flex-shrink-0 inline-flex p-3 rounded-lg bg-blue-100 text-blue-700">
                <span class="material-symbols-outlined text-[24px]">location_searching</span>
            </div>
            <div class="flex-1">
                <h2 id="enrich-titulo" class="text-lg font-bold text-blue-900">Verificar Localização via CNPJ</h2>
                <p class="mt-1 text-sm text-blue-800">
                    Consulta a <strong>BrasilAPI / Receita Federal</strong> para obter município, UF, endereço e natureza jurídica de cada empresa.
                    O processamento é feito em lotes de 30 empresas por vez (respeita o rate limit de ~3 req/s).
                </p>

                {{-- Barra de progresso de localização --}}
                @php
                    $pctLocalizadas = $stats['total'] > 0 ? round(($stats['com_localizacao'] / $stats['total']) * 100) : 0;
                @endphp
                <div class="mt-3 flex items-center gap-3">
                    <div class="flex-1 bg-blue-200 rounded-full h-2.5 overflow-hidden">
                        <div class="bg-blue-600 h-2.5 rounded-full transition-all duration-500"
                             style="width: {{ $pctLocalizadas }}%"
                             id="barra-progresso-loc"></div>
                    </div>
                    <span class="text-sm font-semibold text-blue-900 whitespace-nowrap" id="texto-progresso-loc">
                        {{ number_format($stats['com_localizacao'], 0, ',', '.') }} / {{ number_format($stats['total'], 0, ',', '.') }} verificadas ({{ $pctLocalizadas }}%)
                    </span>
                </div>

                @if($stats['sem_localizacao'] === 0 && $stats['total'] > 0)
                    <p class="mt-2 text-sm font-semibold text-green-700 flex items-center gap-1">
                        <span class="material-symbols-outlined text-[18px]">check_circle</span>
                        Todas as empresas já têm localização verificada!
                    </p>
                @elseif($stats['sem_localizacao'] > 0)
                    <p class="mt-2 text-xs text-blue-700">
                        <strong>{{ number_format($stats['sem_localizacao'], 0, ',', '.') }}</strong> empresas ainda aguardam verificação.
                    </p>
                @endif

                {{-- Feedback da operação --}}
                <div id="enrich-feedback" class="hidden mt-3 p-3 rounded-lg text-sm font-medium"></div>

                <div class="mt-4 flex flex-wrap items-center gap-3">
                    @if($stats['sem_localizacao'] > 0)
                        <button id="btn-enriquecer"
                                onclick="iniciarEnriquecimento()"
                                class="inline-flex items-center justify-center gap-2 rounded-lg bg-blue-700 px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-blue-800 disabled:opacity-50">
                            <span class="material-symbols-outlined text-lg" id="icon-enriquecer">travel_explore</span>
                            <span id="text-enriquecer">Buscar Localização (próximas 30)</span>
                        </button>
                        <button id="btn-enriquecer-tudo"
                                onclick="enriquecerTudo()"
                                class="inline-flex items-center justify-center gap-2 rounded-lg border border-blue-300 bg-white px-4 py-2.5 text-sm font-semibold text-blue-700 shadow-sm transition hover:bg-blue-50 disabled:opacity-50">
                            <span class="material-symbols-outlined text-lg">all_inclusive</span>
                            Buscar Todas (automático)
                        </button>
                    @endif
                    <span class="text-xs text-blue-700 italic">Dados fornecidos pela BrasilAPI (Receita Federal)</span>
                </div>
            </div>
        </div>
    </section>

    {{-- Card de Upload e Importação de PDF --}}
    <section class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
        <div class="mb-4">
            <h2 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                <span class="material-symbols-outlined text-[#0077fc]">upload_file</span>
                Importar Lista de Empresas Conveniadas (PDF)
            </h2>
            <p class="text-sm text-gray-600 mt-1">
                Selecione o documento PDF fornecido pelo CEFET. O sistema fará a leitura automática das empresas, salvará um arquivo .csv intermediário e persistirá os dados na tabela do portal.
            </p>
        </div>

        <form action="{{ route('admin.empresas.importar') }}" method="POST" enctype="multipart/form-data" id="form-importar-pdf" class="space-y-4">
            @csrf

            <div id="drop-zone" class="relative border-2 border-dashed border-gray-300 hover:border-[#0077fc] rounded-xl p-8 text-center transition-all bg-gray-50 hover:bg-blue-50/40 cursor-pointer">
                <input type="file" name="arquivo_pdf" id="arquivo_pdf" accept="application/pdf" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10" required />
                
                <div class="flex flex-col items-center justify-center gap-2 pointer-events-none">
                    <div class="w-12 h-12 rounded-full bg-blue-100 text-[#0077fc] flex items-center justify-center">
                        <span class="material-symbols-outlined text-2xl">picture_as_pdf</span>
                    </div>
                    <p class="text-sm font-semibold text-gray-900" id="file-label-main">
                        Arraste o arquivo PDF aqui ou <span class="text-[#0077fc] underline">clique para selecionar</span>
                    </p>
                    <p class="text-xs text-gray-500" id="file-label-sub">
                        Suporte a arquivos PDF até 30 MB contendo a listagem oficial de convênios.
                    </p>
                </div>
            </div>

            {{-- Fluxo visual explicativo --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-3 pt-2 text-xs text-gray-500">
                <div class="flex items-center gap-2 p-2.5 rounded-lg bg-gray-50 border border-gray-100">
                    <span class="material-symbols-outlined text-[#0077fc] text-[18px]">document_scanner</span>
                    <span><strong>1. Leitura do PDF:</strong> Extrai razão social, CNPJ e datas de vigência.</span>
                </div>
                <div class="flex items-center gap-2 p-2.5 rounded-lg bg-gray-50 border border-gray-100">
                    <span class="material-symbols-outlined text-green-600 text-[18px]">table_view</span>
                    <span><strong>2. Geração do .CSV:</strong> Estrutura os campos e gera planilha para download.</span>
                </div>
                <div class="flex items-center gap-2 p-2.5 rounded-lg bg-gray-50 border border-gray-100">
                    <span class="material-symbols-outlined text-purple-600 text-[18px]">database</span>
                    <span><strong>3. Banco de Dados:</strong> Atualiza ou insere na migration de convênios.</span>
                </div>
            </div>

            <div class="flex justify-end pt-2">
                <button type="submit" id="btn-submit"
                        class="inline-flex items-center justify-center gap-2 rounded-lg bg-[#0077fc] px-6 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-[#0056c9] disabled:opacity-50">
                    <span class="material-symbols-outlined text-lg" id="btn-icon">play_arrow</span>
                    <span id="btn-text">Processar e Importar PDF</span>
                </button>
            </div>
        </form>
    </section>

    {{-- Filtros e Busca da Tabela --}}
    <section class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm sm:p-5" aria-label="Filtros de empresas">
        <form method="GET" action="{{ route('admin.empresas') }}" class="grid gap-3 md:grid-cols-[minmax(0,1fr)_160px_160px_auto]">
            <label class="relative block">
                <span class="sr-only">Buscar empresas</span>
                <span class="material-symbols-outlined pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">search</span>
                <input name="busca" type="search" value="{{ $busca }}" placeholder="Buscar por razão social ou CNPJ..."
                       class="w-full rounded-lg border-gray-300 py-2.5 pl-10 pr-3 text-sm focus:border-[#0077fc] focus:ring-[#0077fc]" />
            </label>
            <label>
                <span class="sr-only">Filtrar por status</span>
                <select name="status" class="w-full rounded-lg border-gray-300 py-2.5 text-sm focus:border-[#0077fc] focus:ring-[#0077fc]">
                    <option value="">Todos os status</option>
                    <option value="vigente" {{ $status === 'vigente' ? 'selected' : '' }}>Somente Vigentes</option>
                    <option value="expirado" {{ $status === 'expirado' ? 'selected' : '' }}>Somente Expirados</option>
                </select>
            </label>
            <label>
                <span class="sr-only">Filtrar por UF</span>
                <select name="uf" class="w-full rounded-lg border-gray-300 py-2.5 text-sm focus:border-[#0077fc] focus:ring-[#0077fc]">
                    <option value="">Todos os estados</option>
                    @foreach($ufsDisponiveis as $ufOpcao)
                        <option value="{{ $ufOpcao }}" {{ $uf === $ufOpcao ? 'selected' : '' }}>{{ $ufOpcao }}</option>
                    @endforeach
                </select>
            </label>
            <div class="flex gap-2">
                <button type="submit" class="inline-flex items-center justify-center gap-1.5 rounded-lg bg-gray-100 hover:bg-gray-200 px-4 py-2.5 text-sm font-semibold text-gray-700 transition">
                    <span class="material-symbols-outlined text-[18px]">filter_list</span>
                    Filtrar
                </button>
                @if($busca || $status || $uf)
                    <a href="{{ route('admin.empresas') }}" class="inline-flex items-center justify-center rounded-lg border border-gray-300 px-3 py-2.5 text-sm font-medium text-gray-600 hover:bg-gray-50 transition">
                        Limpar
                    </a>
                @endif
            </div>
        </form>
    </section>

    {{-- Tabela de Empresas Conveniadas --}}
    <section class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-left text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="whitespace-nowrap px-4 py-3.5 text-xs font-semibold uppercase tracking-wide text-gray-500 sm:px-6">Empresa / Razão Social</th>
                        <th scope="col" class="whitespace-nowrap px-4 py-3.5 text-xs font-semibold uppercase tracking-wide text-gray-500 sm:px-6">CNPJ</th>
                        <th scope="col" class="whitespace-nowrap px-4 py-3.5 text-xs font-semibold uppercase tracking-wide text-gray-500 sm:px-6">Localização</th>
                        <th scope="col" class="whitespace-nowrap px-4 py-3.5 text-xs font-semibold uppercase tracking-wide text-gray-500 sm:px-6">Início Vigência</th>
                        <th scope="col" class="whitespace-nowrap px-4 py-3.5 text-xs font-semibold uppercase tracking-wide text-gray-500 sm:px-6">Fim Vigência</th>
                        <th scope="col" class="whitespace-nowrap px-4 py-3.5 text-xs font-semibold uppercase tracking-wide text-gray-500 sm:px-6">Situação</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($empresas as $empresa)
                        <tr class="hover:bg-gray-50/70 transition-colors">
                            <td class="px-4 py-4 text-sm font-semibold text-gray-900 sm:px-6">
                                {{ $empresa->empresa }}
                            </td>
                            <td class="whitespace-nowrap px-4 py-4 text-sm font-mono text-gray-600 sm:px-6">
                                {{ $empresa->cnpj_formatado }}
                            </td>
                            <td class="whitespace-nowrap px-4 py-4 text-sm sm:px-6">
                                @if($empresa->localizacao_verificada && $empresa->municipio)
                                    <span class="inline-flex items-center gap-1.5 text-gray-700">
                                        <span class="material-symbols-outlined text-[14px] text-[#0077fc]">location_on</span>
                                        {{ $empresa->municipio }}@if($empresa->uf) / {{ $empresa->uf }}@endif
                                    </span>
                                @else
                                    <span class="text-xs text-gray-400 italic">Não verificado</span>
                                @endif
                            </td>
                            <td class="whitespace-nowrap px-4 py-4 text-sm text-gray-600 sm:px-6">
                                {{ $empresa->vig_inicial ? $empresa->vig_inicial->format('d/m/Y') : '—' }}
                            </td>
                            <td class="whitespace-nowrap px-4 py-4 text-sm text-gray-600 sm:px-6">
                                {{ $empresa->vig_final ? $empresa->vig_final->format('d/m/Y') : '—' }}
                            </td>
                            <td class="whitespace-nowrap px-4 py-4 text-sm sm:px-6">
                                @if($empresa->isVigente())
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-700">
                                        <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span>
                                        Vigente
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-700">
                                        <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span>
                                        Expirado
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-sm text-gray-500">
                                <div class="flex flex-col items-center justify-center gap-2">
                                    <span class="material-symbols-outlined text-gray-300 text-[48px]">business_center</span>
                                    <p class="font-medium text-gray-700">Nenhuma empresa conveniada encontrada</p>
                                    <p class="text-xs text-gray-400">Faça o upload de um arquivo PDF acima para importar a listagem oficial.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Paginação --}}
        @if($empresas->hasPages())
            <div class="px-6 py-4 border-t border-gray-100 bg-gray-50">
                {{ $empresas->links() }}
            </div>
        @endif
    </section>

    {{-- Seção de Exclusão / Limpeza de Empresas Conveniadas --}}
    <section class="rounded-xl border border-red-200 bg-red-50 p-5 sm:p-6" aria-labelledby="limpeza-empresas-titulo">
        <div class="flex items-start gap-3">
            <span class="material-symbols-outlined text-2xl text-red-600 mt-0.5">delete_forever</span>
            <div class="flex-1">
                <h2 id="limpeza-empresas-titulo" class="text-lg font-bold text-red-900">Limpar Base de Empresas Conveniadas</h2>
                <p class="mt-1 text-sm text-red-800">
                    Esta ação apagará <strong>todas as empresas conveniadas</strong> da tabela do portal. Os usuários e estágios não serão afetados. Esta operação é irreversível.
                </p>
                <form action="{{ route('admin.empresas.limpar') }}" method="POST" id="form-limpar-empresas" class="mt-4 flex flex-col gap-3 sm:flex-row sm:items-end">
                    @csrf
                    <label class="block w-full max-w-md text-sm font-medium text-red-900">
                        Para confirmar a exclusão, digite <span class="font-bold underline">APAGAR TODAS AS EMPRESAS</span>:
                        <input id="input-confirmar-limpeza" name="confirmacao" autocomplete="off" placeholder="APAGAR TODAS AS EMPRESAS"
                               class="mt-1.5 block w-full rounded-lg border-red-300 bg-white px-3 py-2 text-sm focus:border-red-500 focus:ring-red-500" required />
                    </label>
                    <button id="btn-limpar-empresas" type="submit" disabled
                            class="inline-flex items-center justify-center gap-1.5 rounded-lg bg-red-700 px-5 py-2.5 text-sm font-semibold text-white shadow hover:bg-red-800 transition disabled:cursor-not-allowed disabled:opacity-50">
                        <span class="material-symbols-outlined text-lg">delete_sweep</span>
                        Apagar Todas as Empresas
                    </button>
                </form>
            </div>
        </div>
    </section>
</main>

<script>
    const fileInput = document.getElementById('arquivo_pdf');
    const labelMain = document.getElementById('file-label-main');
    const labelSub = document.getElementById('file-label-sub');
    const form = document.getElementById('form-importar-pdf');
    const btnSubmit = document.getElementById('btn-submit');
    const btnText = document.getElementById('btn-text');
    const btnIcon = document.getElementById('btn-icon');

    fileInput.addEventListener('change', function(e) {
        if (this.files && this.files.length > 0) {
            const file = this.files[0];
            const sizeMb = (file.size / (1024 * 1024)).toFixed(2);
            labelMain.innerHTML = `<strong class="text-gray-900">${file.name}</strong>`;
            labelSub.innerHTML = `Tamanho: ${sizeMb} MB &bull; Pronto para importar`;
        }
    });

    form.addEventListener('submit', function() {
        btnSubmit.disabled = true;
        btnText.innerText = 'Processando PDF e Importando...';
        btnIcon.classList.add('animate-spin');
        btnIcon.innerText = 'sync';
    });

    const inputLimpeza = document.getElementById('input-confirmar-limpeza');
    const btnLimpeza = document.getElementById('btn-limpar-empresas');
    if (inputLimpeza && btnLimpeza) {
        inputLimpeza.addEventListener('input', function() {
            btnLimpeza.disabled = this.value.trim().toUpperCase() !== 'APAGAR TODAS AS EMPRESAS';
        });
    }

    // ─── Enriquecimento via BrasilAPI ───────────────────────────────────────
    const ROTA_ENRIQUECER = '{{ route("admin.empresas.enriquecer_lote") }}';
    const CSRF_TOKEN      = document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}';

    let enriquecendoTudo = false;
    let totalPendentes   = {{ $stats['sem_localizacao'] ?? 0 }};
    let totalGlobal      = {{ $stats['total'] ?? 0 }};
    let totalVerificadas = {{ $stats['com_localizacao'] ?? 0 }};

    function setEnrichButtonsDisabled(disabled) {
        const btnE = document.getElementById('btn-enriquecer');
        const btnT = document.getElementById('btn-enriquecer-tudo');
        if (btnE) btnE.disabled = disabled;
        if (btnT) btnT.disabled = disabled;
    }

    function atualizarBarraProgresso(verificadas, total) {
        const pct = total > 0 ? Math.round((verificadas / total) * 100) : 0;
        const barra = document.getElementById('barra-progresso-loc');
        const texto = document.getElementById('texto-progresso-loc');
        if (barra) barra.style.width = pct + '%';
        if (texto) texto.innerText = `${verificadas.toLocaleString('pt-BR')} / ${total.toLocaleString('pt-BR')} verificadas (${pct}%)`;
    }

    function mostrarFeedbackEnrich(tipo, mensagem) {
        const el = document.getElementById('enrich-feedback');
        if (!el) return;
        el.className = 'mt-3 p-3 rounded-lg text-sm font-medium ' +
            (tipo === 'sucesso'  ? 'bg-green-100 text-green-800 border border-green-200' :
             tipo === 'erro'     ? 'bg-red-100 text-red-800 border border-red-200' :
                                   'bg-blue-100 text-blue-800 border border-blue-200');
        el.textContent = mensagem;
        el.classList.remove('hidden');
    }

    async function chamarLote() {
        const iconEl = document.getElementById('icon-enriquecer');
        const textEl = document.getElementById('text-enriquecer');
        if (iconEl) { iconEl.classList.add('animate-spin'); iconEl.innerText = 'sync'; }
        if (textEl) textEl.innerText = 'Buscando...';

        const resp = await fetch(ROTA_ENRIQUECER, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': CSRF_TOKEN,
            },
            body: JSON.stringify({ limite: 30 }),
        });

        const data = await resp.json();

        if (!resp.ok || !data.sucesso) {
            mostrarFeedbackEnrich('erro', 'Erro: ' + (data.mensagem || 'Falha na comunicação com o servidor.'));
            setEnrichButtonsDisabled(false);
            if (iconEl) { iconEl.classList.remove('animate-spin'); iconEl.innerText = 'travel_explore'; }
            if (textEl) textEl.innerText = 'Buscar Localização (próximas 30)';
            enriquecendoTudo = false;
            return false;
        }

        totalVerificadas += data.sucessos;
        totalPendentes   = data.pendentes;
        atualizarBarraProgresso(totalVerificadas, totalGlobal);
        mostrarFeedbackEnrich(
            'sucesso',
            `Lote processado: ${data.processadas} consultadas, ${data.sucessos} localizações encontradas, ${data.falhas} sem dados. Restam: ${data.pendentes}.`
        );

        if (iconEl) { iconEl.classList.remove('animate-spin'); iconEl.innerText = 'travel_explore'; }
        if (textEl) textEl.innerText = `Buscar Localização (próximas 30)`;

        return data.pendentes > 0;
    }

    async function iniciarEnriquecimento() {
        setEnrichButtonsDisabled(true);
        enriquecendoTudo = false;
        await chamarLote();
        setEnrichButtonsDisabled(false);
    }

    async function enriquecerTudo() {
        enriquecendoTudo = true;
        setEnrichButtonsDisabled(true);
        mostrarFeedbackEnrich('info', 'Iniciando verificação automática... isso pode levar alguns minutos.');

        let temMais = true;
        let lotes   = 0;
        while (temMais && enriquecendoTudo) {
            temMais = await chamarLote();
            lotes++;
            if (temMais) {
                await new Promise(r => setTimeout(r, 500)); // pequena pausa entre lotes
            }
        }

        setEnrichButtonsDisabled(false);
        enriquecendoTudo = false;
        if (!temMais) {
            mostrarFeedbackEnrich('sucesso', `Verificação concluída! ${lotes} lotes processados. Todas as empresas com CNPJ válido foram consultadas.`);
            // Desabilita os botões pois não há mais pendentes
            const btnE = document.getElementById('btn-enriquecer');
            const btnT = document.getElementById('btn-enriquecer-tudo');
            if (btnE) btnE.style.display = 'none';
            if (btnT) btnT.style.display = 'none';
        }
    }
</script>
@endsection
