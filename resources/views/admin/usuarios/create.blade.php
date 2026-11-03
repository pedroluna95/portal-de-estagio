@extends('layouts.app')

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<main class="mx-auto max-w-3xl px-4 py-8 sm:px-6 lg:px-8">
    <a href="/admin/usuarios" class="mb-5 inline-flex items-center gap-1 text-sm font-medium text-[#0056c9] hover:underline">
        <span class="material-symbols-outlined text-lg">arrow_back</span>Voltar aos usuários
    </a>
    <header class="mb-6">
        <p class="text-sm font-medium text-[#0056c9]">Administração</p>
        <h1 class="mt-1 text-2xl font-bold text-gray-900">Novo usuário</h1>
        <p class="mt-1 text-sm text-gray-600">Preencha os dados e selecione o perfil de acesso.</p>
    </header>

    <section class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm sm:p-7">
        <form id="usuario-form" class="space-y-5">
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <label class="text-sm font-medium text-gray-700 sm:col-span-2">Nome completo
                    <input name="nome" id="nome" autocomplete="name" class="mt-1.5 block w-full rounded-lg border-gray-300 text-sm focus:border-[#0077fc] focus:ring-[#0077fc]" required />
                </label>
                <label class="text-sm font-medium text-gray-700 sm:col-span-2">E-mail
                    <input name="email" id="email" type="email" autocomplete="email" class="mt-1.5 block w-full rounded-lg border-gray-300 text-sm focus:border-[#0077fc] focus:ring-[#0077fc]" required />
                </label>
                <label class="text-sm font-medium text-gray-700">Senha
                    <input name="senha" id="senha" type="password" autocomplete="new-password" class="mt-1.5 block w-full rounded-lg border-gray-300 text-sm focus:border-[#0077fc] focus:ring-[#0077fc]" required />
                </label>
                <label class="text-sm font-medium text-gray-700">CPF
                    <input name="cpf" id="cpf" inputmode="numeric" pattern="[0-9]*" maxlength="11" placeholder="Somente números" class="mt-1.5 block w-full rounded-lg border-gray-300 text-sm focus:border-[#0077fc] focus:ring-[#0077fc]" required />
                </label>
                <label class="text-sm font-medium text-gray-700 sm:col-span-2">Perfil
                <select name="tipo" id="tipo" class="mt-1.5 block w-full rounded-lg border-gray-300 text-sm focus:border-[#0077fc] focus:ring-[#0077fc]">
                    <option value="aluno">Aluno</option>
                    <option value="supervisor">Supervisor</option>
                    <option value="orientador">Orientador</option>
                </select>
                </label>
                <div id="matricula-group" class="hidden">
                    <label class="text-sm font-medium text-gray-700">Matrícula
                        <input name="matricula" id="matricula" maxlength="30" class="mt-1.5 block w-full rounded-lg border-gray-300 text-sm focus:border-[#0077fc] focus:ring-[#0077fc]" />
                    </label>
                </div>
                <div id="curso-group" class="hidden">
                    <label class="text-sm font-medium text-gray-700">Curso
                    <select name="curso" id="curso" class="mt-1.5 block w-full rounded-lg border-gray-300 text-sm focus:border-[#0077fc] focus:ring-[#0077fc]">
                        <option value="">Selecione o curso</option>
                        <option value="Informatica">Informática</option>
                        <option value="Automacao Industrial">Automação Industrial</option>
                        <option value="Telecomunicações">Telecomunicações</option>
                        <option value="Enfermagem">Enfermagem</option>
                    </select>
                    </label>
                </div>
                <div id="siape-group" class="hidden">
                    <label class="text-sm font-medium text-gray-700">SIAPE
                        <input name="siape" id="siape" placeholder="8 dígitos" inputmode="numeric" pattern="[0-9]*" maxlength="8" class="mt-1.5 block w-full rounded-lg border-gray-300 text-sm focus:border-[#0077fc] focus:ring-[#0077fc]" />
                    </label>
                </div>
            </div>
            <div class="flex flex-col-reverse gap-3 border-t border-gray-100 pt-5 sm:flex-row sm:justify-end">
                <a href="/admin/usuarios" class="inline-flex justify-center rounded-lg border border-gray-300 px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50">Cancelar</a>
                <button type="submit" class="inline-flex justify-center rounded-lg bg-[#0077fc] px-5 py-2.5 text-sm font-semibold text-white hover:bg-[#0056c9]">Salvar usuário</button>
            </div>
        </form>
    </section>
</main>

<script src="/js/admin-usuarios.js?v=2"></script>
@endsection
