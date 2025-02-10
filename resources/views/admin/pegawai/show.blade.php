@extends('layouts.admin.template')
@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Management Karyawan /</span> <span
                class="text-muted fw-light">Pegawai /</span> Show</h4>
        <div class="card mb-4">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h5 class="mb-0">Informasi Pengguna</h5>
            </div><br>
            <div class="card-body">
                <div class="row">
                    <div class="col">
                        <p><strong>Nama:</strong> {{ $pegawai->nama_pegawai }} </p>
                        <p><strong>Jabatan:</strong>
                            {{ $pegawai->jabatan ? $pegawai->jabatan->nama_jabatan : 'Tidak Ada' }}
                        </p>
                        <p><strong>Tempat Lahir:</strong>
                            {{ $pegawai->tempat_lahir ? $pegawai->tempat_lahir : 'Tidak Ada' }} </p>
                        <p><strong>Tanggal Lahir:</strong>
                            {{ $pegawai->tanggal_lahir ? \Carbon\Carbon::parse($pegawai->tanggal_lahir)->translatedFormat('d F Y') : 'Tidak Ada' }}
                        </p>
                        <p><strong>Umur:</strong> {{ $pegawai->umur }} Tahun </p>
                    </div>
                    <div class="col">
                        <p><strong>Email:</strong> {{ $pegawai->email }} </p>
                        <p><strong>Alamat:</strong> {{ $pegawai->alamat ? $pegawai->alamat : 'Tidak Ada' }}
                        </p>
                        <p><strong>Tanggal Masuk:</strong>
                            {{ $pegawai->tanggal_masuk ? \Carbon\Carbon::parse($pegawai->tanggal_masuk)->translatedFormat('d F Y') : 'Tidak Ada' }}
                        </p>
                        <p><strong>Gaji:</strong> {{ $pegawai->gaji ?? 'Tidak Ada' }} </p>
                    </div>
                    <p><strong>Ditempatkan
                            di:
                        </strong>{{ $pegawai->nama_provinsi . ', ' . $pegawai->nama_kota . ', ' . $pegawai->nama_kecamatan . ', ' . $pegawai->nama_kelurahan }}
                    </p>

                    <p><strong>Status:</strong>
                        @if ($pegawai->status_pegawai == 1)
                            <span class="badge bg-label-info">— Pegawai Aktif —</span>
                        @else
                            <span class="badge bg-label-dark">— Pegawai Tidak Aktif —</span>
                        @endif
                    </p>
                    <div class="row mt-3">
                        <div class="col-sm-10">
                            <a href="{{ route('pegawai.index') }} " class="btn btn-primary">Kembali</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
