<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Perfil</title>
</head>
<body>
<h1>Editar Perfil</h1>

@if(session('success'))
    <div style="color: green;">{{ session('success') }}</div>
@endif

<form method="POST" action="{{ route('user.update') }}">
    @csrf
    @method('PUT')

    <div>
        <label>Nombre:</label>
        <input type="text" name="name" value="{{ old('name', $user->name) }}" required>
    </div>

    <div>
        <label>Correo Electrónico:</label>
        <input type="email" name="email" value="{{ old('email', $user->email) }}" required>
    </div>

    <div>
        <label>Contraseña (dejar vacío para no cambiarla):</label>
        <input type="password" name="password">
    </div>

    <div>
        <label>Confirmar Contraseña:</label>
        <input type="password" name="password_confirmation">
    </div>

    <button type="submit">Guardar Cambios</button>
</form>
</body>
</html>
