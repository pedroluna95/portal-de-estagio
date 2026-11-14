@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900 mb-1">Empresas Conveniadas</h1>
        <p class="text-gray-500">Empresas parceiras oficiais com convênio ativo de estágio com o CEFET</p>
    </div>

    {{-- Stats Dinâmicos --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 mb-8">
        <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-200">
            <div class="inline-flex p-3 rounded-lg bg-blue-50 text-blue-600 mb-4">
                <span class="material-symbols-outlined">business</span>
            </div>
            <h3 class="text-sm text-gray-600 mb-1 font-medium">Empresas Cadastradas</h3>
            <p class="text-3xl font-bold text-gray-900">{{ number_format($stats['total'], 0, ',', '.') }}</p>
            <p class="text-xs text-gray-500 mt-1">Total na base oficial de convênios</p>
        </div>
        <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-200">
            <div class="inline-flex p-3 rounded-lg bg-green-50 text-green-600 mb-4">
                <span class="material-symbols-outlined">verified</span>
            </div>
            <h3 class="text-sm text-gray-600 mb-1 font-medium">Convênios Vigentes</h3>
            <p class="text-3xl font-bold text-green-700">{{ number_format($stats['vigentes'], 0, ',', '.') }}</p>
            <p class="text-xs text-gray-500 mt-1">Prontos para formalização de estágio</p>
        </div>
    </div>

    {{-- Busca e Filtros Funcionais --}}
    <form method="GET" action="{{ route('empresas.index') }}" class="bg-white rounded-xl shadow-sm border border-gray-200 mb-6 overflow-hidden">
        <div class="p-4 flex flex-col sm:flex-row gap-3">
            <div class="flex-1 relative">
                <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-[18px] text-gray-400">search</span>
                <input type="text" name="busca" value="{{ $busca }}" placeholder="Buscar empresa por razão social ou CNPJ…"
                    class="w-full pl-9 pr-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-[#0077fc] focus:border-transparent outline-none"/>
            </div>
            <div class="flex gap-2">
                <button type="button" onclick="toggleFiltros()" id="btn-filtros"
                    class="flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg border-2 {{ ($status || ($uf ?? '') || $ordem !== 'nome_asc') ? 'border-[#0077fc] bg-blue-50 text-[#0077fc]' : 'border-gray-200 text-gray-600' }} text-sm font-medium hover:bg-gray-50 transition-colors">
                    <span class="material-symbols-outlined text-[18px]">tune</span>
                    Filtros
                </button>
                <button type="submit"
                    class="flex items-center justify-center gap-2 px-5 py-2.5 bg-[#0077fc] hover:bg-[#0056c9] text-white rounded-lg text-sm font-semibold transition-colors shadow-sm">
                    Buscar
                </button>
            </div>
        </div>

        <div id="painel-filtros" class="{{ ($status || $ordem !== 'nome_asc') ? '' : 'hidden' }} px-4 pb-5 pt-3 border-t border-gray-100 bg-gray-50/50">
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1.5">Situação do Convênio</label>
                    <select name="status"
                        class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-[#0077fc] focus:border-transparent outline-none bg-white">
                        <option value="">Todos os convênios</option>
                        <option value="vigente" {{ $status === 'vigente' ? 'selected' : '' }}>Somente Vigentes (Ativos)</option>
                        <option value="expirado" {{ $status === 'expirado' ? 'selected' : '' }}>Somente Expirados</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1.5">Estado (UF)</label>
                    <select name="uf"
                        class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-[#0077fc] focus:border-transparent outline-none bg-white">
                        <option value="">Todos os estados</option>
                        @foreach($ufsDisponiveis as $ufOpcao)
                            <option value="{{ $ufOpcao }}" {{ ($uf ?? '') === $ufOpcao ? 'selected' : '' }}>{{ $ufOpcao }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1.5">Ordenação</label>
                    <select name="ordem"
                        class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-[#0077fc] focus:border-transparent outline-none bg-white">
                        <option value="nome_asc" {{ $ordem === 'nome_asc' ? 'selected' : '' }}>Nome da Empresa (A - Z)</option>
                        <option value="nome_desc" {{ $ordem === 'nome_desc' ? 'selected' : '' }}>Nome da Empresa (Z - A)</option>
                        <option value="vig_fim_asc" {{ $ordem === 'vig_fim_asc' ? 'selected' : '' }}>Próximos do Vencimento</option>
                        <option value="recentes" {{ $ordem === 'recentes' ? 'selected' : '' }}>Cadastrados Recentemente</option>
                    </select>
                </div>
                <div class="flex items-end gap-2">
                    <button type="submit" class="px-4 py-2 bg-[#0077fc] text-white rounded-lg text-sm font-semibold hover:bg-[#0056c9] transition">
                        Aplicar Filtros
                    </button>
                    @if($busca || $status || ($uf ?? '') || $ordem !== 'nome_asc')
                        <a href="{{ route('empresas.index') }}" class="px-3 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-600 hover:bg-gray-100 transition">
                            Limpar
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </form>

    <div class="flex justify-between items-center mb-4">
        <p class="text-sm text-gray-500">
            <strong>{{ number_format($empresas->total(), 0, ',', '.') }}</strong>
            {{ $empresas->total() === 1 ? 'empresa encontrada' : 'empresas encontradas' }}
        </p>
    </div>

    {{-- Grid de empresas reais --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($empresas as $empresa)
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 hover:shadow-md hover:border-[#0077fc] transition-all flex flex-col justify-between">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-3 gap-2">
                        <div class="w-12 h-12 rounded-xl {{ $empresa->cor_card['bg'] }} flex items-center justify-center flex-shrink-0">
                            <span class="material-symbols-outlined {{ $empresa->cor_card['txt'] }} text-[24px]">business</span>
                        </div>
                        @if($empresa->isVigente())
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-green-100 text-green-700 text-xs font-semibold rounded-full">
                                <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span>
                                Convênio ativo
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-red-100 text-red-700 text-xs font-semibold rounded-full">
                                <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span>
                                Convênio expirado
                            </span>
                        @endif
                    </div>

                    <h3 class="font-bold text-gray-900 mb-2 line-clamp-2" title="{{ $empresa->empresa }}">
                        {{ $empresa->empresa }}
                    </h3>

                    <div class="flex flex-wrap gap-2 mb-4">
                        <span class="inline-flex items-center gap-1 px-2.5 py-1 bg-gray-100 text-gray-600 rounded-full text-xs">
                            <span class="material-symbols-outlined text-[14px]">verified</span>
                            CEFET/RJ
                        </span>
                        @if($empresa->localizacao_verificada && $empresa->municipio)
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 bg-blue-50 text-blue-700 rounded-full text-xs">
                                <span class="material-symbols-outlined text-[14px]">location_on</span>
                                {{ $empresa->municipio }}@if($empresa->uf) / {{ $empresa->uf }}@endif
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 bg-gray-100 text-gray-600 rounded-full text-xs">
                                <span class="material-symbols-outlined text-[14px]">location_on</span>
                                Brasil
                            </span>
                        @endif
                    </div>

                    <div class="space-y-2 border-t border-gray-100 pt-3 text-sm">
                        <div class="flex justify-between items-center">
                            <span class="text-gray-500 text-xs">CNPJ</span>
                            <span class="font-mono text-xs font-medium text-gray-800">{{ $empresa->cnpj_formatado }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-500 text-xs">Início</span>
                            <span class="text-xs font-medium text-gray-800">{{ $empresa->vig_inicial ? $empresa->vig_inicial->format('d/m/Y') : '—' }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-500 text-xs">Vigência até</span>
                            <span class="text-xs font-semibold {{ $empresa->isVigente() ? 'text-green-700' : 'text-red-600' }}">
                                {{ $empresa->vig_final ? $empresa->vig_final->format('d/m/Y') : '—' }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-1 sm:col-span-2 lg:col-span-3 bg-white rounded-xl p-12 text-center shadow-sm border border-gray-200">
                <span class="material-symbols-outlined text-gray-300 text-[56px]">business</span>
                <h3 class="text-lg font-semibold text-gray-900 mb-1 mt-4">Nenhuma empresa encontrada</h3>
                <p class="text-sm text-gray-500 mb-5">Tente ajustar os termos de busca ou remover os filtros aplicados.</p>
                <a href="{{ route('empresas.index') }}" class="inline-flex px-5 py-2.5 bg-[#0077fc] text-white rounded-lg hover:bg-[#0056c9] transition-colors text-sm font-semibold">
                    Limpar filtros
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
</div>

<script>
function toggleFiltros() {
    const painel = document.getElementById('painel-filtros');
    painel.classList.toggle('hidden');
}
</script>
@endsection
