@extends('layouts.app')

@section('title', 'Dashboard de Usuario')

@section('content')

    <h1>Dashboard de Usuario</h1>

    <p>Bienvenido {{auth()->user()->name}}
    

@endsectionuse