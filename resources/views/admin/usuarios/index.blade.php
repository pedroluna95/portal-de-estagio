@extends('layouts.app')

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<div class="container mx-auto p-6">
    <div class="flex items-center justify-between mb-4">
        <h1 class="text-2xl font-semibold">Gerenciar Usuários</h1>
        <a href="/admin/usuarios/create" class="bg-primary text-white px-4 py-2 rounded">Novo Usuário</a>
    </div>

    <div class="mb-4 flex flex-col md:flex-row md:items-center gap-3 bg-white p-4 rounded shadow-sm border border-gray-200">
        <input id="busca-usuarios" type="text" placeholder="Buscar por nome, email, CPF ou identificador" class="flex-1 border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-[#0077fc] focus:border-transparent outline-none" />
        <select id="filtro-tipo" class="border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-[#0077fc] focus:border-transparent outline-none">
            <option value="">Todos os perfis</option>
            <option value="aluno">Aluno</option>
            <option value="orientador">Orientador</option>
            <option value="supervisor">Supervisor</option>
            <option value="administrador">Administrador</option>
        </select>
    </div>

    <div class="overflow-x-auto bg-white shadow rounded">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">ID</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nome</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Email</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">CPF</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Identificação</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tipo</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Ações</th>
                </tr>
            </thead>
            <tbody id="usuarios-tbody" class="bg-white divide-y divide-gray-200"></tbody>
        </table>
    </div>
</div>

<script src="/js/admin-usuarios.js?v=1"></script>
@endsection
