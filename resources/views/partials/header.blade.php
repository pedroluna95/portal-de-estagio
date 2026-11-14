<!-- Header para usuários logados -->
@if(session('user_id'))
<header class="bg-[#0077fc] border-b border-white sticky top-0 z-50 fig-gradient">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-20">

        {{-- Logo e nome --}}
            <a href="/dashboard" class="flex items-center gap-2">
                <div class="rounded-lg p-2">
                    <img src="{{ asset('documents/Nexo_Logo.png') }}" alt="Logo NEXO" class="h-8 w-8 object-contain">
                </div>
                <span class="text-xl font-semibold text-white" style="font-family: 'Inter', sans-serif;">NEXO</span>
            </a>

            {{-- Variações de usuário --}}
            <nav class="hidden md:flex items-center gap-6">
                <a href="/tutorial" class="text-white hover:text-blue-100 transition-colors">Tutorial</a>
                @if(session('user_type') === 'aluno')
                    <a href="/controle-horas" class="text-white hover:text-blue-100 transition-colors">Controle de Horas</a>
                @elseif(session('user_type') === 'orientador')
                    <a href="/estagios" class="text-white hover:text-blue-100 transition-colors">Meus estágios</a>
                @elseif(session('user_type') === 'supervisor')
                    <a href="/estagios" class="text-white hover:text-blue-100 transition-colors">Validar presenças</a>
                @endif
                <a href="/empresas" class="text-white hover:text-blue-100 transition-colors">Empresas</a>
            </nav>

            {{-- Menu do Usuário --}}
            <div class="hidden md:flex items-center gap-2">

                {{-- Toggle Dark Mode --}}
                <button data-theme-toggle
                        type="button"
                        title="Ativar tema escuro"
                        aria-label="Ativar tema escuro"
                        class="p-2 rounded-lg text-white hover:bg-white/20 transition-colors flex items-center gap-1">
                    <span class="material-symbols-outlined text-[22px] theme-icon">dark_mode</span>
                </button>

                <a href="/perfil" class="flex items-center gap-2 px-3 py-2 rounded-lg hover:bg-white/20 transition-colors">
                    <div class="w-8 h-8 bg-white rounded-full flex items-center justify-center">
                        <span class="material-symbols-outlined text-[#004aad] text-[18px]">person</span>
                    </div>
                    <div class="text-left">
                        <div class="text-sm font-medium text-white">{{ session('user_name', 'Usuário') }}</div>
                        <div class="text-xs text-blue-100 capitalize">{{ session('user_type', 'aluno') }}</div>
                    </div>
                </a>
                <form method="POST" action="{{ url('/logout') }}" class="inline">
                    @csrf
                    <button type="submit" class="p-2 rounded-lg hover:bg-red-500 transition-colors" title="Sair" aria-label="Sair">
                        <span class="material-symbols-outlined text-white text-[22px]">logout</span>
                    </button>
                </form>
            </div>

            <!-- Botão Menu Mobile -->
            <div class="flex items-center gap-2 md:hidden">
                {{-- Toggle Dark Mode (mobile) --}}
                <button data-theme-toggle
                        type="button"
                        title="Ativar tema escuro"
                        aria-label="Ativar tema escuro"
                        class="p-2 rounded-lg text-white hover:bg-white/20 transition-colors">
                    <span class="material-symbols-outlined text-[22px] theme-icon">dark_mode</span>
                </button>
                <button onclick="document.getElementById('mobile-menu').classList.toggle('hidden')" class="p-2 rounded-lg hover:bg-white/20">
                    <span class="material-symbols-outlined text-white">menu</span>
                </button>
            </div>
        </div>

        <!-- Menu Mobile -->
        <div id="mobile-menu" class="hidden md:hidden py-4 border-t border-white/20">
            <nav class="flex flex-col gap-2">
                <a href="/tutorial" class="px-4 py-2 rounded-lg hover:bg-white/20 text-white">Tutorial</a>
                @if(session('user_type') === 'aluno')
                    <a href="/controle-horas" class="px-4 py-2 rounded-lg hover:bg-white/20 text-white">Controle de Horas</a>
                @elseif(session('user_type') === 'orientador')
                    <a href="/estagios" class="px-4 py-2 rounded-lg hover:bg-white/20 text-white">Meus estágios</a>
                @elseif(session('user_type') === 'supervisor')
                    <a href="/estagios" class="px-4 py-2 rounded-lg hover:bg-white/20 text-white">Validar presenças</a>
                @endif
                <a href="/empresas" class="px-4 py-2 rounded-lg hover:bg-white/20 text-white">Empresas</a>
                <a href="/perfil" class="px-4 py-2 rounded-lg hover:bg-white/20 text-white">Meu Perfil</a>
                <form method="POST" action="{{ url('/logout') }}" class="px-2">
                    @csrf
                    <button type="submit" class="flex items-center gap-2 px-2 py-2 rounded-lg hover:bg-red-500 text-white transition-colors w-full">
                        <span class="material-symbols-outlined text-[20px]">logout</span>
                        Sair
                    </button>
                </form>
            </nav>
        </div>
    </div>
</header>


@else
<!-- Header para visitantes -->
<header class="fig-gradient sticky top-0 z-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-16">
            <a href="/home" class="flex items-center gap-2">
                <div class="rounded-lg p-2">
                    <img src="{{ asset('documents/Nexo_Logo.png') }}" alt="Logo NEXO" class="h-5 w-5 object-contain">
                </div>
                <span class="text-xl font-semibold text-white" style="font-family: 'Inter', sans-serif;">NEXO</span>
            </a>
            <div class="flex items-center gap-3">
                {{-- Toggle Dark Mode (visitantes) --}}
                <button data-theme-toggle
                        type="button"
                        title="Ativar tema escuro"
                        aria-label="Ativar tema escuro"
                        class="p-2 rounded-lg text-white hover:bg-white/20 transition-colors">
                    <span class="material-symbols-outlined text-[20px] theme-icon">dark_mode</span>
                </button>
                <a href="/login" class="px-4 py-2 text-white font-medium hover:bg-white/10 rounded-lg transition-colors">Entrar</a>
                <a href="/cadastro" class="px-4 py-2 bg-white text-[#0077fc] font-medium rounded-lg hover:bg-blue-50 transition-colors">Cadastrar</a>
            </div>
        </div>
    </div>
</header>
@endif
