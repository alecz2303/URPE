<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>URPE Gestión Clínica</title>
</head>
<body>
    <main>
        <h1>URPE Gestión Clínica</h1>
        <p>Plataforma clínica en desarrollo bajo el flujo canónico de URPE.</p>

        @auth
            <p><a href="{{ route('dashboard') }}">Ir al dashboard</a></p>
        @else
            <p><a href="{{ route('login') }}">Iniciar sesión</a></p>
        @endauth
    </main>
</body>
</html>
