@extends('layout')

@section('title', 'Resumen semanal · Cuadrante de turnos')

@section('content')
    <header class="encabezado">
        <p class="antetitulo">Revisión de la planificación</p>
        <h1>Resumen semanal</h1>
        <p>{{ $title !== '' ? $title : 'Semana sin título' }}</p>
        <p class="texto-secundario">Estos avisos ayudan a completar la semana y no bloquean las asignaciones.</p>
    </header>

    <h2 class="seccion">Horas por empleado · R03</h2>
    <div class="tabla-scroll">
        <table>
            <caption class="solo-lector">Comparación entre horas asignadas y contrato semanal</caption>
            <thead>
                <tr>
                    <th scope="col">Empleado</th>
                    <th scope="col">Asignadas</th>
                    <th scope="col">Contrato</th>
                    <th scope="col">Balance</th>
                </tr>
            </thead>
            <tbody>
                @foreach($employees as $row)
                    <tr>
                        <th scope="row">
                            <a href="{{ route('employees.show', ['id' => $row['employee']->id]) }}">{{ $row['employee']->name }}</a>
                        </th>
                        <td>{{ $row['hours'] }} h</td>
                        <td>{{ $row['employee']->contractHours }} h</td>
                        <td>
                            @if($row['excess'] > 0)
                                <span class="estado-aviso">Exceso de {{ $row['excess'] }} h</span>
                            @elseif($row['hours'] === $row['employee']->contractHours)
                                Contrato cubierto
                            @else
                                Faltan {{ $row['employee']->contractHours - $row['hours'] }} h
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <h2 class="seccion">Cobertura de turnos · R04</h2>
    <p>Turnos sin cubrir: <strong>{{ count($emptySlots) }}</strong>.</p>
    <div class="tabla-scroll">
        <table>
            <caption class="solo-lector">Número de empleados asignados a cada turno y día: cero o uno</caption>
            <thead>
                <tr>
                    <th scope="col">Turno</th>
                    @foreach(\App\Data\Shifts::DAYS as $day)
                        <th scope="col" class="capitalizar">{{ $day }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @foreach(\App\Data\Shifts::names() as $shift)
                    <tr>
                        <th scope="row" class="capitalizar">{{ $shift }}</th>
                        @foreach(\App\Data\Shifts::DAYS as $day)
                            <td>
                                @if($coverage[$day][$shift] === 0)
                                    <span class="estado-aviso">0 · Vacío</span>
                                @else
                                    1 · Cubierto
                                @endif
                            </td>
                        @endforeach
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <h2 class="seccion">Días sin responsable · R05</h2>
    @if(count($daysWithoutResponsible) > 0)
        <div class="aviso">
            <p>Falta asignar una persona con rol de responsable en estos días:</p>
            <ul class="lista-dias">
                @foreach($daysWithoutResponsible as $day)
                    <li class="capitalizar">{{ $day }}</li>
                @endforeach
            </ul>
        </div>
    @else
        <p>Todos los días cuentan con al menos un responsable.</p>
    @endif

    <p><a href="{{ route('roster.index') }}">Volver al cuadrante</a></p>
@endsection
