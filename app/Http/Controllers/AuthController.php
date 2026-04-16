<?php
namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('login_1');
    }

    public function showRegister()
    {
        return view('register_1');
    }

    public function register(Request $request)
    {
        $request->validate([
            'name'=>'required|string|max:255',
            'email'=>'required|email|unique:users,email',
            'password'=>'required|min:4',
            'role'=>'required|in:usuario,admin',
        ]);

        User::create([
            'name'=>$request->name,
            'email'=>$request->email,
            'password'=>Hash::make($request->password),
            'role'=>$request->role,
        ]);

        return redirect('/login_1')->with('success','Usuario Creado Correctamente');
    }

    public function login(Request $request)
    {
        $credenciales = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);
    
        if (Auth::attempt($credenciales)) {
            $request->session()->regenerate();
    
            if (Auth::user()->role === 'admin') {
                return redirect('/admin/dashboard');
            }
    
            return redirect('/user/dashboard');
        }
    
        return back()->withErrors([
            'email' => 'Credenciales Incorrectas',
        ])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login_1');
    }
}
