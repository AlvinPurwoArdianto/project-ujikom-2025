@extends('layouts.admin.template')

@section('css')
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />
@endsection

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Menu / Penggajian /</span> Create</h4>

        {{-- UNTUK TOAST NOTIFIKASI --}}
        <div class="toast-container position-fixed top-0 end-0 p-3">
            <div id="validationToast" class="toast hide" role="alert" aria-live="assertive" aria-atomic="true">
                <div class="toast-header">
                    <i class="bi bi-cloud-arrow-up-fill me-2"></i>
                    <div class="me-auto fw-semibold">Success</div>
                    <small>Just Now</small>
                    <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
                <div class="toast-body">
                    Data sudah ada!
                </div>
            </div>
        </div>

        <!-- Toast Untuk Success -->
        @if (session('success'))
            <div class="bs-toast toast toast-placement-ex m-2 bg-success top-0 end-0 fade show toast-custom" role="alert"
                aria-live="assertive" aria-atomic="true" id="toastSuccess">
                <div class="toast-header">
                    <i class="bi bi-check-circle me-2"></i>
                    <div class="me-auto fw-semibold">Success</div>
                    <small>Just Now</small>
                    <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
                <div class="toast-body">
                    {{ session('success') }}
                </div>
            </div>
        @endif

        {{-- Toast Untuk Error --}}
        @if (session('error'))
            <div class="bs-toast toast toast-placement-ex m-2 bg-danger top-0 end-0 fade show toast-custom" role="alert"
                aria-live="assertive" aria-atomic="true" id="toastError">
                <div class="toast-header">
                    <i class="bx bx-error me-2"></i>
                    <div class="me-auto fw-semibold">Error</div>
                    <small>Just Now</small>
                    <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
                <div class="toast-body">
                    {{ session('error') }}
                </div>
            </div>
        @endif

        {{-- Toast Untuk Danger --}}
        @if (session('danger'))
            <div class="bs-toast toast toast-placement-ex m-2 bg-danger top-0 end-0 fade show toast-custom" role="alert"
                aria-live="assertive" aria-atomic="true" id="toastError">
                <div class="toast-header">
                    <i class="bx bx-error me-2"></i>
                    <div class="me-auto fw-semibold">Danger</div>
                    <small>Just Now</small>
                    <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
                <div class="toast-body">
                    {{ session('danger') }}
                </div>
            </div>
        @endif

        {{-- Toast Untuk Warning --}}
        @if (session('warning'))
            <div class="bs-toast toast toast-placement-ex m-2 bg-warning top-0 end-0 fade show toast-custom" role="alert"
                aria-live="assertive" aria-atomic="true" id="toastError">
                <div class="toast-header">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    <div class="me-auto fw-semibold">Warning</div>
                    <small>Just Now</small>
                    <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
                <div class="toast-body">
                    {{ session('warning') }}
                </div>
            </div>
        @endif

        <div class="card mb-4">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h5 class="mb-0">Tambah penggajian</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('penggajian.store') }}" method="POST">
                    @csrf
                    <div class="row">
                        <div class="col mb-0">
                            <label for="nameBasic" class="form-label">Nama Pegawai</label>
                            <select name="id_user" class="form-control" id="pegawai" required>
                                <option selected disabled>-- Nama pegawai --</option>
                                @foreach ($pegawai as $pegawaiItem)
                                    @if ($pegawaiItem->is_admin == 0)
                                        <option value="{{ $pegawaiItem->id }}"
                                            {{ session('id_user') && in_array($pegawaiItem->id, session('id_user')) ? 'disabled' : '' }}
                                            data-jabatan="{{ $pegawaiItem->jabatan ? $pegawaiItem->jabatan->nama_jabatan : 'Tidak ada jabatan' }}"
                                            data-telat="{{ $pegawaiItem->absensi->firstWhere('tanggal_absen', \Carbon\Carbon::today()->toDateString()) ? $pegawaiItem->absensi->firstWhere('tanggal_absen', \Carbon\Carbon::today()->toDateString())->note : 'Hadir tepat waktu' }}"
                                            data-gaji="{{ $pegawaiItem->gaji }}">
                                            {{ $pegawaiItem->nama_pegawai }}
                                        </option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                        <div class="col mb-0">
                            <label class="col-sm-2 col-form-label" for="jabatan">Jabatan</label>
                            <div class="col">
                                <input type="text" class="form-control" id="jabatan" name="jabatan" disabled>
                            </div>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col mb-0">
                            <label for="tanggal_gaji" class="form-label">Tanggal Gaji</label>
                            <input type="date" class="form-control" name="tanggal_gaji" required>
                        </div>
                        <div class="col mb-0">
                            <label for="jumlah_gaji" class="col col-form-label">Jumlah Nominal <span
                                    class="text-danger">*</span></label>
                            <div class="col">
                                <input type="number" class="form-control" name="jumlah_gaji" id="jumlah_gaji" required>
                            </div>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col mb-0">
                            <label for="bonus" class="form-label">Tambahan Bonus</label>
                            <input type="number" class="form-control" name="bonus" id="bonus" value="0">
                        </div>
                        <div class="col mb-0">
                            <label for="potongan" class="col col-form-label">Jumlah Potongan</label>
                            <div class="col">
                                <input type="number" class="form-control" name="potongan" id="potongan"
                                    value="0" readonly>
                            </div>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col mb-0">
                            <label for="gaji_bersih" class="form-label">Gaji Bersih</label>
                            <input type="number" class="form-control" name="gaji_bersih" id="gaji_bersih" readonly>
                        </div>
                    </div>
                    {{-- <div class="modal-footer">
                        <a href="{{ route('penggajian.index') }}" type="button" class="btn btn-secondary"
                            data-bs-dismiss="modal">Kembali</a>
                        <button type="submit" class="btn btn-primary">Kirim</button>
                    </div> --}}
                    <div class="row mt-3">
                        <div class="col-sm-10">
                            <a href="{{ route('penggajian.index') }} " class="btn btn-primary">Kembali</a>
                            <button type="submit" class="btn btn-primary">Simpan</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
    {{-- <script>
        $(document).ready(function() {
            $('#pegawai').select2();
        });
    </script> --}}

    <script>
        document.getElementById('pegawai').addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            const jabatan = selectedOption.getAttribute('data-jabatan');
            const telat = selectedOption.getAttribute('data-telat');
            const gajiPokok = parseInt(selectedOption.getAttribute('data-gaji')) || 0;

            // Set Jabatan
            document.getElementById('jabatan').value = jabatan;

            // Set Potongan berdasarkan status telat
            let potongan = 0;
            if (telat && telat.includes('Telat')) {
                // Ekstrak waktu keterlambatan (misalnya, "Telat 30 menit" atau "Telat 2 jam 15 menit")
                const match = telat.match(/(\d+)\s*(jam|menit)/i); // Menyesuaikan format jam/menit
                if (match) {
                    let minutesLate = 0;
                    if (match[2].toLowerCase() === 'jam') {
                        minutesLate = parseInt(match[1]) * 60; // Konversi jam ke menit
                    } else if (match[2].toLowerCase() === 'menit') {
                        minutesLate = parseInt(match[1]);
                    }
                    // Hitung potongan berdasarkan menit keterlambatan
                    potongan = Math.round(minutesLate * 10000); // Menggunakan pembulatan
                }
            }
            document.getElementById('potongan').value = potongan;

            // Menghitung gaji bersih
            const bonus = parseInt(document.getElementById('bonus').value) || 0;
            const totalGaji = gajiPokok + bonus - potongan;
            document.getElementById('gaji_bersih').value = Math.max(0, totalGaji); // Pastikan tidak negatif
        });

        // Update gaji bersih jika jumlah gaji atau bonus diubah
        document.getElementById('jumlah_gaji').addEventListener('input', updateGajiBersih);
        document.getElementById('bonus').addEventListener('input', updateGajiBersih);

        function updateGajiBersih() {
            const jumlahGaji = parseInt(document.getElementById('jumlah_gaji').value) || 0;
            const bonus = parseInt(document.getElementById('bonus').value) || 0;
            const potongan = parseInt(document.getElementById('potongan').value) || 0;

            const gajiBersih = jumlahGaji + bonus - potongan;
            document.getElementById('gaji_bersih').value = Math.max(0, gajiBersih); // Pastikan tidak negatif
        }
    </script>
@endpush
