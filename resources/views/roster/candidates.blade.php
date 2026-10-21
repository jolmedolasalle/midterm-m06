@extends('layout')

@section('title', 'Empleados disponibles · Cuadrante de turnos')

@section('content')
    <header class="encabezado">
        <p class="antetitulo">Ayuda para cubrir una celda vacía</p>
        <h1>Empleados disponibles</h1>
        <p>Turno de <strong>{{ $shift }}</strong> del <strong>{{ $day }}</strong>.</p>
        <p class="texto-secundario">
            {{ count($candidates) }} candidatos respetan el descanso mínimo y no tienen otro turno ese día.
            Se muestran en el orden de la plantilla.
        </p>
    </header>

    @if(count($candidates) > 0)
        <div class="tabla-scroll">
            <table>
                <caption class="solo-lector">Empleados que pueden cubrir el turno seleccionado</caption>
                <thead>
                    <tr>
                        <th scope="col">Empleado</th>
                        <th scope="col">Rol</th>
                        <th scope="col">Contrato semanal</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($candidates as $employee)
                        <tr>
                            <th scope="row">
                                <a href="{{ route('employees.show', ['id' => $employee->id]) }}">{{ $employee->name }}</a>
                            </th>
                            <td>{{ $employee->role }}</td>
                            <td>{{ $employee->contractHours }} h</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <h2 class="seccion">Rellenar este turno</h2>
        <p>El primer candidato es <strong>{{ $candidates[0]->name }}</strong>.</p>
        <form method="POST" action="{{ route('roster.auto') }}">
            @csrf
            <input type="hidden" name="day" value="{{ $day }}">
            <input type="hidden" name="shift" value="{{ $shift }}">
            <button type="submit" class="primario">Asignar al primer candidato</button>
        </form>
        <p class="texto-secundario">
            Al pulsar se comprueba de nuevo quién puede cubrirlo. Los avisos sobre horas de contrato
            se consultan después en el resumen y no excluyen candidatos.
        </p>
    @else
        <div class="aviso">
            No hay empleados disponibles para esta celda. Revisa las asignaciones y los descansos del cuadrante.
        </div>
    @endif

    <p><a href="{{ route('roster.index') }}">Volver al cuadrante</a></p>
@endsection
