@extends('layout')

@section('title'){{ $employee->name }} · Cuadrante de turnos
@endsection

@section('content')
    <header class="encabezado">
        <p class="antetitulo">Ficha de empleado · {{ $employee->id }}</p>
        <h1>{{ $employee->name }}</h1>
        <p class="texto-secundario">Datos de la plantilla para organizar la semana.</p>
    </header>

    <dl class="panel ficha">
        <div>
            <dt>Identificador</dt>
            <dd>{{ $employee->id }}</dd>
        </div>
        <div>
            <dt>Rol</dt>
            <dd>{{ $roleLabels[$employee->role] }}</dd>
        </div>
        <div>
            <dt>Horas de contrato</dt>
            <dd>{{ $employee->contractHours }} h por semana</dd>
        </div>
    </dl>

    <a href="{{ route('employees.index') }}">Volver al listado de empleados</a>
@endsection
