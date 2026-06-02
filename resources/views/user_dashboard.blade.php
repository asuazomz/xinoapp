<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Usuario</title>
</head>
<body>
    <h1>Dashboard de Usuario</h1>

    <p>Bienvenido {{ auth()->user()->name }}</p>
    <p>Email: {{ auth()->user()->email }}</p>
    <p>Rol: {{ auth()->user()->role }}</p>

    <p>Este es el panel para usuarios normales.</p>

    <form method="POST" action="/logout">
        @csrf
        <button type="submit">Cerrar sesión</button>
    </form>
</body>
</html>