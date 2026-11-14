/**
 * Nexo Theme Toggle — Dark / Light mode
 *
 * Lê e persiste a escolha no localStorage com a chave 'nexo-theme'.
 * O script anti-FOUC em layouts/app.blade.php aplica a classe 'dark'
 * no <html> ANTES do primeiro paint, eliminando qualquer piscar branco.
 */
(function () {
    'use strict';

    function getTheme() {
        var saved = localStorage.getItem('nexo-theme');
        if (saved === 'dark' || saved === 'light') return saved;
        return window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
    }

    function applyTheme(theme) {
        var html = document.documentElement;
        if (theme === 'dark') {
            html.classList.add('dark');
            html.classList.remove('light');
        } else {
            html.classList.remove('dark');
            html.classList.add('light');
        }
        localStorage.setItem('nexo-theme', theme);
        updateButtons(theme);
    }

    function updateButtons(theme) {
        document.querySelectorAll('[data-theme-toggle]').forEach(function (btn) {
            var iconEl = btn.querySelector('.theme-icon');
            var labelEl = btn.querySelector('.theme-label');
            if (iconEl) iconEl.textContent = theme === 'dark' ? 'light_mode' : 'dark_mode';
            if (labelEl) labelEl.textContent = theme === 'dark' ? 'Tema Claro' : 'Tema Escuro';
            btn.setAttribute('aria-label', theme === 'dark' ? 'Ativar tema claro' : 'Ativar tema escuro');
            btn.setAttribute('title', theme === 'dark' ? 'Ativar tema claro' : 'Ativar tema escuro');
        });
    }

    function toggleTheme() {
        var current = getTheme();
        applyTheme(current === 'dark' ? 'light' : 'dark');
    }

    // Inicializa botões ao carregar
    document.addEventListener('DOMContentLoaded', function () {
        var theme = getTheme();
        updateButtons(theme);

        document.querySelectorAll('[data-theme-toggle]').forEach(function (btn) {
            btn.addEventListener('click', function (e) {
                e.preventDefault();
                toggleTheme();
            });
        });
    });
})();
