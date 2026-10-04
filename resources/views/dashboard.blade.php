@extends('layouts.app')

@section('content')
{{-- Dashboard — orquestador por perfil, igual ao Figma --}}
@php $tipo = $papelDashboard ?? session('user_type', 'aluno'); @endphp

@if($tipo === 'aluno')
@include('partials.dashboard-aluno')
@elseif($tipo === 'supervisor')
@include('partials.dashboard-supervisor')
@elseif($tipo === 'orientador')
@include('partials.dashboard-orientador')
@elseif($tipo === 'contratante')
@include('partials.dashboard-contratante')
@else
@include('partials.dashboard-administrador')
@endif
@endsection
