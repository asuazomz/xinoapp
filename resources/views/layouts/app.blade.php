<!DOCTYPE html>

<html lang="es">
    <head>
        <meta charset="UTF-8">
        <title>@yield('title','Mi app')</title>
            <style>
                body{
                   font-family: Arial, Helvetica, sans-serif;
                   background: #f5f5f5;
                   text-align: center;
                   padding-top: 40px;
                }
                nav{
                    margin: 20px;
                }
                a,button{
                    padding: 8px 16px;
                    margin: 5px;
                    background: black;
                    color: white;
                    text-decoration: none;
                    border: none;
                    cursor: pointer;
                }
                .error{color: red}
                .success{color: green}
            </style>
    </head>

    <body>

        <nav>
            @auth
               <span>{{auth()->user()->name}}</span>              
                    @if(auth()->user()->role==='admin')
                            <a href="/admin/dashboard">Admin</a>
                    @endif
               <a href="/dashboard">Dashboard</a>
                    <form method="POST" action="/logout" style="display:inline;">
                            @csrf
                            <button type="submit">Salir</button>
                    </form>
            @endauth
            @guest
                <a href="/login">Login</a>
               <a href="/register">Registro</a>
            @endguest
        </nav>

            @if(session('success'))
            <p class="success">{{session('success')}}</p>
            @endif

            @if($errors->any())
                <div class="error">
                    @foreach($errors->all() as $error)
                    <p>{{$error}}</p>
                    @endforeach
                </div>
            @endif
        
        @yield('content')

    </body>
</html>