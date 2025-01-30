<?php
namespace App\Http\Controllers;

use App\Models\Absensi;
use App\Models\Penggajian;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class PenggajianController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    public function index()
    {
        $penggajian = Penggajian::latest()->get();
        $pegawai    = User::all();
        $absensi    = Absensi::all();
        confirmDelete('Hapus Penggajian!', 'Apakah Anda Yakin?');
        return view('admin.penggajian.index', compact('penggajian', 'pegawai', 'absensi'));
    }

    public function index1()
    {
        $penggajian = Penggajian::latest()->get();
        $pegawai    = User::all();
        confirmDelete('Hapus Penggajian!', 'Apakah Anda Yakin?');
        return view('user.penggajian.index', compact('penggajian', 'pegawai'));
    }

    public function create()
    {
        $penggajian = Penggajian::all();
        $pegawai    = User::all();
        return view('admin.penggajian.index', compact('penggajian', 'pegawai'));
    }

    // public function store(Request $request)
    // {
    //     // Buat entri penggajian
    //     $penggajian               = new Penggajian();
    //     $penggajian->id_user      = $request->id_user;
    //     $penggajian->tanggal_gaji = $request->tanggal_gaji;
    //     $penggajian->jumlah_gaji  = $request->jumlah_gaji;
    //     $penggajian->bonus        = $request->bonus;
    //     $penggajian->potongan     = $request->potongan;

    //     // Ambil data absensi untuk pegawai yang bersangkutan
    //     $absensi = Absensi::where('id_user', $request->id_user)
    //         ->whereDate('tanggal_absen', $request->tanggal_gaji)
    //         ->first();

    //     // Jika ada data absensi, hitung keterlambatan
    //     if ($absensi && $absensi->jam_masuk) {
    //         $batas_waktu = '08:00:00'; // Batas waktu masuk yang ditentukan, bisa disesuaikan
    //         $jam_masuk   = $absensi->jam_masuk;

    //         // Menghitung selisih waktu keterlambatan (dalam menit)
    //         $waktu_telat = strtotime($jam_masuk) - strtotime($batas_waktu);
    //         if ($waktu_telat > 0) {                  // Jika keterlambatan lebih dari 0 menit
    //             $telat_menit = round($waktu_telat / 60); // Menghitung keterlambatan dalam menit

    //             // Tentukan potongan per menit (misalnya 100.000 per menit keterlambatan)
    //             $potongan_per_menit = 100000;
    //             $potongan_telat     = $telat_menit * $potongan_per_menit;

    //             // Tambahkan potongan ke dalam penggajian
    //             $penggajian->potongan += $potongan_telat;
    //         }
    //     }

    //     // Simpan penggajian
    //     $penggajian->save();

    //     // Update total gaji pegawai
    //     $pegawai = User::find($request->id_user);
    //     if ($pegawai) {
    //         // Hitung total gaji (gaji dasar + bonus - potongan)
    //         $total_gaji = $request->jumlah_gaji + $request->bonus - $penggajian->potongan;
    //         $pegawai->gaji += $total_gaji;
    //         $pegawai->save();
    //     }

    //     return redirect()->route('penggajian.index')->with('success', 'Penggajian berhasil ditambahkan dan total gaji diperbarui.');
    // }

    public function store(Request $request)
    {
        // dd(request()->all());
        date_default_timezone_set('Asia/Jakarta');

        $request->validate([
            'id_user' => 'required|exists:users,id',
        ]);

        $currentTime = Carbon::now('Asia/Jakarta');
        $pegawai     = User::find($request->id_user);
        if (! $pegawai) {
            return redirect()->route('penggajian.index')->with('error', 'User tidak ditemukan!');
        }

        $absensi = Absensi::where('id_user', $pegawai->id)
            ->whereDate('tanggal_absen', Carbon::today('Asia/Jakarta'))
            ->first();

        if ($absensi && $absensi->status == 'Telat') {
            $latenessTime = Carbon::createFromTime(8, 0, 0, 'Asia/Jakarta');
            $currentTime  = Carbon::parse($absensi->jam_masuk);

            $diffInMinutes = floor($latenessTime->diffInSeconds($currentTime) / 60);

            $potonganPerMenit = 10000;
            $potongan         = $diffInMinutes * $potonganPerMenit;
        } else {
            $potongan = 0;
        }

        $gajiBersih = $request->jumlah_gaji - $potongan + ($request->bonus ?? 0);

        Penggajian::create([
            'id_user'      => $request->id_user,
            'jabatan'      => $request->jabatan,
            'tanggal_gaji' => $currentTime->toDateString(),
            'jumlah_gaji'  => $request->jumlah_gaji,
            'bonus'        => $request->bonus ?? 0,
            'potongan'     => $potongan,
            'gaji_bersih'  => $gajiBersih,
        ]);

        $pegawai->update([
            'gaji' => $gajiBersih,
        ]);

        return redirect()->route('penggajian.index')->with('success', 'Data penggajian berhasil disimpan!');
    }

    public function update(Request $request, $id)
    {
        $penggajian               = Penggajian::findOrFail($id);
        $penggajian->id_user      = $request->id_user;
        $penggajian->tanggal_gaji = $request->tanggal_gaji;
        $penggajian->jumlah_gaji  = $request->jumlah_gaji;
        $penggajian->bonus        = $request->bonus;
        $penggajian->potongan     = $request->potongan;
        $penggajian->save();

        // Update total gaji pegawai
        $pegawai = User::find($request->id_user);
        if ($pegawai) {
            $pegawai->gaji = $penggajian->jumlah_gaji + ($request->bonus) - ($request->potongan);
            $pegawai->save();
        }

        return redirect()->route('penggajian.index')->with('success', 'Penggajian berhasil diperbarui dan total gaji diperbarui.');
    }

    public function destroy($id)
    {
        $penggajian = Penggajian::findOrFail($id);

        $pegawai = User::find($penggajian->id_user);
        if ($pegawai) {
            $pegawai->gaji -= ($penggajian->jumlah_gaji + $penggajian->bonus - $penggajian->potongan);
            $pegawai->save();
        }

        $penggajian->delete();
        return redirect()->route('penggajian.index')->with('danger', 'Penggajian berhasil dihapus dan gaji diperbarui!');
    }
}
