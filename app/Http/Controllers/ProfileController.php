<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\ProfileRequest; // Import the profile request
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary; // Pastikan Cloudinary sudah di-import

class ProfileController extends Controller
{
    // Menampilkan data profil pengguna yang sedang login
    public function showProfile()
    {
        $user = Auth::user(); // Ambil pengguna yang sedang login

        // Tentukan view berdasarkan jenis pengguna
        $view = Auth::guard('pengelola')->check() ? 'pengelola.profile' : 'pengguna.profile';
        return view($view, compact('user'));
    }

    // Memperbarui data profil pengguna yang sedang login
    public function update(ProfileRequest $request) // Menggunakan ProfileRequest di sini
    {
        $user = Auth::user(); // Ambil pengguna yang sedang login

        try {
            $this->updateUserData($user, $request);
            // Gunakan ID yang sesuai berdasarkan tipe pengguna
            $userId = Auth::guard('pengelola')->check() ? $user->id_pengelola : $user->id_pengguna;
            Log::info('Profil berhasil diperbarui untuk pengguna ID: ' . $userId);
            return back()->with('success', 'Profile berhasil diperbarui.');
        } catch (\Exception $e) {
            Log::error('Gagal memperbarui profil untuk pengguna ID: ' . ($user->id_pengguna ?? $user->id_pengelola) . ' - Error: ' . $e->getMessage());
            return back()->withErrors('Gagal memperbarui profil. Silakan coba lagi.'); // Menampilkan pesan error
        }
    }

    // Memperbarui data pengguna berdasarkan request
    private function updateUserData($user, $request)
    {
        // Mengupdate nama dan email
        $user->nama = $request->nama;
        $user->email = $request->email;

        // Menangani upload foto profil
        if ($request->hasFile('foto')) {
            // Jika ada foto lama, hapus foto lama dari Cloudinary
            if ($user->foto) {
                Cloudinary::destroy($user->foto); // Hapus foto lama menggunakan public_id
                Log::info("Foto lama berhasil dihapus: {$user->foto}");
            }

            // Upload foto baru ke Cloudinary
            $fotoBaru = $request->file('foto');
            $uploaded = Cloudinary::upload($fotoBaru->getRealPath(), [
                'folder' => 'profil'
            ]);

            // Ambil public_id dari Cloudinary dan simpan di database
            $user->foto = $uploaded->getPublicId(); // Simpan public_id ke database
            Log::info("Foto baru disimpan dengan public_id: {$user->foto}");
        }

        // Memperbarui password jika diberikan
        if ($request->filled('password')) {
            $user->password = $request->password; // Encrypt password before saving
        }

        // Menyimpan data pengguna
        if (!$user->save()) {
            throw new \Exception('Gagal memperbarui profil.'); // Melemparkan exception jika gagal
        }
    }
}
