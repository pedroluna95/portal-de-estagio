{{-- Login — igual ao LoginPage do Figma (página de tela cheia, gradiente azul) --}}
<!DOCTYPE html>
<html class="light" lang="pt-br">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>Entrar — Portal Estágio CEFET</title>
    @vite('resources/css/app.css')
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
</head>
<body style="font-family: 'Inter', sans-serif;">
<div class="min-h-screen fig-gradient flex items-center justify-center p-4">
    <div class="w-full max-w-md">
        {{-- Logo e Header --}}
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-16 h-16 bg-white rounded-2xl mb-4">
                <span class="material-symbols-outlined text-[40px] text-[#0077fc]">school</span>
            </div>
            <h1 class="text-3xl font-bold text-white mb-2">Portal Estágio CEFET</h1>
            <p class="text-blue-100">Faça login para continuar</p>
        </div>

        {{-- Card de Login --}}
        <div class="bg-white rounded-2xl shadow-xl p-8">
            @if($errors->any())
                <div role="alert" class="mb-5 rounded-lg bg-red-50 p-3 text-sm text-red-700">
                    <ul class="list-disc pl-5">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
                </div>
            @endif
            @if(session('success'))
                <div role="status" class="mb-5 rounded-lg bg-green-50 p-3 text-sm text-green-700">{{ session('success') }}</div>
            @endif
            <form id="form-login" method="POST" action="{{ url('/login') }}" class="space-y-6">
                @csrf
                {{-- E-mail --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">E-mail</label>
                    <div class="relative">
                        <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-[20px] text-gray-400">mail</span>
                        <input type="email" name="email" id="email"
                            class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#0077fc] focus:border-transparent outline-none transition-all"
                            placeholder="seu.email@cefet.br" value="{{ old('email') }}" autocomplete="email" required/>
                    </div>
                </div>

                {{-- Senha --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Senha</label>
                    <div class="relative">
                        <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-[20px] text-gray-400">lock</span>
                        <input type="password" name="senha" id="senha"
                            class="w-full pl-10 pr-12 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#0077fc] focus:border-transparent outline-none transition-all"
                            placeholder="••••••••" required/>
                        <button type="button" onclick="toggleSenha()"
                            class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                            <span id="icone-senha" class="material-symbols-outlined text-[20px]">visibility</span>
                        </button>
                    </div>
                </div>

                {{-- Esqueceu a senha --}}
                <div class="text-right">
                    <a href="#" class="text-sm text-[#0077fc] hover:underline">Esqueceu sua senha?</a>
                </div>

                {{-- Botão de Login --}}
                <button type="submit" id="btn-entrar"
                    class="w-full bg-[#0077fc] text-white py-3 rounded-lg font-medium hover:bg-[#0056c9] transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                    Entrar
                </button>
            </form>

            {{-- Link para Cadastro --}}
            <div class="mt-6 text-center text-sm text-gray-600">
                Não tem uma conta?
                <a href="/cadastro" class="text-[#0077fc] font-medium hover:underline">Cadastre-se</a>
            </div>
        </div>

        {{-- Footer --}}
        <p class="text-center text-blue-100 text-sm mt-6">
            © 2026 CEFET. Todos os direitos reservados.
        </p>
    </div>
</div>

<script>
    function toggleSenha() {
        const input = document.getElementById('senha');
        const icone = document.getElementById('icone-senha');
        if (input.type === 'password') {
            input.type = 'text';
            icone.textContent = 'visibility_off';
        } else {
            input.type = 'password';
            icone.textContent = 'visibility';
        }
    }

    // Estado de carregamento ao enviar
    document.getElementById('form-login').addEventListener('submit', function () {
        const btn = document.getElementById('btn-entrar');
        btn.disabled = true;
        btn.textContent = 'Entrando...';
    });
</script>
</body>
</html>
