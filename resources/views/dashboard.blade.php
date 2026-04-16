<!DOCTYPE html>
<html>
    <head>
        <title>Dashboard</title>
    </head>

    <body>
        <h1>Dashboard</h1>
        <p>Bienvenido {{auth()->user()->name}}</p>
        <p>Email {{auth()->user()->email}}</p>
        <p>Rol {{auth()->user()->role}}</p>
        <br>
        @if(auth()->user()->role==='admin')
        <a href="/admin">Ir al panel de admin</a>
        @endif
        <br>
        <form method="POST" action="/logout_1">
            @csrf
            <button type="submit">Cerrar Sesión</button>
        </form>
    </body>
</html>