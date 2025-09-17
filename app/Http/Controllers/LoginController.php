<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function index(){
        return view('login.login');
    }

    public function login(Request $request){

        $credentials = [
            "email" => $request->user,
            "password" => $request->password
        ];

        $remember = ($request->has('remember') ? true : false);

        if (Auth::attempt($credentials,$remember)) {

            $request->session()->regenerate();

            return redirect()->intended(route('inventory'));

        }else{
            return redirect('login')->withErrors(['email' => 'Las credenciales no coinciden.',])->onlyInput('email');
        }
    }

    public function logOut(Request $request){
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();  

        return redirect(route('login'));
    }
}
