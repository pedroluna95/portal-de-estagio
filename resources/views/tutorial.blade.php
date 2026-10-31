@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    {{-- Hero --}}
    <div class="fig-gradient rounded-2xl p-8 mb-8 text-white">
        <h1 class="text-3xl font-bold mb-2">Trilha Completa do Estágio</h1>
        <p class="text-blue-100">7 etapas para você completar seu estágio com segurança e sem dúvidas</p>
        <div class="flex flex-wrap gap-4 mt-6 text-sm">
            <span class="inline-flex items-center gap-2 px-3 py-1.5 bg-white/15 rounded-full">
                <span class="material-symbols-outlined text-[16px]">route</span> 7 Etapas
            </span>
            <span class="inline-flex items-center gap-2 px-3 py-1.5 bg-white/15 rounded-full">
                <span class="material-symbols-outlined text-[16px]">schedule</span> 240h de estágio
            </span>
            <span class="inline-flex items-center gap-2 px-3 py-1.5 bg-white/15 rounded-full">
                <span class="material-symbols-outlined text-[16px]">business</span> 48+ empresas conveniadas
            </span>
        </div>
    </div>

    {{-- Timeline das etapas --}}
    <div class="space-y-4">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 flex gap-4">
            <div class="w-12 h-12 rounded-xl bg-blue-50 flex items-center justify-center flex-shrink-0">
                <span class="material-symbols-outlined text-blue-600">lightbulb</span>
            </div>
            <div>
                <h3 class="text-lg font-semibold text-gray-900 mb-1">1. Conceito de Estágio</h3>
                <p class="text-sm text-gray-600">O estágio é um ato educativo escolar supervisionado que prepara o estudante para o ambiente de trabalho produtivo pondo em prática os conhecimentos adquiridos durante o curso. É regulamentado pela Lei nº 11.788/2008 e faz parte do projeto pedagógico do curso.</p>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 flex gap-4">
            <div class="w-12 h-12 rounded-xl bg-purple-50 flex items-center justify-center flex-shrink-0">
                <span class="material-symbols-outlined text-purple-600">category</span>
            </div>
            <div>
                <h3 class="text-lg font-semibold text-gray-900 mb-1">2. Tipos de Estágio</h3>
                <p class="text-sm text-gray-600">O estágio pode ser <strong>obrigatório</strong> (exigido pela grade curricular) ou <strong>não obrigatório</strong> (opcional, complementa a formação). Podendo ser interno (na sua unidade do CEFET através de professores e projetos fornecendo estágio). Ou externo (em empresas conveniadas).</p>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 flex gap-4">
            <div class="w-12 h-12 rounded-xl bg-orange-50 flex items-center justify-center flex-shrink-0">
                <span class="material-symbols-outlined text-orange-600">timer</span>
            </div>
            <div>
                <h3 class="text-lg font-semibold text-gray-900 mb-1">3. Horas Obrigatórias</h3>
                <p class="text-sm text-gray-600">Cada curso exige um número total de horas: geralmente <strong>400</strong> para estágio obrigatório. Desse total de horas, há uma parcela de <strong>160 horas </strong> que <strong>podem</strong> ser cumpridas sendo monitor de alguma matéria do técnico ou participando de projetos de extensão. Assim estabelecendo que se voce seja monitor ou participe de algum projeto, <strong>o mínimo de horas a serem cumpridas de estágio interno ou externo é de 240 horas</strong>.</p>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 flex gap-4">
            <div class="w-12 h-12 rounded-xl bg-green-50 flex items-center justify-center flex-shrink-0">
                <span class="material-symbols-outlined text-green-600">business</span>
            </div>
            <div class="flex-1">
                <h3 class="text-lg font-semibold text-gray-900 mb-1">4. Empresas Conveniadas</h3>
                <p class="text-sm text-gray-600">O CEFET possui parcerias com mais de 48 empresas. O processo de documentação é simplificado: basta consultar a lista de empresas conveniadas e escolher onde deseja estagiar.</p>
                <a href="/empresas" class="inline-flex items-center gap-2 mt-3 px-4 py-2 bg-[#0077fc] text-white rounded-lg text-sm font-medium hover:bg-[#0056c9] transition-colors">
                    <span class="material-symbols-outlined text-[16px]">business</span>
                    Ver Empresas Conveniadas
                </a>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 flex gap-4">
            <div class="w-12 h-12 rounded-xl bg-indigo-50 flex items-center justify-center flex-shrink-0">
                <span class="material-symbols-outlined text-indigo-600">folder</span>
            </div>
            <div>
                <h3 class="text-lg font-semibold text-gray-900 mb-1">5. Documentação Necessária</h3>
                <p class="text-sm text-gray-600">Para formalizar o estágio são necessários: <strong>Termo de Compromisso</strong> (assinado por aluno, empresa e instituição), <strong>Plano de Atividades</strong> e seguro contra acidentes pessoais.</p>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 flex gap-4">
            <div class="w-12 h-12 rounded-xl bg-yellow-50 flex items-center justify-center flex-shrink-0">
                <span class="material-symbols-outlined text-yellow-600">calendar_month</span>
            </div>
            <div class="flex-1">
                <h3 class="text-lg font-semibold text-gray-900 mb-1">6. Registro e Controle de Horas</h3>
                <p class="text-sm text-gray-600">Registre suas presenças diariamente no controle de horas do portal, como um ponto digital. Seu supervisor validará as horas registradas.</p>
                <a href="/controle-horas" class="inline-flex items-center gap-2 mt-3 px-4 py-2 bg-[#0077fc] text-white rounded-lg text-sm font-medium hover:bg-[#0056c9] transition-colors">
                    <span class="material-symbols-outlined text-[16px]">calendar_month</span>
                    Registrar Presença
                </a>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 flex gap-4">
            <div class="w-12 h-12 rounded-xl bg-green-50 flex items-center justify-center flex-shrink-0">
                <span class="material-symbols-outlined text-green-600">flag</span>
            </div>
            <div>
                <h3 class="text-lg font-semibold text-gray-900 mb-1">7. Conclusão do Estágio</h3>
                <p class="text-sm text-gray-600">Ao completar as horas exigidas, o orientador faz a avaliação final e a ficha de conclusão é gerada automaticamente. As horas são então registradas no histórico acadêmico.</p>
            </div>

            {{-- Botões que dão acesso à ficha de frequência e ao modelo do relatório --}}
            <a href="{{ asset('documents/CEFET - Ficha Individual de Frequência.xlsx') }}" class="inline-flex items-center gap-2 mt-3 px-4 py-2 bg-[#0077fc] text-white rounded-lg text-sm font-medium hover:bg-[#0056c9] transition-colors">
                    <span class="material-symbols-outlined text-[16px]">app_registration</span>
                    Ficha de Frequência
                </a>
            <a href="{{ asset('documents/CEFET -Relatorio de estagio modelo 5.docx') }}" class="inline-flex items-center gap-2 mt-3 px-4 py-2 bg-[#0077fc] text-white rounded-lg text-sm font-medium hover:bg-[#0056c9] transition-colors">
                    <span class="material-symbols-outlined text-[16px]">description</span>
                    Modelo de Relatório
                </a>
        </div>
    </div>

    {{-- CTA final --}}
    <div class="fig-gradient rounded-2xl p-8 mt-8 text-white text-center">
        <h2 class="text-2xl font-bold mb-2">Pronto para começar?</h2>
        <p class="text-blue-100 mb-6">Consulte as empresas conveniadas ao CEFET e conheça as etapas para formalizar seu estágio.</p>
        <a href="/empresas" class="btn-pill btn-pill-primary">Ver Empresas Conveniadas</a>
    </div>
</div>
@endsection
