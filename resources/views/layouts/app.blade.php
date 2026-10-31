<!DOCTYPE html>
<html class="light" lang="pt-br">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>Portal Estágio CEFET</title>
    <!-- Tailwind e Fontes -->
    @vite('resources/css/app.css')
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
</head>
<body class="min-h-screen bg-gray-50">
    @include('partials.header')
    @yield('content')
    @include('partials.footer')
    <script src="/js/password-toggle.js?v=1"></script>
</body>
</html>
