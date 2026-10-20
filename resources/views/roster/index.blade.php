{{--
    La rejilla 7 × 3 del cuadrante, ya hecha: no es un ejercicio de diseño.

    Le falta el marco: hazla heredar de TU layout con @extends y ponla dentro
    de un @section. El formulario y el botón de quitar envían a
    roster.assign y roster.remove, con los campos day, shift y employee_id.
--}}

    {{-- El título es texto libre y se vuelve a mostrar: {{ }}, NUNCA {!! !!}. --}}
    <h1>{{ $title ?? 'Semana sin título' }}</h1>

    @if ($errors->any())
        <div class="errors">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

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

    {{-- F03/F04 · Asignar. La validación vive en el Form Request. --}}
    <form method="POST" action="{{ route('roster.assign') }}" class="asignar">
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

