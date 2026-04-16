@extends('layouts.app')

@section('title', 'Admin Dashboard')

@section('content')

<h1>Panel de Admin</h1>
<p>Bienvenido {{auth()->user()->name}}</p>
<p>Rol {{auth()->user()->role}}</p>

@endsection