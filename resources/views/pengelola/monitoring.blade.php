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
    // ======================= JAM REALTIME =======================
    function updateTime() {
        const now = new Date();
        const formattedTime = now.toLocaleTimeString('id-ID', {
            hour12: false
        });
        document.getElementById("current-time").textContent = formattedTime;
    }
    setInterval(updateTime, 1000);
    updateTime();

    // ================= MONITORING PARKIR REALTIME ================
    function fetchMonitoringData() {
        $.get('/monitoring', function(data) {
            let tbody = '';
            data.forEach(item => {
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

    // =============== AUDIO & STATE PLAT ===============
    const audio = document.getElementById("suksesMasukAudio");
    let displayedPlates = new Set();
    let pendingPlates = [];
    let isChecking = false;

    // =============== CEK STATUS PLAT DB ===============
    async function checkPlatNomor(platNomor) {
        try {
            const response = await fetch(`https://alpu.web.id/api/check_plate/${platNomor}`);
            if (!response.ok) throw new Error("Gagal fetch validasi plat");
            const data = await response.json();
            return data.exists;
        } catch (error) {
            console.error("Error checking plate:", error);
            showConnectionError();
            return false;
        }
    }

    // =============== FETCH PLAT YOLO DAN CEK ===============
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
                displayedPlates.add(currentPlat);

                document.getElementById("platNomor").innerText = currentPlat;
                document.getElementById("infoText").innerText = "Memeriksa data kendaraan...";
                document.getElementById("infoText").style.display = "block";

                const isRegistered = await checkPlatNomor(currentPlat);

                if (isRegistered) {
                    audio?.play();
                    document.getElementById("infoText").innerText = `✅ Plat ${currentPlat} terdaftar. Silakan masuk/keluar.`;
                } else {
                    document.getElementById("infoText").innerText = `❌ Plat ${currentPlat} tidak terdaftar di sistem.`;
                }

                isChecking = false;
            }
        } catch (error) {
            console.error("Gagal fetch/cek plat:", error);
            showConnectionError();
            isChecking = false;
        }
    }
    setInterval(fetchPlatNomor, 1000);

    // ============== PESAN DEFAULT & ERROR ==============
    function showDefaultMessage() {
        document.getElementById("platNomor").innerText = '-';
        document.getElementById("infoText").innerText = "Posisikan kendaraan menghadap kamera dengan jelas.";
        document.getElementById("infoText").style.display = "block";
    }

    function showConnectionError() {
        document.getElementById("platNomor").innerText = '-';
        document.getElementById("infoText").innerText = "Gagal koneksi ke server deteksi.";
        document.getElementById("infoText").style.display = "block";
    }

    // ================= WEBCAM & FRAME YOLO =================
    const webcam = document.getElementById("webcam");
    const canvas = document.getElementById("canvas");
    const ctx = canvas.getContext("2d");

    async function startCamera() {
        try {
            const stream = await navigator.mediaDevices.getUserMedia({
                video: true
            });
            webcam.srcObject = stream;
            webcam.onloadedmetadata = () => {
                canvas.width = webcam.videoWidth;
                canvas.height = webcam.videoHeight;
            };
        } catch (error) {
            console.error("Gagal akses kamera:", error);
        }
    }

    // =========== KIRIM FRAME WEBCAM KE SERVER TANPA BLOCKING ===========
    let lastSent = 0;
    const SEND_INTERVAL = 700; // kirim setiap 700ms

    async function sendFrameToServer() {
        const now = Date.now();
        if (!webcam.videoWidth || !webcam.videoHeight || now - lastSent < SEND_INTERVAL) {
            requestAnimationFrame(sendFrameToServer);
            return;
        }
        lastSent = now;

        try {
            const tempCanvas = document.createElement("canvas");
            tempCanvas.width = webcam.videoWidth;
            tempCanvas.height = webcam.videoHeight;
            const tempCtx = tempCanvas.getContext("2d");
            tempCtx.drawImage(webcam, 0, 0, tempCanvas.width, tempCanvas.height);

            const base64Image = tempCanvas.toDataURL("image/jpeg").split(',')[1]; // hanya data base64 tanpa prefix

            await fetch("https://alpu.web.id/server/upload_frame", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json"
                },
                body: JSON.stringify({
                    image: base64Image
                })
            });
        } catch (err) {
            console.error("❌ Gagal upload frame:", err);
        }
        requestAnimationFrame(sendFrameToServer);
    }

    // =========== AMBIL FRAME YOLO UNTUK DITAMPILKAN ==============
    async function fetchProcessedFrame() {
        try {
            const response = await fetch("https://alpu.web.id/server/get_processed_frame");
            if (!response.ok) {
                console.error("Gagal fetch processed frame:", await response.text());
                return;
            }
            const frameData = await response.json();

            if (frameData.frame) {
                const img = new Image();
                img.onload = () => {
                    ctx.clearRect(0, 0, canvas.width, canvas.height);
                    ctx.drawImage(img, 0, 0, canvas.width, canvas.height);
                };
                img.src = frameData.frame;
            }
        } catch (error) {
            console.error("❌ Gagal ambil frame YOLO:", error);
        }
    }
    setInterval(fetchProcessedFrame, 700); // ambil hasil setiap 700ms

    // =========== INISIALISASI APLIKASI ============
    async function initMonitoringParkir() {
        await startCamera();
        requestAnimationFrame(sendFrameToServer);
    }
    initMonitoringParkir();
</script>





@endsection