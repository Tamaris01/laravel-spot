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
            // Hapus foto lama di Cloudinary jika ada
            if ($user->foto) {
                Cloudinary::destroy($user->foto);
                Log::info("Foto lama berhasil dihapus: {$user->foto}");
            }

            // Upload foto baru ke Cloudinary, simpan di folder 'images/profil' supaya konsisten dengan register
            $fotoBaru = $request->file('foto');
            $uploaded = Cloudinary::upload($fotoBaru->getRealPath(), [
                'folder' => 'images/profil',
                'resource_type' => 'image'
            ]);

            $user->foto = $uploaded->getPublicId(); // Simpan public_id di database
            Log::info("Foto baru disimpan dengan public_id: {$user->foto}");
        }

        // Update password hanya jika diisi, dan lakukan hashing
        if ($request->filled('password')) {
            $user->password = $request->password;
        }

        if (!$user->save()) {
            throw new \Exception('Gagal memperbarui profil.');
        }
    }
}
