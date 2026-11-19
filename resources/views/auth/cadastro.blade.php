<!DOCTYPE html>
<html class="light" lang="pt-br">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>NEXO - Portal de Estágio</title>
    <link rel="icon" type="image/png" href="{{ asset('documents/Nexo_Logo.png') }}"/>
    <link rel="apple-touch-icon" href="{{ asset('documents/Nexo_Logo.png') }}"/>

    {{-- Anti-FOUC para modo escuro --}}
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

    {{-- Header Minimalista --}}
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

    {{-- Área Central do Cadastro --}}
    <main class="flex-1 flex items-center justify-center p-4 py-8">
        <div class="w-full max-w-2xl">
            <div class="rounded-3xl border border-slate-200/80 dark:border-slate-800 bg-white dark:bg-slate-900 shadow-xl shadow-slate-200/50 dark:shadow-none p-8 sm:p-10">
                <div class="text-center mb-8">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-blue-50 dark:bg-blue-950/60 text-blue-600 dark:text-blue-400 mb-2">
                        Novo Cadastro
                    </span>
                    <h1 class="text-2xl sm:text-3xl font-black text-slate-900 dark:text-slate-100 tracking-tight">Criar Conta no Portal</h1>
                    <p id="subtitulo" class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 mt-1">
                        Selecione seu papel para habilitar os campos específicos do perfil
                    </p>
                </div>

                {{-- Passo 1: Seleção de Tipo de Usuário --}}
                <div id="passo-tipo" class="grid grid-cols-1 sm:grid-cols-3 gap-4 {{ $errors->any() || !empty($tipoInicial) ? 'hidden' : '' }}">
                    <a href="?tipo=aluno" onclick="selecionarTipo('aluno'); return false;" class="rounded-2xl border border-slate-200/80 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-800/40 p-5 text-center hover:border-blue-500 hover:bg-blue-50/40 dark:hover:bg-blue-950/30 transition-all group block">
                        <div class="w-12 h-12 rounded-xl bg-blue-100 dark:bg-blue-900/60 text-blue-600 dark:text-blue-400 flex items-center justify-center mx-auto mb-3 group-hover:scale-105 transition-transform">
                            <span class="material-symbols-outlined text-[26px]">school</span>
                        </div>
                        <h3 class="font-bold text-sm text-slate-900 dark:text-slate-100 mb-1">Aluno</h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400 leading-snug">Estudante em busca de estágio ou horas</p>
                    </a>

                    <a href="?tipo=supervisor" onclick="selecionarTipo('supervisor'); return false;" class="rounded-2xl border border-slate-200/80 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-800/40 p-5 text-center hover:border-purple-500 hover:bg-purple-50/40 dark:hover:bg-purple-950/30 transition-all group block">
                        <div class="w-12 h-12 rounded-xl bg-purple-100 dark:bg-purple-900/60 text-purple-600 dark:text-purple-400 flex items-center justify-center mx-auto mb-3 group-hover:scale-105 transition-transform">
                            <span class="material-symbols-outlined text-[26px]">verified_user</span>
                        </div>
                        <h3 class="font-bold text-sm text-slate-900 dark:text-slate-100 mb-1">Supervisor</h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400 leading-snug">Responsável corporativo na empresa</p>
                    </a>

                    <a href="?tipo=orientador" onclick="selecionarTipo('orientador'); return false;" class="rounded-2xl border border-slate-200/80 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-800/40 p-5 text-center hover:border-indigo-500 hover:bg-indigo-50/40 dark:hover:bg-indigo-950/30 transition-all group block">
                        <div class="w-12 h-12 rounded-xl bg-indigo-100 dark:bg-indigo-900/60 text-indigo-600 dark:text-indigo-400 flex items-center justify-center mx-auto mb-3 group-hover:scale-105 transition-transform">
                            <span class="material-symbols-outlined text-[26px]">person_pin</span>
                        </div>
                        <h3 class="font-bold text-sm text-slate-900 dark:text-slate-100 mb-1">Orientador</h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400 leading-snug">Professor docente da UFCI-RJ</p>
                    </a>
                </div>

                {{-- Passo 2: Formulário Dinâmico --}}
                <form id="passo-form" class="{{ $errors->any() || !empty($tipoInicial) ? '' : 'hidden' }} space-y-4" method="POST" action="{{ url('/cadastro') }}">
                    @csrf

                    @if($errors->any())
                        <div role="alert" class="rounded-2xl bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-900/60 p-4 text-xs font-medium text-rose-700 dark:text-rose-300">
                            <ul class="list-disc pl-4 space-y-1">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
                        </div>
                    @endif

                    <div class="flex items-center justify-between pb-2 border-b border-slate-100 dark:border-slate-800">
                        <button type="button" onclick="voltarTipo()" class="inline-flex items-center gap-1 text-xs font-semibold text-blue-600 dark:text-blue-400 hover:underline">
                            <span class="material-symbols-outlined text-[16px]">arrow_back</span>
                            Trocar perfil de cadastro
                        </button>
                        <span id="badge-tipo-selecionado" class="px-2.5 py-0.5 rounded-full text-xs font-bold uppercase tracking-wider bg-blue-100 dark:bg-blue-900/60 text-blue-700 dark:text-blue-300"></span>
                    </div>

                    <input type="hidden" name="tipo" id="tipo" value="{{ old('tipo', $tipoInicial ?? '') }}"/>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1.5">Nome Completo *</label>
                        <input type="text" name="nome" value="{{ old('nome') }}" placeholder="Ex: Maria Clara Silva" required
                               class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-100 text-sm focus:border-blue-500 focus:ring-blue-500 outline-none placeholder-slate-400"/>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1.5">E-mail *</label>
                            <input type="email" name="email" value="{{ old('email') }}" placeholder="seu.email@ufci.br" required
                                   class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-100 text-sm focus:border-blue-500 focus:ring-blue-500 outline-none placeholder-slate-400"/>
                        </div>

                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1.5">CPF (somente números) *</label>
                            <input type="text" name="cpf" value="{{ old('cpf') }}" placeholder="12345678901" inputmode="numeric" pattern="[0-9]*" maxlength="11" oninput="this.value=this.value.replace(/\D/g,'')" required
                                   class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-100 text-sm focus:border-blue-500 focus:ring-blue-500 outline-none placeholder-slate-400"/>
                        </div>
                    </div>

                    <div id="campos-especificos"></div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1.5">Senha *</label>
                            <input type="password" name="senha" id="senha" placeholder="Mínimo 6 caracteres" required
                                   class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-100 text-sm focus:border-blue-500 focus:ring-blue-500 outline-none placeholder-slate-400"/>
                        </div>

                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1.5">Confirmar Senha *</label>
                            <input type="password" name="confirmar_senha" id="confirmar_senha" placeholder="Repita a senha" required
                                   class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-100 text-sm focus:border-blue-500 focus:ring-blue-500 outline-none placeholder-slate-400"/>
                        </div>
                    </div>

                    <button type="submit" class="w-full py-3 px-4 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-sm shadow-md shadow-blue-500/20 transition-all hover:shadow-lg mt-4">
                        Finalizar e Criar Conta
                    </button>
                </form>

                <div class="mt-8 pt-6 border-t border-slate-100 dark:border-slate-800 text-center text-xs text-slate-500 dark:text-slate-400">
                    Já possui conta ativa no portal?
                    <a href="/login" class="text-blue-600 dark:text-blue-400 font-bold hover:underline ml-1">Fazer login</a>
                </div>
            </div>
        </div>
    </main>

    <footer class="p-6 text-center text-xs text-slate-400 dark:text-slate-500">
        NEXO · UFCI-RJ &copy; {{ date('Y') }}
    </footer>

    <script>
        const camposEspecificos = {
            aluno: `
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1.5">Matrícula *</label>
                        <input type="text" name="matricula" placeholder="Ex: 20241TADS001" maxlength="30" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-100 text-sm focus:border-blue-500 focus:ring-blue-500 outline-none placeholder-slate-400" required/>
                    </div>
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1.5">Curso *</label>
                        <select name="curso" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-100 text-sm focus:border-blue-500 focus:ring-blue-500 outline-none" required>
                            <option value="">Selecione o curso</option>
                            <option value="Informatica" {{ old('curso') === 'Informatica' ? 'selected' : '' }}>Informática</option>
                            <option value="Automacao Industrial" {{ old('curso') === 'Automacao Industrial' ? 'selected' : '' }}>Automação Industrial</option>
                            <option value="Telecomunicações" {{ old('curso') === 'Telecomunicações' ? 'selected' : '' }}>Telecomunicações</option>
                            <option value="Enfermagem" {{ old('curso') === 'Enfermagem' ? 'selected' : '' }}>Enfermagem</option>
                        </select>
                    </div>
                </div>
            `,
            orientador: `
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1.5">SIAPE (8 dígitos) *</label>
                    <input type="text" name="siape" placeholder="Ex: 12345678" inputmode="numeric" pattern="[0-9]*" maxlength="8" oninput="this.value=this.value.replace(/\\D/g,'')" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-100 text-sm focus:border-blue-500 focus:ring-blue-500 outline-none placeholder-slate-400" required/>
                </div>
            `,
            supervisor: ''
        };

        function selecionarTipo(tipo) {
            document.getElementById('tipo').value = tipo;
            document.getElementById('campos-especificos').innerHTML = camposEspecificos[tipo] || '';
            document.getElementById('passo-tipo').classList.add('hidden');
            document.getElementById('passo-form').classList.remove('hidden');
            const badge = document.getElementById('badge-tipo-selecionado');
            if (badge) badge.textContent = tipo;
            const subtitulo = document.getElementById('subtitulo');
            if (subtitulo) subtitulo.textContent = 'Preencha seus dados para completar o cadastro como ' + tipo;
        }

        function voltarTipo() {
            document.getElementById('passo-form').classList.add('hidden');
            document.getElementById('passo-tipo').classList.remove('hidden');
            const subtitulo = document.getElementById('subtitulo');
            if (subtitulo) subtitulo.textContent = 'Selecione seu papel para habilitar os campos específicos do perfil';
        }

        // Caso haja erro de validação com tipo pré-selecionado
        document.addEventListener('DOMContentLoaded', () => {
            const tipoAtual = "{{ old('tipo', $tipoInicial ?? '') }}";
            if (tipoAtual) {
                selecionarTipo(tipoAtual);
            }
        });
    </script>
    <script src="/js/password-toggle.js?v=2"></script>
    <script src="/js/theme-toggle.js?v=1"></script>
</body>
</html>
