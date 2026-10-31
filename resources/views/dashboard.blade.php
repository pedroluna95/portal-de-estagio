@extends('layouts.app')

@section('content')
@php $tipo = $papelDashboard ?? session('user_type', 'aluno'); @endphp

@if($tipo === 'aluno')
@include('partials.dashboard-aluno')
@elseif($tipo === 'supervisor')
@include('partials.dashboard-supervisor')
@elseif($tipo === 'orientador')
@include('partials.dashboard-orientador')
@elseif($tipo === 'administrador')
@include('partials.dashboard-administrador')
@endif
@endsection
