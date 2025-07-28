<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

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
    <!-- Google tag (gtag.js) -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-3G8CML90EN"></script>
    <script>
        window.dataLayer = window.dataLayer || [];

        function gtag() {
            dataLayer.push(arguments);
        }
        gtag('js', new Date());

        gtag('config', 'G-3G8CML90EN');
    </script>
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
                    <li class="nav-item"><a class="nav-link" href="#features">Fitur Utama</a></li>
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
    <!-- Problem Statement Section -->
    <section id="about" class="about-section py-5" style="background-color: #ffdb4d">
        <div class="container">
            <div class="row align-items-center">
                <!-- Text Content -->
                <div class="col-md-6 mb-4 mb-md-0" data-aos="fade-right">
                    <h2 class="fw-bold mb-4 text-center">Anda Masih Mengelola Parkir Secara Manual?</h2>
                    <img src="{{ asset('images/kehilangan.png') }}" alt="Ilustrasi Kehilangan Kendaraan" class="img-fluid">
                </div>

                <!-- Illustration / Animation -->
                <div class="col-md-6" data-aos="fade-left">
                    <p class="lead text-muted">
                        Sistem parkir manual menyimpan berbagai risiko yang sering terjadi di lapangan:
                    </p>
                    <ul class="list-unstyled lead mt-3">
                        <li class="mb-2"><i class="bi bi-x-circle-fill text-black me-2"></i> Risiko kehilangan kendaraan tinggi</li>
                        <li class="mb-2"><i class="bi bi-clock-fill text-black  me-2"></i> Pencatatan lambat & tidak akurat</li>
                        <li class="mb-2"><i class="bi bi-eye-slash-fill text-black r me-2"></i> Pengawasan minim & tidak realtime</li>
                    </ul>
                    <p class="lead text-muted mt-4">
                        Kini saatnya mempertimbangkan sistem yang lebih aman, efisien, dan sesuai dengan era digital.
                    </p>
                    <a href="#solution" class="btn btn-outline-black">Cari Tahu Solusinya</a>
                </div>
            </div>
        </div>
    </section>

    <!-- About Section -->
    <section id="about" class="about-section" style="background-color: #f8f8f8">
        <div class="about-container">
            <div class="about-content">
                <h2 data-aos="fade-right">Sistem Parkir Otomatis Terjamin</h2>
                <p data-aos="fade-right">
                    SPOT adalah sistem parkir modern yang dirancang untuk meningkatkan keamanan dan efisiensi pengelolaan parkir.
                    Menggunakan teknologi canggih seperti Internet of Things (IoT), QR code, dan deteksi plat nomor, SPOT memudahkan
                    proses masuk dan keluar kendaraan secara otomatis. Sistem ini memastikan kendaraan yang masuk terverifikasi dengan akurat
                    dan membantu pengelola menganalisis pola parkir agar kapasitas lebih optimal. Dengan SPOT, parkir jadi lebih cepat, aman,
                    dan nyaman — tanpa ribet! 🚗✨
                </p>
                <div class="about-buttons" data-aos="zoom-in">
                    <a href="{{ route('register') }}" class="btn btn-outline-black">Daftar</a>
                    <a href="{{ route('login') }}" class="btn btn-black">Masuk</a>
                </div>
            </div>

            <div class="about-image" data-aos="fade-up">
                <img src="{{ asset('images/Tentang.png') }}" alt="SPOT Illustration">
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
    <!-- Section Harga Layanan SPOT -->
    <section class="py-5 bg-light" id="pricing">
        <div class="container text-center">
            <h2 class="fw-bold mb-4">💰 Harga Layanan SPOT</h2>
            <p class="mb-5 text-muted">Solusi parkir otomatis dengan harga transparan dan fitur lengkap, sesuai kebutuhan Anda.</p>

            <div class="row g-4">
                <!-- Paket Dasar -->
                <div class="col-md-4">
                    <div class="card h-100 shadow-sm">
                        <div class="card-body">
                            <h4 class="card-title fw-semibold">🧩 Paket Dasar</h4>
                            <h3 class="text-primary">Rp 4.500.000</h3>
                            <p class="text-muted">Pembayaran sekali, tanpa biaya bulanan</p>
                            <ul class="list-unstyled text-start">
                                <li>✅ 1 set perangkat IoT SPOT</li>
                                <li>✅ Panduan instalasi & dokumentasi</li>
                                <li>✅ Gratis maintenance 1 bulan</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Paket Profesional -->
                <div class="col-md-4">
                    <div class="card h-100 shadow-sm border-primary">
                        <div class="card-body">
                            <h4 class="card-title fw-semibold text-primary">🔧 Paket Profesional</h4>
                            <h3 class="text-primary">Rp 6.500.000</h3>
                            <p class="text-muted">+ Rp 300.000/bulan untuk maintenance & support</p>
                            <ul class="list-unstyled text-start">
                                <li>✅ 1 set perangkat IoT SPOT</li>
                                <li>✅ Sistem monitoring berbasis web</li>
                                <li>✅ Instalasi langsung oleh tim SPOT</li>
                                <li>✅ Support teknis via WA/Telegram</li>
                                <li>✅ Maintenance perangkat rutin</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Paket Enterprise -->
                <div class="col-md-4">
                    <div class="card h-100 shadow-sm">
                        <div class="card-body">
                            <h4 class="card-title fw-semibold">🏢 Paket Enterprise</h4>
                            <h3 class="text-primary">Custom</h3>
                            <p class="text-muted">Solusi fleksibel untuk skala besar</p>
                            <ul class="list-unstyled text-start">
                                <li>✅ 1 set perangkat IoT SPOT</li>
                                <li>✅ Sistem monitoring berbasis web</li>
                                <li>✅ Integrasi dengan sistem instansi anda</li>
                                <li>✅ Penyesuaian branding & fitur</li>
                                <li>✅ Update sistem & fitur berkala</li>
                                <li>✅ Layanan pelatihan pengguna</li>

                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-5">
                <p class="text-muted">🔁 <strong>Perpanjangan Maintenance Bulanan</strong>: Mulai dari <strong>Rp 300.000/bulan</strong></p>
                <p class="text-muted">💡 <em>Tanyakan penawaran khusus untuk pilot project kampus, kantor, atau perumahan!</em></p>
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