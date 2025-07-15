<?php

namespace App\Http\Controllers;

use App\Models\AktivitasPenggunaParkir;
use App\Models\SessionPenggunaParkir; // ✅ Tambah
use App\Models\PenggunaParkir;
use App\Models\PengelolaParkir;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str; // ✅ Tambah
use Carbon\Carbon;

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
        $credentials = $request->validate([
            'id' => ['required'],
            'password' => ['required'],
        ]);

        Log::info('Percobaan login', ['id' => $credentials['id']]);

        // Cek Pengelola
        $user = PengelolaParkir::where('id_pengelola', $credentials['id'])->first();
        $guard = 'pengelola';

        // Jika bukan pengelola, cek pengguna
        if (!$user) {
            $user = PenggunaParkir::where('id_pengguna', $credentials['id'])->first();
            $guard = 'pengguna';
        }

        if ($user) {
            Log::info('Akun ditemukan', ['id' => $credentials['id'], 'guard' => $guard]);

            // Cek status untuk pengguna
            if ($guard === 'pengguna' && $user->status !== 'aktif') {
                Log::warning('Akun belum aktif', ['id_pengguna' => $user->id_pengguna]);
                return back()
                    ->with('status', 'error')
                    ->with('message', 'Maaf, akun anda belum aktif!')
                    ->onlyInput('id');
            }

            // Cek password
            if (Hash::check($credentials['password'], $user->password)) {
                Auth::guard($guard)->login($user);
                $request->session()->regenerate();

                if ($guard === 'pengguna') {
                    Log::info('Pengguna berhasil login', ['id_pengguna' => $user->id_pengguna]);

                    // ✅ Catat aktivitas login
                    AktivitasPenggunaParkir::create([
                        'id_pengguna' => $user->id_pengguna,
                        'aktivitas' => 'login',
                        'keterangan' => 'User login ke aplikasi',
                        'waktu_aktivitas' => Carbon::now(),
                    ]);

                    // ✅ Catat session start
                    $sessionId = Str::uuid();
                    SessionPenggunaParkir::create([
                        'id_pengguna' => $user->id_pengguna,
                        'session_id' => $sessionId,
                        'session_start' => now(),
                    ]);
                    session(['session_id_penggunaparkir' => $sessionId]);

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

            // ✅ Update session end & duration
            if (session()->has('session_id_penggunaparkir')) {
                $sessionId = session('session_id_penggunaparkir');
                $sessionRecord = SessionPenggunaParkir::where('session_id', $sessionId)->first();

                if ($sessionRecord) {
                    $sessionRecord->session_end = now();
                    $sessionRecord->duration_seconds = now()->diffInSeconds($sessionRecord->session_start);
                    $sessionRecord->save();
                }

                session()->forget('session_id_penggunaparkir');
            }

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
