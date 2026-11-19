<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1"/>
    <title>NEXO - Portal de Estágio</title>
    <link rel="icon" type="image/png" href="{{ asset('documents/Nexo_Logo.png') }}"/>
    <link rel="apple-touch-icon" href="{{ asset('documents/Nexo_Logo.png') }}"/>

    {{-- Anti-FOUC para Dark Mode --}}
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
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
</head>
<body class="min-h-screen bg-slate-50 dark:bg-slate-950 flex items-center justify-center p-6 text-slate-900 dark:text-slate-100 transition-colors">
    <main class="w-full max-w-lg rounded-3xl border border-slate-200/80 dark:border-slate-800 bg-white dark:bg-slate-900 p-8 sm:p-10 shadow-xl shadow-slate-200/50 dark:shadow-none text-center">
        <div class="w-16 h-16 rounded-2xl bg-rose-50 dark:bg-rose-950/60 text-rose-600 dark:text-rose-400 flex items-center justify-center mx-auto mb-5">
            <span class="material-symbols-outlined text-[36px]">error_outline</span>
        </div>
        <span class="text-xs font-bold uppercase tracking-wider text-rose-600 dark:text-rose-400">Falha Temporária</span>
        <h1 class="text-2xl font-black tracking-tight text-slate-900 dark:text-slate-100 mt-1 mb-2">Não foi possível concluir a ação</h1>
        <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 leading-relaxed mb-6">
            Ocorreu uma falha no processamento. Se a situação persistir, informe o código de protocolo abaixo à coordenação de estágios.
        </p>

        <div class="mb-6 rounded-2xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200/60 dark:border-slate-700/60 p-3.5 text-xs font-mono text-slate-600 dark:text-slate-300">
            <strong>Protocolo:</strong> {{ $requestId ?? 'N/A' }}
        </div>

        <div class="flex justify-center gap-3">
            <a href="{{ url('/home') }}" class="px-5 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold shadow-sm transition-colors">
                Voltar ao Início
            </a>
            <a href="javascript:history.back()" class="px-5 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 text-slate-700 dark:text-slate-300 text-xs font-semibold hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors">
                Tentar Novamente
            </a>
        </div>
    </main>
</body>
</html>
