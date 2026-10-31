<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-2">Dashboard Administrador</h1>
        <p class="text-gray-600">Gerencie todo o sistema de estágios</p>
    </div>

    {{-- status --}}
    <div class="grid grid-cols-1 gap-6 mb-8">
        <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-200">
            <div class="inline-flex p-3 rounded-lg bg-blue-50 text-blue-600 mb-4"><span class="material-symbols-outlined">groups</span></div>
            <h3 class="text-sm text-gray-600 mb-1">Total de Usuários</h3>
            <p class="text-2xl font-bold text-gray-900">{{ $total_usuarios }}</p>
            <p class="text-xs text-gray-500 mt-1"></p>
        </div>
        <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-200">
            <div class="inline-flex p-3 rounded-lg bg-green-50 text-green-600 mb-4"><span class="material-symbols-outlined">business</span></div>
            <h3 class="text-sm text-gray-600 mb-1">Empresas Conveniadas</h3>
            <p class="text-2xl font-bold text-gray-900">48</p>
            <p class="text-xs text-gray-500 mt-1">3 aguardando aprovação</p>
        </div>
        <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-200">
            <div class="inline-flex p-3 rounded-lg bg-orange-50 text-orange-600 mb-4"><span class="material-symbols-outlined">trending_up</span></div>
            <h3 class="text-sm text-gray-600 mb-1">Estágios Ativos</h3>
            <p class="text-2xl font-bold text-gray-900">89</p>
            <p class="text-xs text-gray-500 mt-1">+7 este semestre</p>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-6">
        {{-- Ações Rápidas --}}
        <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-200">
            <h2 class="text-xl font-semibold mb-4">Ações Rápidas</h2>
            <div class="space-y-3">
                <a href="/admin/usuarios" class="w-full block">
                <div class="w-full flex items-center gap-3 p-4 border-2 border-gray-200 rounded-lg hover:border-[#0077fc] hover:bg-blue-50 transition-all text-left">
                    <div class="p-2 bg-blue-50 rounded-lg">
                        <span class="material-symbols-outlined text-[#0077fc]">person_add</span>
                    </div>
                    <div>
                        <p class="font-medium">Cadastrar Novo Usuário</p>
                        <p class="text-sm text-gray-500">Adicionar aluno, supervisor ou orientador</p>
                    </div>
                </div>
                </a>
                <button class="w-full flex items-center gap-3 p-4 border-2 border-gray-200 rounded-lg hover:border-[#0077fc] hover:bg-blue-50 transition-all text-left">
                    <div class="p-2 bg-green-50 rounded-lg">
                        <span class="material-symbols-outlined text-green-600">add_circle</span>
                    </div>
                    <div>
                        <p class="font-medium">Adicionar Empresa</p>
                        <p class="text-sm text-gray-500">Cadastrar nova empresa conveniada</p>
                    </div>
                </button>
                <button class="w-full flex items-center gap-3 p-4 border-2 border-gray-200 rounded-lg hover:border-[#0077fc] hover:bg-blue-50 transition-all text-left">
                    <div class="p-2 bg-orange-50 rounded-lg">
                        <span class="material-symbols-outlined text-orange-600">settings</span>
                    </div>
                    <div>
                        <p class="font-medium">Configurações do Sistema</p>
                        <p class="text-sm text-gray-500">Ajustar parâmetros e preferências</p>
                    </div>
                </button>
            </div>
        </div>

        {{-- Atividades Recentes --}}
        <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-200">
            <h2 class="text-xl font-semibold mb-4">Atividades Recentes</h2>
            <div class="space-y-4">
                <div class="flex items-start gap-3 pb-3 border-b border-gray-100">
                    <span class="w-2 h-2 bg-[#0077fc] rounded-full mt-2 flex-shrink-0"></span>
                    <div class="flex-1">
                        <p class="text-sm font-medium text-gray-900">João Silva se cadastrou como Aluno</p>
                        <p class="text-xs text-gray-500 mt-1">2 min atrás</p>
                    </div>
                </div>
                <div class="flex items-start gap-3 pb-3 border-b border-gray-100">
                    <span class="w-2 h-2 bg-[#0077fc] rounded-full mt-2 flex-shrink-0"></span>
                    <div class="flex-1">
                        <p class="text-sm font-medium text-gray-900">Inovação Digital renovou convênio</p>
                        <p class="text-xs text-gray-500 mt-1">1 hora atrás</p>
                    </div>
                </div>
                <div class="flex items-start gap-3 pb-3 border-b border-gray-100">
                    <span class="w-2 h-2 bg-[#0077fc] rounded-full mt-2 flex-shrink-0"></span>
                    <div class="flex-1">
                        <p class="text-sm font-medium text-gray-900">Maria Santos concluiu o estágio</p>
                        <p class="text-xs text-gray-500 mt-1">2 horas atrás</p>
                    </div>
                </div>
                <div class="flex items-start gap-3">
                    <span class="w-2 h-2 bg-[#0077fc] rounded-full mt-2 flex-shrink-0"></span>
                    <div class="flex-1">
                        <p class="text-sm font-medium text-gray-900">5 presenças foram validadas</p>
                        <p class="text-xs text-gray-500 mt-1">3 horas atrás</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
