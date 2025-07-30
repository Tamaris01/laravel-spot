<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Google tag (gtag.js) -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-3XHY9WDYCP"></script>
    <script>
        window.dataLayer = window.dataLayer || [];

        function gtag() {
            dataLayer.push(arguments);
        }
        gtag('js', new Date());

        gtag('config', 'G-3XHY9WDYCP');
    </script>
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('images/spot-logo.png') }}">

    <title>SPOT - Sistem Parkir Otomatis Terjamin</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="{{ asset('css/landingpage.css') }}">

    <!-- FontAwesome Icons -->
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
    <!-- FontAwesome & Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
    <!--  AOS CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.css">
    <!--  AOS JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.js"></script>
    <!-- CSS Icon-->
    <link href="https://cdn.jsdelivr.net/npm/remixicon/fonts/remixicon.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">


</head>

<body>
    <!-- Overlay Loading -->
    <div id="loading-overlay">
        <img id="loading-logo" src="{{ asset('images/spot-logo.png') }}" alt="SPOT Logo">
    </div>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light">
        <div class="container-fluid">
            <a class="navbar-brand fw-bold" href="#">
                <img src="{{ asset('images/spot-logo.png') }}" alt="SPOT Logo" />
            </a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item"><a class="nav-link" href="#home">Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="#about">Tentang</a></li>
                    <li class="nav-item"><a class="nav-link" href="#features">Fitur</a></li>
                    <li class="nav-item"><a class="nav-link" href="#pengguna">Pengguna</a></li>
                    <li class="nav-item"><a class="nav-link" href="#penawaran">Penawaran</a></li>
                    <li class="nav-item"><a class="nav-link" href="#team">Tim</a></li>
                    <li class="nav-item"><a class="nav-link" href="#contact">Hubungi Kami</a></li>
                </ul>
                <div class="d-flex gap-2">
                    <a href="{{ route('register') }}" class="btn btn-outline-black">Daftar</a>
                    <a href="{{ route('login') }}" class="btn btn-black">Masuk</a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section id="home" class="hero">
        <h1 data-aos="fade-up" data-aos-delay="300">
            <span class="spot-highlight">
                <span class="black">Selamat Datang di <span class="yellow">SPOT</span></span>
            </span>
        </h1>
        <h2 data-aos="fade-up" data-aos-delay="500">
            <span class="spot-highlight">
                <span class="yellow">S</span><span class="black">istem </span>
                <span class="yellow">P</span><span class="black">arkir </span>
                <span class="yellow">O</span><span class="black">tomatis </span>
                <span class="yellow">T</span><span class="black">erjamin</span>
            </span>
        </h2>
        <p data-aos="fade-up" data-aos-delay="700">
            Nikmati kemudahan parkir cerdas yang dirancang untuk <br>
            keamanan dan kenyamanan Anda
        </p>

        <a href="#about" class="btn btn-yellow" data-aos="fade-up" data-aos-delay="900">
            Selengkapnya <i class="fas fa-arrow-down"></i>
        </a>
    </section>
    <!-- About Section-->
    <section id="about" class="about-section py-5">
        <div class="container">
            <!-- Judul -->
            <div class="text-center mb-5" data-aos="fade-down">
                <h2 class="fw-bold">Anda Masih Mengelola Parkir Secara Manual?</h2>
            </div>

            <!-- Bagian Problem -->
            <div class="row align-items-center mb-5">
                <div class="col-md-6 mb-4 mb-md-0" data-aos="fade-right">
                    <img src="{{ asset('images/kehilangan.png') }}" alt="Ilustrasi Kehilangan Kendaraan" class="img-fluid rounded">
                </div>
                <div class="col-md-6" data-aos="fade-left">
                    <p class="lead text-muted">
                        Sistem parkir manual menyimpan berbagai risiko yang sering terjadi di lapangan:
                    </p>
                    <ul class="list-unstyled lead mt-3">
                        <li class="mb-2"><i class="bi bi-x-circle-fill text-black me-2"></i> Risiko kehilangan kendaraan tinggi</li>
                        <li class="mb-2"><i class="bi bi-clock-fill text-black me-2"></i> Pencatatan lambat & tidak akurat</li>
                        <li class="mb-2"><i class="bi bi-eye-slash-fill text-black me-2"></i> Pengawasan minim & tidak realtime</li>
                    </ul>
                    <p class="lead text-muted mt-4">
                        Kini saatnya mempertimbangkan sistem yang lebih aman, efisien, dan sesuai dengan era digital.
                    </p>
                </div>
            </div>
            <!-- Transisi ke Solusi -->
            <div class="text-center my-5" data-aos="zoom-in">
                <i class="bi bi-arrow-down-circle-fill fs-1 text-dark mb-3"></i>
                <h3 class="fw-bold text-dark">Inilah Solusinya!</h3>
            </div>
            <!-- Bagian Tentang SPOT -->
            <div class="row align-items-center">
                <div class="col-md-6 order-md-2 mb-4 mb-md-0" data-aos="fade-left">
                    <img src="{{ asset('images/Tentang.png') }}" alt="SPOT Illustration" class="img-fluid rounded">
                </div>
                <div class="col-md-6 order-md-1" data-aos="fade-right">
                    <h3 class="fw-bold mb-3">Sistem Parkir Otomatis Terjamin</h3>
                    <p class="lead text-muted">
                        SPOT adalah sistem parkir modern yang dirancang untuk meningkatkan keamanan dan efisiensi pengelolaan parkir.
                        Menggunakan teknologi canggih seperti Internet of Things (IoT), QR code, dan deteksi plat nomor, SPOT memudahkan
                        proses masuk dan keluar kendaraan secara otomatis. Sistem ini memastikan kendaraan yang masuk terverifikasi dengan akurat
                        dan membantu pengelola menganalisis pola parkir agar kapasitas lebih optimal.
                    </p>
                    <p class="lead text-muted">
                        Dengan SPOT, parkir jadi lebih cepat, aman, dan nyaman — tanpa ribet! 🚗✨
                    </p>
                    <div class="mt-4">
                        <a href="{{ route('register') }}" class="btn btn-outline-black me-2">Daftar</a>
                        <a href="{{ route('login') }}" class="btn btn-black">Masuk</a>
                    </div>
                </div>
            </div>
        </div>
    </section>



    <!-- Features Section -->
    <section id="features" class="py-5 bg-light">
        <div class="container text-center">
            <h2 class="text-center mb-5 section-title" data-aos="zoom-in">Fitur <span class="yellow">SPOT</span></h2>
            <div class="row g-4">
                <div class="col-md-4 col-sm-6">
                    <div class="card shadow-box p-4 hidden-card" data-aos="fade-up">
                        <img src="{{ asset('images/pendaftaran.png') }}" class="img-fluid mb-3" alt="Pendaftaran Akun">
                        <h5>Pendaftaran Akun</h5>
                        <p>Daftar akun dengan mudah untuk mengakses layanan kami.</p>
                    </div>
                </div>
                <div class="col-md-4 col-sm-6">
                    <div class="card shadow-box p-4 hidden-card" data-aos="fade-up" data-aos-delay="200">
                        <img src="{{ asset('images/kelola pengguna.png') }}" class="img-fluid mb-3" alt="Kelola Pengguna">
                        <h5>Kelola Pengguna</h5>
                        <p>Kelola data pengguna dengan <br> aman dan efisien.</p>
                    </div>
                </div>
                <div class="col-md-4 col-sm-6">
                    <div class="card shadow-box p-4 hidden-card" data-aos="fade-up" data-aos-delay="400">
                        <img src="{{ asset('images/kelola kendaraan.png') }}" class="img-fluid mb-3" alt="Kelola Kendaraan">
                        <h5>Kelola Kendaraan</h5>
                        <p>Kelola data kendaraan secara <br> praktis dan terorganisir.</p>
                    </div>
                </div>
                <div class="col-md-4 col-sm-6">
                    <div class="card shadow-box p-4 hidden-card" data-aos="fade-up" data-aos-delay="600">
                        <img src="{{ asset('images/AksesQR.png') }}" class="img-fluid mb-3" alt="Akses QR Code & Deteksi Plat">
                        <h5>Akses QR Code & Deteksi Plat</h5>
                        <p>Pindai QR Code dan deteksi otomatis nomor plat untuk akses parkir yang aman.</p>
                    </div>
                </div>
                <div class="col-md-4 col-sm-6">
                    <div class="card shadow-box p-4 hidden-card" data-aos="fade-up" data-aos-delay="800">
                        <img src="{{ asset('images/Monitor.png') }}" class="img-fluid mb-3" alt="Monitoring Parkir">
                        <h5>Monitoring Parkir</h5>
                        <p>Pantau aktivitas keluar masuk kendaraan dan status parkir secara real-time.</p>
                    </div>
                </div>
                <div class="col-md-4 col-sm-6">
                    <div class="card shadow-box p-4 hidden-card" data-aos="fade-up" data-aos-delay="1000">
                        <img src="{{ asset('images/laporan.png') }}" class="img-fluid mb-3" alt="Laporan Parkir">
                        <h5>Laporan Parkir</h5>
                        <p>Generate laporan parkir otomatis untuk analisis yang lebih akurat dan optimal.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <section id="pengguna" class="py-5" style="background-color: #ffdb4d;" data-aos="fade-up">
        <div class="container">
            <div class="col text-center">
                <h2 class="text-center fw-bold mb-5" data-aos="zoom-in">Pengguna SPOT</h2>
            </div>

            <!-- Active Users -->
            <div class="row text-center mb-5">
                <div class="col-md-4 mb-4" data-aos="fade-right">
                    <div class="p-4 rounded-4 h-100 shadow-lg bg-light bg-gradient hover-zoom" style="transition: 0.3s ease; border-top: 5px solid #ffdb4d;">
                        <i class="bi bi-calendar3 fs-1 mb-3" style="color:#ffdb4d;"></i>
                        <h5 class="fw-semibold text-dark">MAU</h5>
                        <h2 class="fw-bold" style="color:#ffdb4d;">100+</h2>
                        <p class="text-muted">Pengguna Aktif Bulanan</p>
                    </div>
                </div>
                <div class="col-md-4 mb-4" data-aos="fade-up">
                    <div class="p-4 rounded-4 h-100 shadow-lg bg-light bg-gradient hover-zoom" style="transition: 0.3s ease; border-top: 5px solid #ffdb4d;">
                        <i class="bi bi-calendar-week fs-1 mb-3" style="color:#ffdb4d;"></i>
                        <h5 class="fw-semibold text-dark">WAU</h5>
                        <h2 class="fw-bold" style="color:#ffdb4d;">80</h2>
                        <p class="text-muted">Pengguna Aktif Mingguan</p>
                    </div>
                </div>
                <div class="col-md-4 mb-4" data-aos="fade-left">
                    <div class="p-4 rounded-4 h-100 shadow-lg bg-light bg-gradient hover-zoom" style="transition: 0.3s ease; border-top: 5px solid #ffdb4d;">
                        <i class="bi bi-calendar-day fs-1 mb-3" style="color:#ffdb4d;"></i>
                        <h5 class="fw-semibold text-dark">DAU</h5>
                        <h2 class="fw-bold" style="color:#ffdb4d;">50</h2>
                        <p class="text-muted">Pengguna Aktif Harian</p>
                    </div>
                </div>
            </div>

            <!-- Additional Metrics -->
            <div class="row mb-5">
                <div class="col-md-4 mb-4" data-aos="flip-left">
                    <!-- Session Duration -->
                    <div class="bg-white rounded-4 shadow-lg p-4 h-100 hover-zoom border-start border-5 position-relative" style="transition: 0.3s ease; border-color: #ffdb4d;">
                        <span class="badge text-dark position-absolute top-0 end-0 m-3" style="background-color: #ffdb4d;">Harian</span>
                        <div class="d-flex align-items-center mb-3">
                            <div class="rounded-circle d-flex justify-content-center align-items-center"
                                style="width: 50px; height: 50px; background-color: rgba(255,219,77,0.25);">
                                <i class="bi bi-clock-history fs-4" style="color:#ffdb4d;"></i>
                            </div>
                            <h5 class="ms-3 mb-0 fw-semibold text-dark">Durasi & Frekuensi</h5>
                        </div>
                        <p class="text-muted mb-1">Rata-rata sesi:</p>
                        <h6 class="fw-bold text-dark">4 menit 20 detik</h6>
                        <p class="text-muted mb-1">Kunjungan harian:</p>
                        <h6 class="fw-bold text-dark">2–3 kali</h6>
                    </div>
                </div>

                <div class="col-md-4 mb-4" data-aos="flip-up">
                    <!-- Retention Rate -->
                    <div class="bg-white rounded-4 shadow-lg p-4 h-100 hover-zoom border-start border-5 position-relative" style="transition: 0.3s ease; border-color: #ffdb4d;">
                        <span class="badge text-dark position-absolute top-0 end-0 m-3" style="background-color: #ffdb4d;">7 Hari</span>
                        <div class="d-flex align-items-center mb-3">
                            <div class="rounded-circle d-flex justify-content-center align-items-center" style="width: 50px; height: 50px; background-color: rgba(255,219,77,0.25);">
                                <i class="bi bi-arrow-repeat fs-4" style="color:#ffdb4d;"></i>
                            </div>
                            <h5 class="ms-3 mb-0 fw-semibold text-dark">Retensi Pengguna</h5>
                        </div>
                        <h2 class="fw-bold" style="color:#ffdb4d;">72%</h2>
                        <p class="text-muted">Kembali dalam 7 hari terakhir</p>
                    </div>
                </div>

                <div class="col-md-4 mb-4" data-aos="flip-right">
                    <!-- Feature Usage -->
                    <div class="bg-white rounded-4 shadow-lg p-4 h-100 hover-zoom border-start border-5 position-relative" style="transition: 0.3s ease; border-color: #ffdb4d;">
                        <span class="badge text-dark position-absolute top-0 end-0 m-3" style="background-color: #ffdb4d;">Top 3</span>
                        <div class="d-flex align-items-center mb-3">
                            <div class="rounded-circle d-flex justify-content-center align-items-center" style="width: 50px; height: 50px; background-color: rgba(255,219,77,0.25);">
                                <i class="bi bi-stars fs-4" style="color:#ffdb4d;"></i>
                            </div>
                            <h5 class="ms-3 mb-0 fw-semibold text-dark">Fitur Populer</h5>
                        </div>
                        <ul class="list-unstyled text-muted mb-0">
                            <li><i class="bi bi-check-circle-fill text-success me-2"></i>70% QR Scan</li>
                            <li><i class="bi bi-check-circle-fill text-success me-2"></i>35% Deteksi Plat</li>
                            <li><i class="bi bi-check-circle-fill text-success me-2"></i>15% Laporan Parkir</li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Testimonials -->
            <div class="mb-5">
                <h3 class="fw-bold mb-4 text-center" data-aos="zoom-in-up">Apa Kata Pengguna Kami?</h3>
                <div class="row g-4">
                    <div class="col-md-4" data-aos="fade-up" data-aos-delay="100">
                        <div class="card h-100 border-0 shadow-lg rounded-4 p-3" style="background-color: #ffffff; transition: transform 0.3s;">
                            <div class="card-body">
                                <div class="d-flex align-items-start mb-3">
                                    <div class="me-3">
                                        <i class="bi bi-shield-check fs-2" style="color:#ffdb4d;"></i>
                                    </div>
                                    <div>
                                        <p class="mb-2 fs-5">“Parkir jadi lebih aman dan nyaman di kampus.”</p>
                                        <small class="text-muted">— Penguji Mahasiswa</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4" data-aos="fade-up" data-aos-delay="200">
                        <div class="card h-100 border-0 shadow-lg rounded-4 p-3" style="background-color: #ffffff; transition: transform 0.3s;">
                            <div class="card-body">
                                <div class="d-flex align-items-start mb-3">
                                    <div class="me-3">
                                        <i class="bi bi-qr-code-scan fs-2" style="color:#ffdb4d;"></i>
                                    </div>
                                    <div>
                                        <p class="mb-2 fs-5">“QR code cepat dan mudah dipindai.”</p>
                                        <small class="text-muted">— Pengguna Beta</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4" data-aos="fade-up" data-aos-delay="300">
                        <div class="card h-100 border-0 shadow-lg rounded-4 p-3" style="background-color: #ffffff; transition: transform 0.3s;">
                            <div class="card-body">
                                <div class="d-flex align-items-start mb-3">
                                    <div class="me-3">
                                        <i class="bi bi-camera-video fs-2" style="color:#ffdb4d;"></i>
                                    </div>
                                    <div>
                                        <p class="mb-2 fs-5">“Monitoring bantu cek kendaraan masuk.”</p>
                                        <small class="text-muted">— Penguji Admin</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>


        </div>
    </section>

    <!-- Section Harga Layanan SPOT -->
    <section class="py-5 bg-light" id="penawaran">
        <div class="container text-center">
            <h2 class="fw-bold mb-4" data-aos="zoom-in-up" data-aos-duration="800">Penawaran terbatas, serbu sekarang!</h2>
            <p class="mb-5 text-muted" data-aos="fade-up" data-aos-delay="100">Solusi parkir otomatis dengan harga transparan dan fitur lengkap, sesuai kebutuhan Anda.</p>

            <div class="row g-4">
                <!-- Paket Dasar -->
                <div class="col-md-4" data-aos="fade-right" data-aos-delay="150">
                    <div class="card h-100 shadow-sm">
                        <div class="card-body">
                            <div class="mb-3">
                                <i class="bi bi-box fs-1 text-warning"></i>
                            </div>
                            <h4 class="card-title fw-semibold">Paket Dasar</h4>
                            <h3 style="color: #ffdb4d;">Rp 4.500.000</h3>
                            <p class="text-muted">Pembayaran sekali, tanpa biaya bulanan</p>
                            <ul class="list-unstyled text-start">
                                <li><i class="bi bi-check-circle-fill text-success me-2"></i> Sistem scan QR</li>
                                <li><i class="bi bi-check-circle-fill text-success me-2"></i> Dashboard Admin</li>
                                <li><i class="bi bi-check-circle-fill text-success me-2"></i> Riwayat Transaksi</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Paket Profesional -->
                <div class="col-md-4" data-aos="zoom-in" data-aos-delay="250">
                    <div class="card h-100 shadow-sm" style="border: 1px solid #ffdb4d;">
                        <div class="card-body">
                            <div class="mb-3">
                                <i class="bi bi-tools fs-1 text-warning"></i>
                            </div>
                            <h4 class="card-title fw-semibold text-warning">Paket Profesional</h4>
                            <h3 style="color: #ffdb4d;">Rp 6.500.000</h3>
                            <p class="text-muted">+ Rp 300.000/bulan untuk maintenance & support</p>
                            <ul class="list-unstyled text-start">
                                <li><i class="bi bi-check-circle-fill text-success me-2"></i> Fitur Dasar + Kamera Deteksi</li>
                                <li><i class="bi bi-check-circle-fill text-success me-2"></i> Statistik & Laporan Otomatis</li>
                                <li><i class="bi bi-check-circle-fill text-success me-2"></i> Fitur Komplain Pengguna</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Paket Enterprise -->
                <div class="col-md-4" data-aos="fade-left" data-aos-delay="150">
                    <div class="card h-100 shadow-sm">
                        <div class="card-body">
                            <div class="mb-3">
                                <i class="bi bi-building fs-1 text-warning"></i>
                            </div>
                            <h4 class="card-title fw-semibold">Paket Enterprise</h4>
                            <h3 style="color: #ffdb4d;">Custom</h3>
                            <p class="text-muted">Solusi fleksibel untuk skala besar</p>
                            <ul class="list-unstyled text-start">
                                <li><i class="bi bi-check-circle-fill text-success me-2"></i> Seluruh fitur Profesional</li>
                                <li><i class="bi bi-check-circle-fill text-success me-2"></i> Integrasi Sistem Pihak Ketiga</li>
                                <li><i class="bi bi-check-circle-fill text-success me-2"></i> Dukungan SLA Khusus</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Footer Harga -->
            <div class="mt-5" data-aos="fade-up" data-aos-delay="300">
                <p class="text-muted"><i class="bi bi-arrow-repeat me-2"></i><strong>Perpanjangan Maintenance Bulanan</strong>: Mulai dari <strong>Rp 300.000/bulan</strong></p>
                <p class="text-muted">
                    <i class="bi bi-info-circle me-2"></i>
                    <em>
                        Hubungi kami untuk pilihan penawaran yang Anda inginkan sekarang!
                        <a href="#contact" style="color: #ffdb4d; font-weight: bold; text-decoration: underline;">Hubungi kami</a>
                    </em>
                </p>
            </div>
        </div>
    </section>
    <!-- End Section Harga -->



    <!-- Team Section -->
    <section id="team" class="section bg-team py-5">
        <div class="container">
            <h2 class="text-center mb-5 section-title" data-aos="fade-right">Tim Kami</h2>
            <p class="text-center mb-5 section-subtitle" data-aos="fade-up" data-aos-delay="100">
                Bersama, kami membangun solusi inovatif yang mendorong masa depan teknologi.
                Dengan semangat kolaborasi, kreativitas, dan keahlian, kami menghadirkan inovasi yang berarti
                untuk menjawab tantangan dunia digital yang terus berkembang.
            </p>
            <div class="row">
                <div class="col-12 col-sm-6 col-lg-3 mb-4">
                    <div class="team-card text-center" data-aos="zoom-in" data-aos-delay="100">
                        <img src="{{ asset('images/Tamaris.png') }}" alt="Team Member" class="team-img">
                        <h4>Tamaris Roulina</h4>
                        <p>Lead Developer</p>
                        <div class="social-icons">
                            <a href="mailto:#" class="social-icon"><i class="ri-mail-fill"></i></a>
                            <a href="#" class="social-icon"><i class="ri-instagram-fill"></i></a>
                            <a href="#" class="social-icon"><i class="ri-whatsapp-fill"></i></a>
                            <a href="#" class="social-icon"><i class="ri-linkedin-box-fill"></i></a>
                            <a href="#" class="social-icon"><i class="ri-youtube-fill"></i></a>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-sm-6 col-lg-3 mb-4">
                    <div class="team-card text-center" data-aos="zoom-in" data-aos-delay="200">
                        <img src="{{ asset('images/Alifzidan.png') }}" alt="Team Member" class="team-img">
                        <h4>Alifzidan Rizky</h4>
                        <p>UI/UX Designer</p>
                        <div class="social-icons">
                            <a href="mailto:#" class="social-icon"><i class="ri-mail-fill"></i></a>
                            <a href="#" class="social-icon"><i class="ri-instagram-fill"></i></a>
                            <a href="#" class="social-icon"><i class="ri-whatsapp-fill"></i></a>
                            <a href="#" class="social-icon"><i class="ri-linkedin-box-fill"></i></a>
                            <a href="#" class="social-icon"><i class="ri-youtube-fill"></i></a>
                        </div>

                    </div>
                </div>
                <div class="col-12 col-sm-6 col-lg-3 mb-4">
                    <div class="team-card text-center" data-aos="zoom-in" data-aos-delay="300">
                        <img src="{{ asset('images/Elicia.png') }}" alt="Team Member" class="team-img">
                        <h4>Elicia Sandova</h4>
                        <p>Testing</p>
                        <div class="social-icons">
                            <a href="mailto:#" class="social-icon"><i class="ri-mail-fill"></i></a>
                            <a href="#" class="social-icon"><i class="ri-instagram-fill"></i></a>
                            <a href="#" class="social-icon"><i class="ri-whatsapp-fill"></i></a>
                            <a href="#" class="social-icon"><i class="ri-linkedin-box-fill"></i></a>
                            <a href="#" class="social-icon"><i class="ri-youtube-fill"></i></a>
                        </div>

                    </div>
                </div>
                <div class="col-12 col-sm-6 col-lg-3 mb-4">
                    <div class="team-card text-center" data-aos="zoom-in" data-aos-delay="400">
                        <img src="{{ asset('images/Maulana.png') }}" alt="Team Member" class="team-img">
                        <h4>Maulana Arianto</h4>
                        <p>Backend Developer</p>
                        <div class="social-icons">
                            <a href="mailto:#" class="social-icon"><i class="ri-mail-fill"></i></a>
                            <a href="#" class="social-icon"><i class="ri-instagram-fill"></i></a>
                            <a href="#" class="social-icon"><i class="ri-whatsapp-fill"></i></a>
                            <a href="#" class="social-icon"><i class="ri-linkedin-box-fill"></i></a>
                            <a href="#" class="social-icon"><i class="ri-youtube-fill"></i></a>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </section>







    <!-- Contact Section -->
    <section id="contact" class="section py-5 bg-light">
        <h2 class="text-center mb-5 section-title" data-aos="zoom-in">Hubungi Kami</h2>
        <div class="container d-flex justify-content-center flex-wrap gap-3">

            <form class="contact-form d-flex flex-column" style="flex: 1; max-width: 400px;" data-aos="fade-right">
                <input type="text" class="form-control mb-2" placeholder="Nama Lengkap Anda">
                <input type="email" class="form-control mb-2" placeholder="Alamat Email Anda">
                <textarea class="form-control mb-2" rows="3" placeholder="Isi Pesan Anda"></textarea>
                <button type="submit" class="btn btn-dark">
                    <i class="fa fa-paper-plane"></i> Kirim
                </button>
            </form>

            <!-- Contact Info -->
            <div class="contact-info d-flex flex-column" style="flex: 1; max-width: 400px;" data-aos="fade-right">
                <div class="contact-item d-flex" data-aos="zoom-in">
                    <div class="contact-icon-box">
                        <i class="fa fa-phone contact-icon"></i>
                    </div>
                    <div class="contact-text-box">
                        <p>+62 821 7147 5991</p>
                    </div>
                </div>
                <div class="contact-item d-flex" data-aos="zoom-in" data-aos-delay="100">
                    <div class="contact-icon-box">
                        <i class="fa fa-envelope contact-icon"></i>
                    </div>
                    <div class="contact-text-box">
                        <p>spotid618@gmail.com</p>
                    </div>
                </div>
                <div class="contact-item d-flex" data-aos="zoom-in" data-aos-delay="200">
                    <div class="contact-icon-box">
                        <i class="fab fa-instagram contact-icon"></i>
                    </div>
                    <div class="contact-text-box">
                        <p>@spotid618</p>
                    </div>
                </div>
                <div class="contact-item d-flex" data-aos="zoom-in" data-aos-delay="300">
                    <div class="contact-icon-box">
                        <i class="fas fa-map-marker-alt contact-icon"></i>
                    </div>
                    <div class="contact-text-box">
                        <p>Politeknik Negeri Batam</p>
                    </div>
                </div>
            </div>

        </div>
    </section>





    <!-- Footer -->
    <footer>
        <p>&copy; 2025 - Sistem Parkir Otomatis Terjamin</p>
    </footer>
    <!-- Scroll to Top Button -->
    <button id="scrollTopBtn" onclick="scrollToTop()"><i class="fas fa-chevron-up"></i></button>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Tampilkan tombol scroll jika user menggulir ke bawah 100px
        window.onscroll = function() {
            var scrollTopBtn = document.getElementById("scrollTopBtn");
            if (document.body.scrollTop > 100 || document.documentElement.scrollTop > 100) {
                scrollTopBtn.style.display = "block";
            } else {
                scrollTopBtn.style.display = "none";
            }
        };

        // Fungsi untuk scroll ke atas
        function scrollToTop() {
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        }

        document.addEventListener("DOMContentLoaded", function() {
            const navbar = document.querySelector(".navbar");
            const toggle = document.querySelector(".navbar-toggle");
            const nav = document.querySelector(".navbar-nav");

            // Navbar berubah saat scroll
            window.addEventListener("scroll", function() {
                if (window.scrollY > 50) {
                    navbar.classList.add("scrolled");
                } else {
                    navbar.classList.remove("scrolled");
                }
            });

            // Toggle menu di mobile
            toggle.addEventListener("click", function() {
                nav.classList.toggle("active");
            });
        });
        //card
        AOS.init({
            duration: 1000, // Durasi animasi (ms)
            once: false, // Animasi akan terjadi lebih dari sekali
            easing: 'ease-in-out',
        });

        //loading
        document.addEventListener("DOMContentLoaded", function() {
            let loadingOverlay = document.getElementById("loading-overlay");

            setTimeout(() => {
                loadingOverlay.style.display = "flex";
            }, 100);

            window.onload = function() {
                setTimeout(() => {
                    loadingOverlay.style.opacity = "0";
                    setTimeout(() => {
                        loadingOverlay.style.display = "none";
                        document.getElementById("content").style.display = "block";
                    }, 500);
                }, 300);
            };
        });
    </script>

</body>

</html>