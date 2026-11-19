@extends('layouts.app')

@section('content')
<main class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">

    {{-- Topo do Perfil --}}
    <div class="border-b border-slate-200 dark:border-slate-800 pb-5">
        <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 dark:text-slate-100 tracking-tight">
            Meu Perfil
        </h1>
        <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
            Visualize e atualize suas credenciais, dados pessoais e senha de acesso.
        </p>
    </div>

    @if(session('success'))
        <div role="status" class="rounded-2xl border border-emerald-200 dark:border-emerald-900/60 bg-emerald-50 dark:bg-emerald-950/40 p-4 text-sm font-semibold text-emerald-800 dark:text-emerald-300 flex items-center gap-2">
            <span class="material-symbols-outlined text-[20px]">check_circle</span>
            {{ session('success') }}
        </div>
    @endif
    @if($errors->any())
        <div role="alert" class="rounded-2xl border border-rose-200 dark:border-rose-900/60 bg-rose-50 dark:bg-rose-950/40 p-4 text-sm text-rose-800 dark:text-rose-300">
            <ul class="list-disc space-y-1 pl-5">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Card de Identificação do Usuário --}}
        <div class="rounded-2xl border border-slate-200/80 dark:border-slate-800 bg-white dark:bg-slate-900 p-6 text-center shadow-sm flex flex-col justify-between">
            <div>
                <div class="w-24 h-24 rounded-3xl bg-gradient-to-tr from-blue-600 to-indigo-600 flex items-center justify-center text-white text-3xl font-black mx-auto mb-4 shadow-md shadow-blue-500/20">
                    {{ strtoupper(substr(session('user_name', 'U'), 0, 1)) }}
                </div>
                <h2 class="text-lg font-bold text-slate-900 dark:text-slate-100">{{ session('user_name', 'Usuário') }}</h2>
                <div class="space-y-0.5 mt-1 mb-4 text-xs text-slate-500 dark:text-slate-400">
                    <p class="truncate">{{ session('user_email', 'email@exemplo.com') }}</p>
                    <p class="font-mono">{{ session('user_cpf', 'CPF não informado') }}</p>
                </div>
                <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-blue-50 dark:bg-blue-950/60 text-blue-700 dark:text-blue-300 border border-blue-200/60 dark:border-blue-900/40 rounded-full text-xs font-semibold uppercase tracking-wider mb-6">
                    <span class="material-symbols-outlined text-[15px]">badge</span>
                    {{ session('user_type', 'aluno') }}
                </span>
            </div>

            <div class="space-y-3 border-t border-slate-100 dark:border-slate-800 pt-4 text-left text-xs">
                <div class="flex justify-between items-center text-slate-500 dark:text-slate-400">
                    <span>Matrícula / SIAPE</span>
                    <span class="font-mono font-semibold text-slate-900 dark:text-slate-100">{{ session('user_matricula', session('user_siape', '—')) }}</span>
                </div>
                @unless(session('user_type') === 'administrador')
                <div class="flex justify-between items-center text-slate-500 dark:text-slate-400">
                    <span>Curso Acadêmico</span>
                    <span class="font-semibold text-slate-900 dark:text-slate-100">{{ ['Informatica' => 'Informática', 'Automacao Industrial' => 'Automação Industrial'][$cursoAluno ?? ''] ?? $cursoAluno ?? '—' }}</span>
                </div>
                <div class="flex justify-between items-center text-slate-500 dark:text-slate-400">
                    <span>Período Atual</span>
                    <span class="font-semibold text-slate-900 dark:text-slate-100">{{ session('user_periodo', '—') }}</span>
                </div>
                @endunless
            </div>
        </div>

        {{-- Formulários de Atualização --}}
        <div class="lg:col-span-2 space-y-6">
            {{-- Dados Pessoais --}}
            <div class="rounded-2xl border border-slate-200/80 dark:border-slate-800 bg-white dark:bg-slate-900 p-6 sm:p-7 shadow-sm">
                <h3 class="text-base font-bold text-slate-900 dark:text-slate-100 mb-1 flex items-center gap-2">
                    <span class="material-symbols-outlined text-blue-600 dark:text-blue-400 text-[20px]">person</span>
                    Informações Pessoais
                </h3>
                <p class="text-xs text-slate-500 dark:text-slate-400 mb-5">Mantenha seu nome e e-mail sempre atualizados para recebimento de notificações.</p>

                <form action="{{ url('/perfil') }}" method="POST" class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    @csrf
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1.5">Nome Completo</label>
                        <input type="text" name="nome" value="{{ session('user_name', '') }}" required
                               class="w-full px-3.5 py-2.5 rounded-xl border-slate-300 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-100 text-sm focus:border-blue-500 focus:ring-blue-500"/>
                    </div>
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1.5">E-mail Principal</label>
                        <input type="email" name="email" value="{{ session('user_email', '') }}" required
                               class="w-full px-3.5 py-2.5 rounded-xl border-slate-300 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-100 text-sm focus:border-blue-500 focus:ring-blue-500"/>
                    </div>
                    <div class="sm:col-span-2 flex justify-end pt-2">
                        <button type="submit" class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-xs font-semibold shadow-sm transition-colors">
                            Salvar Alterações
                        </button>
                    </div>
                </form>
            </div>

            {{-- Segurança / Senha --}}
            <div class="rounded-2xl border border-slate-200/80 dark:border-slate-800 bg-white dark:bg-slate-900 p-6 sm:p-7 shadow-sm">
                <h3 class="text-base font-bold text-slate-900 dark:text-slate-100 mb-1 flex items-center gap-2">
                    <span class="material-symbols-outlined text-blue-600 dark:text-blue-400 text-[20px]">lock_reset</span>
                    Segurança e Senha
                </h3>
                <p class="text-xs text-slate-500 dark:text-slate-400 mb-5">Altere sua senha de acesso periodicamente para manter sua conta protegida.</p>

                <form action="{{ url('/perfil/senha') }}" method="POST" class="space-y-4 max-w-lg">
                    @csrf
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1.5">Senha Atual</label>
                        <input type="password" name="senha_atual" required
                               class="w-full px-3.5 py-2.5 rounded-xl border-slate-300 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-100 text-sm focus:border-blue-500 focus:ring-blue-500"/>
                    </div>
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1.5">Nova Senha</label>
                        <input type="password" name="nova_senha" id="nova_senha" required
                               class="w-full px-3.5 py-2.5 rounded-xl border-slate-300 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-100 text-sm focus:border-blue-500 focus:ring-blue-500"/>
                    </div>
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1.5">Confirmar Nova Senha</label>
                        <input type="password" name="nova_senha_confirmation" required
                               class="w-full px-3.5 py-2.5 rounded-xl border-slate-300 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-100 text-sm focus:border-blue-500 focus:ring-blue-500"/>
                    </div>
                    <div class="pt-2">
                        <button type="submit" class="px-5 py-2.5 bg-slate-900 dark:bg-slate-800 hover:bg-slate-800 dark:hover:bg-slate-700 text-white rounded-xl text-xs font-semibold shadow-sm transition-colors">
                            Atualizar Senha
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</main>
@endsection
