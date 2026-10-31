<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1"/>
    <title>Erro inesperado — Portal Estágio CEFET</title>
    @vite('resources/css/app.css')
</head>
<body class="min-h-screen bg-gray-50 flex items-center justify-center p-6">
    <main class="w-full max-w-lg rounded-lg border border-gray-200 bg-white p-8 shadow-sm">
        <p class="mb-2 text-sm font-semibold text-red-700">Erro inesperado</p>
        <h1 class="mb-3 text-2xl font-semibold text-gray-900">Não foi possível concluir</h1>
        <p class="mb-5 text-gray-700">Ocorreu uma falha ao processar sua solicitação. Tente novamente. Se o problema continuar, informe o código abaixo ao suporte.</p>
        <p class="mb-6 rounded bg-gray-50 p-3 text-sm text-gray-700"><strong>Código de atendimento:</strong> {{ $requestId }}</p>
        <a href="{{ url('/home') }}" class="inline-flex rounded bg-primary px-4 py-2 font-medium text-white">Voltar ao início</a>
    </main>
</body>
</html>