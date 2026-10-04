document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('input[type="password"]').forEach((input) => {
        const parent = input.parentElement;
        if (!parent || parent.querySelector('button.toggle-senha, button[onclick*="toggleSenha"], button[data-password-toggle]')) return;

        const wrapper = document.createElement('div');
        wrapper.classList.add('relative');
        parent.insertBefore(wrapper, input);
        wrapper.appendChild(input);
        input.classList.add('pr-12');

        const button = document.createElement('button');
        button.type = 'button';
        button.dataset.passwordToggle = 'true';
        button.className = 'absolute right-3 top-1/2 -translate-y-1/2 text-gray-500 hover:text-gray-700';
        button.setAttribute('aria-label', 'Mostrar senha');
        button.setAttribute('aria-pressed', 'false');
        if (input.id) button.setAttribute('aria-controls', input.id);

        const icon = document.createElement('span');
        icon.className = 'material-symbols-outlined text-xl';
        icon.textContent = 'visibility';
        button.appendChild(icon);

        button.addEventListener('click', () => {
            const mostrar = input.type === 'password';
            input.type = mostrar ? 'text' : 'password';
            button.setAttribute('aria-label', mostrar ? 'Ocultar senha' : 'Mostrar senha');
            button.setAttribute('aria-pressed', String(mostrar));
            icon.textContent = mostrar ? 'visibility_off' : 'visibility';
        });

        wrapper.appendChild(button);
    });
});