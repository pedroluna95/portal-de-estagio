@extends('layouts.app')

@section('content')
{{-- Hero Principal NEXO --}}
<section class="relative overflow-hidden bg-gradient-to-br from-blue-600 via-indigo-600 to-blue-800 text-white py-20 lg:py-28">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        <div class="max-w-3xl">
            <div class="inline-flex items-center gap-2 px-3.5 py-1.5 bg-white/15 backdrop-blur-md rounded-full text-xs font-semibold text-blue-100 mb-6 border border-white/20 shadow-sm">
                <span class="material-symbols-outlined text-[18px]">school</span>
                Portal Oficial de Estágios · UFCI-RJ
            </div>
            <h1 class="text-4xl sm:text-5xl lg:text-6xl font-black leading-tight tracking-tight mb-6">
                Seu estágio formalizado em um só lugar.
            </h1>
            <p class="text-base sm:text-xl text-blue-100 mb-8 leading-relaxed font-normal">
                Conecte alunos, orientadores e supervisores de empresas. Controle digital de frequência diária, validação segura de ponto e catálogo de empresas conveniadas.
            </p>
            <div class="flex flex-col sm:flex-row gap-3.5">
                <a href="/cadastro" class="inline-flex justify-center items-center gap-2 px-7 py-3.5 rounded-2xl bg-white text-blue-700 font-bold text-sm shadow-xl shadow-blue-900/20 hover:bg-blue-50 transition-all hover:-translate-y-0.5">
                    <span class="material-symbols-outlined text-[20px]">person_add</span>
                    Criar Minha Conta
                </a>
                <a href="/login" class="inline-flex justify-center items-center gap-2 px-7 py-3.5 rounded-2xl bg-white/10 backdrop-blur-md border border-white/25 text-white font-semibold text-sm hover:bg-white/20 transition-all">
                    <span class="material-symbols-outlined text-[20px]">login</span>
                    Já tenho conta
                </a>
            </div>
        </div>
    </div>
    {{-- Detalhes decorativos de fundo --}}
    <div class="absolute -right-20 -bottom-20 w-96 h-96 bg-white/10 rounded-full blur-3xl pointer-events-none"></div>
    <div class="absolute top-10 right-1/4 w-72 h-72 bg-indigo-400/20 rounded-full blur-3xl pointer-events-none"></div>
</section>

{{-- Funcionalidades Principais --}}
<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 sm:py-24">
    <div class="text-center max-w-2xl mx-auto mb-14">
        <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-900 dark:text-slate-100 tracking-tight">
            Tudo o que você precisa para o seu estágio
        </h2>
        <p class="mt-2 text-sm sm:text-base text-slate-500 dark:text-slate-400">
            Uma plataforma pensada para simplificar todas as obrigações pedagógicas e burocráticas da formação técnica e superior.
        </p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        {{-- Card 1 --}}
        <div class="rounded-2xl border border-slate-200/80 dark:border-slate-800 bg-white dark:bg-slate-900 p-7 shadow-sm hover:shadow-md hover:border-blue-400 dark:hover:border-blue-800 transition-all">
            <div class="w-12 h-12 rounded-2xl bg-blue-50 dark:bg-blue-950/60 text-blue-600 dark:text-blue-400 flex items-center justify-center mb-5">
                <span class="material-symbols-outlined text-[26px]">schedule</span>
            </div>
            <h3 class="text-lg font-bold text-slate-900 dark:text-slate-100 mb-2">Controle Digital de Horas</h3>
            <p class="text-sm text-slate-600 dark:text-slate-400 leading-relaxed">
                Marcação simplificada com grade visual mensal. A carga diária acordada é contabilizada e enviada para o supervisor validar com agilidade.
            </p>
        </div>

        {{-- Card 2 --}}
        <div class="rounded-2xl border border-slate-200/80 dark:border-slate-800 bg-white dark:bg-slate-900 p-7 shadow-sm hover:shadow-md hover:border-blue-400 dark:hover:border-blue-800 transition-all">
            <div class="w-12 h-12 rounded-2xl bg-indigo-50 dark:bg-indigo-950/60 text-indigo-600 dark:text-indigo-400 flex items-center justify-center mb-5">
                <span class="material-symbols-outlined text-[26px]">school</span>
            </div>
            <h3 class="text-lg font-bold text-slate-900 dark:text-slate-100 mb-2">Tutorial Passo a Passo</h3>
            <p class="text-sm text-slate-600 dark:text-slate-400 leading-relaxed">
                Trilha com 7 etapas claras instruindo os regulamentos da Lei nº 11.788, cálculo de horas mínimas e modelos de relatórios finais.
            </p>
        </div>

        {{-- Card 3 --}}
        <div class="rounded-2xl border border-slate-200/80 dark:border-slate-800 bg-white dark:bg-slate-900 p-7 shadow-sm hover:shadow-md hover:border-blue-400 dark:hover:border-blue-800 transition-all">
            <div class="w-12 h-12 rounded-2xl bg-emerald-50 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 flex items-center justify-center mb-5">
                <span class="material-symbols-outlined text-[26px]">domain</span>
            </div>
            <h3 class="text-lg font-bold text-slate-900 dark:text-slate-100 mb-2">Empresas Conveniadas</h3>
            <p class="text-sm text-slate-600 dark:text-slate-400 leading-relaxed">
                Catálogo sempre atualizado com empresas conveniadas à UFCI-RJ, facilitando a formalização com documentação simplificada.
            </p>
        </div>

        {{-- Card 4 --}}
        <div class="rounded-2xl border border-slate-200/80 dark:border-slate-800 bg-white dark:bg-slate-900 p-7 shadow-sm hover:shadow-md hover:border-blue-400 dark:hover:border-blue-800 transition-all">
            <div class="w-12 h-12 rounded-2xl bg-purple-50 dark:bg-purple-950/60 text-purple-600 dark:text-purple-400 flex items-center justify-center mb-5">
                <span class="material-symbols-outlined text-[26px]">verified_user</span>
            </div>
            <h3 class="text-lg font-bold text-slate-900 dark:text-slate-100 mb-2">Validação em Três Níveis</h3>
            <p class="text-sm text-slate-600 dark:text-slate-400 leading-relaxed">
                Estudantes lançam horas, supervisores validam o cumprimento no ambiente corporativo e orientadores homologam a aprovação acadêmica.
            </p>
        </div>

        {{-- Card 5 --}}
        <div class="rounded-2xl border border-slate-200/80 dark:border-slate-800 bg-white dark:bg-slate-900 p-7 shadow-sm hover:shadow-md hover:border-blue-400 dark:hover:border-blue-800 transition-all">
            <div class="w-12 h-12 rounded-2xl bg-amber-50 dark:bg-amber-950/60 text-amber-600 dark:text-amber-400 flex items-center justify-center mb-5">
                <span class="material-symbols-outlined text-[26px]">description</span>
            </div>
            <h3 class="text-lg font-bold text-slate-900 dark:text-slate-100 mb-2">Download de Modelos Oficiais</h3>
            <p class="text-sm text-slate-600 dark:text-slate-400 leading-relaxed">
                Acesse com um clique os modelos de Ficha Individual de Frequência e Relatório de Conclusão já formatados nos padrões da UFCI.
            </p>
        </div>

        {{-- Card 6 --}}
        <div class="rounded-2xl border border-slate-200/80 dark:border-slate-800 bg-white dark:bg-slate-900 p-7 shadow-sm hover:shadow-md hover:border-blue-400 dark:hover:border-blue-800 transition-all">
            <div class="w-12 h-12 rounded-2xl bg-rose-50 dark:bg-rose-950/60 text-rose-600 dark:text-rose-400 flex items-center justify-center mb-5">
                <span class="material-symbols-outlined text-[26px]">dark_mode</span>
            </div>
            <h3 class="text-lg font-bold text-slate-900 dark:text-slate-100 mb-2">Interface Moderna e Dark Mode</h3>
            <p class="text-sm text-slate-600 dark:text-slate-400 leading-relaxed">
                Visual totalmente reformulado com suporte a temas claro e escuro, acessível em qualquer smartphone, tablet ou computador.
            </p>
        </div>
    </div>
</section>
@endsection
