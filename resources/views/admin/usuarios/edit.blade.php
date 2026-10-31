@extends('layouts.app')

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<div class="container mx-auto p-6">
    <h1 class="text-2xl font-semibold mb-4">Editar Usuário</h1>

    <div class="bg-white shadow rounded p-6">
        <form id="usuario-form" data-user-id="{{ request()->segment(3) }}">
            <div class="grid grid-cols-1 gap-4">
                <input name="nome" id="nome" placeholder="Nome" class="border p-2" required />
                <input name="email" id="email" placeholder="Email" class="border p-2" required />
                <input name="senha" id="senha" type="password" placeholder="Senha (deixe em branco para manter)" class="border p-2" />
                <input name="cpf" id="cpf" placeholder="Somente números" inputmode="numeric" pattern="[0-9]*" maxlength="11" oninput="this.value=this.value.replace(/\D/g,'')" class="border p-2" />
                <select name="tipo" id="tipo" class="border p-2">
                    <option value="aluno">Aluno</option>
                    <option value="supervisor">Supervisor</option>
                    <option value="orientador">Orientador</option>
                </select>
                <div id="matricula-group" class="hidden">
                    <input name="matricula" id="matricula" placeholder="Matrícula" class="border p-2" maxlength="30" />
                </div>
                <div id="curso-group" class="hidden">
                    <select name="curso" id="curso" class="border p-2">
                        <option value="">Selecione o curso</option>
                        <option value="Informatica">Informática</option>
                        <option value="Automacao Industrial">Automação Industrial</option>
                        <option value="Telecomunicações">Telecomunicações</option>
                        <option value="Enfermagem">Enfermagem</option>
                    </select>
                </div>
                <div id="siape-group" class="hidden">
                    <input name="siape" id="siape" placeholder="SIAPE (8 dígitos)" inputmode="numeric" pattern="[0-9]*" maxlength="8" oninput="this.value=this.value.replace(/\D/g,'')" class="border p-2" />
                </div>
                <div class="flex items-center gap-3">
                    <button type="submit" class="bg-primary text-white px-4 py-2 rounded">Salvar</button>
                    <a href="/admin/usuarios" class="text-gray-600">Cancelar</a>
                </div>
            </div>
        </form>
    </div>
</div>

<script src="/js/admin-usuarios.js?v=1"></script>
@endsection
