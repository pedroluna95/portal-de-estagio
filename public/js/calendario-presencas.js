document.addEventListener('DOMContentLoaded', () => {
    const dadosElement = document.getElementById('dados-calendario-presencas');
    const grade = document.getElementById('calendario-presencas');
    const tituloMes = document.getElementById('mes-calendario');
    const botaoAnterior = document.getElementById('mes-anterior');
    const botaoProximo = document.getElementById('proximo-mes');
    const campoData = document.getElementById('data-presenca');
    const modal = document.getElementById('modal-presenca');
    const botaoAbrirModal = document.getElementById('abrir-modal-presenca');
    const botaoFecharModal = document.getElementById('fechar-modal-presenca');
    const botaoCancelarModal = document.getElementById('cancelar-modal-presenca');
    const formulario = campoData?.form;

    if (!dadosElement || !grade || !tituloMes || !botaoAnterior || !botaoProximo || !campoData || !modal) return;

    const dados = JSON.parse(dadosElement.textContent);
    const [anoHoje, mesHoje] = dados.hoje.split('-').map(Number);
    const [anoInicio, mesInicio] = dados.inicio.split('-').map(Number);
    const [anoFim, mesFim] = dados.fim.split('-').map(Number);
    const mesMinimo = new Date(anoInicio, mesInicio - 1, 1);
    const mesMaximo = new Date(anoFim, mesFim - 1, 1);
    let mesAtual = new Date(anoHoje, mesHoje - 1, 1);

    if (mesAtual < mesMinimo) mesAtual = new Date(mesMinimo);
    if (mesAtual > mesMaximo) mesAtual = new Date(mesMaximo);

    const formatarData = (data) => [
        data.getFullYear(),
        String(data.getMonth() + 1).padStart(2, '0'),
        String(data.getDate()).padStart(2, '0'),
    ].join('-');

    const dentroDoEstagio = (data) => data >= dados.inicio && data <= dados.fim;

    function selecionarDataInicial() {
        if (campoData.value) return;

        const [ano, mes, dia] = dados.hoje.split('-').map(Number);
        const data = new Date(ano, mes - 1, dia);
        data.setDate(data.getDate() - 1);

        while (formatarData(data) >= dados.inicio) {
            const chaveData = formatarData(data);
            const diaIso = data.getDay() === 0 ? 7 : data.getDay();
            if (dados.dias.includes(diaIso) && !dados.presencas[chaveData] && dentroDoEstagio(chaveData)) {
                campoData.value = chaveData;
                return;
            }
            data.setDate(data.getDate() - 1);
        }
    }

    function abrirModal() {
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        requestAnimationFrame(() => campoData.focus());
    }

    function fecharModal() {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        botaoAbrirModal?.focus();
    }

    function criarCelula(numero, data, status, rotulo, selecionavel) {
        const elemento = document.createElement(selecionavel ? 'button' : 'span');
        if (selecionavel) elemento.type = 'button';
        elemento.className = 'aspect-square min-w-0 rounded text-sm font-medium transition-colors';
        elemento.setAttribute('role', 'gridcell');
        elemento.textContent = String(numero);

        const classes = {
            presente: 'border border-green-300 bg-green-200 text-green-900 hover:bg-green-300',
            falta: 'border border-red-300 bg-red-200 text-red-900 hover:bg-red-300',
            recusada: 'border border-red-300 bg-red-200 text-red-900 hover:bg-red-300',
            pendente: 'border border-amber-300 bg-amber-200 text-amber-950 hover:bg-amber-300',
            pessoal: 'border border-blue-300 bg-blue-200 text-blue-950 hover:bg-blue-300',
            neutro: 'bg-gray-100 text-gray-500',
            fora: 'bg-white text-gray-300',
        };
        elemento.classList.add(...classes[status].split(' '));
        elemento.setAttribute('aria-label', `${numero}: ${rotulo}`);
        elemento.title = rotulo;

        if (data === dados.hoje) elemento.classList.add('ring-2', 'ring-[#0077fc]', 'ring-offset-1');
        if (selecionavel) {
            elemento.setAttribute('aria-pressed', String(campoData.value === data));
            elemento.addEventListener('click', () => {
                campoData.value = data;
                campoData.dispatchEvent(new Event('change', {bubbles: true}));
                    abrirModal();
                renderizar();
            });
        }

        return elemento;
    }

    function renderizar() {
        tituloMes.textContent = new Intl.DateTimeFormat('pt-BR', {
            month: 'long',
            year: 'numeric',
        }).format(mesAtual);
        tituloMes.textContent = tituloMes.textContent.charAt(0).toLocaleUpperCase('pt-BR') + tituloMes.textContent.slice(1);
        botaoAnterior.disabled = mesAtual <= mesMinimo;
        botaoProximo.disabled = mesAtual >= mesMaximo;
        botaoAnterior.classList.toggle('opacity-40', botaoAnterior.disabled);
        botaoProximo.classList.toggle('opacity-40', botaoProximo.disabled);
        grade.replaceChildren();

        const primeiroDia = new Date(mesAtual.getFullYear(), mesAtual.getMonth(), 1);
        const totalDias = new Date(mesAtual.getFullYear(), mesAtual.getMonth() + 1, 0).getDate();
        const deslocamento = primeiroDia.getDay();

        for (let indice = 0; indice < 42; indice++) {
            const dia = indice - deslocamento + 1;
            if (dia < 1 || dia > totalDias) {
                const vazio = document.createElement('span');
                vazio.className = 'aspect-square';
                vazio.setAttribute('aria-hidden', 'true');
                grade.appendChild(vazio);
                continue;
            }

            const data = new Date(mesAtual.getFullYear(), mesAtual.getMonth(), dia);
            const chaveData = formatarData(data);
            const diaIso = data.getDay() === 0 ? 7 : data.getDay();
            const registro = dados.presencas[chaveData];
            const previsto = dados.dias.includes(diaIso);
            const estaNoPeriodo = dentroDoEstagio(chaveData);
            let status = 'neutro';
            let rotulo = 'Dia fora da jornada';

            if (!estaNoPeriodo) {
                status = 'fora';
                rotulo = 'Fora do período do estágio';
            } else if (registro?.modo === 'pessoal') {
                status = 'pessoal';
                rotulo = 'Registro pessoal, visível somente para você';
            } else if (registro?.status === 'recusada') {
                status = 'recusada';
                rotulo = 'Presença recusada';
            } else if (registro?.status === 'pendente') {
                status = 'pendente';
                rotulo = 'Presença oficial aguardando validação do supervisor';
            } else if (registro?.status === 'aprovada') {
                status = 'presente';
                rotulo = 'Presença validada pelo supervisor';
            } else if (previsto && chaveData < dados.hoje) {
                status = 'falta';
                rotulo = 'Falta, sem presença registrada';
            } else if (previsto) {
                status = 'pendente';
                rotulo = 'Pendente preencher';
            }

            const podeSelecionar = estaNoPeriodo && previsto && !registro && chaveData < dados.hoje;
            grade.appendChild(criarCelula(dia, chaveData, status, rotulo, podeSelecionar));
        }
    }

    botaoAnterior.addEventListener('click', () => {
        mesAtual = new Date(mesAtual.getFullYear(), mesAtual.getMonth() - 1, 1);
        renderizar();
    });
    botaoProximo.addEventListener('click', () => {
        mesAtual = new Date(mesAtual.getFullYear(), mesAtual.getMonth() + 1, 1);
        renderizar();
    });
    campoData.addEventListener('change', renderizar);
    botaoAbrirModal?.addEventListener('click', abrirModal);
    botaoFecharModal?.addEventListener('click', fecharModal);
    botaoCancelarModal?.addEventListener('click', fecharModal);
    modal.addEventListener('click', (evento) => {
        if (evento.target === modal) fecharModal();
    });
    document.addEventListener('keydown', (evento) => {
        if (evento.key === 'Escape' && !modal.classList.contains('hidden')) fecharModal();
    });
    selecionarDataInicial();
    renderizar();

    if (modal.dataset.abertoPorErro === 'true') abrirModal();
});