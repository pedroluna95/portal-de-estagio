@extends('layouts.app')

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<main class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8 space-y-6">

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 border-b border-slate-200 dark:border-slate-800 pb-5">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <a href="/dashboard" class="text-xs font-semibold text-slate-500 hover:text-blue-600 dark:hover:text-blue-400 transition-colors flex items-center gap-1">
                    <span class="material-symbols-outlined text-[16px]">arrow_back</span>
                    Dashboard
                </a>
                <span class="text-slate-300 dark:text-slate-700">/</span>
                <span class="text-xs font-semibold text-blue-600 dark:text-blue-400 uppercase tracking-wider">Administração</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 dark:text-slate-100 tracking-tight">
                Inspetor de Banco de Dados
            </h1>
            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                Visualize os dados brutos e a integridade das tabelas autorizadas do sistema.
            </p>
        </div>
        <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-xl bg-slate-100 dark:bg-slate-800 text-xs font-mono text-slate-600 dark:text-slate-300">
            <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
            Modo Leitura Autorizada
        </div>
    </div>

    {{-- Painel de Seleção de Tabelas --}}
    <div class="rounded-2xl border border-slate-200/80 dark:border-slate-800 bg-white dark:bg-slate-900 p-6 shadow-sm">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-sm font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 flex items-center gap-2">
                <span class="material-symbols-outlined text-blue-600 dark:text-blue-400 text-[20px]">database</span>
                Tabelas Disponíveis
            </h2>
            <span id="tables-status" class="text-xs text-slate-500 dark:text-slate-400 font-medium">Carregando tabelas autorizadas...</span>
        </div>

        <div id="tables-list" class="flex flex-wrap gap-2.5">
            {{-- Botões das tabelas injetados dinamicamente via JS --}}
        </div>
    </div>

    {{-- Área de Visualização dos Registros da Tabela --}}
    <div id="table-results-container" class="hidden rounded-2xl border border-slate-200/80 dark:border-slate-800 bg-white dark:bg-slate-900 overflow-hidden shadow-sm">
        <div class="p-5 border-b border-slate-200 dark:border-slate-800 flex items-center justify-between bg-slate-50 dark:bg-slate-800/80">
            <div class="flex items-center gap-2">
                <span class="material-symbols-outlined text-blue-600 text-[20px]">table_rows</span>
                <span id="active-table-name" class="font-bold text-sm text-slate-900 dark:text-slate-100 font-mono"></span>
                <span id="active-table-count" class="text-xs px-2.5 py-0.5 rounded-full bg-blue-100 dark:bg-blue-950/60 text-blue-700 dark:text-blue-300 font-semibold"></span>
            </div>
            <button onclick="document.getElementById('table-results-container').classList.add('hidden')" class="p-1 rounded-lg text-slate-400 hover:text-slate-600 dark:hover:text-slate-200">
                <span class="material-symbols-outlined text-[20px]">close</span>
            </button>
        </div>
        <div class="p-5 overflow-auto max-h-[600px] bg-slate-950 text-slate-100 font-mono text-xs">
            <pre id="table-raw-json" class="leading-relaxed"></pre>
        </div>
    </div>

</main>

<script>
(async () => {
    const status = document.getElementById('tables-status');
    const list = document.getElementById('tables-list');
    const container = document.getElementById('table-results-container');
    const tableNameEl = document.getElementById('active-table-name');
    const tableCountEl = document.getElementById('active-table-count');
    const rawJsonEl = document.getElementById('table-raw-json');

    try {
        const response = await fetch('/admin/api/tables', {
            credentials: 'same-origin',
            headers: {Accept: 'application/json'}
        });
        const tables = await response.json().catch(() => null);
        if (!response.ok) throw new Error(tables?.message || 'Não foi possível carregar as tabelas.');
        if (!Array.isArray(tables)) throw new Error('Lista de tabelas inválida.');

        list.innerHTML = tables.map(table => `
            <button class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl text-xs font-semibold font-mono border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-700 dark:text-slate-200 hover:border-blue-500 hover:bg-blue-50 dark:hover:bg-blue-950/40 hover:text-blue-600 dark:hover:text-blue-400 transition-all cursor-pointer" data-table="${table}">
                <span class="material-symbols-outlined text-[16px]">storage</span>
                ${table}
            </button>
        `).join('');

        status.textContent = 'Clique em uma tabela para inspecionar os registros.';

        list.querySelectorAll('[data-table]').forEach(button => button.addEventListener('click', async () => {
            const table = button.dataset.table;
            status.textContent = `Carregando dados da tabela ${table}...`;
            button.classList.add('opacity-50');

            try {
                const res = await fetch('/admin/api/tables/' + encodeURIComponent(table), {
                    headers: {Accept: 'application/json'}
                });
                const rows = await res.json().catch(() => null);
                button.classList.remove('opacity-50');

                if (!res.ok) throw new Error(rows?.message || 'Não foi possível consultar esta tabela.');

                container.classList.remove('hidden');
                tableNameEl.textContent = table;
                tableCountEl.textContent = Array.isArray(rows) ? `${rows.length} registros` : '1 registro';
                rawJsonEl.textContent = JSON.stringify(rows, null, 2);
                status.textContent = `Exibindo tabela: ${table}`;
                container.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
            } catch (error) {
                button.classList.remove('opacity-50');
                status.textContent = error.message || 'Falha ao consultar tabela.';
            }
        }));
    } catch (error) {
        status.textContent = error.message;
    }
})();
</script>
@endsection
