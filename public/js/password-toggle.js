/**
 * NEXO Password Visibility Toggle
 *
 * Suporta:
 * 1. Função global window.toggleSenha() para botões inline em login e formulários
 * 2. Injeção automática segura para qualquer input[type="password"] que não possua botão próprio
 * 3. Delegação de eventos para formulários dinâmicos (como o passo 2 de cadastro)
 */

window.toggleSenha = function (inputId = 'senha', iconId = 'icone-senha') {
    const input = document.getElementById(inputId);
    const icon = document.getElementById(iconId);
    if (!input) return;

    const isPassword = input.type === 'password';
    input.type = isPassword ? 'text' : 'password';

    if (icon) {
        icon.textContent = isPassword ? 'visibility_off' : 'visibility';
    }
};

document.addEventListener('DOMContentLoaded', () => {
    // Adiciona botão para campos de senha que ainda não tenham controle próprio
    function bindPasswordToggles() {
        document.querySelectorAll('input[type="password"]').forEach((input) => {
            const parent = input.parentElement;
            if (!parent) return;

            // Se o elemento pai ou o próprio container já tem um botão de toggle, pula
            if (parent.querySelector('[data-password-toggle], button[onclick*="toggleSenha"], .toggle-senha')) {
                return;
            }

            // Se o input não estiver dentro de um wrapper relativo, cria um
            let wrapper = parent;
            if (!parent.classList.contains('relative')) {
                wrapper = document.createElement('div');
                wrapper.className = 'relative w-full';
                parent.insertBefore(wrapper, input);
                wrapper.appendChild(input);
            }

            input.classList.add('pr-11');

            const button = document.createElement('button');
            button.type = 'button';
            button.dataset.passwordToggle = 'true';
            button.className = 'absolute right-3.5 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 transition-colors cursor-pointer focus:outline-none flex items-center justify-center';
            button.setAttribute('aria-label', 'Mostrar senha');

            const icon = document.createElement('span');
            icon.className = 'material-symbols-outlined text-[20px] select-none';
            icon.textContent = 'visibility';
            button.appendChild(icon);

            button.addEventListener('click', (e) => {
                e.preventDefault();
                e.stopPropagation();
                const isPass = input.type === 'password';
                input.type = isPass ? 'text' : 'password';
                icon.textContent = isPass ? 'visibility_off' : 'visibility';
                button.setAttribute('aria-label', isPass ? 'Ocultar senha' : 'Mostrar senha');
            });

            wrapper.appendChild(button);
        });
    }

    bindPasswordToggles();

    // Observa mudanças no DOM caso campos de senha sejam inseridos dinamicamente (como no cadastro)
    const observer = new MutationObserver(() => {
        bindPasswordToggles();
    });
    observer.observe(document.body, { childList: true, subtree: true });
});
