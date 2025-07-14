<?php

namespace App\Http\Controllers;

use App\Models\AktivitasPenggunaParkir;
use Carbon\Carbon;
use App\Models\PenggunaParkir;
use App\Models\PengelolaParkir;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    /**
     * Menampilkan halaman form login.
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * Proses login untuk Pengelola dan Pengguna Parkir.
     */
    public function login(Request $request)
    {
        // Validasi input
        $credentials = $request->validate([
            'id' => ['required'],
            'password' => ['required'],
        ]);

        Log::info('Percobaan login', ['id' => $credentials['id']]);

        // Cek ke Pengelola lebih dulu
        $user = PengelolaParkir::where('id_pengelola', $credentials['id'])->first();
        $guard = 'pengelola';

        // Jika tidak ditemukan di Pengelola, cek Pengguna
        if (!$user) {
            $user = PenggunaParkir::where('id_pengguna', $credentials['id'])->first();
            $guard = 'pengguna';
        }

        if ($user) {
            Log::info('Akun ditemukan', ['id' => $credentials['id'], 'guard' => $guard]);

            // Cek status hanya untuk pengguna
            if ($guard === 'pengguna' && $user->status !== 'aktif') {
                Log::warning('Akun pengguna belum aktif', ['id_pengguna' => $user->id_pengguna]);
                return back()
                    ->with('status', 'error')
                    ->with('message', 'Maaf, akun anda belum aktif!')
                    ->onlyInput('id');
            }

            // Cek password
            if (Hash::check($credentials['password'], $user->password)) {
                Auth::guard($guard)->login($user);
                $request->session()->regenerate();

                // ✅ Catat aktivitas login hanya untuk pengguna parkir
                if ($guard === 'pengguna') {
                    Log::info('Pengguna berhasil login', ['id_pengguna' => $user->id_pengguna]);

                    AktivitasPenggunaParkir::create([
                        'id_pengguna' => $user->id_pengguna,
                        'aktivitas' => 'login',
                        'keterangan' => 'User login ke aplikasi',
                        'waktu_aktivitas' => Carbon::now(),
                    ]);

                    return redirect()->route('pengguna.dashboard')
                        ->with('status', 'success')
                        ->with('message', 'Selamat datang, Anda berhasil masuk!');
                } else {
                    Log::info('Pengelola berhasil login', ['id_pengelola' => $user->id_pengelola]);
                    return redirect()->route('pengelola.dashboard')
                        ->with('status', 'success')
                        ->with('message', 'Selamat datang, Anda berhasil masuk!');
                }
            } else {
                Log::warning('Password salah', ['id' => $credentials['id']]);
                return back()
                    ->with('status', 'error')
                    ->with('message', 'Password anda salah.')
                    ->onlyInput('id');
            }
        } else {
            Log::warning('ID tidak ditemukan', ['id' => $credentials['id']]);
            return back()
                ->with('status', 'error')
                ->with('message', 'ID tidak ditemukan.')
                ->onlyInput('id');
        }
    }

    /**
     * Logout untuk pengguna dan pengelola.
     */
    public function logout(Request $request)
    {
        if (Auth::guard('pengguna')->check()) {
            $user = Auth::guard('pengguna')->user();
            Log::info('Pengguna logout', ['id_pengguna' => $user->id_pengguna]);

            // ✅ Catat aktivitas logout
            AktivitasPenggunaParkir::create([
                'id_pengguna' => $user->id_pengguna,
                'aktivitas' => 'logout',
                'keterangan' => 'User logout dari aplikasi',
                'waktu_aktivitas' => Carbon::now(),
            ]);

            Auth::guard('pengguna')->logout();
        } elseif (Auth::guard('pengelola')->check()) {
            $user = Auth::guard('pengelola')->user();
            Log::info('Pengelola logout', ['id_pengelola' => $user->id_pengelola]);
            Auth::guard('pengelola')->logout();
        }

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login')
            ->with('status', 'success')
            ->with('message', 'Anda berhasil logout.');
    }
}
