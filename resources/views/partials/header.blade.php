<!-- Header para usuários logados -->
@if(session('user_id'))
<header class="bg-[#0077ff] border-b border-white sticky top-0 z-50 fig-gradient">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-20">
            
        {{-- Logo e nome --}}
            <a href="/dashboard" class="flex items-center gap-2">
                <div class=" p-2 rounded-lg">
                    <span class="material-symbols-outlined text-[#ffffff] text-[28px]">school</span>
                </div>
                <span class="text-xl font-semibold text-white">Portal Estágio CEFET</span>
            </a>

            {{-- Variações de usuário --}}
            <nav class="hidden md:flex items-center gap-6">
                <a href="/dashboard" class="text-white hover:text-[#004a9e] transition-colors">Dashboard</a>
                @if(session('user_type') === 'aluno')
                    <a href="/tutorial" class="text-white hover:text-[#004a9e] transition-colors">Tutorial</a>
                    <a href="/controle-horas" class="text-white hover:text-[#004a9e] transition-colors">Controle de Horas</a>
                @elseif(session('user_type') === 'orientador')
                    <a href="/estagios" class="text-white hover:text-[#004a9e] transition-colors">Meus estágios</a>
                @elseif(session('user_type') === 'supervisor')
                    <a href="/estagios" class="text-white hover:text-[#004a9e] transition-colors">Validar presenças</a>
                @endif
                <a href="/empresas" class="text-white hover:text-[#004a9e] transition-colors">Empresas</a>
            </nav>

            {{-- Menu do Usuário --}}
            <div class="hidden md:flex items-center gap-4">
                <a href="/perfil" class="flex items-center gap-2 px-3 py-2 rounded-lg hover:bg-#004a9e transition-colors">
                    <div class="w-8 h-8 bg-white rounded-full flex items-center justify-center">
                        <span class="material-symbols-outlined text-[#004a9e] text-[18px]">person</span>
                    </div>
                    <div class="text-left">
                        <div class="text-sm font-medium text-white">{{ session('user_name', 'Aluno') }}</div>
                        <div class="text-xs text-white capitalize">{{ session('user_type', 'aluno') }}</div>
                    </div>
                </a>
                <form method="POST" action="{{ url('/logout') }}" class="inline">
                    @csrf
                    <button type="submit" class="p-2 rounded-lg hover:bg-red-500 transition-colors" title="Sair" aria-label="Sair">
                        <span class="material-symbols-outlined text-white text-[22px]">
                            logout
                        </span>
                    </button>
                </form>
                </a>
            </div>

            <!-- Botão Menu Mobile -->
            <button onclick="document.getElementById('mobile-menu').classList.toggle('hidden')" class="md:hidden p-2 rounded-lg hover:bg-#0056c9">
                <span class="material-symbols-outlined text-white">menu</span>
            </button>
        </div>

        <!-- Menu Mobile -->
        <div id="mobile-menu" class="hidden md:hidden py-4 border-t border-gray-200">
            <nav class="flex flex-col gap-2">
                <a href="/dashboard" class="px-4 py-2 rounded-lg hover:bg-##004a9e text-white">Dashboard</a>
                @if(session('user_type') === 'aluno')
                    <a href="/tutorial" class="px-4 py-2 rounded-lg hover:bg-##004a9e text-white">Tutorial</a>
                    <a href="/controle-horas" class="px-4 py-2 rounded-lg hover:bg-##004a9e text-white">Controle de Horas</a>
                @elseif(session('user_type') === 'orientador')
                    <a href="/estagios" class="px-4 py-2 rounded-lg hover:bg-##004a9e text-white">Meus estágios</a>
                @elseif(session('user_type') === 'supervisor')
                    <a href="/estagios" class="px-4 py-2 rounded-lg hover:bg-##004a9e text-white">Validar presenças</a>
                @endif
                <a href="/empresas" class="px-4 py-2 rounded-lg hover:bg-##004a9e text-white">Empresas</a>
                <a href="/perfil" class="px-4 py-2 rounded-lg hover:bg-##004a9e text-white">Meu Perfil</a>
                <form method="POST" action="{{ url('/logout') }}" class="inline">
                    @csrf
                    <button type="submit" class="p-2 rounded-lg hover:bg-red-500 transition-colors" title="Sair" aria-label="Sair">
                        <span class="material-symbols-outlined text-white">
                            logout
                        </span>
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
                <div class=" p-2 rounded-lg">
                    <span class="material-symbols-outlined text-white text-[28px]">school</span>
                </div>
                <span class="text-xl font-semibold text-white">Portal Estágio CEFET</span>
            </a>
            <div class="flex items-center gap-3">
                <a href="/login" class="px-4 py-2 text-white font-medium hover:bg-white/10 rounded-lg transition-colors">Entrar</a>
                <a href="/cadastro" class="px-4 py-2 bg-white text-[#0077fc] font-medium rounded-lg hover:bg-blue-50 transition-colors">Cadastrar</a>
            </div>
        </div>
    </div>
</header>
@endif
