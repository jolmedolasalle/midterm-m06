@extends('layout')

@section('title', 'Página no encontrada · Cuadrante de turnos')

@section('content')
    <section class="panel pagina-error">
        <p class="antetitulo">Error 404</p>
        <h1>No hemos encontrado lo que buscas</h1>
        <p>{{ $message ?? 'La página que buscas no existe.' }}</p>
        <a href="{{ route('employees.index') }}">Consultar la plantilla de empleados</a>
    </section>
@endsection
