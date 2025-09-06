<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Menampilkan halaman formulir login.
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * Memproses permintaan login.
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // Tambahkan syarat status aktif
        $credentials['status'] = 'aktif';

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            $user = Auth::user();

            // Redirect sesuai role
            if ($user->role === 'kasir') {
                return redirect()->route('dashboard');
            } elseif ($user->role === 'admin') {
                return redirect()->route('kas.index'); // misal ke kas
            }

            // Default fallback (kalau role tidak dikenali)
            return redirect()->intended('/');
        }

        throw ValidationException::withMessages([
            'email' => 'Email atau password salah atau akun tidak aktif.',
        ]);
    }


    /**
     * Menampilkan formulir pendaftaran.
     */
    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    /**
     * Memproses permintaan pendaftaran.
     */
    public function register(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'email' => 'required|email|unique:user,email',
            'password' => 'required|string|min:8|confirmed',
        ]);

        User::create([
            'nama' => $request->nama,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        return redirect()->route('login')->with('success', 'Pendaftaran berhasil! Silakan masuk.');
    }

    /**
     * Melakukan logout pengguna.
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }
}
