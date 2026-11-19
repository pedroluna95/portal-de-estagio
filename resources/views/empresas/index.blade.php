@extends('layouts.app')

@section('content')
<main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8">
    {{-- Cabeçalho da Página --}}
    <div class="border-b border-slate-200 dark:border-slate-800 pb-5">
        <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-blue-50 dark:bg-blue-950/60 text-blue-600 dark:text-blue-400 border border-blue-200/60 dark:border-blue-900/40 mb-2">
            <span class="material-symbols-outlined text-[14px]">domain</span>
            Catálogo Oficial de Parcerias
        </div>
        <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 dark:text-slate-100 tracking-tight">
            Empresas Conveniadas
        </h1>
        <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
            Consulte empresas parceiras com convênio formalizado para estágio de alunos da UFCI-RJ.
        </p>
    </div>

    {{-- Stats Dinâmicos --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div class="rounded-2xl border border-slate-200/80 dark:border-slate-800 bg-white dark:bg-slate-900 p-6 shadow-sm flex items-center justify-between">
            <div>
                <span class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400 block mb-1">
                    Empresas Cadastradas
                </span>
                <span class="text-3xl font-black text-slate-900 dark:text-slate-100 tracking-tight">
                    {{ number_format($stats['total'], 0, ',', '.') }}
                </span>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Total homologado na base do portal</p>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-blue-50 dark:bg-blue-950/60 text-blue-600 dark:text-blue-400 flex items-center justify-center shrink-0">
                <span class="material-symbols-outlined text-[28px]">domain</span>
            </div>
        </div>

        <div class="rounded-2xl border border-slate-200/80 dark:border-slate-800 bg-white dark:bg-slate-900 p-6 shadow-sm flex items-center justify-between">
            <div>
                <span class="text-xs font-bold uppercase tracking-wider text-emerald-600 dark:text-emerald-400 block mb-1">
                    Convênios Vigentes (Ativos)
                </span>
                <span class="text-3xl font-black text-emerald-700 dark:text-emerald-300 tracking-tight">
                    {{ number_format($stats['vigentes'], 0, ',', '.') }}
                </span>
                <p class="text-xs text-emerald-600/80 dark:text-emerald-400/80 mt-1">Prontos para admissão e assinatura de TCE</p>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-emerald-50 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 flex items-center justify-center shrink-0">
                <span class="material-symbols-outlined text-[28px]">verified</span>
            </div>
        </div>
    </div>

    {{-- Busca e Filtros Funcionais --}}
    <form method="GET" action="{{ route('empresas.index') }}" class="rounded-2xl border border-slate-200/80 dark:border-slate-800 bg-white dark:bg-slate-900 shadow-sm overflow-hidden">
        <div class="p-4 sm:p-5 flex flex-col sm:flex-row gap-3">
            <div class="flex-1 relative">
                <span class="material-symbols-outlined pointer-events-none absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-[20px]">search</span>
                <input type="text" name="busca" value="{{ $busca }}" placeholder="Buscar por razão social, nome fantasia ou CNPJ..."
                       class="w-full rounded-xl border-slate-300 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-100 py-2.5 pl-10 pr-4 text-sm focus:border-blue-500 focus:ring-blue-500 placeholder-slate-400" />
            </div>
            <div class="flex items-center gap-2">
                <button type="button" onclick="toggleFiltros()" id="btn-filtros"
                        class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl border text-xs font-semibold transition-colors
                               {{ ($status || ($uf ?? '') || $ordem !== 'nome_asc')
                                   ? 'border-blue-500 bg-blue-50 dark:bg-blue-950/40 text-blue-600 dark:text-blue-400'
                                   : 'border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-700 dark:text-slate-200 hover:bg-slate-100' }}">
                    <span class="material-symbols-outlined text-[18px]">tune</span>
                    Filtros
                </button>
                <button type="submit"
                        class="inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-xs font-semibold shadow-sm transition-colors">
                    <span class="material-symbols-outlined text-[18px]">search</span>
                    Buscar
                </button>
            </div>
        </div>

        <div id="painel-filtros" class="{{ ($status || $ordem !== 'nome_asc') ? '' : 'hidden' }} px-4 sm:px-5 pb-5 pt-2 border-t border-slate-100 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-800/40">
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4 pt-2">
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 dark:text-slate-300 mb-1.5">Situação do Convênio</label>
                    <select name="status" class="w-full rounded-xl border-slate-300 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-100 py-2 px-3 text-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="">Todos os convênios</option>
                        <option value="vigente" {{ $status === 'vigente' ? 'selected' : '' }}>Somente Vigentes (Ativos)</option>
                        <option value="expirado" {{ $status === 'expirado' ? 'selected' : '' }}>Somente Expirados</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 dark:text-slate-300 mb-1.5">Estado (UF)</label>
                    <select name="uf" class="w-full rounded-xl border-slate-300 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-100 py-2 px-3 text-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="">Todos os estados</option>
                        @foreach($ufsDisponiveis as $ufOpcao)
                            <option value="{{ $ufOpcao }}" {{ ($uf ?? '') === $ufOpcao ? 'selected' : '' }}>{{ $ufOpcao }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 dark:text-slate-300 mb-1.5">Ordenação</label>
                    <select name="ordem" class="w-full rounded-xl border-slate-300 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-100 py-2 px-3 text-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="nome_asc" {{ $ordem === 'nome_asc' ? 'selected' : '' }}>Nome da Empresa (A - Z)</option>
                        <option value="nome_desc" {{ $ordem === 'nome_desc' ? 'selected' : '' }}>Nome da Empresa (Z - A)</option>
                        <option value="vig_fim_asc" {{ $ordem === 'vig_fim_asc' ? 'selected' : '' }}>Próximos do Vencimento</option>
                        <option value="recentes" {{ $ordem === 'recentes' ? 'selected' : '' }}>Cadastrados Recentemente</option>
                    </select>
                </div>
                <div class="sm:col-span-2 md:col-span-3 flex items-center gap-2 pt-1">
                    <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-xs font-semibold transition-colors">
                        Aplicar Filtros
                    </button>
                    @if($busca || $status || ($uf ?? '') || $ordem !== 'nome_asc')
                        <a href="{{ route('empresas.index') }}" class="px-3.5 py-2 rounded-xl border border-slate-300 dark:border-slate-700 text-xs font-semibold text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors">
                            Limpar
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </form>

    <div class="flex justify-between items-center text-xs font-medium text-slate-500 dark:text-slate-400">
        <span>Exibindo <strong>{{ number_format($empresas->total(), 0, ',', '.') }}</strong> {{ $empresas->total() === 1 ? 'empresa' : 'empresas' }}</span>
    </div>

    {{-- Grid de Empresas --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
        @forelse($empresas as $empresa)
            <div class="rounded-2xl border border-slate-200/80 dark:border-slate-800 bg-white dark:bg-slate-900 shadow-sm hover:shadow-md hover:border-blue-400 dark:hover:border-blue-800 transition-all flex flex-col justify-between overflow-hidden group">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-4 gap-2">
                        <div class="w-11 h-11 rounded-xl {{ $empresa->cor_card['bg'] }} flex items-center justify-center shrink-0">
                            <span class="material-symbols-outlined {{ $empresa->cor_card['txt'] }} text-[22px]">domain</span>
                        </div>
                        @if($empresa->isVigente())
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-400 border border-emerald-200/60 dark:border-emerald-900/40 text-xs font-semibold rounded-full">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                Vigente
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 bg-rose-50 dark:bg-rose-950/40 text-rose-700 dark:text-rose-400 border border-rose-200/60 dark:border-rose-900/40 text-xs font-semibold rounded-full">
                                <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span>
                                Expirado
                            </span>
                        @endif
                    </div>

                    <h3 class="font-bold text-base text-slate-900 dark:text-slate-100 mb-2 line-clamp-2 leading-snug group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors" title="{{ $empresa->empresa }}">
                        {{ $empresa->empresa }}
                    </h3>

                    <div class="flex flex-wrap gap-1.5 mb-4">
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 rounded-md text-[11px] font-medium">
                            <span class="material-symbols-outlined text-[13px]">verified</span>
                            UFCI/RJ
                        </span>
                        @if($empresa->localizacao_verificada && $empresa->municipio)
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 bg-blue-50 dark:bg-blue-950/40 text-blue-700 dark:text-blue-300 rounded-md text-[11px] font-medium">
                                <span class="material-symbols-outlined text-[13px]">location_on</span>
                                {{ $empresa->municipio }}@if($empresa->uf) / {{ $empresa->uf }}@endif
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 rounded-md text-[11px] font-medium">
                                <span class="material-symbols-outlined text-[13px]">location_on</span>
                                Brasil
                            </span>
                        @endif
                    </div>

                    <div class="space-y-1.5 border-t border-slate-100 dark:border-slate-800 pt-3 text-xs">
                        <div class="flex justify-between items-center text-slate-500 dark:text-slate-400">
                            <span>CNPJ</span>
                            <span class="font-mono font-medium text-slate-800 dark:text-slate-200">{{ $empresa->cnpj_formatado }}</span>
                        </div>
                        <div class="flex justify-between items-center text-slate-500 dark:text-slate-400">
                            <span>Início do Convênio</span>
                            <span class="font-medium text-slate-800 dark:text-slate-200">{{ $empresa->vig_inicial ? $empresa->vig_inicial->format('d/m/Y') : '—' }}</span>
                        </div>
                        <div class="flex justify-between items-center text-slate-500 dark:text-slate-400">
                            <span>Término da Vigência</span>
                            <span class="font-semibold {{ $empresa->isVigente() ? 'text-emerald-600 dark:text-emerald-400' : 'text-rose-600 dark:text-rose-400' }}">
                                {{ $empresa->vig_final ? $empresa->vig_final->format('d/m/Y') : '—' }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full rounded-2xl border border-slate-200/80 dark:border-slate-800 bg-white dark:bg-slate-900 p-12 text-center shadow-sm">
                <span class="material-symbols-outlined text-slate-300 dark:text-slate-600 text-[56px]">domain_disabled</span>
                <h3 class="text-base font-bold text-slate-900 dark:text-slate-100 mb-1 mt-3">Nenhuma empresa encontrada</h3>
                <p class="text-xs text-slate-500 dark:text-slate-400 mb-5">Tente remover filtros ou pesquisar por outro nome ou CNPJ.</p>
                <a href="{{ route('empresas.index') }}" class="inline-flex px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-xs font-semibold transition-colors">
                    Limpar Filtros
                </a>
            </div>
        @endforelse
    </div>

    {{-- Paginação --}}
    @if($empresas->hasPages())
        <div class="mt-8">
            {{ $empresas->links() }}
        </div>
    @endif
</main>

<script>
function toggleFiltros() {
    const painel = document.getElementById('painel-filtros');
    painel.classList.toggle('hidden');
}
</script>
@endsection
