@extends('layouts.app')

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<main class="mx-auto max-w-7xl space-y-6 px-4 py-8 sm:px-6 lg:px-8">

    {{-- Header de Gestão de Usuários --}}
    <header class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between border-b border-slate-200 dark:border-slate-800 pb-5">
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
                Gerenciador Geral de Usuários
            </h1>
            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                Consulte, cadastre, edite perfis e alterne o status de acesso dos usuários do portal.
            </p>
        </div>

        <div class="flex items-center gap-3">
            <a href="/admin/usuarios/create" class="inline-flex items-center justify-center gap-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white px-4 py-2.5 text-sm font-semibold shadow-sm shadow-blue-500/20 transition-all hover:shadow-md">
                <span class="material-symbols-outlined text-[20px]">person_add</span>
                Novo Usuário
            </a>
        </div>
    </header>

    {{-- Painel de Filtros e Pesquisa Dinâmica --}}
    <section class="rounded-2xl border border-slate-200/80 dark:border-slate-800 bg-white dark:bg-slate-900 p-5 shadow-sm" aria-label="Filtros de usuários">
        <div class="grid gap-3 sm:grid-cols-[1fr_220px]">
            <div class="relative">
                <span class="material-symbols-outlined pointer-events-none absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-[20px]">search</span>
                <input id="busca-usuarios" type="search" placeholder="Buscar por nome, e-mail, CPF, matrícula ou SIAPE..."
                       class="w-full rounded-xl border-slate-300 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-100 py-2.5 pl-10 pr-4 text-sm focus:border-blue-500 focus:ring-blue-500 placeholder-slate-400" />
            </div>
            <div>
                <select id="filtro-tipo" class="w-full rounded-xl border-slate-300 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-100 py-2.5 px-3 text-sm focus:border-blue-500 focus:ring-blue-500">
                    <option value="">Todos os perfis</option>
                    <option value="aluno">Aluno</option>
                    <option value="orientador">Orientador</option>
                    <option value="supervisor">Supervisor</option>
                    <option value="administrador">Administrador</option>
                </select>
            </div>
        </div>
        <div class="flex items-center justify-between mt-3 text-xs text-slate-500 dark:text-slate-400">
            <p id="usuarios-resultado" aria-live="polite">Carregando catálogo de usuários...</p>
            <span class="hidden sm:inline">Pesquisa em tempo real na API do sistema</span>
        </div>
    </section>

    {{-- Tabela Dinâmica via admin-usuarios.js --}}
    <section class="rounded-2xl border border-slate-200/80 dark:border-slate-800 bg-white dark:bg-slate-900 overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 dark:divide-slate-800 text-left text-sm">
                <thead class="bg-slate-50 dark:bg-slate-800/80">
                    <tr>
                        <th scope="col" class="px-5 py-3.5 text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">ID</th>
                        <th scope="col" class="px-5 py-3.5 text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">Usuário</th>
                        <th scope="col" class="px-5 py-3.5 text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">E-mail</th>
                        <th scope="col" class="px-5 py-3.5 text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">CPF</th>
                        <th scope="col" class="px-5 py-3.5 text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">Identificação</th>
                        <th scope="col" class="px-5 py-3.5 text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">Perfil</th>
                        <th scope="col" class="px-5 py-3.5 text-right text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">Ações</th>
                    </tr>
                </thead>
                <tbody id="usuarios-tbody" class="divide-y divide-slate-100 dark:divide-slate-800">
                    <tr>
                        <td colspan="7" class="px-5 py-12 text-center text-sm text-slate-500 dark:text-slate-400">
                            <div class="flex items-center justify-center gap-2">
                                <span class="material-symbols-outlined animate-spin text-blue-600">sync</span>
                                Carregando usuários...
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </section>

    {{-- Zona de Manutenção / Operação Crítica --}}
    <section class="rounded-2xl border border-rose-200 dark:border-rose-950/70 bg-rose-50/60 dark:bg-rose-950/20 p-6 shadow-sm" aria-labelledby="limpeza-titulo">
        <div class="flex items-start gap-4">
            <div class="p-2.5 rounded-xl bg-rose-100 dark:bg-rose-900/40 text-rose-600 dark:text-rose-400 shrink-0">
                <span class="material-symbols-outlined text-[24px]">warning</span>
            </div>
            <div class="flex-1">
                <h2 id="limpeza-titulo" class="text-base font-bold text-rose-900 dark:text-rose-300">
                    Zona de Segurança: Limpeza de Dados de Teste
                </h2>
                <p class="mt-1 text-sm text-rose-800/90 dark:text-rose-400/90 leading-relaxed max-w-3xl">
                    Apaga dados secundários (estágios de teste, jornadas, presenças e empresas), mantendo a conta de administrador atual. Utilize apenas para reiniciar o ambiente. Esta ação é irreversível.
                </p>

                <form id="limpar-dados-form" class="mt-4 flex flex-col sm:flex-row sm:items-end gap-3 max-w-xl">
                    <label class="block flex-1 text-xs font-bold uppercase tracking-wider text-rose-900 dark:text-rose-300">
                        Confirmação de segurança: digite <span class="text-rose-600 dark:text-rose-400 underline">APAGAR TODOS OS DADOS</span>
                        <input id="limpar-dados-confirmacao" name="confirmacao" autocomplete="off"
                               placeholder="APAGAR TODOS OS DADOS"
                               class="mt-1.5 block w-full rounded-xl border-rose-300 dark:border-rose-900 dark:bg-slate-900 dark:text-rose-200 text-sm focus:border-rose-500 focus:ring-rose-500" required />
                    </label>
                    <button id="limpar-dados-botao" type="submit" disabled
                            class="inline-flex justify-center items-center gap-1.5 rounded-xl bg-rose-600 hover:bg-rose-700 text-white px-5 py-2.5 text-sm font-semibold transition-all disabled:opacity-40 disabled:cursor-not-allowed">
                        <span class="material-symbols-outlined text-[18px]">delete_forever</span>
                        Executar Limpeza
                    </button>
                </form>
            </div>
        </div>
    </section>

</main>

<script src="/js/admin-usuarios.js?v=3"></script>
@endsection
