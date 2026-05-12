<?php
namespace App\Http\Controllers\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function showLoginForm(){ return view('auth.login'); }
    public function login(Request $request)
    {
        $cred = $request->validate(['email'=>'required|email','password'=>'required']);
        if (Auth::attempt([...$cred, 'is_active'=>true], $request->boolean('remember'))) {
            $request->session()->regenerate();
            return auth()->user()->role === 'owner'
                ? redirect()->route('owner.dashboard')
                : redirect()->route('kasir.pos');
        }
        return back()->withErrors(['email'=>'Email atau password salah.'])->onlyInput('email');
    }
    public function logout(Request $request)
    {
        Auth::logout(); $request->session()->invalidate(); $request->session()->regenerateToken();
        return redirect()->route('login');
    }
}
