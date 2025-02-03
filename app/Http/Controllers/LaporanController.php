<?php
namespace App\Http\Controllers;

use App\Exports\AbsensiExport;
use App\Exports\CutiExport;
use App\Exports\PegawaiExport;
use App\Exports\PenggajianExport;
use App\Models\Absensi;
use App\Models\Cutis;
use App\Models\Jabatan;
use App\Models\Penggajian;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;

class LaporanController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    public function pegawai(Request $request)
    {
        $jabatan      = Jabatan::all();
        $tanggalAwal  = $request->input('tanggal_awal');
        $tanggalAkhir = $request->input('tanggal_akhir');
        $jabatanId    = $request->input('jabatan');

        if (! $tanggalAwal || ! $tanggalAkhir) {
            $pegawai = User::where('is_admin', 0)
                ->when($jabatanId, function ($query) use ($jabatanId) {
                    return $query->where('id_jabatan', $jabatanId);
                })
                ->get()
                ->map(function ($pegawai) {
                    $pegawai->umur = floor(Carbon::parse($pegawai->tanggal_lahir)->diffInYears(Carbon::now()));
                    return $pegawai;
                });
        } else {
            $pegawai = User::whereBetween('tanggal_masuk', [$tanggalAwal, $tanggalAkhir])
                ->when($jabatanId, function ($query) use ($jabatanId) {
                    return $query->where('id_jabatan', $jabatanId);
                })
                ->get()
                ->map(function ($pegawai) {
                    $pegawai->umur = floor(Carbon::parse($pegawai->tanggal_lahir)->diffInYears(Carbon::now()));
                    return $pegawai;
                });
        }

        // Tampilkan PDF
        if ($request->has('view_pdf')) {
            $pdf = PDF::loadView('admin.laporan.pdf_pegawai', compact('pegawai'));
            return $pdf->stream('laporan_pegawai.pdf');
        }

        if ($request->has('download_pdf')) {
            $pdf = PDF::loadView('admin.laporan.pdf_pegawai', compact('pegawai'));
            return $pdf->download('laporan_pegawai.pdf');
        }

        // Unduh Excel
        if ($request->has('download_excel')) {
            return Excel::download(new PegawaiExport($pegawai), 'laporan_pegawai.xlsx');
        }

        return view('admin.laporan.pegawai', compact('pegawai', 'jabatan'));
    }

    // LAPORAN BUAT ABSENSI DAN FILTER
    public function absensi(Request $request)
    {
        try {
            // Pastikan model Absensi dapat di-query dengan benar
            $absensiQuery = Absensi::query()->with('user');

            $tanggalAwal  = $request->input('tanggal_awal');
            $tanggalAkhir = $request->input('tanggal_akhir');
            $pegawaiId    = $request->input('pegawai_id');
            $status       = $request->input('status');

            if ($tanggalAwal && $tanggalAkhir) {
                $absensiQuery->whereBetween('tanggal_absen', [$tanggalAwal, $tanggalAkhir]);
            }

            if ($pegawaiId) {
                $absensiQuery->where('id_user', $pegawaiId);
            }

            if ($status) {
                $absensiQuery->where('status', $status);
            }

            $absensi = $absensiQuery->latest()->get();

            $pegawai = User::where('is_admin', 0)->get();

            // Tampilkan PDF
            if ($request->has('view_pdf')) {
                $pdf = Pdf::loadView('admin.laporan.pdf_absensi', compact('absensi'));
                return $pdf->stream('laporan_absensi.pdf');
            }

            // Handle Excel export
            if ($request->has('download_excel')) {
                return Excel::download(new AbsensiExport($pegawaiId, $tanggalAwal, $tanggalAkhir, $status), 'laporan_absensi.xlsx');
            }

            return view('admin.laporan.absensi', compact('absensi', 'pegawai'));
        } catch (\Exception $e) {
            Log::error('Error fetching absensi data:', ['message' => $e->getMessage()]);
            return redirect()->back()->withErrors('Terjadi kesalahan saat mengambil data absensi.');
        }
    }

    //LAPORAN BUAT CUTI DAN FILTER
    public function cuti(Request $request)
    {
        $pegawai      = User::where('is_admin', 0)->get();
        $tanggalAwal  = $request->input('tanggal_awal');
        $tanggalAkhir = $request->input('tanggal_akhir');
        $pegawaiId    = $request->input('pegawai');
        $statusCuti   = $request->input('status_cuti');

        $cutiQuery = Cutis::with(['pegawai.jabatan']);

        if ($tanggalAwal && $tanggalAkhir) {
            $cutiQuery->whereBetween('tanggal_mulai', [$tanggalAwal, $tanggalAkhir]);
        }

        if ($pegawaiId) {
            $cutiQuery->where('id_user', $pegawaiId);
        }

        if ($statusCuti) {
            $cutiQuery->where('status_cuti', $statusCuti);
        }

        $cuti = $cutiQuery->get();

        foreach ($cuti as $item) {
            $tanggalMulai          = \Carbon\Carbon::parse($item->tanggal_mulai);
            $tanggalAkhir          = \Carbon\Carbon::parse($item->tanggal_selesai);
            $item->total_hari_cuti = $tanggalMulai->diffInDays($tanggalAkhir) + 1;
        }

        // Tampilkan PDF
        if ($request->has('view_pdf')) {
            $pdf = PDF::loadView('admin.laporan.pdf_cuti', compact('cuti'));
            return $pdf->stream('laporan_cuti.pdf');
        }

        // Unduh PDF
        if ($request->has('download_pdf')) {
            $pdf = PDF::loadView('admin.laporan.pdf_cuti', compact('cuti'));
            return $pdf->download('laporan_cuti.pdf');
        }

        // Export Excel
        if ($request->has('download_excel')) {
            return Excel::download(new CutiExport($tanggalAwal, $tanggalAkhir, $pegawaiId), 'laporan_cuti.xlsx');
        }

        return view('admin.laporan.cuti', compact('cuti', 'pegawai'));
    }

    public function penggajian(Request $request)
    {
        $pegawai      = User::where('is_admin', 0)->get();
        $tanggalAwal  = $request->input('tanggal_awal');
        $tanggalAkhir = $request->input('tanggal_akhir');
        $pegawaiId    = $request->input('pegawai');
        $status       = $request->input('status');

        $penggajianQuery = Penggajian::with(['pegawai.jabatan']);

        if ($tanggalAwal && $tanggalAkhir) {
            $penggajianQuery->whereBetween('tanggal_penggajian', [$tanggalAwal, $tanggalAkhir]);
        }

        if ($pegawaiId) {
            $penggajianQuery->where('id_user', $pegawaiId);
        }

        if ($status) {
            $penggajianQuery->where('status', $status);
        }

        $penggajian = $penggajianQuery->latest()->get();

        // Tampilkan PDF
        if ($request->has('view_pdf')) {
            $pdf = PDF::loadView('admin.laporan.pdf_penggajian', compact('penggajian'));
            return $pdf->stream('laporan_penggajian.pdf');
        }

        // Unduh PDF
        if ($request->has('download_pdf')) {
            $pdf = PDF::loadView('admin.laporan.pdf_penggajian', compact('penggajian'));
            return $pdf->download('laporan_penggajian.pdf');
        }

        // Export Excel
        if ($request->has('download_excel')) {
            return Excel::download(new PenggajianExport($tanggalAwal, $tanggalAkhir, $pegawaiId), 'laporan_penggajian.xlsx');
        }

        return view('admin.laporan.penggajian', compact('penggajian', 'pegawai'));
    }
}
