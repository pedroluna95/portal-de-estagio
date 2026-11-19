<!DOCTYPE html>
<html class="light" lang="pt-br">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>NEXO - Portal de Estágio</title>
    <link rel="icon" type="image/png" href="{{ asset('documents/Nexo_Logo.png') }}"/>
    <link rel="apple-touch-icon" href="{{ asset('documents/Nexo_Logo.png') }}"/>

    {{-- Anti-FOUC para tema escuro --}}
    <script>
        (function () {
            var saved = localStorage.getItem('nexo-theme');
            var prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
            if (saved === 'dark' || (!saved && prefersDark)) {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
            }
        })();
    </script>

    @vite('resources/css/app.css')
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
</head>
<body class="min-h-screen bg-slate-50 dark:bg-slate-950 text-slate-900 dark:text-slate-100 flex flex-col justify-between transition-colors duration-200">

    {{-- Header Minimalista da Autenticação --}}
    <header class="p-6 flex justify-between items-center max-w-7xl mx-auto w-full">
        <a href="/home" class="flex items-center gap-2.5">
            <div class="h-10 w-10 rounded-xl bg-gradient-to-tr from-blue-600 to-indigo-600 flex items-center justify-center p-2 shadow-md shadow-blue-500/20">
                <img src="{{ asset('documents/Nexo_Logo.png') }}" alt="Logo NEXO" class="h-full w-full object-contain brightness-0 invert">
            </div>
            <span class="text-xl font-black bg-gradient-to-r from-blue-600 to-indigo-600 bg-clip-text text-transparent">NEXO</span>
        </a>

        <div class="flex items-center gap-2">
            <button data-theme-toggle type="button" class="p-2.5 rounded-xl text-slate-500 dark:text-slate-400 hover:bg-slate-200/60 dark:hover:bg-slate-800 transition-colors" title="Alternar tema">
                <span class="material-symbols-outlined text-[20px] theme-icon">dark_mode</span>
            </button>
            <a href="/tutorial" class="text-xs font-semibold text-slate-600 dark:text-slate-300 hover:text-blue-600 px-3 py-2">Tutorial</a>
            <a href="/empresas" class="text-xs font-semibold text-slate-600 dark:text-slate-300 hover:text-blue-600 px-3 py-2">Empresas</a>
        </div>
    </header>

    {{-- Conteúdo Centralizado --}}
    <main class="flex-1 flex items-center justify-center p-4 py-8">
        <div class="w-full max-w-md">
            {{-- Card de Login --}}
            <div class="rounded-3xl border border-slate-200/80 dark:border-slate-800 bg-white dark:bg-slate-900 shadow-xl shadow-slate-200/50 dark:shadow-none p-8 sm:p-10">
                <div class="text-center mb-8">
                    <h1 class="text-2xl sm:text-3xl font-black text-slate-900 dark:text-slate-100 tracking-tight">
                        Acesse sua conta
                    </h1>
                    <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 mt-1">
                        Informe seu e-mail e senha cadastrados no portal
                    </p>
                </div>

                @if($errors->any())
                    <div role="alert" class="mb-6 rounded-2xl bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-900/60 p-4 text-xs font-medium text-rose-700 dark:text-rose-300">
                        <ul class="list-disc pl-4 space-y-1">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
                    </div>
                @endif
                @if(session('success'))
                    <div role="status" class="mb-6 rounded-2xl bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-900/60 p-4 text-xs font-medium text-emerald-700 dark:text-emerald-300">
                        {{ session('success') }}
                    </div>
                @endif

                <form id="form-login" method="POST" action="{{ url('/login') }}" class="space-y-5">
                    @csrf
                    {{-- Campo E-mail --}}
                    <div>
                        <label for="email" class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1.5">E-mail</label>
                        <div class="relative">
                            <span class="material-symbols-outlined pointer-events-none absolute left-3.5 top-1/2 -translate-y-1/2 text-[18px] text-slate-400">mail</span>
                            <input type="email" name="email" id="email"
                                   class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-100 text-sm focus:border-blue-500 focus:ring-blue-500 outline-none transition-all placeholder-slate-400"
                                   placeholder="seu.email@ufci.br" value="{{ old('email') }}" autocomplete="email" required/>
                        </div>
                    </div>

                    {{-- Campo Senha --}}
                    <div>
                        <label for="senha" class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1.5">Senha</label>
                        <div class="relative">
                            <span class="material-symbols-outlined pointer-events-none absolute left-3.5 top-1/2 -translate-y-1/2 text-[18px] text-slate-400">lock</span>
                            <input type="password" name="senha" id="senha"
                                   class="w-full pl-10 pr-11 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-100 text-sm focus:border-blue-500 focus:ring-blue-500 outline-none transition-all placeholder-slate-400"
                                   placeholder="••••••••" required/>
                            <button type="button" onclick="toggleSenha('senha', 'icone-senha')"
                                    class="absolute right-3.5 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 dark:hover:text-slate-300 focus:outline-none cursor-pointer flex items-center justify-center p-1"
                                    aria-label="Alternar visibilidade da senha">
                                <span id="icone-senha" class="material-symbols-outlined text-[20px] select-none">visibility</span>
                            </button>
                        </div>
                    </div>

                    {{-- Botão de Login --}}
                    <button type="submit" id="btn-entrar"
                            class="w-full py-3 px-4 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-sm shadow-md shadow-blue-500/20 transition-all hover:shadow-lg disabled:opacity-50">
                        Entrar na Plataforma
                    </button>
                </form>

                {{-- Rodapé do Card --}}
                <div class="mt-8 pt-6 border-t border-slate-100 dark:border-slate-800 text-center text-xs text-slate-500 dark:text-slate-400">
                    Ainda não possui uma conta?
                    <a href="/cadastro" class="text-blue-600 dark:text-blue-400 font-bold hover:underline ml-1">Cadastre-se gratuitamente</a>
                </div>
            </div>
        </div>
    </main>

    {{-- Footer Simples --}}
    <footer class="p-6 text-center text-xs text-slate-400 dark:text-slate-500">
        NEXO · UFCI-RJ &copy; {{ date('Y') }}
    </footer>

    <script src="/js/password-toggle.js?v=2"></script>
    <script src="/js/theme-toggle.js?v=1"></script>
</body>
</html>
