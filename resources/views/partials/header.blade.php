<!-- Header para usuários logados -->
@if(session('user_id'))
<header class="bg-white border-b border-gray-200 sticky top-0 z-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-16">
            {{-- Logo --}}
            <a href="/dashboard" class="flex items-center gap-2">
                <div class="bg-[#0077fc] p-2 rounded-lg">
                    <span class="material-symbols-outlined text-white text-[28px]">school</span>
                </div>
                <span class="text-xl font-semibold text-gray-900">Portal Estágio CEFET</span>
            </a>

            {{-- Navegação --}}
            <nav class="hidden md:flex items-center gap-6">
                <a href="/dashboard" class="text-gray-700 hover:text-[#0077fc] transition-colors">Dashboard</a>
                @if(session('user_type') === 'aluno')
                    <a href="/tutorial" class="text-gray-700 hover:text-[#0077fc] transition-colors">Tutorial</a>
                    <a href="/vagas" class="text-gray-700 hover:text-[#0077fc] transition-colors">Vagas</a>
                    <a href="/controle-horas" class="text-gray-700 hover:text-[#0077fc] transition-colors">Controle de Horas</a>
                @elseif(session('user_type') === 'orientador')
                    <a href="/estagios" class="text-gray-700 hover:text-[#0077fc] transition-colors">Meus estágios</a>
                @elseif(session('user_type') === 'supervisor')
                    <a href="/estagios" class="text-gray-700 hover:text-[#0077fc] transition-colors">Validar presenças</a>
                @endif
                <a href="/empresas" class="text-gray-700 hover:text-[#0077fc] transition-colors">Empresas</a>
            </nav>

            {{-- Menu do Usuário --}}
            <div class="hidden md:flex items-center gap-4">
                <a href="/perfil" class="flex items-center gap-2 px-3 py-2 rounded-lg hover:bg-gray-100 transition-colors">
                    <div class="w-8 h-8 bg-[#0077fc] rounded-full flex items-center justify-center">
                        <span class="material-symbols-outlined text-white text-[18px]">person</span>
                    </div>
                    <div class="text-left">
                        <div class="text-sm font-medium text-gray-900">{{ session('user_name', 'Aluno') }}</div>
                        <div class="text-xs text-gray-500 capitalize">{{ session('user_type', 'aluno') }}</div>
                    </div>
                </a>
                <form method="POST" action="{{ url('/logout') }}" class="inline">
                    @csrf
                    <button type="submit" class="p-2 rounded-lg hover:bg-gray-100 transition-colors" title="Sair" aria-label="Sair">
                        <span class="material-symbols-outlined text-gray-600 text-[22px]">
                            logout
                        </span>
                    </button>
                </form>
                </a>
            </div>

            <!-- Botão Menu Mobile -->
            <button onclick="document.getElementById('mobile-menu').classList.toggle('hidden')" class="md:hidden p-2 rounded-lg hover:bg-gray-100">
                <span class="material-symbols-outlined text-gray-700">menu</span>
            </button>
        </div>

        <!-- Menu Mobile -->
        <div id="mobile-menu" class="hidden md:hidden py-4 border-t border-gray-200">
            <nav class="flex flex-col gap-2">
                <a href="/dashboard" class="px-4 py-2 rounded-lg hover:bg-gray-100">Dashboard</a>
                @if(session('user_type') === 'aluno')
                    <a href="/tutorial" class="px-4 py-2 rounded-lg hover:bg-gray-100">Tutorial</a>
                    <a href="/vagas" class="px-4 py-2 rounded-lg hover:bg-gray-100">Vagas</a>
                    <a href="/controle-horas" class="px-4 py-2 rounded-lg hover:bg-gray-100">Controle de Horas</a>
                @elseif(session('user_type') === 'orientador')
                    <a href="/estagios" class="px-4 py-2 rounded-lg hover:bg-gray-100">Meus estágios</a>
                @elseif(session('user_type') === 'supervisor')
                    <a href="/estagios" class="px-4 py-2 rounded-lg hover:bg-gray-100">Validar presenças</a>
                @endif
                <a href="/empresas" class="px-4 py-2 rounded-lg hover:bg-gray-100">Empresas</a>
                <a href="/perfil" class="px-4 py-2 rounded-lg hover:bg-gray-100">Meu Perfil</a>
                <a href="/logout" class="px-4 py-2 rounded-lg hover:bg-gray-100 text-red-600">Sair</a>
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
                <div class="bg-white p-2 rounded-lg">
                    <span class="material-symbols-outlined text-[#0077fc] text-[28px]">school</span>
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
