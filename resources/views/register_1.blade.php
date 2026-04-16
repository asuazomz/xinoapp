@extends('layouts.app')

@section('title', 'Registro')

@section('content')

    <h1>Registro</h1>

    <form method="POST" action="/register_1">
        @csrf

        <input type="text" name="name" placeholder="Name">
        <br>
        <input type="email" name="email" placeholder="Email">
        <br>
        <input type="password" name="password" placeholder="Password">
        <br>

        <select name="role">
            <option value="usuario">Usuario</option>
            <option value="admin">Admin</option>
        </select>

        <button type="submit">Registrar</button>
    </form>
@endsection