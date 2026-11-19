@php
    $rotaAtual = request()->path();
    $ehAdmin = session('user_type') === 'administrador';
@endphp

<!-- Header Global Moderno NEXO -->
<header class="sticky top-0 z-50 transition-colors duration-200 border-b border-slate-200/80 dark:border-slate-800/80 bg-white/90 dark:bg-slate-900/90 backdrop-blur-md">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-16 sm:h-20">

            {{-- Marca e Logotipo --}}
            <div class="flex items-center gap-6 lg:gap-8">
                <a href="{{ session('user_id') ? '/dashboard' : '/home' }}" class="flex items-center gap-3 group focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 rounded-xl p-1">
                    <div class="h-10 w-10 sm:h-11 sm:w-11 rounded-xl bg-gradient-to-tr from-blue-600 to-indigo-600 flex items-center justify-center p-2 shadow-md shadow-blue-500/20 group-hover:scale-105 transition-transform duration-200">
                        <img src="{{ asset('documents/Nexo_Logo.png') }}" alt="Logo NEXO" class="h-full w-full object-contain brightness-0 invert">
                    </div>
                    <div class="flex flex-col">
                        <span class="text-xl sm:text-2xl font-black tracking-tight bg-gradient-to-r from-blue-600 to-indigo-600 dark:from-blue-400 dark:to-indigo-400 bg-clip-text text-transparent">
                            NEXO
                        </span>
                        <span class="text-[10px] font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-widest -mt-1 hidden sm:block">
                            Portal de Estágio
                        </span>
                    </div>
                </a>

                {{-- Navegação Principal Desktop (Empresas e Tutorial SEMPRE visíveis) --}}
                <nav class="hidden md:flex items-center gap-1 sm:gap-2">
                    {{-- Tutorial - Sempre presente e obrigatório --}}
                    <a href="/tutorial"
                       class="relative px-3.5 py-2 rounded-lg text-sm font-semibold transition-all duration-150 flex items-center gap-1.5
                              {{ request()->is('tutorial*')
                                  ? 'text-blue-600 dark:text-blue-400 bg-blue-50 dark:bg-blue-950/60 shadow-sm'
                                  : 'text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-800/60' }}">
                        <span class="material-symbols-outlined text-[20px] {{ request()->is('tutorial*') ? 'text-blue-600 dark:text-blue-400' : 'text-slate-400 dark:text-slate-500' }}">school</span>
                        Tutorial
                        @if(request()->is('tutorial*'))
                            <span class="absolute bottom-0 left-3 right-3 h-0.5 bg-blue-600 dark:bg-blue-400 rounded-full"></span>
                        @endif
                    </a>

                    {{-- Empresas - Sempre presente e obrigatório --}}
                    <a href="/empresas"
                       class="relative px-3.5 py-2 rounded-lg text-sm font-semibold transition-all duration-150 flex items-center gap-1.5
                              {{ request()->is('empresas*')
                                  ? 'text-blue-600 dark:text-blue-400 bg-blue-50 dark:bg-blue-950/60 shadow-sm'
                                  : 'text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-800/60' }}">
                        <span class="material-symbols-outlined text-[20px] {{ request()->is('empresas*') ? 'text-blue-600 dark:text-blue-400' : 'text-slate-400 dark:text-slate-500' }}">domain</span>
                        Empresas
                        @if(request()->is('empresas*'))
                            <span class="absolute bottom-0 left-3 right-3 h-0.5 bg-blue-600 dark:bg-blue-400 rounded-full"></span>
                        @endif
                    </a>

                    {{-- Menus específicos por papel autenticado --}}
                    @if(session('user_id'))
                        @if(session('user_type') === 'aluno')
                            <a href="/controle-horas"
                               class="relative px-3.5 py-2 rounded-lg text-sm font-semibold transition-all duration-150 flex items-center gap-1.5
                                      {{ request()->is('controle-horas*')
                                          ? 'text-blue-600 dark:text-blue-400 bg-blue-50 dark:bg-blue-950/60 shadow-sm'
                                          : 'text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-800/60' }}">
                                <span class="material-symbols-outlined text-[20px] {{ request()->is('controle-horas*') ? 'text-blue-600 dark:text-blue-400' : 'text-slate-400 dark:text-slate-500' }}">timer</span>
                                Controle de Horas
                            </a>
                        @elseif(session('user_type') === 'orientador')
                            <a href="/estagios"
                               class="relative px-3.5 py-2 rounded-lg text-sm font-semibold transition-all duration-150 flex items-center gap-1.5
                                      {{ request()->is('estagios*')
                                          ? 'text-blue-600 dark:text-blue-400 bg-blue-50 dark:bg-blue-950/60 shadow-sm'
                                          : 'text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-800/60' }}">
                                <span class="material-symbols-outlined text-[20px] {{ request()->is('estagios*') ? 'text-blue-600 dark:text-blue-400' : 'text-slate-400 dark:text-slate-500' }}">assignment</span>
                                Meus Estágios
                            </a>
                        @elseif(session('user_type') === 'supervisor')
                            <a href="/estagios"
                               class="relative px-3.5 py-2 rounded-lg text-sm font-semibold transition-all duration-150 flex items-center gap-1.5
                                      {{ request()->is('estagios*')
                                          ? 'text-blue-600 dark:text-blue-400 bg-blue-50 dark:bg-blue-950/60 shadow-sm'
                                          : 'text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-800/60' }}">
                                <span class="material-symbols-outlined text-[20px] {{ request()->is('estagios*') ? 'text-blue-600 dark:text-blue-400' : 'text-slate-400 dark:text-slate-500' }}">fact_check</span>
                                Validar Presenças
                            </a>
                        @elseif(session('user_type') === 'administrador')
                            <a href="/dashboard"
                               class="relative px-3.5 py-2 rounded-lg text-sm font-semibold transition-all duration-150 flex items-center gap-1.5
                                      {{ request()->is('dashboard*') || request()->is('admin*')
                                          ? 'text-blue-600 dark:text-blue-400 bg-blue-50 dark:bg-blue-950/60 shadow-sm'
                                          : 'text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-800/60' }}">
                                <span class="material-symbols-outlined text-[20px] {{ request()->is('dashboard*') ? 'text-blue-600 dark:text-blue-400' : 'text-slate-400 dark:text-slate-500' }}">admin_panel_settings</span>
                                Painel Admin
                            </a>
                        @endif
                    @endif
                </nav>
            </div>

            {{-- Lado Direito: Ações / Usuário / Dark Mode --}}
            <div class="flex items-center gap-2 sm:gap-3">

                {{-- Toggle de Tema (Dark / Light) --}}
                <button data-theme-toggle
                        type="button"
                        class="p-2 sm:p-2.5 rounded-xl text-slate-500 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-100 hover:bg-slate-100 dark:hover:bg-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500/20 transition-all"
                        aria-label="Alternar tema claro/escuro"
                        title="Alternar modo claro e escuro">
                    <span class="material-symbols-outlined text-[22px] theme-icon">dark_mode</span>
                </button>

                @if(session('user_id'))
                    {{-- Perfil do Usuário Logado --}}
                    <div class="hidden sm:flex items-center gap-2 border-l border-slate-200 dark:border-slate-800 pl-3">
                        <a href="/perfil" class="flex items-center gap-2.5 p-1.5 pr-3 rounded-xl hover:bg-slate-100 dark:hover:bg-slate-800/80 transition-all group">
                            <div class="w-9 h-9 rounded-xl bg-gradient-to-tr from-blue-600 to-indigo-600 text-white font-bold text-sm flex items-center justify-center shadow-sm shadow-blue-500/20 group-hover:scale-105 transition-transform">
                                {{ strtoupper(substr(session('user_name', 'U'), 0, 1)) }}
                            </div>
                            <div class="text-left hidden lg:block leading-tight">
                                <div class="text-sm font-semibold text-slate-800 dark:text-slate-200 group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors">
                                    {{ session('user_name', 'Usuário') }}
                                </div>
                                <div class="text-xs text-slate-500 dark:text-slate-400 capitalize font-medium">
                                    {{ session('user_type', 'aluno') }}
                                </div>
                            </div>
                        </a>

                        <form method="POST" action="{{ url('/logout') }}" class="inline">
                            @csrf
                            <button type="submit"
                                    class="p-2 sm:p-2.5 rounded-xl text-slate-400 hover:text-red-600 hover:bg-red-50 dark:hover:bg-red-950/40 dark:hover:text-red-400 transition-colors"
                                    title="Sair da conta"
                                    aria-label="Sair">
                                <span class="material-symbols-outlined text-[22px]">logout</span>
                            </button>
                        </form>
                    </div>
                @else
                    {{-- Botões para Visitantes --}}
                    <div class="hidden sm:flex items-center gap-2 border-l border-slate-200 dark:border-slate-800 pl-3">
                        <a href="/login" class="px-4 py-2 text-sm font-semibold text-slate-700 dark:text-slate-200 hover:text-blue-600 dark:hover:text-blue-400 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-xl transition-all">
                            Entrar
                        </a>
                        <a href="/cadastro" class="px-4 py-2 text-sm font-semibold text-white bg-blue-600 hover:bg-blue-700 dark:bg-blue-500 dark:hover:bg-blue-600 rounded-xl shadow-sm shadow-blue-500/20 transition-all">
                            Cadastrar
                        </a>
                    </div>
                @endif

                {{-- Botão Menu Mobile --}}
                <div class="flex items-center md:hidden">
                    <button type="button"
                            onclick="document.getElementById('mobile-menu').classList.toggle('hidden')"
                            class="p-2 rounded-xl text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 focus:outline-none"
                            aria-label="Abrir menu principal">
                        <span class="material-symbols-outlined text-[26px]">menu</span>
                    </button>
                </div>
            </div>
        </div>

        {{-- Menu Responsivo Mobile --}}
        <div id="mobile-menu" class="hidden md:hidden py-4 border-t border-slate-200/80 dark:border-slate-800/80 space-y-1">
            {{-- Empresas e Tutorial Obrigatórios no Mobile --}}
            <a href="/tutorial"
               class="flex items-center gap-3 px-4 py-2.5 rounded-xl text-base font-semibold
                      {{ request()->is('tutorial*')
                          ? 'text-blue-600 dark:text-blue-400 bg-blue-50 dark:bg-blue-950/60'
                          : 'text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                <span class="material-symbols-outlined text-[22px] {{ request()->is('tutorial*') ? 'text-blue-600 dark:text-blue-400' : 'text-slate-400' }}">school</span>
                Tutorial
            </a>
            <a href="/empresas"
               class="flex items-center gap-3 px-4 py-2.5 rounded-xl text-base font-semibold
                      {{ request()->is('empresas*')
                          ? 'text-blue-600 dark:text-blue-400 bg-blue-50 dark:bg-blue-950/60'
                          : 'text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                <span class="material-symbols-outlined text-[22px] {{ request()->is('empresas*') ? 'text-blue-600 dark:text-blue-400' : 'text-slate-400' }}">domain</span>
                Empresas
            </a>

            @if(session('user_id'))
                @if(session('user_type') === 'aluno')
                    <a href="/controle-horas"
                       class="flex items-center gap-3 px-4 py-2.5 rounded-xl text-base font-semibold
                              {{ request()->is('controle-horas*')
                                  ? 'text-blue-600 dark:text-blue-400 bg-blue-50 dark:bg-blue-950/60'
                                  : 'text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                        <span class="material-symbols-outlined text-[22px] text-slate-400">timer</span>
                        Controle de Horas
                    </a>
                @elseif(session('user_type') === 'orientador')
                    <a href="/estagios"
                       class="flex items-center gap-3 px-4 py-2.5 rounded-xl text-base font-semibold
                              {{ request()->is('estagios*')
                                  ? 'text-blue-600 dark:text-blue-400 bg-blue-50 dark:bg-blue-950/60'
                                  : 'text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                        <span class="material-symbols-outlined text-[22px] text-slate-400">assignment</span>
                        Meus Estágios
                    </a>
                @elseif(session('user_type') === 'supervisor')
                    <a href="/estagios"
                       class="flex items-center gap-3 px-4 py-2.5 rounded-xl text-base font-semibold
                              {{ request()->is('estagios*')
                                  ? 'text-blue-600 dark:text-blue-400 bg-blue-50 dark:bg-blue-950/60'
                                  : 'text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                        <span class="material-symbols-outlined text-[22px] text-slate-400">fact_check</span>
                        Validar Presenças
                    </a>
                @elseif(session('user_type') === 'administrador')
                    <a href="/dashboard"
                       class="flex items-center gap-3 px-4 py-2.5 rounded-xl text-base font-semibold
                              {{ request()->is('dashboard*')
                                  ? 'text-blue-600 dark:text-blue-400 bg-blue-50 dark:bg-blue-950/60'
                                  : 'text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                        <span class="material-symbols-outlined text-[22px] text-slate-400">admin_panel_settings</span>
                        Painel Administrativo
                    </a>
                @endif

                <div class="pt-3 mt-3 border-t border-slate-200 dark:border-slate-800 space-y-1">
                    <a href="/perfil" class="flex items-center gap-3 px-4 py-2.5 rounded-xl text-base font-semibold text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800">
                        <span class="material-symbols-outlined text-[22px] text-slate-400">person</span>
                        Meu Perfil ({{ session('user_name') }})
                    </a>
                    <form method="POST" action="{{ url('/logout') }}">
                        @csrf
                        <button type="submit" class="flex items-center gap-3 px-4 py-2.5 rounded-xl text-base font-semibold text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-950/40 w-full text-left">
                            <span class="material-symbols-outlined text-[22px]">logout</span>
                            Sair do Sistema
                        </button>
                    </form>
                </div>
            @else
                <div class="pt-3 mt-3 border-t border-slate-200 dark:border-slate-800 flex flex-col gap-2">
                    <a href="/login" class="flex items-center justify-center py-2.5 text-center font-semibold text-slate-700 dark:text-slate-200 bg-slate-100 dark:bg-slate-800 rounded-xl">
                        Entrar
                    </a>
                    <a href="/cadastro" class="flex items-center justify-center py-2.5 text-center font-semibold text-white bg-blue-600 rounded-xl shadow-sm">
                        Cadastre-se
                    </a>
                </div>
            @endif
        </div>
    </div>
</header>
