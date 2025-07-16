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
            $result = $this->updateUserData($user, $request);

            if ($result !== true) {
                return back()->withErrors(['foto' => $result])->withInput();
            }

            $userId = Auth::guard('pengelola')->check() ? $user->id_pengelola : $user->id_pengguna;
            Log::info('Profil berhasil diperbarui untuk pengguna ID: ' . $userId);

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
            return back()->withErrors(['error' => 'Gagal memperbarui profil. Silakan coba lagi.'])->withInput();
        }
    }

    /**
     * Memperbarui data pengguna berdasarkan request
     * Mengembalikan true jika sukses, atau string pesan error jika gagal validasi foto
     */
    private function updateUserData($user, $request)
    {
        $user->nama = $request->nama;
        $user->email = $request->email;

        if ($request->hasFile('foto')) {
            $imagePath = $request->file('foto')->getRealPath();
            [$width, $height] = getimagesize($imagePath);

            if ($width != 472 || $height != 472) {
                return 'Foto profil harus berukuran tepat 472 x 472 pixel.';
            }

            $oldFoto = $user->foto;
            if ($oldFoto && !str_contains($oldFoto, 'default.jpg')) {
                $parsedUrl = parse_url($oldFoto);
                $path = $parsedUrl['path'] ?? '';
                $filename = pathinfo($path, PATHINFO_FILENAME);
                $publicId = 'images/profil/' . $filename;

                Cloudinary::destroy($publicId);
                Log::info("Foto lama user dihapus dari Cloudinary: {$publicId}");
            }

            $uploaded = Cloudinary::upload($imagePath, [
                'folder' => 'images/profil',
                'resource_type' => 'image'
            ]);

            $user->foto = $uploaded->getSecurePath();
            Log::info("Foto baru user disimpan dengan URL: {$user->foto}");
        }

        if ($request->filled('password')) {
            $user->password = $request->password; // menggunakan mutator hash otomatis
        }

        if (!$user->save()) {
            throw new \Exception('Gagal memperbarui profil.');
        }

        return true;
    }
}
