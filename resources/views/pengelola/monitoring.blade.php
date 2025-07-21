@extends('layouts.pengelola')
@section('title', 'Monitoring Parkir')

@section('content')
<style>
    .webcam-container {
        display: flex;
        gap: 30px;
        align-items: flex-start;
        margin-bottom: 15px;
    }

    .column {
        flex: 1;
        padding: 10px;
        border-radius: 10px;
        display: flex;
        flex-direction: column;
        justify-content: flex-start;
        height: 320px;
        /* Total tinggi kolom */
    }

    .column-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 5px;
        font-size: 12px;
    }

    .column-header select {
        padding: 4px 8px;
        border-radius: 5px;
        border: 1px solid #aaa;
    }

    .section-title {
        margin-bottom: 10px;
        font-size: 18px;

    }

    .video-wrapper {
        flex: none;
        height: 250px;
        width: 100%;
        border: 1px solid #000;
        border-radius: 5px;
        display: block;
        /* Ganti dari flex */
        overflow: hidden;
        /* Crop bagian luar jika proporsi video beda */
    }


    .canvas-wrapper {
        flex: none;
        height: 200px;
        /* Lebih pendek karena ada teks di bawah */
        width: 100%;
        border: 1px solid #000;
        border-radius: 5px;
        background-color: #fff;
        display: flex;
        justify-content: center;
        align-items: center;
    }

    .detected-plate {
        font-size: 24px;
        font-weight: bold;
    }

    .info-text {
        /* margin-top: 10px; */
        font-size: 14px;
        text-align: left;
        /* min-height: 60px; */

    }

    .info-scan {
        margin-top: 10px;
        font-size: 14px;
        text-align: left;
        /* min-height: 60px; */

    }


    #webcam,
    #detection-result {
        width: 100%;
        height: 100%;
        object-fit: cover;
        /* ini yang bikin full */
        display: block;
    }

    .border-black {
        border-color: black;
    }

    .border-black {
        border-color: black;
    }

    .btn-info,
    .btn-danger {
        font-weight: bold;
    }


    /* Styling untuk div utama */
    .d-flex {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 2rem;
    }


    /* Styling untuk jam */
    .date-display {
        font-size: 2rem;
        /* Mengatur ukuran font lebih kecil */
        font-family: 'Courier New', Courier, monospace;
        font-weight: bold;
        color: white;
        /* Mengubah warna teks menjadi hitam */
        padding: 8px 18px;
        background-color: #FFDC40;
        /* Warna latar belakang kuning */
        border-radius: 10px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        transition: transform 0.3s ease-in-out;
    }

    .date-display:hover {
        transform: scale(1.05);
        /* Efek sedikit pembesaran saat hover */
        color: white;
        /* Warna teks saat hover */
        background-color: #FFDC40;
        /* Latar belakang tetap kuning saat hover */
        box-shadow: 0 6px 8px rgba(0, 0, 0, 0.2);
    }

    /* Styling border dan padding */
    .date-display {
        display: inline-block;
        padding: 8px 16px;
        background: black;
        /* Warna latar belakang kuning */
        color: #FFDC40;
        /* Teks hitam */
        font-size: 1rem;
        /* Ukuran font lebih kecil */
        border-radius: 5px;
        text-align: center;
        letter-spacing: 1px;
        transition: transform 0.3s ease;
    }

    .date-display:after {
        content: '';
        display: block;
        width: 100%;
        height: 3px;
        background-color: #FFDC40;
        /* Warna garis bawah tetap kuning */
        margin-top: 5px;
        border-radius: 50%;
    }
</style>
<!-- Overlay Loading -->
<div id="loading-overlay">
    <img id="loading-logo" src="{{ asset('images/spot-logo.png') }}" alt="SPOT Logo">
</div>
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3>Monitoring</h3>
        <p class="date-display mb-0" id="current-time"></p>
    </div>
    <div class="row">
        <div class="col-12">
            <!-- Webcam Real-Time -->
            <div class="webcam-container">
                <!-- Kolom Webcam -->
                <div class="column">
                    <div class="column-header">
                        <h4 class="section-title">Tampilkan</h4>
                        <select>
                            <option value="masuk">Monitoring Masuk</option>
                            <option value="keluar">Monitoring Keluar</option>
                        </select>
                    </div>
                    <div class="video-wrapper" style="position: relative;">
                        <video id="webcam" autoplay playsinline muted style="width:100%; border-radius:8px;"></video>
                        <!-- Canvas untuk bounding box -->
                        <canvas id="canvas" style="position: absolute; top: 0; left: 0; width: 100%; border-radius:8px;"></canvas>
                    </div>
                </div>

                <!-- Kolom Hasil Deteksi -->
                <div class="column">
                    <div class="column-header">
                        <h4 class="section-title">Hasil Deteksi</h4>
                    </div>

                    <!-- Audio untuk deteksi sukses -->
                    <audio id="suksesMasukAudio" src="{{ asset('sukses-masuk.mp3') }}" preload="auto"></audio>
                    <audio id="suksesKeluarAudio" src="{{ asset('sukses-keluar.mp3') }}" preload="auto"></audio>

                    <!-- Info Teks -->
                    <p id="infoText" class="info-text" style="display: none;"></p>

                    <!-- Hasil Plat Nomor -->
                    <div class="canvas-wrapper fake-canvas">
                        <h5 id="result"><strong id="platNomor">-</strong></h5>
                    </div>
                </div>
            </div>


            <div class="card border-black">
                <div class="card-body">
                    <div class="header-container d-flex justify-content-between mb-1">
                        <div class="d-flex align-items-center">
                            <span class="ml-2">Tampilkan</span>
                            <form method="GET" action="{{ route('pengelola.monitoring.index') }}" style="display: inline;">
                                <select id="rows" name="rows" class="custom-select d-inline border-black ml-2" style="width: auto;" onchange="this.form.submit()">
                                    <option value="10" {{ $perPage == 10 ? 'selected' : '' }}>10</option>
                                    <option value="20" {{ $perPage == 20 ? 'selected' : '' }}>20</option>
                                    <option value="30" {{ $perPage == 30 ? 'selected' : '' }}>30</option>
                                    <option value="50" {{ $perPage == 50 ? 'selected' : '' }}>50</option>
                                </select>
                            </form>
                            <span class="ml-2">Baris</span>
                        </div>
                        <div class="search-container">
                            <form method="GET" action="{{ route('pengelola.monitoring.search') }}" class="d-flex align-items-center">
                                <input type="text" name="query" class="form-control border-black" placeholder="Pencarian" style="width: auto;" value="{{ request()->get('query') }}">
                                <button class="btn search-btn ml-2" style="background-color: #ffdc40;" type="submit">
                                    <i class="fas fa-search"></i>
                                </button>
                            </form>
                        </div>
                    </div>

                    @if($riwayatParkir->isEmpty())
                    <p class="mt-3">Tidak ada Monitoring Parkir yang ditemukan untuk "{{ request()->get('query') }}"</p>
                    @endif
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead style="background-color: #ffdc40;">
                                <tr>
                                    <th>ID Parkir</th>
                                    <th>ID Pengguna</th>
                                    <th>Plat Nomor</th>
                                    <th>Waktu Masuk</th>
                                    <th>Status</th>

                                </tr>
                            </thead>
                            <tbody class="bg-putih">
                                @foreach($riwayatParkir as $riwayat)
                                <tr>
                                    <td>{{ $riwayat->id_riwayat_parkir }}</td>
                                    <td>{{ $riwayat->pengguna->id_pengguna }}</td>
                                    <td>{{ $riwayat->kendaraan->plat_nomor }}</td>
                                    <td>{{ $riwayat->waktu_masuk }}</td>
                                    <td>
                                        @if($riwayat->status_parkir == 'masuk')
                                        <span style="color: green;">{{ $riwayat->status_parkir }}</span>
                                        @elseif($riwayat->status_parkir == 'keluar')
                                        <span style="color: red;">{{ $riwayat->status_parkir }}</span>
                                        @else
                                        {{ $riwayat->status_parkir }}
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>

                        </table>
                    </div>

                    <!-- Pagination -->
                    <div>
                        <ul class="pagination d-flex justify-content-end">
                            <li class="page-item {{ $riwayatParkir->onFirstPage() ? 'disabled' : '' }}">
                                <a class="page-link" href="{{ $riwayatParkir->previousPageUrl() }}" aria-label="Previous">
                                    <span aria-hidden="true">&laquo;</span>
                                </a>
                            </li>
                            @foreach ($riwayatParkir->getUrlRange(1,$riwayatParkir->lastPage()) as $page => $url)
                            <li class="page-item {{ $riwayatParkir->currentPage() == $page ? 'active' : '' }}">
                                <a class="page-link" href="{{ $url }}">{{ $page }}</a>
                            </li>
                            @endforeach
                            <li class="page-item {{$riwayatParkir->hasMorePages() ? '' : 'disabled' }}">
                                <a class="page-link" href="{{ $riwayatParkir->nextPageUrl() }}" aria-label="Next">
                                    <span aria-hidden="true">&raquo;</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</div>
@endsection

@section('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>

<script>
    function updateTime() {
        const now = new Date();
        const hours = now.getHours().toString().padStart(2, '0');
        const minutes = now.getMinutes().toString().padStart(2, '0');
        const seconds = now.getSeconds().toString().padStart(2, '0');
        const formattedTime = `${hours}:${minutes}:${seconds}`;
        document.getElementById("current-time").textContent = formattedTime;
    }

    setInterval(updateTime, 1000);
    updateTime();

    function fetchMonitoringData() {
        $.get('/monitoring', function(data) {
            let tbody = '';
            data.forEach(function(item) {
                tbody += `
                <tr>
                    <td>${item.id_riwayat_parkir}</td>
                    <td>${item.pengguna.id_pengguna}</td>
                    <td>${item.kendaraan.plat_nomor}</td>
                    <td>${item.waktu_masuk}</td>
                    <td style="color:${item.status_parkir === 'masuk' ? 'green' : 'red'}">${item.status_parkir}</td>
                </tr>`;
            });
            $('tbody.bg-putih').html(tbody);
        });
    }

    setInterval(fetchMonitoringData, 1000);
    fetchMonitoringData();

    let lastPlatNomor = "";
    let displayedPlates = new Set();
    const audio = document.getElementById("deteksiAudio");
    let pendingPlates = [];
    let isChecking = false;

    async function fetchPlatNomor() {
        if (isChecking) return;

        try {
            const response = await fetch("https://alpu.web.id/server/result");
            if (!response.ok) throw new Error("Gagal fetch plat nomor");

            const data = await response.json();
            const platNomor = data.plat_nomor?.trim() || '-';

            if (platNomor === '-' || platNomor === '') {
                showDefaultMessage();
                return;
            }

            if (!displayedPlates.has(platNomor) && !pendingPlates.includes(platNomor)) {
                pendingPlates.push(platNomor);
            }

            if (pendingPlates.length > 0) {
                isChecking = true;

                const currentPlat = pendingPlates.shift();
                lastPlatNomor = currentPlat;
                displayedPlates.add(currentPlat);

                document.getElementById("platNomor").innerText = currentPlat;
                document.getElementById("infoText").innerText = "Memeriksa data kendaraan...";
                document.getElementById("infoText").style.display = "block";

                const isRegistered = await checkPlatNomor(currentPlat);

                if (isRegistered) {
                    audio.currentTime = 0;
                    audio.play();
                    document.getElementById("infoText").innerText = `✅ Plat nomor ${currentPlat} terdaftar. Silakan scan QR Anda di alat!`;
                } else {
                    document.getElementById("infoText").innerText = `❌ Plat nomor ${currentPlat} kendaraan Anda tidak terdaftar di sistem!`;
                }

                isChecking = false;
            }

        } catch (err) {
            console.error("Gagal ambil atau cek plat nomor:", err);
            showConnectionError();
            isChecking = false;
        }
    }

    function showDefaultMessage() {
        document.getElementById("platNomor").innerText = '-';
        document.getElementById("infoText").innerText = "Posisikan kendaraan menghadap kamera dengan benar.";
        document.getElementById("infoText").style.display = "block";
    }

    function showConnectionError() {
        document.getElementById("platNomor").innerText = '-';
        document.getElementById("infoText").innerText = "Gagal koneksi ke server deteksi.";
        document.getElementById("infoText").style.display = "block";
    }

    async function checkPlatNomor(platNomor) {
        try {
            const response = await fetch(`http://127.0.0.1:8000/api/check_plate/${platNomor}`);
            if (!response.ok) throw new Error("Gagal fetch data validasi plat");
            const data = await response.json();
            return data.exists;
        } catch (error) {
            console.error("Error checking plate number:", error);
            showDefaultMessage();
            return false;
        }
    }

    setInterval(fetchPlatNomor, 1000);

    let waktuScanTerakhir = null;
    let timeoutResetInfo = null;

    function tampilkanInfoScan() {
        fetch('http://127.0.0.1:8000/api/scan-latest')
            .then(response => response.json())
            .then(data => {
                const info = document.getElementById('info-scan');
                const scanMasukAudio = document.getElementById('scanMasukAudio');
                const scanKeluarAudio = document.getElementById('scanKeluarAudio');

                if (!data || !data.timestamp || data.timestamp === waktuScanTerakhir) return;

                waktuScanTerakhir = data.timestamp;
                if (timeoutResetInfo) clearTimeout(timeoutResetInfo);
                info.style.display = 'block';

                switch (data.status) {
                    case 'masuk':
                        info.innerText = `📱 ${data.message}`;
                        info.style.color = 'green';
                        if (scanMasukAudio) {
                            scanMasukAudio.currentTime = 0;
                            scanMasukAudio.play();
                        }
                        break;
                    case 'keluar':
                        info.innerText = `📱 ${data.message}`;
                        info.style.color = 'green';
                        if (scanKeluarAudio) {
                            scanKeluarAudio.currentTime = 0;
                            scanKeluarAudio.play();
                        }
                        break;
                    case 'error':
                        info.innerText = `❌ ${data.message}`;
                        info.style.color = 'red';
                        break;
                    case 'kosong':
                        info.innerText = `ℹ️ ${data.message}`;
                        info.style.color = 'black';
                        break;
                    case 'not_found':
                        info.innerText = `⚠️ ${data.message}`;
                        info.style.color = 'orange';
                        break;
                    default:
                        info.innerText = `🔍 ${data.message}`;
                        info.style.color = 'black';
                }

                timeoutResetInfo = setTimeout(() => {
                    info.innerText = 'ℹ️ Silakan scan QR Code Anda pada alat pemindai!';
                    info.style.color = 'black';
                }, 10000);
            })
            .catch(error => {
                const info = document.getElementById('info-scan');
                info.style.display = 'block';
                info.innerText = '❌ Terjadi kesalahan dalam mengambil data scan!';
                info.style.color = 'red';
            });
    }

    document.addEventListener('DOMContentLoaded', () => {
        setInterval(tampilkanInfoScan, 1000);
    });

    const webcamElement = document.getElementById("webcam");
    const canvasElement = document.getElementById("canvas");
    const ctx = canvasElement.getContext("2d");

    async function startCamera() {
        const stream = await navigator.mediaDevices.getUserMedia({
            video: true
        });
        webcamElement.srcObject = stream;

        webcamElement.onloadedmetadata = () => {
            canvasElement.width = webcamElement.videoWidth;
            canvasElement.height = webcamElement.videoHeight;
        };
    }

    async function sendFrameToServer() {
        if (!webcamElement.videoWidth || !webcamElement.videoHeight) return;

        const tempCanvas = document.createElement("canvas");
        tempCanvas.width = webcamElement.videoWidth;
        tempCanvas.height = webcamElement.videoHeight;
        const tempCtx = tempCanvas.getContext("2d");
        tempCtx.drawImage(webcamElement, 0, 0, tempCanvas.width, tempCanvas.height);

        const base64Image = tempCanvas.toDataURL("image/jpeg");

        try {
            await fetch("https://alpu.web.id/server/upload_frame", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json"
                },
                body: JSON.stringify({
                    image: base64Image
                })
            });

            const frameRes = await fetch("https://alpu.web.id/server/get_processed_frame");
            const frameData = await frameRes.json();

            if (frameData.frame) {
                const img = new Image();
                img.onload = () => {
                    ctx.clearRect(0, 0, canvasElement.width, canvasElement.height);
                    ctx.drawImage(img, 0, 0, canvasElement.width, canvasElement.height);
                };
                img.src = frameData.frame;
            }

            const resultRes = await fetch("https://alpu.web.id/server/result");
            const resultData = await resultRes.json();

            if (resultData.plat_nomor && resultData.plat_nomor !== "-") {
                console.log("🚘 Plat Nomor Terdeteksi:", resultData.plat_nomor);
            }

        } catch (err) {
            console.error("❌ Gagal kirim atau ambil frame:", err);
        }
    }

    async function mainLoop() {
        await startCamera();
        setInterval(sendFrameToServer, 1000);
    }

    mainLoop();
</script>




@endsection