<aside class="main-sidebar sidebar-yellow sidebar-white elevation-4">
    <a href="#" class="brand-link" style="display: flex; justify-content: center; align-items: center; padding: 12px; background-color: white;">
        <img src="{{ asset('images/spot-logo.png') }}" alt="SPOT Logo" class="brand-image" style="width: 100%; max-width: 150px; height: auto; object-fit: contain" />
    </a>
    <div class="sidebar">
        <!-- Profil Pengguna -->
        <div class="user-panel mt-3 pb-2 mb-2 d-flex flex-column align-items-center text-center">
            <div class="image" style="margin-bottom: 5px;">
                <img src="Auth::user()->foto }}"
                    class="img-circle elevation-2"
                    style="width: 80px; height: 80px; border-radius: 50%; object-fit: cover; border: 3px solid white;"
                    alt="User Image">
            </div>
            <div class="info">
                <strong>{{ Auth::user()->nama }}</strong>
            </div>
        </div>


        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                @auth('pengelola')
                <li class="nav-item">
                    <a href="{{ route('pengelola.dashboard') }}" class="nav-link @if (Route::currentRouteName() == 'pengelola.dashboard') active @endif">
                        <i class="nav-icon fas fa-tachometer-alt"></i>
                        <p>Dashboard</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('pengelola.konfirmasi_pendaftaran') }}" class="nav-link @if (Route::currentRouteName() == 'pengelola.konfirmasi_pendaftaran') active @endif">
                        <i class="nav-icon fas fa-check-circle"></i>
                        <p>Konfirmasi Pendaftaran</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('pengelola.kelola_pengguna.index') }}" class="nav-link @if (Route::currentRouteName() == 'pengelola.kelola_pengguna.index') active @endif">
                        <i class="nav-icon fas fa-users"></i>
                        <p>Kelola Pengguna</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('pengelola.kelola_kendaraan.index') }}" class="nav-link @if (Route::currentRouteName() == 'pengelola.kelola_kendaraan.index') active @endif">
                        <i class="nav-icon fas fa-car"></i>
                        <p>Kelola Kendaraan</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('pengelola.monitoring.index') }}" class="nav-link @if (Route::currentRouteName() == 'pengelola.monitoring.index') active @endif">
                        <i class="nav-icon fas fa-chart-line"></i>
                        <p>Monitoring</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('pengelola.laporan_parkir.index') }}" class="nav-link @if (Route::currentRouteName() == 'pengelola.laporan_parkir.index') active @endif">
                        <i class="nav-icon fas fa-file-alt"></i>
                        <p>Laporan Parkir</p>
                    </a>
                </li>
                <!-- Sidebar Logout Button -->
                <li class="nav-item mt-3">
                    <a href="#" class="nav-link logout-btn" data-toggle="modal" data-target="#logoutModal">
                        <i class="nav-icon fas fa-sign-out-alt"></i>
                        <p>Logout</p>
                    </a>
                </li>

                <!-- Logout Form -->
                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                    @csrf
                </form>

                <!-- Logout Confirmation Modal -->
                <div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="logoutModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered" role="document">
                        <div class="modal-content">
                            <div class="modal-header bg-warning">
                                <h5 class="modal-title text-dark" id="logoutModalLabel">Konfirmasi Keluar</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="color: black; font-size: 1.75em; font-weight: bold;">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body text-center">
                                <div>
                                    <i class="fas fa-exclamation-circle text-danger" style="font-size: 3.5em; animation: shake 0.8s infinite;"></i>
                                </div>
                                <p class="mt-3">Anda yakin ingin keluar dari sistem ini?<br><strong>"{{ Auth::user()->nama }}"</strong></p>
                            </div>
                            <div class="modal-footer d-flex justify-content-center">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal" style="width: 100px;">Batal</button>
                                <button type="button" class="btn btn-warning text-dark font-weight-bold" style="width: 100px;" onclick="document.getElementById('logout-form').submit();">Keluar</button>
                            </div>
                        </div>
                    </div>
                </div>


                @endauth
            </ul>
        </nav>
    </div>
</aside>

<style>
    /* Saat sidebar dalam mode collapsed */
    .sidebar-mini.sidebar-collapse .user-panel .image img {
        width: 40px !important;
        /* Sesuaikan ukuran dengan icon */
        height: 40px !important;
        transition: all 0.3s ease;
    }

    /* Mengatur agar text info user hilang saat sidebar collapsed */
    .sidebar-mini.sidebar-collapse .user-panel .info {
        display: none;
    }

    .nav-link.active {
        background-color: white !important;
        color: black !important;
    }

    .nav-link {

        color: black !important;
    }

    .nav-link:hover {
        background-color: rgba(255, 255, 255, 0.2) !important;
        color: white !important;
        transform: scale(1.05);
    }

    .nav-icon {
        color: black;
    }

    .nav-link.active .nav-icon {
        color: black;
    }

    .nav-link:hover .nav-icon {
        color: white;
    }

    /* Tombol Logout */
    /* .logout-btn {
        background: linear-gradient(to right, rgb(69, 62, 61), rgb(85, 80, 78));
        color: white !important;
        padding: 10px 15px;
        border-radius: 8px;
        transition: all 0.3s ease-in-out;
        display: flex;
        align-items: center;
        gap: 10px;
    } */

    /* Pastikan ikon dan teks tetap putih */
    .logout-btn i,
    .logout-btn p {
        color: black !important;
        margin: 0;
    }

    /* Hover Effect */
    .logout-btn:hover {
        background: rgba(255, 255, 255, 0.2) !important;
        /* Transparan putih */
        color: white !important;
        transform: scale(1.05);
    }

    /* Pastikan ikon dan teks tetap putih saat hover */
    .logout-btn:hover i,
    .logout-btn:hover p {
        color: white !important;
    }
</style>