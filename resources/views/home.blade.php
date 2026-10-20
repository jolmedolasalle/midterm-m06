<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name') }}</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body>
    <main>
        <p class="tag">ICB0006 &middot; Acceso a datos</p>
        <h1>El contenedor responde.</h1>
        <p>
            Esto es el <strong>proyecto base</strong>: Laravel {{ app()->version() }}
            sobre PHP {{ PHP_VERSION }}, sin base de datos y sin paso de compilacion.
        </p>
        <p>
            Si ves esta pagina, tu entorno esta bien y puedes empezar. Lo primero
            que haras es sustituirla.
        </p>
        <ul>
            <li>La ruta que te ha traido aqui esta en <code>routes/web.php</code>.</li>
            <li>Esta pagina es <code>resources/views/home.blade.php</code>.</li>
            <li>El estilo es <code>public/css/app.css</code>, servido tal cual.</li>
        </ul>
    </main>
</body>
</html>
