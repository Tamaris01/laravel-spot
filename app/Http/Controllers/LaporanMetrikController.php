<?php

namespace App\Http\Controllers;

use App\Models\AktivitasPenggunaParkir;
use App\Models\PenggunaParkir;
use Illuminate\Http\Request;

class LaporanMetrikController extends Controller
{
    /**
     * Tampilkan laporan Key Usage Metrics SPOT/ALPU.
     */
    public function index()
    {
        // Total Users
        $totalUsers = PenggunaParkir::count();

        // Active Users
        $activeUsers = PenggunaParkir::where('status', 'aktif')->count();

        // DAU
        $dau = AktivitasPenggunaParkir::today()
            ->distinct('id_pengguna')
            ->count('id_pengguna');

        // WAU
        $wau = AktivitasPenggunaParkir::last7Days()
            ->distinct('id_pengguna')
            ->count('id_pengguna');

        // MAU
        $mau = AktivitasPenggunaParkir::last30Days()
            ->distinct('id_pengguna')
            ->count('id_pengguna');

        // Retention Rate 7 hari
        $firstUsers = AktivitasPenggunaParkir::select('id_pengguna')
            ->selectRaw('MIN(waktu_aktivitas) as first_time')
            ->groupBy('id_pengguna')
            ->get();

        $returnedUsers = 0;
        foreach ($firstUsers as $user) {
            $hasReturned = AktivitasPenggunaParkir::where('id_pengguna', $user->id_pengguna)
                ->where('waktu_aktivitas', '>', $user->first_time)
                ->where('waktu_aktivitas', '<=', \Carbon\Carbon::parse($user->first_time)->addDays(7))
                ->exists();

            if ($hasReturned) {
                $returnedUsers++;
            }
        }
        $totalFirstUsers = $firstUsers->count();
        $retentionRate = $totalFirstUsers > 0 ? round(($returnedUsers / $totalFirstUsers) * 100, 2) : 0;

        // Feature Usage
        $topFeatures = AktivitasPenggunaParkir::select('aktivitas')
            ->selectRaw('COUNT(*) as jumlah')
            ->groupBy('aktivitas')
            ->orderByDesc('jumlah')
            ->limit(5)
            ->get();

        return response()->json([
            'total_users' => $totalUsers,
            'active_users' => $activeUsers,
            'DAU' => $dau,
            'WAU' => $wau,
            'MAU' => $mau,
            'retention_rate_7_days' => $retentionRate . '%',
            'top_features' => $topFeatures,
        ]);
    }
}
