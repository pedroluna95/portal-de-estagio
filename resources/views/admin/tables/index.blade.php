@extends('layouts.app')

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<div class="container mx-auto p-6">
    <h1 class="mb-4 text-2xl font-semibold">Visualizador de tabelas autorizadas</h1>
    <div id="tables-status" class="mb-4 text-sm text-gray-600">Carregando tabelas...</div>
    <div id="tables-list" class="space-y-4"></div>
</div>
<script>
(async () => {
    const status = document.getElementById('tables-status');
    const list = document.getElementById('tables-list');
    try {
        const response = await fetch('/admin/api/tables', {
            credentials: 'same-origin',
            headers: {Accept: 'application/json'}
        });
        const tables = await response.json().catch(() => null);
        if (!response.ok) throw new Error(tables?.message || 'Não foi possível carregar as tabelas. Verifique sua conexão e tente novamente.');
        if (!Array.isArray(tables)) throw new Error('O servidor retornou uma lista de tabelas inválida.');
        list.innerHTML = tables.map(table => `<button class="mr-2 rounded bg-blue-600 px-3 py-2 text-white" data-table="${table}">${table}</button>`).join('');
        status.textContent = 'Selecione uma tabela autorizada.';
        list.querySelectorAll('[data-table]').forEach(button => button.addEventListener('click', async () => {
            try {
                const res = await fetch('/admin/api/tables/' + encodeURIComponent(button.dataset.table), {
                    headers: {Accept: 'application/json'}
                });
                const rows = await res.json().catch(() => null);
                if (!res.ok) throw new Error(rows?.message || 'Não foi possível consultar esta tabela.');
                const pre = document.createElement('pre');
                pre.className = 'mt-4 overflow-auto rounded bg-gray-100 p-4 text-sm';
                pre.textContent = JSON.stringify(rows, null, 2);
                list.appendChild(pre);
            } catch (error) {
                status.textContent = error.message || 'Falha de conexão ao consultar a tabela.';
            }
        }));
    } catch (error) { status.textContent = error.message; }
})();
</script>
@endsection
