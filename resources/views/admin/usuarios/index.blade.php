@extends('layouts.app')

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<main class="mx-auto max-w-7xl space-y-6 px-4 py-8 sm:px-6 lg:px-8">
    <header class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <p class="text-sm font-medium text-[#0056c9]">Administração</p>
            <h1 class="mt-1 text-2xl font-bold text-gray-900">Gerenciar usuários</h1>
            <p class="mt-1 text-sm text-gray-600">Consulte e mantenha os perfis cadastrados no portal.</p>
        </div>
        <a href="/admin/usuarios/create" class="inline-flex items-center justify-center gap-2 rounded-lg bg-[#0077fc] px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-[#0056c9]">
            <span class="material-symbols-outlined text-lg">person_add</span>
            Novo usuário
        </a>
    </header>

    <section class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm sm:p-5" aria-label="Filtros de usuários">
        <div class="grid gap-3 md:grid-cols-[minmax(0,1fr)_220px]">
            <label class="relative block">
                <span class="sr-only">Buscar usuários</span>
                <span class="material-symbols-outlined pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">search</span>
                <input id="busca-usuarios" type="search" placeholder="Buscar por nome, e-mail, CPF ou identificação" class="w-full rounded-lg border-gray-300 py-2.5 pl-10 pr-3 text-sm focus:border-[#0077fc] focus:ring-[#0077fc]" />
            </label>
            <label>
                <span class="sr-only">Filtrar por perfil</span>
                <select id="filtro-tipo" class="w-full rounded-lg border-gray-300 py-2.5 text-sm focus:border-[#0077fc] focus:ring-[#0077fc]">
            <option value="">Todos os perfis</option>
            <option value="aluno">Aluno</option>
            <option value="orientador">Orientador</option>
            <option value="supervisor">Supervisor</option>
            <option value="administrador">Administrador</option>
                </select>
            </label>
        </div>
        <p id="usuarios-resultado" class="mt-3 text-xs text-gray-500" aria-live="polite"></p>
    </section>

    <section class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-left text-sm">
                <thead class="bg-gray-50">
                <tr>
                    <th scope="col" class="whitespace-nowrap px-4 py-3 text-xs font-semibold uppercase tracking-wide text-gray-500 sm:px-5">ID</th>
                    <th scope="col" class="whitespace-nowrap px-4 py-3 text-xs font-semibold uppercase tracking-wide text-gray-500 sm:px-5">Nome</th>
                    <th scope="col" class="whitespace-nowrap px-4 py-3 text-xs font-semibold uppercase tracking-wide text-gray-500 sm:px-5">E-mail</th>
                    <th scope="col" class="whitespace-nowrap px-4 py-3 text-xs font-semibold uppercase tracking-wide text-gray-500 sm:px-5">CPF</th>
                    <th scope="col" class="whitespace-nowrap px-4 py-3 text-xs font-semibold uppercase tracking-wide text-gray-500 sm:px-5">Identificação</th>
                    <th scope="col" class="whitespace-nowrap px-4 py-3 text-xs font-semibold uppercase tracking-wide text-gray-500 sm:px-5">Perfil</th>
                    <th scope="col" class="whitespace-nowrap px-4 py-3 text-right text-xs font-semibold uppercase tracking-wide text-gray-500 sm:px-5">Ações</th>
                </tr>
                </thead>
                <tbody id="usuarios-tbody" class="divide-y divide-gray-100">
                    <tr><td colspan="7" class="px-5 py-10 text-center text-sm text-gray-500">Carregando usuários...</td></tr>
                </tbody>
            </table>
        </div>
    </section>

    <section class="rounded-xl border border-red-200 bg-red-50 p-5 sm:p-6" aria-labelledby="limpeza-titulo">
        <h2 id="limpeza-titulo" class="text-lg font-semibold text-red-900">Limpeza completa do portal</h2>
        <p class="mt-1 max-w-3xl text-sm text-red-800">
            Apaga todos os usuários, estágios, jornadas, frequências e empresas conveniadas. Somente o login do administrador atual será mantido. Esta ação não pode ser desfeita.
        </p>
        <form id="limpar-dados-form" class="mt-4 flex flex-col gap-3 sm:flex-row sm:items-end">
            <label class="block w-full max-w-md text-sm font-medium text-red-900">
                Para confirmar, digite <span class="font-bold">APAGAR TODOS OS DADOS</span>
                <input id="limpar-dados-confirmacao" name="confirmacao" autocomplete="off" class="mt-1.5 block w-full rounded-lg border-red-300 bg-white text-sm focus:border-red-500 focus:ring-red-500" required />
            </label>
            <button id="limpar-dados-botao" type="submit" disabled class="inline-flex justify-center rounded-lg bg-red-700 px-4 py-2.5 text-sm font-semibold text-white hover:bg-red-800 disabled:cursor-not-allowed disabled:opacity-50">
                Apagar dados do portal
            </button>
        </form>
        <p id="limpar-dados-resultado" class="mt-3 text-sm" role="status" aria-live="polite"></p>
    </section>
</main>

<script src="/js/admin-usuarios.js?v=3"></script>
@endsection
