function getCsrf() {
    const m = document.querySelector('meta[name="csrf-token"]');
    return m ? m.content : '';
}

function showFormError(message, field = null) {
    const form = document.getElementById('usuario-form');
    if (!form) return;
    let box = form.querySelector('[data-form-error]');
    if (!box) { box = document.createElement('div'); box.dataset.formError = '1'; box.className = 'mb-4 rounded bg-red-50 p-3 text-sm text-red-700'; form.prepend(box); }
    box.textContent = message;
    if (field) document.getElementById(field)?.focus();
}

const emailValidation = (email) => {
    const regex = /^[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$/i;
    return regex.test(email);
}

// Remove todos os caracteres não numéricos (mantemos apenas dígitos)
function onlyDigits(value) {
    return (value || '').toString().replace(/\D/g, '');
}


function getUserIdentifier(u) {
    const tipo = (u.atribuicao || '').toLowerCase();
    if (tipo === 'aluno') {
        return u.matricula ? `Matrícula: ${u.matricula}` : 'Matrícula: —';
    }
    if (tipo === 'orientador') {
        return u.siape ? `SIAPE: ${u.siape}` : 'SIAPE: —';
    }
    return '—';
}

// Render list on index
async function fetchAndRenderUsers() {
    const tbody = document.getElementById('usuarios-tbody');
    if (!tbody) return;
    tbody.innerHTML = '<tr><td colspan="7" class="p-4">Carregando...</td></tr>';

    const filtroTipo = document.getElementById('filtro-tipo');
    const busca = document.getElementById('busca-usuarios');
    const tipoValue = filtroTipo ? filtroTipo.value : '';
    const buscaValue = busca ? busca.value.trim() : '';

    const params = new URLSearchParams();
    if (buscaValue) params.set('q', buscaValue);
    if (tipoValue) params.set('tipo', tipoValue);

    let list;
    try {
        const res = await fetch('/admin/api/usuarios' + (params.toString() ? `?${params.toString()}` : ''), {
            headers: { Accept: 'application/json' }
        });
        const result = await res.json().catch(() => null);
        if (!res.ok) throw new Error(result?.message || 'Não foi possível carregar os usuários. Verifique sua conexão e tente novamente.');
        if (!Array.isArray(result)) throw new Error('O servidor retornou uma resposta inválida ao carregar os usuários.');
        list = result;
    } catch (error) {
        tbody.innerHTML = '';
        const row = document.createElement('tr');
        const cell = document.createElement('td');
        cell.colSpan = 7;
        cell.className = 'p-4 text-red-700';
        cell.textContent = error.message || 'Falha de conexão ao carregar os usuários.';
        row.appendChild(cell);
        tbody.appendChild(row);
        return;
    }
    if (!Array.isArray(list) || list.length === 0) {
        tbody.innerHTML = '<tr><td colspan="7" class="p-4">Nenhum usuário encontrado</td></tr>';
        return;
    }
    tbody.innerHTML = '';
    list.forEach(u => {
        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td class="px-6 py-4">${u.user_ID}</td>
            <td class="px-6 py-4">${u.Nome || ''}</td>
            <td class="px-6 py-4">${u.Email || ''}</td>
            <td class="px-6 py-4">${u.cpf || ''}</td>
            <td class="px-6 py-4">${getUserIdentifier(u)}</td>
            <td class="px-6 py-4">${u.atribuicao || ''}</td>
            <td class="px-6 py-4 text-right">
                <a href="/admin/usuarios/${u.user_ID}/edit" class="text-primary mr-3">Editar</a>
                <button data-id="${u.user_ID}" class="text-red-600 hover:underline btn-delete">Excluir</button>
            </td>
        `;
        tbody.appendChild(tr);
    });

    document.querySelectorAll('.btn-delete').forEach(btn => {
        btn.addEventListener('click', async (e) => {
            const id = e.currentTarget.getAttribute('data-id');
            if (!confirm('Confirmar exclusão?')) return;
            try {
                const response = await fetch(`/admin/api/usuarios/${id}`, {
                    method: 'DELETE',
                    headers: { 'X-CSRF-TOKEN': getCsrf(), Accept: 'application/json' }
                });
                const result = await response.json().catch(() => null);
                if (!response.ok) throw new Error(result?.message || 'Não foi possível excluir o usuário. Verifique sua conexão e tente novamente.');
            } catch (error) {
                window.alert(error.message || 'Falha de conexão ao excluir o usuário.');
                return;
            }
            fetchAndRenderUsers();
        });
    });
}

// Handle form submit for create/edit
async function handleUsuarioForm() {
    const form = document.getElementById('usuario-form');
    if (!form) return;
    const userId = form.getAttribute('data-user-id');
    const nome = document.getElementById('nome');
    const email = document.getElementById('email');
    const senha = document.getElementById('senha');
    const cpf = document.getElementById('cpf');
    const tipo = document.getElementById('tipo');
    const siapeField = document.getElementById('siape');
    const matriculaField = document.getElementById('matricula');
    const cursoField = document.getElementById('curso');

    // CPF: permitir digitar somente números (no frontend) — backend formata ao salvar
    if (cpf) {
        cpf.addEventListener('input', (e) => { e.target.value = onlyDigits(e.target.value).slice(0,11); });
    }
    if (siapeField) {
        siapeField.addEventListener('input', (e) => { e.target.value = onlyDigits(e.target.value).slice(0,8); });
    }

    form.addEventListener('submit', async (e) => {
        e.preventDefault();
        const submitBtn = form.querySelector('button[type="submit"]');
        const origText = submitBtn ? submitBtn.textContent : null;
        form.querySelector('[data-form-error]')?.remove();

        if (!emailValidation(email.value)) return showFormError('Informe um endereço de e-mail válido.', 'email');

        const tipoSelecionado = tipo ? tipo.value : '';
        if (tipoSelecionado === 'administrador') {
            return showFormError('A conta administrativa base não pode ser criada por esta tela.', 'tipo');
        }

        const cpfDigits = cpf ? onlyDigits(cpf.value) : '';
        if (cpf && cpfDigits.length > 0 && cpfDigits.length !== 11) return showFormError('O CPF deve conter 11 dígitos.', 'cpf');

        const siapeDigits = siapeField ? onlyDigits(siapeField.value) : '';
        if (tipoSelecionado === 'orientador' && siapeDigits.length !== 8) return showFormError('O SIAPE deve conter exatamente 8 dígitos.', 'siape');

        // Validação: matrícula é obrigatória para alunos
        if (tipoSelecionado === 'aluno') {
            const matVal = matriculaField ? (matriculaField.value || '').trim() : '';
            if (! matVal) return showFormError('Informe a matrícula do aluno.', 'matricula');
            if (!cursoField || !cursoField.value) return showFormError('Selecione o curso do aluno.', 'curso');
        }

        if (submitBtn) { submitBtn.disabled = true; submitBtn.textContent = 'Salvando...'; }

        const payload = {
            nome: nome.value,
            email: email.value,
            senha: senha ? senha.value : undefined,
            cpf: cpf ? cpfDigits : undefined,
            tipo: tipoSelecionado,
            matricula: matriculaField ? matriculaField.value || undefined : undefined,
            curso: cursoField ? cursoField.value || undefined : undefined,
            siape: siapeField ? siapeDigits || undefined : undefined,
        };

        const url = userId ? `/admin/api/usuarios/${userId}` : '/admin/api/usuarios';
        const method = userId ? 'PUT' : 'POST';

        let res;
        try {
            res = await fetch(url, {
                method,
                credentials: 'same-origin',
                headers: {
                    'Content-Type': 'application/json',
                    Accept: 'application/json',
                    'X-CSRF-TOKEN': getCsrf()
                },
                body: JSON.stringify(payload)
            });

            if (res.ok) {
                window.location.href = '/admin/usuarios';
                return;
            }

            // tratar erros de validação (422) e mensagens JSON
            const err = await res.json().catch(()=>null);
            if (res.status === 422 && err) {
                // Laravel pode retornar errors array
                if (err.errors) {
                    const first = Object.values(err.errors)[0];
                    showFormError(Array.isArray(first) ? first[0] : first);
                } else if (err.message) {
                    showFormError(err.message);
                } else {
                    showFormError('Confira os dados informados e tente novamente.');
                }
            } else if (err && err.message) {
                showFormError(err.message);
            } else {
                showFormError('Não foi possível salvar o usuário. Verifique sua conexão e tente novamente.');
            }
        } catch (ex) {
            showFormError('Falha de conexão ao salvar o usuário. Verifique sua rede e tente novamente.');
        } finally {
            if (submitBtn) { submitBtn.disabled = false; submitBtn.textContent = origText; }
        }
    });

    // If edit page, load user
    if (userId) {
        try {
            const res = await fetch(`/admin/api/usuarios/${userId}`, {
                headers: { Accept: 'application/json' }
            });
            const u = await res.json().catch(() => null);
            if (!res.ok) throw new Error(u?.message || 'Não foi possível carregar os dados deste usuário.');
            if (!u) throw new Error('O servidor retornou uma resposta inválida ao carregar este usuário.');
            nome.value = u.Nome || '';
            email.value = u.Email || '';
            cpf.value = onlyDigits(u.cpf || '');
            tipo.value = u.atribuicao || '';
            if (matriculaField) matriculaField.value = u.matricula || '';
            if (cursoField) cursoField.value = u.curso || '';
            if (siapeField) siapeField.value = onlyDigits(u.siape || '');
        } catch (error) {
            showFormError(error.message || 'Falha de conexão ao carregar este usuário.');
        }
    }

    // Mostrar/ocultar campos conforme tipo selecionado
    function updateFieldVisibility(selected) {
        const tipoLower = (selected || '').toLowerCase();
        const matGroup = document.getElementById('matricula-group');
        const cursoGroup = document.getElementById('curso-group');
        const siapeGroup = document.getElementById('siape-group');
        if (matGroup) matGroup.classList.add('hidden');
        if (cursoGroup) cursoGroup.classList.add('hidden');
        if (siapeGroup) siapeGroup.classList.add('hidden');
        // remover required por padrão
        if (matGroup) { const mf = matGroup.querySelector('input'); if (mf) mf.required = false; }
        if (cursoField) cursoField.required = false;
        if (siapeGroup) { const sf = siapeGroup.querySelector('input'); if (sf) sf.required = false; }
        if (tipoLower === 'aluno') {
            if (matGroup) {
                matGroup.classList.remove('hidden');
                const mf = matGroup.querySelector('input'); if (mf) mf.required = true;
            }
            if (cursoGroup) cursoGroup.classList.remove('hidden');
            if (cursoField) cursoField.required = true;
        } else if (tipoLower === 'orientador') {
            if (siapeGroup) {
                siapeGroup.classList.remove('hidden');
                const sf = siapeGroup.querySelector('input'); if (sf) sf.required = true;
            }
        } else {
            // nenhum dos dois -> ambos ocultos
        }
    }

    if (tipo) {
        // inicializar visibilidade com valor atual
        updateFieldVisibility(tipo.value);
        tipo.addEventListener('change', (e) => updateFieldVisibility(e.target.value));
    }
}

// Inicialização automática conforme a página
document.addEventListener('DOMContentLoaded', () => {
    const filtroTipo = document.getElementById('filtro-tipo');
    const busca = document.getElementById('busca-usuarios');

    if (filtroTipo) {
        filtroTipo.addEventListener('change', fetchAndRenderUsers);
    }
    if (busca) {
        busca.addEventListener('input', fetchAndRenderUsers);
    }

    fetchAndRenderUsers();
    handleUsuarioForm();
});
