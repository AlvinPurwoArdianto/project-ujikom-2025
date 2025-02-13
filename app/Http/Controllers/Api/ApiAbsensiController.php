<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Absensi;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class ApiAbsensiController extends Controller
{
    public function index()
    {
        $absensi = Absensi::whereHas('user', function ($query) {
            $query->where('is_admin', 0);
        })->get();

        $izinSakitCount = Absensi::where('status', 'sakit')->count();

        return response()->json([
            'absensi'        => $absensi,
            'izinSakitCount' => $izinSakitCount,
        ]);
    }

    public function store(Request $request)
    {
        date_default_timezone_set('Asia/Jakarta');
        $request->validate([
            'id_user' => 'required|exists:users,id',
        ]);

        $currentTime = Carbon::now('Asia/Jakarta');
        $pegawai     = User::find($request->id_user);

        if (! $pegawai) {
            return response()->json(['error' => 'User tidak ditemukan!'], 404);
        }

        $sudahAbsen = Absensi::where('id_user', $pegawai->id)
            ->whereDate('created_at', Carbon::today('Asia/Jakarta'))
            ->first();

        if ($sudahAbsen) {
            return response()->json(['error' => 'Anda telah melakukan Absen Hari Ini!'], 400);
        }

        $note         = null;
        $status       = 'Hadir';
        $latenessTime = Carbon::createFromTime(8, 0, 0, 'Asia/Jakarta');

        if ($currentTime->greaterThan($latenessTime)) {
            $diffInMinutes = $latenessTime->diffInMinutes($currentTime);
            $hours         = floor($diffInMinutes / 60);
            $minutes       = $diffInMinutes % 60;

            $note   = $hours > 0 ? "Telat $hours jam $minutes menit" : "Telat $minutes menit";
            $status = 'Telat';
        } else {
            $note = "Hadir tepat waktu";
        }

        $absensi = Absensi::create([
            'id_user'       => $request->id_user,
            'tanggal_absen' => $currentTime->toDateString(),
            'jam_masuk'     => $currentTime->toTimeString(),
            'note'          => $note,
            'status'        => $status,
        ]);

        return response()->json([
            'success' => 'Absen Masuk berhasil disimpan!',
            'absensi' => $absensi,
        ], 201);
    }

    public function update($id)
    {
        date_default_timezone_set('Asia/Jakarta');
        $currentTime = Carbon::now('Asia/Jakarta');
        $today       = Carbon::today('Asia/Jakarta')->format('Y-m-d');

        $absensi = Absensi::where('id', $id)
            ->where('id_user', Auth::id())
            ->where('tanggal_absen', $today)
            ->first();

        if (! $absensi) {
            return response()->json([
                'error' => 'Data absensi tidak ditemukan untuk hari ini.',
            ], 404);
        }

        if (is_null($absensi->jam_keluar)) {
            $absensi->jam_keluar = $currentTime->toTimeString();
            $absensi->save();

            return response()->json([
                'success' => 'Absen pulang berhasil disimpan!',
                'absensi' => $absensi,
            ], 200);
        } else {
            return response()->json([
                'error' => 'Anda sudah melakukan absen pulang hari ini.',
            ], 400);
        }
    }

    public function absenSakit(Request $request)
    {
        $id_user       = Auth::id();
        $tanggal_absen = Carbon::today('Asia/Jakarta')->format('Y-m-d');

        $absensi = Absensi::where('id_user', $id_user)->where('tanggal_absen', $tanggal_absen)->first();

        if ($absensi) {
            return response()->json(['error' => 'Anda sudah absen hari ini'], 400);
        }

        $absensi                = new Absensi();
        $absensi->jam_masuk     = '-';
        $absensi->jam_keluar    = '-';
        $absensi->id_user       = $id_user;
        $absensi->tanggal_absen = $tanggal_absen;
        $absensi->status        = 'Sakit';
        $absensi->note          = 'Sakit';

        if ($request->hasFile('photo')) {
            $file           = $request->file('photo');
            $filePath       = $file->store('photo', 'public');
            $absensi->photo = $filePath;
        }

        $absensi->save();

        return response()->json(['success' => 'Absen sakit berhasil disimpan', 'absensi' => $absensi], 201);
    }

    public function izinSakit()
    {
        $izinSakit      = Absensi::where('status', 'sakit')->orderBy('created_at', 'desc')->get();
        $izinSakitCount = Absensi::where('status', 'sakit')->count();

        return response()->json([
            'izinSakit'      => $izinSakit,
            'izinSakitCount' => $izinSakitCount,
        ]);
    }

    public function absensiUpdateStatus(Request $request)
    {
        $absensi_id      = $request->input('absensi_id');
        $absensi         = Absensi::findOrFail($absensi_id);
        $absensi->viewed = true;
        $absensi->save();

        $newCount = Absensi::where('status', 'Sakit')->where('viewed', false)->count();

        return response()->json([
            'message'   => 'Status updated',
            'new_count' => $newCount,
        ]);
    }

    public function getNotifications()
    {
        $izinSakitCount = Absensi::where('status', 'Sakit')->count();

        return response()->json([
            'count' => $izinSakitCount,
        ]);
    }
}
