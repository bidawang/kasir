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
        // dd($credentials);
        // Tambahkan syarat status aktif
        $credentials['status'] = 'aktif';

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            $user = Auth::user();

            // Redirect sesuai role
            if ($user->role === 'kasir') {
                // dd('berhasil login sebagai kasir');
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
        // Validasi input
        $request->validate([
            'nama' => 'required|string|max:255',
            'email' => 'required|email|unique:user,email',
            'password' => 'required|string|confirmed',
        ], [
            'nama.required' => 'Nama wajib diisi.',
            'nama.max' => 'Nama maksimal 255 karakter.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email sudah digunakan. Gunakan email lain.',
            'password.required' => 'Password wajib diisi.',
            'password.min' => 'Password minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
        ]);

        // Simpan ke database
        User::create([
            'nama' => $request->nama,
            'email' => $request->email,
            'role' => 'kasir', // default
            'password' => Hash::make($request->password),
        ]);

        // Redirect
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
