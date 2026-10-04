<!DOCTYPE html>
<html class="light" lang="pt-br">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>Portal Estágio CEFET</title>
    <!-- Tailwind e Fontes -->
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
    <link rel="stylesheet" href="/css/style.css?v=3">
    <script id="tailwind-config">
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        "primary": "#0077fc",
                        "primary-dark": "#0056c9",
                        "primary-container": "#0077fc",
                        "surface": "#ffffff",
                        "surface-container": "#f9fafb",
                        "surface-container-low": "#f3f4f6",
                        "surface-container-high": "#e5e7eb",
                        "on-surface": "#111827",
                        "on-surface-variant": "#6b7280",
                        "secondary": "#6b7280",
                        "tertiary": "#16a34a"
                    },
                    fontFamily: {
                        "display": ["Inter", "sans-serif"]
                    }
                }
            }
        }
    </script>
</head>
<body class="min-h-screen bg-gray-50">
    @include('partials.header')
    @yield('content')
    @includeWhen(!session('user_id'), 'partials.footer')
    <script src="/js/password-toggle.js?v=1"></script>
</body>
</html>
