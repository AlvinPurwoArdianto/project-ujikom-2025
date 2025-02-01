<?php
namespace App\Http\Controllers;

use App\Models\Absensi;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $pegawai = User::all();

        $totalPegawai    = User::where('is_admin', 0)->count();
        $totalPenggajian = User::sum('gaji');

        $absensiHadir  = Absensi::where('status', 'Hadir')->count();
        $absensiPulang = Absensi::where('status', 'Telat')->count();
        $absensiSakit  = Absensi::where('status', 'Sakit')->count();

        $absensiPerTahun = Absensi::selectRaw('YEAR(tanggal_absen) as tahun, COUNT(*) as jumlah')
            ->groupBy('tahun')
            ->orderBy('tahun', 'asc')
            ->pluck('jumlah', 'tahun');

        return view('home', compact('pegawai', 'totalPegawai', 'totalPenggajian', 'absensiHadir', 'absensiPulang', 'absensiSakit', 'absensiPerTahun'));
    }

    public function dashboard()
    {
        $id_user = Auth::id();
        $absensi = Absensi::where('id_user', $id_user)
            ->select('id_user', 'tanggal_absen', 'status')
            ->get();

        $izinSakit      = Absensi::where('status', 'sakit')->get();
        $izinSakitCount = $izinSakit->count();

        $tanggal_masuk = Auth::user()->tanggal_masuk;

        return view('user.dashboard.index', compact('absensi', 'izinSakit', 'izinSakitCount', 'tanggal_masuk'));
    }
}
