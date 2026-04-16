@extends('layouts.app')

@section('title', 'Login')

@section('content')

    <h1>Login</h1>

    <form method="POST" action="/login_1">
        @csrf
        <input type="email" name="email" placeholder="Email">
        <br>
        <input type="password" name="password" placeholder="Password">
        <br>
        <button type="submit">Entrar</button>
    </form>

@endsection