<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Cuadrante de turnos')</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <link rel="stylesheet" href="{{ asset('css/ui.css') }}">
</head>
<body>
    <nav class="nav" aria-label="Navegación principal">
        <div class="wrap">
            <strong>Cuadrante de turnos</strong>
            <a href="{{ route('roster.index') }}"
                @if(request()->routeIs('roster.index', 'roster.candidates')) aria-current="page" @endif>
                Cuadrante
            </a>
            <a href="{{ route('employees.index') }}"
                @if(request()->routeIs('employees.*')) aria-current="page" @endif>
                Empleados
            </a>
            <a href="{{ route('roster.summary') }}"
                @if(request()->routeIs('roster.summary')) aria-current="page" @endif>
                Resumen
            </a>
        </div>
    </nav>

    <main class="wrap">
        @if(session('status'))
            <div class="flash" role="status">{{ session('status') }}</div>
        @endif

        @yield('content')
    </main>
</body>
</html>
