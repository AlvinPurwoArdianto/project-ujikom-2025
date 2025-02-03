@extends('layouts.admin.template')
@section('css')
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.datatables.net/2.1.8/css/dataTables.bootstrap5.css">
@endsection
@section('content')
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


    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Laporan /</span> Laporan Penggajian</h4>
        <div class="card">
            <div class="card-header">
                <form action="{{ route('laporan.penggajian') }}" method="GET">
                    <div class="row">
                        <div class="col-8">
                            <select id="pegawai" name="pegawai" class="form-control">
                                <option value="" disabled {{ request('pegawai') ? '' : 'selected' }}>
                                    -- Pilih Sesuai Nama Pegawai --
                                </option>
                                @foreach ($pegawai as $data)
                                    <option value="{{ $data->id }}"
                                        {{ request('pegawai') == $data->id ? 'selected' : '' }}>
                                        {{ $data->nama_pegawai }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        @if (!$pegawai->isEmpty())
                            <div class="col">
                                <a href="#" id="lihatPdfButtonCuti" class="btn btn-secondary form-control"
                                    data-bs-toggle="modal" data-bs-target="#pdfModal">
                                    <i class='bx bx-search-alt-2' data-bs-toggle="tooltip" data-bs-offset="0,4"
                                        data-bs-placement="bottom" data-bs-html="true" title="Lihat PDF"></i>
                                </a>
                            </div>
                            <div class="col">
                                <a href="{{ route('laporan.penggajian', ['download_pdf' => true, 'tanggal_awal' => request('tanggal_awal'), 'tanggal_akhir' => request('tanggal_akhir'), 'pegawai' => request('pegawai'), 'status_cuti' => request('status_cuti')]) }}"
                                    class="btn btn-danger form-control" data-bs-toggle="tooltip" data-bs-offset="0,4"
                                    data-bs-placement="bottom" data-bs-html="true" title="Download PDF">
                                    <i class='bx bxs-file-pdf'></i>
                                </a>
                            </div>
                            <div class="col">
                                <a href="{{ route('laporan.penggajian', ['download_excel' => true, 'tanggal_awal' => request('tanggal_awal'), 'tanggal_akhir' => request('tanggal_akhir'), 'pegawai' => request('pegawai'), 'status_cuti' => request('status_cuti')]) }}"
                                    class="btn btn-success form-control" data-bs-toggle="tooltip" data-bs-offset="0,4"
                                    data-bs-placement="bottom" data-bs-html="true" title="Download EXCEL">
                                    <i class="bi bi-file-earmark-excel-fill"></i>
                                </a>
                            </div>
                        @endif
                    </div>
                    <div class="row mt-3">
                        <div class="col-4">
                            <input type="date" class="form-control" name="tanggal_awal"
                                value="{{ request('tanggal_awal') }}">
                        </div>
                        <div class="col-4">
                            <input type="date" class="form-control" name="tanggal_akhir"
                                value="{{ request('tanggal_akhir') }}">
                        </div>
                        <div class="col-2">
                            <button class="btn btn-primary form-control" type="submit">Filter</button>
                        </div>
                        <div class="col-2">
                            <a href="{{ route('laporan.penggajian') }}" class="btn btn-danger form-control">Reset</a>
                        </div>
                    </div>
                </form>
            </div>
            <div class="card-body">
                @if ($pegawai->isEmpty())
                    <div class="alert alert-warning" role="alert">
                        Tidak ada data pegawai yang cuti ditemukan untuk tanggal yang dipilih
                    </div>
                @else
                    <div class="table-responsive text-nowrap">
                        <table class="table table-bordered" id="example">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Nama Pegawai</th>
                                    <th>Tanggal Gajian</th>
                                    <th>Jumlah Gaji Pokok</th>
                                    <th>Potongan</th>
                                    <th>Bonus</th>
                                    <th>Jumlah Gaji Bersih</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $no = 1;
                                @endphp
                                @foreach ($penggajian as $item)
                                    {{-- @dd($item) <!-- Tambahkan ini untuk debugging --> --}}
                                    @if ($item->pegawai->is_admin == 0)
                                        <tr>
                                            <td>{{ $no++ }}</td>
                                            <td>{{ $item->pegawai->nama_pegawai }}</td>
                                            <td>{{ \Carbon\Carbon::parse($item->tanggal_gaji)->translatedFormat('d F Y') }}
                                            </td>
                                            <td>Rp {{ number_format($item->jumlah_gaji, 0, ',', '.') }}</td>
                                            <td>Rp {{ number_format($item->potongan, 0, ',', '.') }}</td>
                                            <td>Rp {{ number_format($item->bonus, 0, ',', '.') }}</td>
                                            <td>Rp
                                                {{ number_format($item->jumlah_gaji + $item->bonus - $item->potongan, 0, ',', '.') }}
                                            </td>
                                            <td>Successfully</td>
                                        </tr>
                                    @endif
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>
    <!-- Modal untuk melihat PDF -->
    <div class="modal fade" id="pdfModal" tabindex="-1" aria-labelledby="pdfModalLabel" aria-hidden="true"
        data-bs-backdrop="static">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="pdfModalLabel">Lihat PDF - Penggajian</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <iframe id="pdfFrame" style="width: 100%; height: 500px;" frameborder="0"></iframe>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('scripts')
    <script src="https://code.jquery.com/jquery-3.7.1.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
@endpush
