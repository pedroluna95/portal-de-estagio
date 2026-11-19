@extends('layouts.app')

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<main class="mx-auto max-w-3xl px-4 py-8 sm:px-6 lg:px-8 space-y-6">

    <a href="/admin/usuarios" class="inline-flex items-center gap-1.5 text-xs font-semibold text-blue-600 dark:text-blue-400 hover:underline">
        <span class="material-symbols-outlined text-[16px]">arrow_back</span>
        Voltar à lista de usuários
    </a>

    <header class="border-b border-slate-200 dark:border-slate-800 pb-5">
        <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-blue-50 dark:bg-blue-950/60 text-blue-600 dark:text-blue-400 border border-blue-200/60 dark:border-blue-900/40 mb-2">
            Administração
        </div>
        <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 dark:text-slate-100 tracking-tight">Novo Usuário</h1>
        <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Preencha as informações cadastrais e selecione o papel de acesso no sistema.</p>
    </header>

    <section class="rounded-2xl border border-slate-200/80 dark:border-slate-800 bg-white dark:bg-slate-900 p-6 sm:p-8 shadow-sm">
        <form id="usuario-form" class="space-y-6">
            <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                <div class="sm:col-span-2">
                    <label for="nome" class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1.5">
                        Nome Completo *
                    </label>
                    <input name="nome" id="nome" autocomplete="name" placeholder="Ex: Maria Clara Silva"
                           class="block w-full rounded-xl border-slate-300 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-100 py-2.5 px-3.5 text-sm focus:border-blue-500 focus:ring-blue-500 placeholder-slate-400" required />
                </div>

                <div class="sm:col-span-2">
                    <label for="email" class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1.5">
                        E-mail Institucional ou Pessoal *
                    </label>
                    <input name="email" id="email" type="email" autocomplete="email" placeholder="usuario@ufci.br"
                           class="block w-full rounded-xl border-slate-300 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-100 py-2.5 px-3.5 text-sm focus:border-blue-500 focus:ring-blue-500 placeholder-slate-400" required />
                </div>

                <div>
                    <label for="senha" class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1.5">
                        Senha Inicial *
                    </label>
                    <input name="senha" id="senha" type="password" autocomplete="new-password" placeholder="Mínimo 6 caracteres"
                           class="block w-full rounded-xl border-slate-300 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-100 py-2.5 px-3.5 text-sm focus:border-blue-500 focus:ring-blue-500 placeholder-slate-400" required />
                </div>

                <div>
                    <label for="cpf" class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1.5">
                        CPF (somente números) *
                    </label>
                    <input name="cpf" id="cpf" inputmode="numeric" pattern="[0-9]*" maxlength="11" placeholder="12345678901"
                           class="block w-full rounded-xl border-slate-300 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-100 py-2.5 px-3.5 text-sm focus:border-blue-500 focus:ring-blue-500 placeholder-slate-400" required />
                </div>

                <div class="sm:col-span-2">
                    <label for="tipo" class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1.5">
                        Perfil de Acesso *
                    </label>
                    <select name="tipo" id="tipo" class="block w-full rounded-xl border-slate-300 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-100 py-2.5 px-3.5 text-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="aluno">Aluno</option>
                        <option value="supervisor">Supervisor de Empresa</option>
                        <option value="orientador">Professor Orientador</option>
                    </select>
                </div>

                <div id="matricula-group" class="hidden">
                    <label for="matricula" class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1.5">
                        Matrícula do Aluno
                    </label>
                    <input name="matricula" id="matricula" maxlength="30" placeholder="Ex: 20241TADS001"
                           class="block w-full rounded-xl border-slate-300 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-100 py-2.5 px-3.5 text-sm focus:border-blue-500 focus:ring-blue-500 placeholder-slate-400" />
                </div>

                <div id="curso-group" class="hidden">
                    <label for="curso" class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1.5">
                        Curso
                    </label>
                    <select name="curso" id="curso" class="block w-full rounded-xl border-slate-300 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-100 py-2.5 px-3.5 text-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="">Selecione o curso</option>
                        <option value="Informatica">Informática</option>
                        <option value="Automacao Industrial">Automação Industrial</option>
                        <option value="Telecomunicações">Telecomunicações</option>
                        <option value="Enfermagem">Enfermagem</option>
                    </select>
                </div>

                <div id="siape-group" class="hidden sm:col-span-2">
                    <label for="siape" class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1.5">
                        SIAPE do Orientador
                    </label>
                    <input name="siape" id="siape" placeholder="8 dígitos numéricos" inputmode="numeric" pattern="[0-9]*" maxlength="8"
                           class="block w-full rounded-xl border-slate-300 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-100 py-2.5 px-3.5 text-sm focus:border-blue-500 focus:ring-blue-500 placeholder-slate-400" />
                </div>
            </div>

            <div class="flex flex-col-reverse gap-3 border-t border-slate-200 dark:border-slate-800 pt-6 sm:flex-row sm:justify-end">
                <a href="/admin/usuarios" class="inline-flex justify-center rounded-xl border border-slate-300 dark:border-slate-700 px-5 py-2.5 text-sm font-semibold text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors">
                    Cancelar
                </a>
                <button type="submit" class="inline-flex justify-center items-center gap-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white px-6 py-2.5 text-sm font-semibold shadow-sm transition-colors">
                    <span class="material-symbols-outlined text-[18px]">save</span>
                    Salvar Usuário
                </button>
            </div>
        </form>
    </section>
</main>

<script src="/js/admin-usuarios.js?v=3"></script>
@endsection
