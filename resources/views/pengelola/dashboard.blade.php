@extends('layouts.pengelola')

@section('title', 'Dashboard Pengelola')

@section('content')

<style>
    body {
        background-color: #f5f5f5;
        font-family: 'Roboto', Arial, sans-serif;
    }

    .card-spot {
        margin-bottom: 20px;
        border: 1px solid black;
        border-radius: 0;
    }

    .icon-spot {
        font-size: 2rem;
        color: black;
        margin-bottom: 10px;
    }

    .card-title-spot {
        font-size: 1.25rem;
        font-weight: bold;
        color: black;
        margin-bottom: 5px;
    }

    .card-text-spot {
        font-size: 1rem;
        color: black;
    }

    .highlight {
        background-color: #FFDC40;
        padding: 10px;
        border: 1px solid black;
        border-bottom-left-radius: 5px;
        border-bottom-right-radius: 5px;
    }

    .card-body-spot {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        text-align: center;
        background-color: white;
        border: 1px solid black;
        border-top-left-radius: 5px;
        border-top-right-radius: 5px;
    }

    .chart {
        height: 200px;
    }

    @media (max-width: 767.98px) {
        .card-body {
            flex-direction: column;
            text-align: center;
        }

        .icon {
            margin-top: 10px;
        }
    }

    .greeting-message {
        color: #FFDC40;
        /* Text color */
        background-color: black;
        /* Background color */
        padding: 8px;
        /* Add some padding */
        border-radius: 5px;
        /* Rounded corners */
        display: flex;
        /* Aligns icon and text */
        align-items: center;
        /* Vertically center icon with text */
    }

    .greeting-message i {
        margin-left: 5px;
        /* Space between text and icon */
    }



    /* hp */
    .date-display {
        margin-left: auto;
        /* Pushes the date to the far right */
        text-align: right;
        /* Ensures the text is aligned to the right */
    }
</style>

<!-- Overlay Loading -->
<div id="loading-overlay">
    <img id="loading-logo" src="{{ asset('images/spot-logo.png') }}" alt="SPOT Logo">
</div>
<div class="container">
    <div class="row">
        <div class="col-12 d-flex justify-content-between align-items-center">
            <h3>Dashboard</h3>
            <p class="date-display mb-0">{{ $date }}</p>
        </div>
        <div class="col-12">
            <p class="greeting-message">
                Hallo, {{ Auth::user()->nama }} <i class="fas fa-smile"></i>
            </p>
        </div>
    </div>

    <div class="row">
        <div class="col-md-4 col-sm-6 mb-3">
            <div class="card-body-spot">
                <div class="icon-spot"><i class="fas fa-users"></i></div>
                <h6 class="card-title-spot">{{ $jumlahPengguna }}</h6>
                <p class="card-text-spot">Jumlah Pengguna</p>
            </div>
            <div class="highlight"></div>
        </div>
        <div class="col-md-4 col-sm-6 mb-3">
            <div class="card-body-spot">
                <div class="icon-spot"><i class="fas fa-car"></i></div>
                <h6 class="card-title-spot">{{ $jumlahParkirMasuk }}</h6>
                <p class="card-text-spot">Jumlah Parkir Masuk</p>
            </div>
            <div class="highlight"></div>
        </div>
        <div class="col-md-4 col-sm-6 mb-3">
            <div class="card-body-spot">
                <div class="icon-spot"><i class="fas fa-car-side"></i></div>
                <h6 class="card-title-spot">{{ $jumlahParkirKeluar }}</h6>
                <p class="card-text-spot">Jumlah Parkir Keluar</p>
            </div>
            <div class="highlight"></div>
        </div>
    </div>

    <!-- <div class="card shadow-sm mb-4 border-0">
        <div class="card-body p-3">
            <h6 class="font-weight-bold mb-3 text-black">
                {{ $jumlahPenggunaAktif }} Pengguna Aktif Hari Ini
            </h6>

            @if($penggunaAktif->count() > 0)
            <ul class="list-group list-group-flush">
                @foreach($penggunaAktif as $pengguna)
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center">
                        <div class="rounded-circle d-flex align-items-center justify-content-center mr-2 text-black"
                            style="background-color: #FFDC40; width: 35px; height: 35px;">
                            {{ strtoupper(substr($pengguna->nama, 0, 1)) }}
                        </div>
                        <div>
                            <div class="font-weight-bold">{{ $pengguna->nama }}</div>
                            <small class="text-muted">{{ $pengguna->id_pengguna }}</small>
                        </div>
                    </div>
                    <span class="badge badge-success">Online</span>
                </li>
                @endforeach
            </ul>
            @if($jumlahPenggunaAktifLainnya > 0)
            <div class="mt-2 text-center text-muted">
                Pengguna aktif lainnya: {{ $jumlahPenggunaAktifLainnya }}
            </div>
            @endif
            @else
            <p class="text-center text-muted">Tidak ada pengguna aktif saat ini.</p>
            @endif

        </div>
    </div> -->


    <div class="row">
        <div class="col-md-6">
            <div class="card-spot">
                <div class="card-header text-center" style="border-bottom: 1px solid black; background-color: white; font-weight: 500px; color: black;">
                    Pengguna Parkir Hari ini</div>
                <div class="card-body">
                    <canvas class="chart" id="barChart"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card-spot">
                <div class="card-header text-center" style="border-bottom: 1px solid black; background-color: white; font-weight: 500px; color: black;">
                    Waktu Puncak Penggunaan</div>
                <div class="card-body">
                    <canvas class="chart" id="lineChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="card-spot">
                <div class="card-header text-center" style="border-bottom: 1px solid black; background-color: white; font-weight: 500px; color: black;">

                    Presentase Jenis Kendaraan</div>
                <div class="card-body">
                    <canvas class="chart" id="pieChart"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card-spot">
                <div class="card-header text-center" style="border-bottom: 1px solid black; background-color: white; font-weight: 500px; color: black;">
                    Statistik Kendaraan Masuk
                </div>
                <div class="card-body">
                    <canvas class="chart" id="doughnutChart"></canvas>
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
    @endif
</div>
@endsection

@section('scripts')



<!-- jQuery and Bootstrap 4 JS -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
    function reloadPenggunaAktif() {
        $.get('{{ url("/pengguna-aktif/realtime") }}', function(data) {
            $('#jumlahPenggunaAktif').text(data.jumlah);
            $('#pengguna-aktif-wrapper').html(data.html);
        });
    }

    // Panggil tiap 5 detik
    setInterval(reloadPenggunaAktif, 2000);
    // Data kategori pengguna dari controller
    var kategoriData = @json($kategoriDataChart);
    var kategoriLabels = kategoriData.map(function(item) {
        return item.kategori;
    });
    var kategoriCounts = kategoriData.map(function(item) {
        return item.jumlah;
    });

    var warnaKategori = ['#66b3ff', '#ff9999', '#1cc88a'];

    // Pastikan jumlah warna cukup untuk semua kategori
    var backgroundColors = kategoriLabels.map((_, index) => warnaKategori[index % warnaKategori.length]);

    // Bar Chart - Jumlah Pengguna berdasarkan kategori
    var barChartCtx = document.getElementById('barChart').getContext('2d');
    var barChart = new Chart(barChartCtx, {
        type: 'bar',
        data: {
            labels: kategoriLabels,
            datasets: [{
                label: 'Jumlah Pengguna',
                data: kategoriCounts,
                backgroundColor: backgroundColors,
                borderColor: backgroundColors,
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true
                }
            },
            plugins: {
                legend: {
                    display: true
                }
            }
        }
    });

    // Data waktu puncak penggunaan dari controller
    var waktuPuncakData = @json($waktuPuncakChartData);
    var kategoriWaktuData = Object.keys(waktuPuncakData);
    var warnaKategoriLine = ['#FF5733', '#33FF57', '#3357FF'];

    // Membuat datasets untuk chart berdasarkan kategori dan waktu puncak
    var datasets = kategoriWaktuData.map(function(kategori, index) {
        var data = waktuPuncakData[kategori];
        return {
            label: kategori,
            data: data,
            borderColor: warnaKategoriLine[index % warnaKategoriLine.length],
            pointBackgroundColor: warnaKategoriLine[index % warnaKategoriLine.length],
            fill: false
        };
    });

    // Line Chart - Waktu Puncak Penggunaan
    var lineChartCtx = document.getElementById('lineChart').getContext('2d');
    var lineChart = new Chart(lineChartCtx, {
        type: 'line',
        data: {
            labels: Array.from({
                length: 24
            }, (_, i) => i),
            datasets: datasets
        },
        options: {
            scales: {
                x: {
                    title: {
                        display: true,
                        text: 'Jam'
                    }
                },
                y: {
                    title: {
                        display: true,
                        text: 'Jumlah'
                    }
                }
            }
        }
    });

    // Pie Chart - Jenis Kendaraan
    var jenisKendaraanData = @json($jenisKendaraanData);
    var jenisKendaraanLabels = jenisKendaraanData.map(function(item) {
        return item.jenis;
    });
    var jenisKendaraanCounts = jenisKendaraanData.map(function(item) {
        return item.jumlah;
    });

    var pieChartCtx = document.getElementById('pieChart').getContext('2d');
    var pieChart = new Chart(pieChartCtx, {
        type: 'pie',
        data: {
            labels: jenisKendaraanLabels,
            datasets: [{
                label: 'Jenis Kendaraan',
                data: jenisKendaraanCounts,
                backgroundColor: ['#ff9999', '#66b3ff'],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true
        }
    });

    // Doughnut Chart - Kendaraan Masuk berdasarkan Waktu
    var kendaraanMasukWaktuData = @json($kendaraanMasukWaktuData);
    var waktuLabels = kendaraanMasukWaktuData.map(function(item) {
        return item.waktu;
    });
    var kendaraanMasukCounts = kendaraanMasukWaktuData.map(function(item) {
        return item.jumlah;
    });

    var doughnutChartCtx = document.getElementById('doughnutChart').getContext('2d');
    var doughnutChart = new Chart(doughnutChartCtx, {
        type: 'doughnut',
        data: {
            labels: waktuLabels,
            datasets: [{
                label: 'Kendaraan Masuk Berdasarkan Waktu',
                data: kendaraanMasukCounts,
                backgroundColor: ['#ffb3b3', '#66b3ff', '#ffff99', '#99ff99'],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true
        }
    });

    // Modal sukses masuk
    $(document).ready(function() {
        console.log('Page loaded');
        $('#successModal').modal('show'); // Temporarily show the modal to see if it triggers without conditions
    });

    // Fungsi dashboard realtime
    $(document).ready(function() {
        // Fungsi untuk memuat data secara otomatis setiap beberapa detik
        function fetchDashboardData() {
            $.ajax({
                url: '{{ route("pengelola.dashboard") }}', // Pastikan route benar, tanpa spasi ekstra
                method: 'GET',
                dataType: 'json',
                cache: false, // Pastikan selalu mengambil data terbaru
                success: function(data) {
                    // Pastikan data tidak null/undefined
                    if (!data) return;

                    // Update data dashboard
                    $('#jumlahPengguna').text(data.jumlahPengguna || 0);
                    $('#jumlahParkirMasuk').text(data.jumlahParkirMasuk || 0);
                    $('#jumlahParkirKeluar').text(data.jumlahParkirKeluar || 0);

                    // Update chart atau data lainnya
                    updateChartData(data);
                },
                error: function(xhr, status, error) {
                    console.error("Gagal mengambil data dashboard:", error);
                }
            });
        }

        // Fungsi untuk memperbarui chart atau data lain
        function updateChartData(data) {
            if (typeof barChart !== "undefined") {
                barChart.data.labels = (data.kategoriDataChart || []).map(item => item.kategori);
                barChart.data.datasets[0].data = (data.kategoriDataChart || []).map(item => item.jumlah);
                barChart.update();
            }

            if (typeof lineChart !== "undefined") {
                let kategoriWaktuData = Object.keys(data.waktuPuncakChartData || {});
                let datasets = kategoriWaktuData.map((kategori, index) => ({
                    label: kategori,
                    data: data.waktuPuncakChartData[kategori] || [],
                    borderColor: warnaKategoriLine[index % warnaKategoriLine.length],
                    pointBackgroundColor: warnaKategoriLine[index % warnaKategoriLine.length],
                    fill: false
                }));
                lineChart.data.datasets = datasets;
                lineChart.update();
            }

            if (typeof pieChart !== "undefined") {
                pieChart.data.labels = (data.jenisKendaraanData || []).map(item => item.jenis);
                pieChart.data.datasets[0].data = (data.jenisKendaraanData || []).map(item => item.jumlah);
                pieChart.update();
            }

            if (typeof doughnutChart !== "undefined") {
                doughnutChart.data.labels = (data.kendaraanMasukWaktuData || []).map(item => item.waktu);
                doughnutChart.data.datasets[0].data = (data.kendaraanMasukWaktuData || []).map(item => item.jumlah);
                doughnutChart.update();
            }
        }

        // Ambil data pertama kali saat halaman dimuat
        fetchDashboardData();

        // Ambil data setiap 2 detik
        setInterval(fetchDashboardData, 2000); // 2000 ms = 2 detik
    });
</script>




@endsection