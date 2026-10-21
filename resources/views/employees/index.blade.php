@extends('layout')

@section('title', 'Empleados · Cuadrante de turnos')

@section('content')
    <header class="encabezado">
        <p class="antetitulo">La plantilla del comercio</p>
        <h1>Empleados</h1>
        <p class="texto-secundario">Consulta el equipo, su función y las horas de su contrato semanal.</p>
    </header>

    <form method="GET" action="{{ route('employees.index') }}" class="filtros panel">
        <div class="campo">
            <label for="role">Filtrar por rol</label>
            <select id="role" name="role">
                <option value="" @selected($role === null)>Todos los roles</option>
                @foreach($roleLabels as $value => $label)
                    <option value="{{ $value }}" @selected($role === $value)>{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <div class="campo">
            <label for="sort">Ordenar por</label>
            <select id="sort" name="sort">
                <option value="name" @selected($sort === 'name')>Nombre: A–Z</option>
                <option value="hours" @selected($sort === 'hours')>Horas: de menor a mayor</option>
            </select>
        </div>
        <button type="submit" class="primario">Aplicar</button>
        <a href="{{ route('employees.index') }}">Restablecer</a>
    </form>

    <p class="texto-secundario">Mostrando {{ count($employees) }} de {{ $totalEmployees }} empleados.</p>
    <div class="tabla-scroll">
        <table>
            <caption class="solo-lector">Plantilla del comercio con los filtros y la ordenación seleccionados</caption>
            <thead>
                <tr>
                    <th scope="col">Empleado</th>
                    <th scope="col">Rol</th>
                    <th scope="col">Contrato semanal</th>
                </tr>
            </thead>
            <tbody>
                @forelse($employees as $employee)
                    <tr>
                        <th scope="row">
                            <a href="{{ route('employees.show', ['id' => $employee->id]) }}">{{ $employee->name }}</a>
                        </th>
                        <td>{{ $roleLabels[$employee->role] }}</td>
                        <td>{{ $employee->contractHours }} h</td>
                    </tr>
                @empty
                    <tr><td colspan="3">No hay empleados que coincidan con este filtro.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
