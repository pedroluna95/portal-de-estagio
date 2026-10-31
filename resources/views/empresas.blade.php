@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900 mb-1">Empresas Conveniadas</h1>
        <p class="text-gray-500">Empresas parceiras com convênio com o CEFET</p>
    </div>

    {{-- Stats --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 mb-8">
        <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-200">
            <div class="inline-flex p-3 rounded-lg bg-blue-50 text-blue-600 mb-4"><span class="material-symbols-outlined">business</span></div>
            <h3 class="text-sm text-gray-600 mb-1">Empresas Ativas</h3>
            <p class="text-2xl font-bold text-gray-900">48</p>
        </div>
        <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-200">
            <div class="inline-flex p-3 rounded-lg bg-green-50 text-green-600 mb-4"><span class="material-symbols-outlined">badge</span></div>
            <h3 class="text-sm text-gray-600 mb-1">Convênios Ativos</h3>
            <p class="text-2xl font-bold text-gray-900">35</p>
        </div>
    </div>

    {{-- Busca e filtros --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 mb-6 overflow-hidden">
        <div class="p-4 flex flex-col sm:flex-row gap-3">
            <div class="flex-1 relative">
                <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-[18px] text-gray-400">search</span>
                <input type="text" id="busca-empresa" oninput="filtrar()" placeholder="Buscar empresa por nome ou cidade…"
                    class="w-full pl-9 pr-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-[#0077fc] focus:border-transparent outline-none"/>
            </div>
            <button onclick="toggleFiltros()" id="btn-filtros"
                class="flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg border-2 border-gray-200 text-gray-600 text-sm font-medium hover:bg-gray-50 transition-colors">
                <span class="material-symbols-outlined text-[18px]">tune</span>
                Filtros
            </button>
        </div>
        <div id="painel-filtros" class="hidden px-4 pb-5 pt-1 border-t border-gray-100">
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1.5">Setor</label>
                    <select id="filtro-setor" onchange="filtrar()"
                        class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-[#0077fc] focus:border-transparent outline-none bg-white">
                        <option value="">Todos os setores</option>
                        <option value="Tecnologia">Tecnologia</option>
                        <option value="Engenharia">Engenharia</option>
                        <option value="Consultoria">Consultoria</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1.5">Cidade</label>
                    <select id="filtro-cidade" onchange="filtrar()"
                        class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-[#0077fc] focus:border-transparent outline-none bg-white">
                        <option value="">Todas as cidades</option>
                        <option value="Rio de Janeiro">Rio de Janeiro</option>
                        <option value="São Paulo">São Paulo</option>
                        <option value="Belo Horizonte">Belo Horizonte</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    <p id="contagem" class="text-sm text-gray-500 mb-4"></p>

    {{-- Grid de empresas --}}
    <div id="grid-empresas" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6"></div>

    <div id="empty-state" class="hidden bg-white rounded-xl p-12 text-center shadow-sm border border-gray-200">
        <span class="material-symbols-outlined text-gray-200 text-[56px]">business</span>
        <h3 class="text-lg font-semibold text-gray-900 mb-1 mt-4">Nenhuma empresa encontrada</h3>
        <p class="text-sm text-gray-500 mb-5">Tente ajustar os filtros para ver mais resultados</p>
        <button onclick="limparFiltros()" class="px-5 py-2.5 bg-[#0077fc] text-white rounded-lg hover:bg-[#0056c9] transition-colors text-sm font-medium">
            Limpar filtros
        </button>
    </div>
</div>

<script>
const EMPRESAS = [
    {
        nome: "Tech Solutions Ltda", cidade: "Rio de Janeiro", setor: "Tecnologia",
        cnpj: "12.345.678/0001-90", convênio: "31/12/2027",
        descricao: "Empresa especializada em desenvolvimento de software e soluções tecnológicas para o setor corporativo.",
        cor: "blue"
    },
    {
        nome: "Inovação Digital S.A.", cidade: "São Paulo", setor: "Tecnologia",
        cnpj: "98.765.432/0001-10", convênio: "30/06/2027",
        descricao: "Consultoria em transformação digital, inteligência artificial e análise de dados.",
        cor: "green"
    },
    {
        nome: "Consultoria Tech", cidade: "Belo Horizonte", setor: "Consultoria",
        cnpj: "11.222.333/0001-44", convênio: "31/03/2027",
        descricao: "Consultoria em gestão de projetos de tecnologia da informação e engenharia de software.",
        cor: "purple"
    },
    {
        nome: "Engenharia Civil RJ", cidade: "Rio de Janeiro", setor: "Engenharia",
        cnpj: "44.555.666/0001-77", convênio: "31/12/2026",
        descricao: "Construção civil e infraestrutura urbana com foco em obras de grande porte.",
        cor: "orange"
    },
    {
        nome: "Mecânica Industrial SA", cidade: "São Paulo", setor: "Engenharia",
        cnpj: "77.888.999/0001-22", convênio: "30/09/2027",
        descricao: "Fabricação de equipamentos industriais e manutenção de sistemas mecânicos.",
        cor: "red"
    },
    {
        nome: "Eletro Power Ltda", cidade: "Belo Horizonte", setor: "Engenharia",
        cnpj: "33.444.555/0001-88", convênio: "31/05/2027",
        descricao: "Geração, transmissão e distribuição de energia elétrica e automação industrial.",
        cor: "yellow"
    }
];

function corClasses(cor) {
    const map = {
        blue: ['bg-blue-100', 'text-blue-700'],
        green: ['bg-green-100', 'text-green-700'],
        purple: ['bg-purple-100', 'text-purple-700'],
        orange: ['bg-orange-100', 'text-orange-700'],
        red: ['bg-red-100', 'text-red-700'],
        yellow: ['bg-yellow-100', 'text-yellow-700']
    };
    return map[cor] || map.blue;
}

function renderCard(e) {
    const [bg, txt] = corClasses(e.cor);
    return `
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 hover:shadow-md hover:border-[#0077fc] transition-all">
        <div class="p-6">
            <div class="flex items-center justify-between mb-3">
                <div class="w-12 h-12 rounded-xl ${bg} flex items-center justify-center">
                    <span class="material-symbols-outlined ${txt} text-[24px]">business</span>
                </div>
                <span class="px-2.5 py-1 bg-green-100 text-green-700 text-xs font-medium rounded-full">Convenio ativo</span>
            </div>
            <h3 class="font-semibold text-gray-900 mb-1">${e.nome}</h3>
            <p class="text-sm text-gray-500 mb-3">${e.descricao}</p>
            <div class="flex flex-wrap gap-2 mb-4">
                <span class="inline-flex items-center gap-1 px-2.5 py-1 bg-gray-100 text-gray-600 rounded-full text-xs"><span class="material-symbols-outlined text-[14px]">location_on</span>${e.cidade}</span>
                <span class="inline-flex items-center gap-1 px-2.5 py-1 bg-gray-100 text-gray-600 rounded-full text-xs"><span class="material-symbols-outlined text-[14px]">category</span>${e.setor}</span>
            </div>
            <div class="space-y-2 border-t border-gray-100 pt-3">
                <div class="flex justify-between text-sm"><span class="text-gray-500">CNPJ</span><span class="font-medium text-gray-900">${e.cnpj}</span></div>
                <div class="flex justify-between text-sm"><span class="text-gray-500">Convênio até</span><span class="font-medium text-gray-900">${e.convênio}</span></div>
            </div>
        </div>
    </div>`;
}

function filtrar() {
    const busca = document.getElementById('busca-empresa').value.toLowerCase();
    const setor = document.getElementById('filtro-setor').value;
    const cidade = document.getElementById('filtro-cidade').value;
    const filtradas = EMPRESAS.filter(e => {
        const m1 = !busca || e.nome.toLowerCase().includes(busca) || e.cidade.toLowerCase().includes(busca);
        const m2 = !setor || e.setor === setor;
        const m3 = !cidade || e.cidade === cidade;
        return m1 && m2 && m3;
    });
    document.getElementById('grid-empresas').innerHTML = filtradas.map(renderCard).join('');
    document.getElementById('contagem').textContent = `${filtradas.length} ${filtradas.length === 1 ? 'empresa encontrada' : 'empresas encontradas'}`;
    document.getElementById('empty-state').classList.toggle('hidden', filtradas.length > 0);
    document.getElementById('grid-empresas').classList.toggle('hidden', filtradas.length === 0);
}

function toggleFiltros() {
    const painel = document.getElementById('painel-filtros');
    painel.classList.toggle('hidden');
    const btn = document.getElementById('btn-filtros');
    if (!painel.classList.contains('hidden')) {
        btn.classList.add('border-[#0077fc]', 'bg-blue-50', 'text-[#0077fc]');
        btn.classList.remove('border-gray-200', 'text-gray-600');
    } else {
        btn.classList.remove('border-[#0077fc]', 'bg-blue-50', 'text-[#0077fc]');
        btn.classList.add('border-gray-200', 'text-gray-600');
    }
}

function limparFiltros() {
    document.getElementById('busca-empresa').value = '';
    document.getElementById('filtro-setor').value = '';
    document.getElementById('filtro-cidade').value = '';
    filtrar();
}

filtrar();
</script>
@endsection
