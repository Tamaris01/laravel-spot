<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\ProfileRequest;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;
use App\Models\AktivitasPenggunaParkir;
use Carbon\Carbon;

class ProfileController extends Controller
{
    /**
     * Menampilkan data profil pengguna yang sedang login
     */
    public function showProfile()
    {
        $user = Auth::user();
        $view = Auth::guard('pengelola')->check() ? 'pengelola.profile' : 'pengguna.profile';

        return view($view, compact('user'));
    }

    /**
     * Memperbarui data profil pengguna yang sedang login
     */
    public function update(ProfileRequest $request)
    {
        $user = Auth::user();

        try {
            $this->updateUserData($user, $request);

            $userId = Auth::guard('pengelola')->check() ? $user->id_pengelola : $user->id_pengguna;
            Log::info('Profil berhasil diperbarui untuk pengguna ID: ' . $userId);

            // Catat aktivitas hanya untuk pengguna parkir
            if (Auth::guard('pengguna')->check()) {
                AktivitasPenggunaParkir::create([
                    'id_pengguna' => $user->id_pengguna,
                    'aktivitas' => 'update_profile',
                    'keterangan' => 'User memperbarui profil',
                    'waktu_aktivitas' => Carbon::now(),
                ]);
            }

            return back()->with('success', 'Profil berhasil diperbarui.');
        } catch (\Exception $e) {
            Log::error('Gagal memperbarui profil untuk pengguna ID: ' . ($user->id_pengguna ?? $user->id_pengelola) . ' - Error: ' . $e->getMessage());
            return back()->withErrors('Gagal memperbarui profil. Silakan coba lagi.');
        }
    }

    /**
     * Memperbarui data pengguna berdasarkan request
     */
    private function updateUserData($user, $request)
    {
        $user->nama = $request->nama;
        $user->email = $request->email;

        if ($request->hasFile('foto')) {
            // Validasi dimensi 472 x 472 secara manual jika ingin validasi di controller
            [$width, $height] = getimagesize($request->file('foto'));
            if ($width != 472 || $height != 472) {
                throw new \Exception('Foto harus berukuran tepat 472 x 472 pixel.');
            }

            // Hapus foto lama dari Cloudinary jika bukan default
            $oldFoto = $user->foto;
            if ($oldFoto && !str_contains($oldFoto, 'default.jpg')) {
                $parsedUrl = parse_url($oldFoto);
                $path = $parsedUrl['path'] ?? '';
                $filename = pathinfo($path, PATHINFO_FILENAME);
                $publicId = 'images/profil/' . $filename;

                Cloudinary::destroy($publicId);
                Log::info("Foto lama user dihapus dari Cloudinary: {$publicId}");
            }

            // Upload foto baru ke Cloudinary
            $uploaded = Cloudinary::upload($request->file('foto')->getRealPath(), [
                'folder' => 'images/profil',
                'resource_type' => 'image'
            ]);

            $user->foto = $uploaded->getSecurePath();
            Log::info("Foto baru user disimpan dengan URL: {$user->foto}");
        }

        if ($request->filled('password')) {
            $user->password = $request->password; // Terenkripsi otomatis via mutator
        }

        if (!$user->save()) {
            throw new \Exception('Gagal memperbarui profil.');
        }
    }
}
