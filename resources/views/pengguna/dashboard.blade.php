@extends('layouts.pengguna')

@section('content')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.js"></script>

<style>
    body {
        background-color: #f5f5f5;
        font-family: 'Roboto', Arial, sans-serif;
    }

    .card {
        border: 1px solid black;
        background-color: white;
        overflow: hidden;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        border-radius: 8px;
    }

    .user-card {
        height: 100%;
        display: flex;
        flex-direction: column;
        justify-content: start;
    }

    h6.text-center {
        color: black;
        border-bottom: 1px solid black;
        padding: 10px 0;
        margin: 0;
        font-weight: bold;
    }

    .foto-container {
        width: 150px;
        aspect-ratio: 1/1;
        overflow: hidden;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 15px auto;
    }

    .foto-container img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        border-radius: 8px;
    }

    .details table {
        width: 100%;
    }

    .details th,
    .details td {
        padding: 6px;
        font-size: 0.95rem;
    }

    .card-qr {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        flex: 1;
        padding: 15px;
    }

    .qr-code {
        width: 180px;
        height: 180px;
        object-fit: contain;
        margin-bottom: 10px;
    }

    .btn-download {
        background-color: #FFDC40;
        color: black;
        padding: 8px 20px;
        border-radius: 5px;
        text-decoration: none;
        font-weight: 500;
        border: none;
    }

    .btn-download:hover {
        background-color: #e5c930;
        color: black;
    }

    @media (max-width: 768px) {
        .foto-container {
            width: 120px;
        }

        .qr-code {
            width: 140px;
            height: 140px;
        }

        .details th,
        .details td {
            font-size: 0.85rem;
        }
    }
</style>


<!-- Overlay Loading -->
<div id="loading-overlay">
    <img id="loading-logo" src="{{ asset('images/spot-logo.png') }}" alt="SPOT Logo">
</div>
<div class="container-fluid mt-4">
    <div class="d-flex justify-content-between align-items-center mt-3" data-aos="fade-right">
        <h4 class="dashboard-title black">Dashboard</h4>
        <p class="date-display mb-0">{{ $date }}</p>
    </div>
    <p class="greeting-message" data-aos="fade-up" data-aos-delay="200">
        Hallo, {{ Auth::user()->nama }} <i class="fas fa-smile"></i>
    </p>
</div>

<div class="row mx-0">
    <div class="col-md-4 col-sm-6 mb-3" data-aos="fade-up">
        <div class="card-body">
            <div class="icon">
                <i class="fas fa-users"></i>
            </div>
            <h6 class="card-title">{{ $jumlahPengguna }}</h6>
            <p class="card-text">Jumlah Pengguna</p>
        </div>
        <div class="highlight"></div>
    </div>

    <div class="col-md-4 col-sm-6 mb-3" data-aos="fade-up" data-aos-delay="100">
        <div class="card-body">
            <div class="icon">
                <i class="fas fa-car"></i>
            </div>
            <h6 class="card-title">{{ $jumlahParkirMasuk }}</h6>
            <p class="card-text">Jumlah Parkir Masuk</p>
        </div>
        <div class="highlight"></div>
    </div>

    <div class="col-md-4 col-sm-6 mb-3" data-aos="fade-up" data-aos-delay="200">
        <div class="card-body">
            <div class="icon">
                <i class="fas fa-car-side"></i>
            </div>
            <h6 class="card-title">{{ $jumlahParkirKeluar }}</h6>
            <p class="card-text">Jumlah Parkir Keluar</p>
        </div>
        <div class="highlight"></div>
    </div>
</div>

<div class="row mx-0">
    <div class="container mt-4">
        <div class="row align-items-stretch">
            <!-- User Info Card -->
            <div class="col-lg-8 col-md-12 mb-3">
                <div class="card user-card">
                    <h6 class="text-center">Data Diri Pengguna</h6>
                    <div class="d-flex flex-column flex-lg-row align-items-center p-3">
                        <div class="user-info text-center">
                            <div class="foto-container">
                                <img src="{{ Auth::user()->foto }}" alt="Foto Pengguna">
                            </div>
                        </div>
                        <div class="details ps-lg-3">
                            <table class="table table-borderless mb-0">
                                <tbody>
                                    <tr>
                                        <th>NID</th>
                                        <td>:</td>
                                        <td>{{ $penggunaDetail->id_pengguna }}</td>
                                    </tr>
                                    <tr>
                                        <th>Nama</th>
                                        <td>:</td>
                                        <td>{{ $penggunaDetail->nama }}</td>
                                    </tr>
                                    <tr>
                                        <th>Kategori</th>
                                        <td>:</td>
                                        <td>{{ $penggunaDetail->kategori }}</td>
                                    </tr>
                                    <tr>
                                        <th>Email</th>
                                        <td>:</td>
                                        <td>{{ $penggunaDetail->email }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- QR Code Card -->
            <div class="col-lg-4 col-md-12 mb-3">
                <div class="card user-card text-center">
                    <h6 class="text-center">QR Code</h6>
                    <div class="card-qr">
                        @if ($qrCodePath)
                        <img src="{{ $qrCodePath }}" alt="QR Code" class="qr-code">
                        <a href="{{ $qrCodePath }}" download="QR_Code_{{ Auth::user()->nama }}" class="btn-download mt-2">
                            Unduh <i class="fas fa-download"></i>
                        </a>
                        @else
                        <span>Tidak ada QR Code tersedia.</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

<!-- Modal for Success Message -->
@if (session('status') === 'success' && session('message'))
<div id="successModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="successModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document"> <!-- Center the modal -->
        <div class="modal-content">
            <div class="modal-header" style="background-color: #FFDC40;">
                <h5 class="modal-title" id="successModalLabel">Berhasil Masuk</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body text-center">
                <div>
                    <i class="fas fa-check-circle" style="color: green; font-size: 3em; animation: bounce 1s infinite; padding-bottom:10px;"></i>
                </div>
                <p>Selamat datang, Anda berhasil masuk!<br>"{{ Auth::user()->nama }}"</p>
            </div>
            <div class="modal-footer d-flex justify-content-center">
                <button type="button" class="btn" style="background-color: #FFDC40; color: black; width: 100px;" data-dismiss="modal">Oke</button>

            </div>
        </div>
    </div>
</div>
@endif

@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        console.log('Page loaded');
        $('#successModal').modal('show'); // Temporarily show the modal to see if it triggers without conditions
    });
    setInterval(function() {
        $.ajax({
            url: '{{ route("dashboard.cek.notif.parkir") }}',
            type: 'GET',
            success: function(response) {
                if (response.status) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Informasi Parkir',
                        text: response.message,
                        timer: 3000,
                        showConfirmButton: false
                    });
                }
            },
            error: function(xhr) {
                console.error(xhr.responseText);
            }
        });
    }, 1000); // cek setiap 5 detik
</script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<style>
    @keyframes bounce {

        0%,
        20%,
        50%,
        80%,
        100% {
            transform: translateY(0);
        }

        40% {
            transform: translateY(-10px);
        }

        60% {
            transform: translateY(-5px);
        }
    }
</style>
@endsection