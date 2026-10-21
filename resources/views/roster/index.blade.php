@extends('layout')

@section('title', 'Cuadrante semanal · Cuadrante de turnos')

@section('content')
    {{-- El título es texto libre y se vuelve a mostrar: {{ }}, NUNCA {!! !!}. --}}
    <header class="encabezado">
        <p class="antetitulo">Planificación semanal</p>
        <h1>{{ $title !== '' ? $title : 'Semana sin título' }}</h1>
        <p class="texto-secundario">Un empleado por turno. Asignar una celda ocupada sustituye a su empleado.</p>
    </header>

    @if ($errors->any())
        <div class="errors" role="alert">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('roster.title') }}" class="filtros panel">
        @csrf
        <div class="campo campo-titulo">
            <label for="week-title">Título de la semana</label>
            <input id="week-title" name="title" type="text" maxlength="160"
                value="{{ is_string(old('title', $title)) ? old('title', $title) : $title }}"
                placeholder="Por ejemplo: Semana del 19 de octubre">
        </div>
        <button type="submit" class="primario">Guardar título</button>
    </form>

    <div class="tabla-scroll">
    <div class="rejilla">
        <div></div>
        @foreach (\App\Data\Shifts::DAYS as $day)
            <div class="cab">{{ $day }}</div>
        @endforeach

        @foreach (\App\Data\Shifts::names() as $shift)
            <div class="turno">{{ $shift }}</div>

            @foreach (\App\Data\Shifts::DAYS as $day)
                <x-shift-cell :day="$day" :shift="$shift" :employee="$roster[$day][$shift] ?? null" />
            @endforeach
        @endforeach
    </div>
    </div>

    <p class="texto-secundario">Cada celda muestra quién cubre ese turno. Los turnos vacíos se marcan con un guion.</p>

    {{-- F03/F04 · Asignar. La validación vive en el Form Request. --}}
    <h2 class="seccion">Asignar un turno</h2>
    <form method="POST" action="{{ route('roster.assign') }}" class="asignar panel">
        @csrf

        <label>Día
            <select name="day">
                @foreach (\App\Data\Shifts::DAYS as $day)
                    <option value="{{ $day }}" @selected(old('day') === $day)>{{ $day }}</option>
                @endforeach
            </select>
        </label>

        <label>Turno
            <select name="shift">
                @foreach (\App\Data\Shifts::names() as $shift)
                    <option value="{{ $shift }}" @selected(old('shift') === $shift)>{{ $shift }}</option>
                @endforeach
            </select>
        </label>

        <label>Empleado
            <select name="employee_id">
                @foreach (\App\Data\Staff::all() as $employee)
                    <option value="{{ $employee->id }}" @selected((int) old('employee_id') === $employee->id)>{{ $employee->name }}</option>
                @endforeach
            </select>
        </label>

        <button class="primario">Asignar</button>
    </form>

    <p class="texto-secundario">
        La asignación comprueba que no haya dos turnos el mismo día y que se respeten 12 horas de descanso.
        Consulta los avisos de cobertura y horas en el <a href="{{ route('roster.summary') }}">resumen semanal</a>.
    </p>
@endsection
