<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.8.1/font/bootstrap-icons.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/pageall.css') }}">

    <!--  AOS JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.js"></script>
    <title>Pendaftaran Pengguna Parkir</title>
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            font-family: "Roboto", sans-serif;
            background-image: url('images/daftar.png'), url('images/mobil.png');
            background-size: 20%, 20%;
            /* Ukuran gambar daftar dan mobil */
            background-repeat: no-repeat, no-repeat;
            /* Mencegah pengulangan */
            background-position: right 20px bottom 20px, left 20px bottom 20px;
            /* Jarak dari tepi kanan/kiri dan bawah */
            background-attachment: fixed, fixed;
            /* Gambar tetap pada posisi saat di-scroll */
        }

        .container {
            display: flex;
            justify-content: center;
            align-items: center;
            margin-top: 50px;
            max-width: 600px;
            flex-direction: column;
            padding: 20px;
        }

        @media (max-width: 768px) {
            body {
                background-size: 30%, 30%;
                /* Ukuran gambar lebih besar untuk layar kecil */
                background-position: right 10px bottom 10px, left 10px bottom 10px;
                /* Jarak lebih kecil untuk layar sempit */
            }
        }

        @media (max-width: 480px) {
            body {
                background-size: 40%, 40%;
                /* Ukuran gambar lebih besar untuk layar sangat kecil */
                background-position: right 5px bottom 5px, left 5px bottom 5px;
                /* Jarak lebih kecil untuk layar sempit */
            }
        }

        h2 {
            padding-bottom: 1px;
            font-weight: bold;
        }

        .card-spot {
            width: 100%;
            max-width: 600px;
            border-radius: 10px;
            box-shadow: 0px 10px 10px rgba(0, 0, 0, 0.3), -0px -10px 10px rgba(0, 0, 0, 0.3);
            background: white;
            padding: 50px;
            border: none;
            transition: all 0.3s ease;
        }

        .form-control::placeholder {
            font-size: 14px;
        }

        .btn-block:hover {
            background-color: #e0a800;
            color: white;
            transform: scale(1.05);
        }

        .button-palang-spot {
            background-color: #ffdb4d;
            border: none;
            color: black;
            font-weight: bold;
            padding: 10px;
            border-radius: 5px;
            transition: all 0.3s ease;
        }

        .button-white-spot {
            background-color: white;
            border: 1px solid black;
            font-weight: bold;
            padding: 10px;
            border-radius: 5px;
            transition: all 0.3s ease;
        }

        .step {
            display: none;
        }

        .step.active {
            display: block;
        }


        .form-control {
            border: 1px solid black;
            color: black;
        }

        .input-group-text {
            border: 1px solid black;
            border-radius: 5px 0 0 5px;
        }

        .input-group-text i {
            border-radius: 0px 5px 5px 0;
        }

        .input-group .input-group-text i {
            color: black;
            padding: 0 5px;
        }


        .subtitle {
            text-align: center;
            margin-bottom: 15px;
        }

        .register-link {
            text-align: center;
            margin-top: 15px;
        }

        .register-link a {
            text-decoration: none;
            color: #ffc107;
            font-weight: bold;
        }

        .register-link a:hover {
            text-decoration: underline;
        }

        .upload-area {
            border: 2px dashed grey;
            border-radius: 5px;
            padding: 20px;
            margin-bottom: 20px;
            text-align: center;
            cursor: pointer;
            position: relative;
            height: 150px;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: #f8f9fa;
        }

        .upload-area img {
            max-width: 100%;
            max-height: 100%;
            object-fit: cover;
            border-radius: 5px;
            display: none;
        }

        .upload-label {
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .upload-label i {
            font-size: 30px;
            margin-top: 20px;
            margin-bottom: 10px;
            color: #000000;
        }

        .upload-area p {
            font-size: 14px;
            color: #000000;
        }

        .input-group .input-group-text {
            background-color: #f8f9fa;
            border: 1px solid black;
        }

        .input-group .input-group-text i {
            color: black;
            padding: 0 5px;
        }

        .form-control:focus {
            box-shadow: none;
            border-color: black;
        }
    </style>
</head>

<body>
    <!-- Overlay Loading -->
    <div id="loading-overlay">
        <img id="loading-logo" src="{{ asset('images/spot-logo.png') }}" alt="SPOT Logo">
    </div>
    <div class="container">
        <div class="card-spot">
            <h2 class="text-center font-weight-bold  padding-bottom: 2px" id="formHeader">Daftar Akun</h2>
            <div class="subtitle">
                <p>Mari persiapkan agar dapat mengakses akun pribadi anda</p>
            </div>

            <!-- Notifikasi sukses atau error -->
            @if(session('success'))
            <div class="alert alert-success" role="alert">
                {{ session('success') }}
            </div>
            @elseif(session('error'))
            <div class="alert alert-danger" role="alert">
                {{ session('error') }}
            </div>
            @endif


            <!-- Pesan Error Validasi -->
            @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif


            <!-- Formulir Pendaftaran -->
            <form action="{{ route('register') }}" method="POST" enctype="multipart/form-data" id="formPendaftaran">

                @csrf

                <!-- Step 1: Pilih Kategori Akun -->
                <div class="step active" id="step-1">
                    <div class="form-group">
                        <div class="input-group">
                            <span class="input-group-text border border-black text-black"><i class="fas fa-user-alt"></i></span>
                            <select id="kategori" name="kategori" class="form-select border border-black text-black @error('kategori') is-invalid @enderror" required>

                                @foreach($kategoriArray as $value)
                                <option value="{{ $value }}" {{ old('kategori') == $value ? 'selected' : '' }}>{{ $value }}</option>
                                @endforeach
                            </select>
                        </div>
                        @error('kategori')
                        <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <button type="button" class="button-palang-spot btn-block" id="nextButtonStep1">Selanjutnya</button>
                    <div class="register-link">
                        <p>Sudah punya akun? <a href="{{ route('login') }}">Masuk!</a></p>
                    </div>
                </div>

                <!-- Step 2: Data Pengguna -->
                <div class="step" id="step-2">
                    <div class="upload-area" onclick="document.getElementById('uploadPhotoUser').click()">
                        <img id="previewUser" src="" alt="Preview Foto Pengguna" style="display:none;">
                        <p id="labelPhotoUser" class="upload-label">
                            <i class="fas fa-camera"></i> Unggah Foto Pengguna
                        </p>
                        <input type="file" id="uploadPhotoUser" name="foto" style="display: none;" accept="image/*" onchange="previewImage(event, 'previewUser', 'labelPhotoUser')">
                    </div>
                    <div>
                        @error('foto')
                        <!-- <span class="text-danger">{{ $message }}</span> -->
                        @enderror
                    </div>




                    <!-- Form input ID Pengguna -->
                    <div class="form-group" id="idPenggunaField">
                        <div class="input-group">
                            <span class="input-group-text border border-black text-black"><i class="fas fa-id-card"></i></span>
                            <input type="text" id="id_pengguna" name="id_pengguna" autocomplete="off"
                                class="form-control border border-black text-black @error('id_pengguna') is-invalid @enderror"
                                placeholder="Masukkan ID Pengguna" required
                                value="{{ old('id_pengguna') }}">
                        </div>
                        @error('id_pengguna')
                        @enderror
                    </div>

                    <div class="form-group mb-3">
                        <div class="input-group">
                            <span class="input-group-text border border-black text-black"><i class="bi bi-person-fill"></i></span>
                            <input type="text" name="nama" class="form-control border border-black @error('nama') is-invalid @enderror" required placeholder="Masukkan Nama" value="{{ old('nama') }}">
                        </div>
                        @error('nama')
                        <div class="invalid-feedback">{{ $message }}</div> <!-- Menampilkan pesan error jika ada -->
                        @enderror
                    </div>


                    <div class="form-group">
                        <div class="input-group">
                            <span class="input-group-text border border-black text-black"><i class="bi bi-envelope-fill"></i></span>
                            <input type="email" name="email" class="form-control border border-black @error('email') is-invalid @enderror" required placeholder="Masukkan Email" value="{{ old('email') }}">
                        </div>
                        @error('email')
                        @enderror
                    </div>
                    <div class="form-group">
                        <div class="input-group">
                            <span class="input-group-text border border-black text-black"><i class="bi bi-lock-fill"></i></span>
                            <input type="password" name="password"
                                class="form-control border border-black text-black @error('password') is-invalid @enderror"
                                required placeholder="Masukkan Kata Sandi"
                                value="{{ old('password') }}" id="passwordInput">
                            <span class="input-group-text" onclick="togglePasswordVisibility()">
                                <i class="bi bi-eye-fill" id="toggleIcon"></i>
                            </span>
                        </div>
                        @error('password')
                        <!-- <span class="text-danger">{{ $message }}</span> -->
                        @enderror
                    </div>
                    <button type="button" class="button-white-spot  btn-block" id="prevButtonStep2">Sebelumnya</button>
                    <button type="button" class="button-palang-spot btn-block" id="nextButtonStep2">Selanjutnya</button>
                    <div class="register-link">
                        <p>Sudah punya akun? <a href="{{ route('login') }}">Masuk!</a></p>
                    </div>
                </div>

                <!-- Step 3: Data Kendaraan -->
                <div class="step" id="step-3">
                    <div class="upload-area" onclick="document.getElementById('uploadPhotoKendaraan').click()">
                        <img id="previewKendaraan" src="" alt="Preview Foto Kendaraan" style="display:none;">
                        <p id="labelPhotoKendaraan" class="upload-label">
                            <i class="fas fa-camera"></i> Unggah Foto Kendaraan
                        </p>
                        <input type="file" id="uploadPhotoKendaraan" name="foto_kendaraan" style="display: none;" accept="image/*" onchange="previewImage(event, 'previewKendaraan', 'labelPhotoKendaraan')">

                    </div>
                    <div>
                        @error('foto_kendaraan')
                        @enderror
                    </div>


                    <div class="form-group">
                        <div class="input-group">
                            <span class="input-group-text border border-black text-black"><i class="fas fa-id-card"></i></span>
                            <input type="text" name="plat_nomor" class="form-control border border-black @error('plat_nomor') is-invalid @enderror" required placeholder="Masukkan Nomor Plat" value="{{ old('plat_nomor') }}">
                        </div>
                        @error('plat_nomor')
                        @enderror
                    </div>

                    <div class="form-group">
                        <div class="input-group">
                            <span class="input-group-text border border-black text-black"><i class="fas fa-list"></i></span>
                            <select name="jenis" class="form-select border border-black @error('jenis') is-invalid @enderror" required>
                                <option value="">Pilih Jenis Kendaraan</option>
                                @foreach($jenisKendaraanArray as $value)
                                <option value="{{ $value }}" {{ old('jenis') == $value ? 'selected' : '' }}>{{ $value }}</option>
                                @endforeach
                            </select>
                        </div>
                        @error('jenis')
                        @enderror
                    </div>

                    <div class="form-group">
                        <div class="input-group">
                            <span class="input-group-text border border-black text-black"><i class="fas fa-paint-brush"></i></span>
                            <select name="warna" class="form-select border border-black @error('warna') is-invalid @enderror" required>
                                <option value="">Pilih Warna Kendaraan</option>
                                @foreach($warnaKendaraanArray as $value)
                                <option value="{{ $value }}" {{ old('warna') == $value ? 'selected' : '' }}>{{ $value }}</option>
                                @endforeach
                            </select>
                        </div>
                        @error('warna')
                        @enderror
                    </div>

                    <button type="button" class="button-white-spot  btn-block" id="prevButtonStep3">Sebelumnya</button>
                    <button type="submit" class="button-palang-spot btn-block" id="submitButton">Daftar</button>

                    <div class="register-link">
                        <p>Sudah punya akun? <a href="{{ route('login') }}">Masuk!</a></p>
                    </div>
                </div>
            </form>

        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const nextButtonStep1 = document.getElementById('nextButtonStep1');
            const nextButtonStep2 = document.getElementById('nextButtonStep2');
            const prevButtonStep2 = document.getElementById('prevButtonStep2');
            const prevButtonStep3 = document.getElementById('prevButtonStep3');
            const submitButton = document.getElementById('submitButton');

            const step1 = document.getElementById('step-1');
            const step2 = document.getElementById('step-2');
            const step3 = document.getElementById('step-3');
            const formHeader = document.getElementById('formHeader');
            const kategoriSelect = document.getElementById('kategori');
            const idPenggunaField = document.getElementById('idPenggunaField');

            let currentStep = 0; // Mulai dari step 1

            function showStep(step) {
                const steps = [step1, step2, step3];
                steps.forEach((s, index) => {
                    if (index === step) {
                        s.classList.add('active');
                        s.style.display = 'block';
                    } else {
                        s.classList.remove('active');
                        s.style.display = 'none';
                    }
                });
            }

            function validateStep(step) {
                const inputs = step.querySelectorAll("input, select");
                let isValid = true;

                inputs.forEach(input => {
                    if (input.offsetParent !== null) { // hanya yang terlihat
                        if (!input.checkValidity()) {
                            input.classList.add("is-invalid");
                            isValid = false;
                        } else {
                            input.classList.remove("is-invalid");
                        }
                    }
                });

                return isValid;
            }

            // Next Step 1
            if (nextButtonStep1) {
                nextButtonStep1.addEventListener('click', () => {
                    if (validateStep(step1)) {
                        currentStep = 1;
                        showStep(currentStep);
                        formHeader.innerText = "Data Pengguna";
                    } else {
                        alert("Pastikan semua field pada langkah ini sudah diisi dengan benar.");
                    }
                });
            }

            // Next Step 2
            if (nextButtonStep2) {
                nextButtonStep2.addEventListener('click', () => {
                    if (validateStep(step2)) {
                        currentStep = 2;
                        showStep(currentStep);
                        formHeader.innerText = "Data Kendaraan";
                    } else {
                        alert("Pastikan semua field pada langkah ini sudah diisi dengan benar.");
                    }
                });
            }

            // Prev Step 2 -> Step 1
            if (prevButtonStep2) {
                prevButtonStep2.addEventListener('click', () => {
                    currentStep = 0;
                    showStep(currentStep);
                    formHeader.innerText = "Daftar Akun";
                });
            }

            // Prev Step 3 -> Step 2
            if (prevButtonStep3) {
                prevButtonStep3.addEventListener('click', () => {
                    currentStep = 1;
                    showStep(currentStep);
                    formHeader.innerText = "Data Pengguna";
                });
            }

            // Submit Step 3
            if (submitButton) {
                submitButton.addEventListener('click', (e) => {
                    console.log("Submit button clicked");
                    if (!validateStep(step3)) {
                        e.preventDefault();
                        alert("Pastikan semua field pada langkah ini sudah diisi dengan benar.");
                    }
                });
            }

            // Toggle id_pengguna visibility dan required
            function toggleIdPenggunaField() {
                const idPenggunaInput = idPenggunaField ? idPenggunaField.querySelector('input') : null;
                if (kategoriSelect && kategoriSelect.value === "Tamu") {
                    if (idPenggunaField) idPenggunaField.style.display = 'none';
                    if (idPenggunaInput) {
                        idPenggunaInput.value = '';
                        idPenggunaInput.removeAttribute('required');
                        idPenggunaInput.disabled = true;
                    }
                } else {
                    if (idPenggunaField) idPenggunaField.style.display = 'block';
                    if (idPenggunaInput) {
                        idPenggunaInput.setAttribute('required', 'required');
                        idPenggunaInput.disabled = false;
                    }
                }
            }

            if (kategoriSelect) {
                kategoriSelect.addEventListener('change', toggleIdPenggunaField);
            }

            // Membuat previewImage global agar bisa diakses dari onchange
            window.previewImage = function(event, previewId, labelId) {
                const input = event.target;
                const preview = document.getElementById(previewId);
                const label = document.getElementById(labelId);
                const reader = new FileReader();

                reader.onload = function() {
                    if (preview) {
                        preview.src = reader.result;
                        preview.style.display = "block";
                    }
                    if (label) {
                        label.style.display = "none";
                    }
                };

                if (input.files && input.files[0]) {
                    reader.readAsDataURL(input.files[0]);
                }
            };

            // Toggle password visibility
            window.togglePasswordVisibility = function() {
                const passwordInput = document.getElementById('passwordInput');
                const toggleIcon = document.getElementById('toggleIcon');
                if (passwordInput && toggleIcon) {
                    if (passwordInput.type === 'password') {
                        passwordInput.type = 'text';
                        toggleIcon.classList.remove('bi-eye-fill');
                        toggleIcon.classList.add('bi-eye-slash-fill');
                    } else {
                        passwordInput.type = 'password';
                        toggleIcon.classList.remove('bi-eye-slash-fill');
                        toggleIcon.classList.add('bi-eye-fill');
                    }
                }
            };

            // Loading overlay handling
            const loadingOverlay = document.getElementById("loading-overlay");
            const content = document.getElementById("content");

            function hideOverlay() {
                if (loadingOverlay) {
                    loadingOverlay.style.opacity = "0";
                    loadingOverlay.addEventListener('transitionend', () => {
                        loadingOverlay.style.display = "none";
                        if (content) content.style.display = "block";
                    }, {
                        once: true
                    });

                    // fallback jika transitionend tidak terpanggil
                    setTimeout(() => {
                        if (loadingOverlay.style.display !== "none") {
                            loadingOverlay.style.display = "none";
                            if (content) content.style.display = "block";
                        }
                    }, 1000);
                } else {
                    if (content) content.style.display = "block";
                }
            }

            // INIT
            currentStep = 0;
            showStep(currentStep);
            toggleIdPenggunaField();
            hideOverlay();
        });
    </script>



    <!-- Pastikan Anda menyertakan Bootstrap 5 CSS dan JS di halaman Anda -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>

</body>

</html>