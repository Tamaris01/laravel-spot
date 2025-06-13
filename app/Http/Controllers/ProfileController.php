<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\ProfileRequest;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash; // Untuk enkripsi password
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;

class ProfileController extends Controller
{
    // Menampilkan data profil pengguna yang sedang login
    public function showProfile()
    {
        $user = Auth::user();

        $view = Auth::guard('pengelola')->check() ? 'pengelola.profile' : 'pengguna.profile';
        return view($view, compact('user'));
    }

    // Memperbarui data profil pengguna yang sedang login
    public function update(ProfileRequest $request)
    {
        $user = Auth::user();

        try {
            $this->updateUserData($user, $request);

            $userId = Auth::guard('pengelola')->check() ? $user->id_pengelola : $user->id_pengguna;
            Log::info('Profil berhasil diperbarui untuk pengguna ID: ' . $userId);

            return back()->with('success', 'Profil berhasil diperbarui.');
        } catch (\Exception $e) {
            Log::error('Gagal memperbarui profil untuk pengguna ID: ' . ($user->id_pengguna ?? $user->id_pengelola) . ' - Error: ' . $e->getMessage());
            return back()->withErrors('Gagal memperbarui profil. Silakan coba lagi.');
        }
    }

    // Memperbarui data pengguna berdasarkan request
    private function updateUserData($user, $request)
    {
        $user->nama = $request->nama;
        $user->email = $request->email;

        if ($request->hasFile('foto')) {
            // Hapus foto lama jika bukan default
            if ($user->foto && !str_contains($user->foto, 'default.jpg')) {
                if (str_starts_with($user->foto, 'http')) {
                    // Ekstrak public_id dari URL
                    $parsedUrl = parse_url($user->foto);
                    $path = $parsedUrl['path'] ?? '';
                    $publicIdWithExt = ltrim($path, '/'); // contoh: images/profil/abc123.jpg
                    $publicId = preg_replace('/\.(jpg|jpeg|png)$/', '', $publicIdWithExt);
                } else {
                    $publicId = $user->foto;
                }

                Cloudinary::destroy($publicId);
                Log::info("Foto lama dihapus: {$publicId}");
            }

            // Upload foto baru
            $uploaded = Cloudinary::upload($request->file('foto')->getRealPath(), [
                'folder' => 'images/profil',
                'resource_type' => 'image'
            ]);

            // Simpan URL lengkap ke database (agar bisa langsung dipakai di <img src="{{ $user->foto }}">)
            $user->foto = $uploaded->getSecurePath();
            Log::info("Foto baru disimpan dengan URL: {$user->foto}");
        }

        if ($request->filled('password')) {
            $user->password = $request->password; // Jangan lupa enkripsi
        }

        if (!$user->save()) {
            throw new \Exception('Gagal memperbarui profil.');
        }
    }
}
