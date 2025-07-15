<?php

namespace App\Http\Controllers;

use App\Http\Requests\KendaraanRequest;
use Illuminate\Http\Request;
use App\Models\PenggunaParkir;
use App\Models\Kendaraan;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;


class KendaraanController extends Controller
{
    protected $jenisKendaraanArray = [];
    protected $warnaKendaraanArray = [];

    public function __construct()
    {
        // Initialize enum values for vehicle type and color
        $this->jenisKendaraanArray = $this->getEnumValues('kendaraan', 'jenis');
        $this->warnaKendaraanArray = $this->getEnumValues('kendaraan', 'warna');
    }

    /**
     * Display vehicle data for the logged-in user.
     */
    public function showKendaraanUser()
    {
        $user = Auth::guard('pengguna')->user(); // Use the 'pengguna' guard for authentication
        $kendaraan = $user->kendaraan; // Assuming a hasOne relationship in PenggunaParkir

        return view('pengguna.kendaraan', [
            'kendaraan' => $kendaraan,
            'jenisKendaraanArray' => $this->jenisKendaraanArray,
            'warnaKendaraanArray' => $this->warnaKendaraanArray,
        ]);
    }

    /**
     * Update the logged-in user's vehicle data.
     */
    public function update(KendaraanRequest $request)
    {
        $user = Auth::guard('pengguna')->user();
        $kendaraan = $user->kendaraan;

        Log::info('User attempting to update vehicle:', ['user_id' => $user->id, 'vehicle' => $kendaraan]);

        if (!$kendaraan) {
            Log::warning('Vehicle not found for user:', ['user_id' => $user->id]);
            return redirect()->back()->with('error', 'Kendaraan tidak ditemukan.');
        }

        Log::info('Received data for update:', $request->all());

        try {
            Log::info('Vehicle data before update:', $kendaraan->toArray());

            // Update jenis dan warna kendaraan
            $kendaraan->jenis = $request->jenis;
            $kendaraan->warna = ucwords(strtolower($request->warna));

            // Jika ada foto baru
            if ($request->hasFile('foto_kendaraan')) {

                // Hapus foto lama dari Cloudinary jika ada
                if ($kendaraan->foto) {
                    $parsedUrl = parse_url($kendaraan->foto);
                    $path = $parsedUrl['path'] ?? '';
                    $publicId = pathinfo($path, PATHINFO_FILENAME);

                    if ($publicId) {
                        Cloudinary::destroy("images/kendaraan/{$publicId}");
                        Log::info("Old vehicle photo deleted from Cloudinary: {$publicId}");
                    }
                }

                // Upload foto baru ke Cloudinary
                $uploadedFileUrl = Cloudinary::upload(
                    $request->file('foto_kendaraan')->getRealPath(),
                    ['folder' => 'images/kendaraan', 'resource_type' => 'image']
                )->getSecurePath();

                // Simpan URL foto ke database
                $kendaraan->foto = $uploadedFileUrl;

                Log::info('New vehicle photo uploaded to Cloudinary:', ['url' => $uploadedFileUrl]);
            }

            $kendaraan->save();

            Log::info('Vehicle data after update:', $kendaraan->toArray());

            return redirect()->back()->with('success', 'Data kendaraan berhasil diperbarui.');
        } catch (\Exception $e) {
            Log::error('Failed to update vehicle data: ' . $e->getMessage(), ['user_id' => $user->id]);
            return redirect()->back()->with('error', 'Gagal memperbarui data kendaraan.');
        }
    }

    /**
     * Get enum values from a specific table column.
     *
     * @param string $table
     * @param string $column
     * @return array
     */
    protected function getEnumValues($table, $column)
    {
        $result = DB::select("SHOW COLUMNS FROM `$table` WHERE Field = ?", [$column]);
        if (count($result) > 0) {
            $type = $result[0]->Type;
            if (preg_match('/^enum\((.*)\)$/', $type, $matches)) {
                $enum = [];

                foreach (explode(',', $matches[1]) as $value) {
                    $enum[] = trim($value, "'");
                }

                return $enum;
            }
        }

        return []; // Return an empty array if no results or if no match is found
    }
}
