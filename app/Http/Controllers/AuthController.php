<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Kasir;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $credentials['status'] = 'aktif';

        if (!Auth::attempt($credentials)) {
            throw ValidationException::withMessages([
                'email' => 'Email atau password salah atau akun tidak aktif.',
            ]);
        }

        $request->session()->regenerate();

        $user = Auth::user();

        // Kasir: cari cabang yang terhubung dengan akun
        if ($user->role === 'kasir') {

            $warung = Kasir::where('id_user', $user->id)
                ->first();

            if (!$warung) {
                Auth::logout();

                throw ValidationException::withMessages([
                    'email' => 'Akun kasir belum memiliki cabang.',
                ]);
            }

            session([
                'id_warung' => $warung->id,
            ]);

            return redirect()->route('dashboard');
        }

        // Admin / Owner: pilih cabang terlebih dahulu
        if ($user->role === 'admin') {

            session()->forget('id_warung');

            return redirect()->route('cabang.index');
        }

        return redirect()->intended('/');
    }

    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
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

        User::create([
            'nama' => $request->nama,
            'email' => $request->email,
            'role' => 'kasir',
            'password' => Hash::make($request->password),
        ]);

        return redirect()
            ->route('login')
            ->with('success', 'Pendaftaran berhasil! Silakan masuk.');
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}

