<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel Admin</title>
</head>
<body>
    <h1>Panel de administrador</h1>

    <p>Hola, {{ auth()->user()->name }}</p>
    <p>Tu rol es: {{ auth()->user()->role }}</p>

    <a href="/dashboard">Volver al dashboard</a>
</body>
</html>