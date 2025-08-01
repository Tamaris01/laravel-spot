@extends('layouts.pengelola')
@section('title', 'Konfirmasi Pendaftaran')

@section('content')
<style>
    body {
        background-color: #f5f5f5;
        font-family: 'Roboto', Arial, sans-serif;
    }

    .border-black {
        border-color: black;
    }

    .bg-putih {
        background-color: #ffff;
    }

    .jarak-button {
        margin-right: 10px;
    }

    #userImage {
        width: 100%;
        max-width: 300px;
        height: auto;
        object-fit: cover;
        max-height: 300px;
    }

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
<!-- Overlay Loading -->
<div id="loading-overlay">
    <img id="loading-logo" src="{{ asset('images/spot-logo.png') }}" alt="SPOT Logo">
</div>
<div class="container">
    <h3 class="mb-4">Konfirmasi Pendaftaran</h3>

    @if (session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
    @endif

    <div class="row">
        <div class="col-md-12">
            <div class="card border-black">
                <div class="card-body">
                    <div class="header-container d-flex justify-content-between mb-3">
                        <form method="GET" action="{{ route('pengelola.konfirmasi_pendaftaran') }}" class="d-inline">
                            <div>
                                <span class="ml-2">Tampilkan</span>
                                <select name="rows" id="rows" class="custom-select d-inline border-black" style="width: auto;" onchange="this.form.submit()">
                                    <option value="5" {{ $perPage == 5 ? 'selected' : '' }}>5</option>
                                    <option value="10" {{ $perPage == 10 ? 'selected' : '' }}>10</option>
                                    <option value="15" {{ $perPage == 15 ? 'selected' : '' }}>15</option>
                                    <option value="20" {{ $perPage == 20 ? 'selected' : '' }}>20</option>
                                </select>
                                <span class="ml-2">Baris</span>
                            </div>
                        </form>

                        <div class="search-container d-flex">
                            <form method="GET" action="{{ route('pengelola.konfirmasi_pendaftaran.search') }}" class="d-flex">
                                <input type="text" name="query" class="form-control border-black" placeholder="Pencarian" style="width: 250px;" value="{{ request()->get('query') }}">
                                <button class="btn ml-2" style="background-color: #ffdc40;" type="submit">
                                    <i class="fas fa-search"></i>
                                </button>
                            </form>
                        </div>
                    </div>

                    @if($pendaftar->isEmpty())
                    <p class="mt-3">Tidak ada data yang ditemukan untuk "{{ request()->get('query') }}"</p>
                    @endif

                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead style="background-color: #ffdc40;">
                                <tr>
                                    <th>No</th>
                                    <th>ID Pengguna</th>
                                    <th>Nama</th>
                                    <th>Email</th>
                                    <th>Foto</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="bg-putih">
                                @foreach ($pendaftar as $index => $data)
                                <tr>
                                    <td>{{ $loop->iteration + ($pendaftar->firstItem() - 1) }}</td>

                                    <td>{{ $data->id_pengguna }}</td>
                                    <td>{{ $data->nama }}</td>
                                    <td>{{ $data->email }}</td>
                                    <td class="text-center">
                                        <!-- Tombol pada tabel -->
                                        <button class="btn btn-success btn-sm" data-toggle="modal" data-target="#imageModal-{{ $data->id_pengguna }}">
                                            <i class="fas fa-eye"></i> Lihat
                                        </button>
                                    </td>
                                    <td>
                                        <button type="button" class="btn btn-info btn-sm jarak-button" onclick="showConfirmationModal('terima', '{{ $data->id_pengguna }}')">
                                            <i class="fa-sharp fa-solid fa-circle-check"></i> Terima
                                        </button>
                                        <button type="button" class="btn btn-danger btn-sm" onclick="showConfirmationModal('tolak', '{{ $data->id_pengguna }}')">
                                            <i class="fa-sharp fa-solid fa-circle-xmark"></i> Tolak
                                        </button>

                                    </td>
                                </tr>
                                <!-- Modal untuk lihat Pengguna -->
                                <div class="modal fade" id="imageModal-{{ $data->id_pengguna }}" tabindex="-1" role="dialog" aria-labelledby="imageModalLabel-{{ $data->id_pengguna }}" aria-hidden="true">
                                    <div class="modal-dialog" style="display: flex; justify-content: center; align-items: center; text-align: center;" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header" style="background-color: #FFDC40;">
                                                <h5 class="modal-title" id="imageModalLabel-{{ $data->id_pengguna }}">Foto Pengguna</h5>
                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                            <div class="modal-body text-center">
                                                <img src="{{ $data->foto }}" alt="Foto Pengguna {{ $data->nama }}" class="img-fluid" style="height: 50%; width: 50%;">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div>
                        <ul class="pagination d-flex justify-content-end">
                            {{-- Tombol Previous --}}
                            @if ($pendaftar->onFirstPage())
                            <li class="page-item disabled">
                                <span class="page-link" aria-label="Previous">
                                    <span aria-hidden="true">&laquo;</span>
                                </span>
                            </li>
                            @else
                            <li class="page-item">
                                <a class="page-link" href="{{ $pendaftar->previousPageUrl() }}" aria-label="Previous">
                                    <span aria-hidden="true">&laquo;</span>
                                </a>
                            </li>
                            @endif

                            {{-- Tombol Halaman --}}
                            @foreach ($pendaftar->getUrlRange(1, $pendaftar->lastPage()) as $page => $url)
                            <li class="page-item {{ $pendaftar->currentPage() == $page ? 'active' : '' }}">
                                <a class="page-link" href="{{ $url }}">{{ $page }}</a>
                            </li>
                            @endforeach

                            {{-- Tombol Next --}}
                            @if ($pendaftar->hasMorePages())
                            <li class="page-item">
                                <a class="page-link" href="{{ $pendaftar->nextPageUrl() }}" aria-label="Next">
                                    <span aria-hidden="true">&raquo;</span>
                                </a>
                            </li>
                            @else
                            <li class="page-item disabled">
                                <span class="page-link" aria-label="Next">
                                    <span aria-hidden="true">&raquo;</span>
                                </span>
                            </li>
                            @endif
                        </ul>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal for confirmation -->
<div class="modal fade" id="confirmationModal" tabindex="-1" role="dialog" aria-labelledby="confirmationModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document"> <!-- Center the modal -->
        <div class="modal-content">
            <div class="modal-header" style="background-color: #FFDC40;">
                <h5 class="modal-title" id="confirmationModalLabel">Konfirmasi Pendaftaran</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body text-center d-flex flex-column align-items-center">
                <div class="d-flex justify-content-center align-items-center" style="padding-bottom:10px;">
                    <i class="fas fa-question-circle" style="color: red; font-size: 3em; animation: bounce 1s infinite;"></i>
                </div>
                <p class="text-center">Apakah Anda yakin ingin <span id="actionText"></span> pendaftaran ini? <br> ID Pengguna : <span id="userId"></span></p>
            </div>
            <div class="modal-footer d-flex justify-content-center">
                <button type="button" class="btn" style="background-color: #FFFFFF; color: black; border: 1px solid black; width: 100px;" data-dismiss="modal">Batal</button>
                <button type="button" class="btn" id="confirmAction" style="background-color: #FFDC40; color: black; width: 100px;">Ya</button>
            </div>
        </div>
    </div>
</div>

@endsection
@section('scripts')
<!-- jQuery dan Bootstrap 4 JS harus diletakkan di atas agar $ dikenali -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">

<script>
    function showConfirmationModal(action, id_pengguna) {
        const actionText = action === 'terima' ? 'menerima' : 'menolak';
        document.getElementById('actionText').textContent = actionText;
        document.getElementById('userId').textContent = id_pengguna;

        $('#confirmationModal').modal('show');

        document.getElementById('confirmAction').onclick = function() {
            const routeUrl = action === 'terima' ?
                `{{ route('pengelola.konfirmasi_pendaftaran.terima', ':id') }}` :
                `{{ route('pengelola.konfirmasi_pendaftaran.tolak', ':id') }}`;

            const url = routeUrl.replace(':id', id_pengguna);

            // Membuat dan submit form POST secara dinamis
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = url;

            // CSRF token
            const csrfToken = document.createElement('input');
            csrfToken.type = 'hidden';
            csrfToken.name = '_token';
            csrfToken.value = '{{ csrf_token() }}';
            form.appendChild(csrfToken);

            document.body.appendChild(form);
            form.submit();
        };
    }
</script>
@endsection