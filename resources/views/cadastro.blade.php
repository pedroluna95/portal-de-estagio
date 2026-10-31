{{-- Cadastro — igual ao RegisterPage do Figma: 2 passos (tipo de usuário → formulário) --}}
<!DOCTYPE html>
<html class="light" lang="pt-br">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>Criar Conta — Portal Estágio CEFET</title>
    @vite('resources/css/app.css')
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
    <script defer src="/js/password-toggle.js?v=1"></script>
</head>
<body style="font-family: 'Inter', sans-serif;">
<div class="min-h-screen fig-gradient flex items-center justify-center p-4 py-12">
    <div class="w-full max-w-4xl">
        {{-- Logo e Header --}}
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-16 h-16 bg-white rounded-2xl mb-4">
                <span class="material-symbols-outlined text-[40px] text-[#0077fc]">school</span>
            </div>
            <h1 class="text-3xl font-bold text-white mb-2">Criar Conta</h1>
            <p id="subtitulo" class="text-blue-100">Selecione o tipo de usuário</p>
        </div>

        <div class="bg-white rounded-2xl shadow-xl p-8">
            {{-- Passo 1: Seleção de tipo --}}
            <div id="passo-tipo" class="grid grid-cols-1 md:grid-cols-1 gap-4 {{ $errors->any() || !empty($tipoInicial) ? 'hidden' : '' }}">
                <a href="?tipo=aluno" onclick="selecionarTipo('aluno'); return false;" class="p-6 border-2 border-gray-200 rounded-xl hover:border-[#0077fc] hover:bg-blue-50 transition-all text-left group block">
                    <div class="flex items-start gap-4">
                        <div class="p-3 bg-blue-50 group-hover:bg-[#0077fc] rounded-lg transition-colors">
                            <span class="material-symbols-outlined text-[#0077fc] group-hover:text-white">person</span>
                        </div>
                        <div>
                            <h3 class="font-semibold text-lg mb-1">Aluno</h3>
                            <p class="text-sm text-gray-600">Estudante em busca de estágio</p>
                        </div>
                    </div>
                </a>

                <a href="?tipo=supervisor" onclick="selecionarTipo('supervisor'); return false;" class="p-6 border-2 border-gray-200 rounded-xl hover:border-[#0077fc] hover:bg-blue-50 transition-all text-left group block">
                    <div class="flex items-start gap-4">
                        <div class="p-3 bg-blue-50 group-hover:bg-[#0077fc] rounded-lg transition-colors">
                            <span class="material-symbols-outlined text-[#0077fc] group-hover:text-white">manage_accounts</span>
                        </div>
                        <div>
                            <h3 class="font-semibold text-lg mb-1">Supervisor</h3>
                            <p class="text-sm text-gray-600">Supervisor de estágio na empresa</p>
                        </div>
                    </div>
                </a>

                <a href="?tipo=orientador" onclick="selecionarTipo('orientador'); return false;" class="p-6 border-2 border-gray-200 rounded-xl hover:border-[#0077fc] hover:bg-blue-50 transition-all text-left group block">
                    <div class="flex items-start gap-4">
                        <div class="p-3 bg-blue-50 group-hover:bg-[#0077fc] rounded-lg transition-colors">
                            <span class="material-symbols-outlined text-[#0077fc] group-hover:text-white">school</span>
                        </div>
                        <div>
                            <h3 class="font-semibold text-lg mb-1">Orientador</h3>
                            <p class="text-sm text-gray-600">Professor orientador do CEFET</p>
                        </div>
                    </div>
                </a>

            </div>

            {{-- Passo 2: Formulário dinâmico --}}
            <form id="passo-form" class="{{ $errors->any() || !empty($tipoInicial) ? '' : 'hidden' }} space-y-4" method="POST" action="{{ url('/cadastro') }}">
                @csrf
                @if($errors->any())
                <div class="mb-4 text-red-600">
                    <ul>
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif
                @if(session('success'))
                <div class="mb-4 text-green-600">{{ session('success') }}</div>
                @endif
                <button type="button" onclick="voltarTipo()" class="flex items-center gap-2 text-[#0077fc] hover:underline mb-4">
                    <span class="material-symbols-outlined text-[18px]">arrow_back</span>
                    Voltar
                </button>

                <input type="hidden" name="tipo" id="tipo" value="{{ old('tipo', $tipoInicial ?? '') }}"/>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Nome Completo</label>
                    <input type="text" name="nome" value="{{ old('nome') }}" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#0077fc] focus:border-transparent outline-none" required/>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">E-mail</label>
                    <input type="email" name="email" value="{{ old('email') }}" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#0077fc] focus:border-transparent outline-none" required/>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">CPF (somente números)</label>
                    <input type="text" name="cpf" value="{{ old('cpf') }}" placeholder="Somente números" inputmode="numeric" pattern="[0-9]*" maxlength="11" oninput="this.value=this.value.replace(/\D/g,'')" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#0077fc] focus:border-transparent outline-none" required/>
                </div>

                <div id="campos-especificos"></div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Senha</label>
                    <input type="password" name="senha" id="senha" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#0077fc] focus:border-transparent outline-none" required/>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Confirmar Senha</label>
                    <input type="password" name="confirmar_senha" id="confirmar_senha" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#0077fc] focus:border-transparent outline-none" required/>
                </div>

                <button type="submit" class="w-full bg-[#0077fc] text-white py-3 rounded-lg font-medium hover:bg-[#0056c9] transition-colors mt-6">
                    Criar Conta
                </button>
            </form>

            <div class="mt-6 text-center text-sm text-gray-600">
                Já tem uma conta?
                <a href="/login" class="text-[#0077fc] font-medium hover:underline">Faça login</a>
            </div>
        </div>
    </div>
</div>

<script>
    const camposEspecificos = {
        aluno: `
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Matrícula</label>
                    <input type="text" name="matricula" placeholder="Digite sua matrícula" maxlength="30" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#0077fc] focus:border-transparent outline-none" required/>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Curso</label>
                    <select name="curso" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#0077fc] focus:border-transparent outline-none bg-white" required>
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
                <label class="block text-sm font-medium text-gray-700 mb-2">SIAPE (8 dígitos)</label>
                <input type="text" name="siape" placeholder="Digite o SIAPE" inputmode="numeric" pattern="[0-9]*" maxlength="8" oninput="this.value=this.value.replace(/\D/g,'')" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#0077fc] focus:border-transparent outline-none" required/>
            </div>
        `,
        supervisor: ''
    };

    function selecionarTipo(tipo) {
        document.getElementById('tipo').value = tipo;
        document.getElementById('campos-especificos').innerHTML = camposEspecificos[tipo] || '';
        document.getElementById('passo-tipo').classList.add('hidden');
        document.getElementById('passo-form').classList.remove('hidden');
        document.getElementById('subtitulo').textContent = 'Preencha seus dados';
    }

    function voltarTipo() {
        document.getElementById('passo-tipo').classList.remove('hidden');
        document.getElementById('passo-form').classList.add('hidden');
        document.getElementById('subtitulo').textContent = 'Selecione o tipo de usuário';
    }

    // Auto-abre o formulário se ?tipo= estiver presente na URL (fallback caso o usuário venha por link)
    document.addEventListener('DOMContentLoaded', function () {
        try {
            const params = new URLSearchParams(window.location.search);
            const tipoParam = document.getElementById('tipo').value || params.get('tipo');
            if (tipoParam) {
                selecionarTipo(tipoParam);
            }
        } catch (e) {
            // silent
        }
    });

    // Validação de senhas e campos obrigatórios por tipo
    document.getElementById('passo-form').addEventListener('submit', function (e) {
        const tipo = document.getElementById('tipo').value;
        const senha = document.getElementById('senha').value;
        const confirmar = document.getElementById('confirmar_senha').value;

        if (senha !== confirmar) {
            e.preventDefault();
            alert('As senhas não coincidem!');
            return;
        }

        if (tipo === 'aluno') {
            const matricula = document.querySelector('input[name="matricula"]');
            const curso = document.querySelector('select[name="curso"]');
            if (!matricula || !matricula.value.trim()) {
                e.preventDefault();
                alert('Aluno deve informar a matrícula obrigatoriamente.');
                matricula?.focus();
                return;
            }
            if (!curso || !curso.value) {
                e.preventDefault();
                alert('Aluno deve selecionar o curso obrigatoriamente.');
                curso?.focus();
            }
        }

        if (tipo === 'orientador') {
            const siape = document.querySelector('input[name="siape"]');
            if (!siape || !siape.value.trim()) {
                e.preventDefault();
                alert('Orientador deve informar o SIAPE obrigatoriamente.');
                siape?.focus();
            }
        }
    });
</script>
</body>
</html>
