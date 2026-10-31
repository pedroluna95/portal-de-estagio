@extends('layouts.app')

@section('content')
{{-- Hero do Figma — gradiente azul #0077fc → #0056c9 --}}
<section class="fig-gradient text-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20 lg:py-28">
        <div class="max-w-3xl">
            <span class="inline-flex items-center gap-2 px-4 py-1.5 bg-white/15 rounded-full text-sm font-medium text-blue-100 mb-6">
                <span class="material-symbols-outlined text-[18px]">school</span>
                CEFET-RJ — Estágios Internos
            </span>
            <h1 class="text-4xl sm:text-5xl font-bold leading-tight mb-6">
                Seu estágio começa aqui
            </h1>
            <p class="text-lg text-blue-100 mb-8 leading-relaxed">
                Portal oficial de estágios do CEFET-RJ. Monitoria, projetos de extensão e estágios
                internos em um só lugar — com controle de horas, tutorial completo e empresas conveniadas.
            </p>
            <div class="flex flex-col sm:flex-row gap-4">
                <a href="/cadastro" class="btn-pill btn-pill-primary text-center">Criar Conta</a>
                <a href="/login" class="btn-pill btn-pill-secondary text-center">Já tenho conta</a>
            </div>
        </div>
    </div>
</section>

{{-- Funcionalidades (estilo Figma) --}}
<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
    <div class="text-center mb-12">
        <h2 class="text-3xl font-bold text-gray-900 mb-3">Tudo o que você precisa</h2>
        <p class="text-gray-600 max-w-2xl mx-auto">
            Uma plataforma completa para alunos, supervisores, orientadores e empresas conveniadas.
        </p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="card-figma p-6">
            <div class="w-12 h-12 rounded-xl bg-green-50 flex items-center justify-center mb-4">
                <span class="material-symbols-outlined text-green-600">schedule</span>
            </div>
            <h3 class="text-lg font-semibold text-gray-900 mb-2">Controle de Horas</h3>
            <p class="text-gray-600 text-sm">Registro de presença tipo ponto digital, com validação pelo supervisor de estágio interno.</p>
        </div>

        <div class="card-figma p-6">
            <div class="w-12 h-12 rounded-xl bg-purple-50 flex items-center justify-center mb-4">
                <span class="material-symbols-outlined text-purple-600">menu_book</span>
            </div>
            <h3 class="text-lg font-semibold text-gray-900 mb-2">Tutorial Completo</h3>
            <p class="text-gray-600 text-sm">Trilha com 7 etapas ensinando todo o processo, do conceito até a conclusão do estágio.</p>
        </div>

        <div class="card-figma p-6">
            <div class="w-12 h-12 rounded-xl bg-orange-50 flex items-center justify-center mb-4">
                <span class="material-symbols-outlined text-orange-600">groups</span>
            </div>
            <h3 class="text-lg font-semibold text-gray-900 mb-2">Empresas Conveniadas</h3>
            <p class="text-gray-600 text-sm">Mais de 100 empresas conveniadas ao CEFET com o processo de documentação simplificado.</p>
        </div>

        <div class="card-figma p-6">
            <div class="w-12 h-12 rounded-xl bg-pink-50 flex items-center justify-center mb-4">
                <span class="material-symbols-outlined text-pink-600">verified_user</span>
            </div>
            <h3 class="text-lg font-semibold text-gray-900 mb-2">Validação Segura</h3>
            <p class="text-gray-600 text-sm">Alunos, orientadores e supervisores acompanham e validam as etapas que lhes competem.</p>
        </div>

        <div class="card-figma p-6">
            <div class="w-12 h-12 rounded-xl bg-cyan-50 flex items-center justify-center mb-4">
                <span class="material-symbols-outlined text-cyan-600">description</span>
            </div>
            <h3 class="text-lg font-semibold text-gray-900 mb-2">Documentação Automática</h3>
            <p class="text-gray-600 text-sm">Ao completar as horas, o sistema gera a ficha de avaliação e o relatório final.</p>
        </div>
    </div>
</section>
        </div>
    </div>
</section>
@endsection
