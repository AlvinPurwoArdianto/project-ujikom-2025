<?php
namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Cutis;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ApiCutiController extends Controller
{
    public function index()
    {
        $cuti = Cutis::with(['pegawai.jabatan'])
            ->where('id_user', Auth::id())
            ->get();

        // Hitung total hari cuti untuk setiap record cuti
        foreach ($cuti as $item) {
            $tanggalMulai          = Carbon::parse($item->tanggal_mulai);
            $tanggalAkhir          = Carbon::parse($item->tanggal_selesai);
            $item->total_hari_cuti = $tanggalMulai->diffInDays($tanggalAkhir) + 1;
        }

        return response()->json([
            'success' => true,
            'data'    => $cuti,
        ], 200);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'tanggal_cuti'    => [
                'required',
                'date',
                'after_or_equal:' . now()->addDays(7)->toDateString(),
            ],
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_cuti',
            'kategori_cuti'   => 'required|string|max:255',
            'alasan'          => 'required|string|max:255',
        ], [
            'tanggal_cuti.after_or_equal'    => 'Anda hanya dapat mengajukan cuti setelah satu minggu kedepan.',
            'tanggal_selesai.after_or_equal' => 'Tanggal selesai cuti harus setelah atau sama dengan tanggal mulai cuti.',
        ]);

        $cuti = Cutis::create([
            'id_user'         => Auth::id(),
            'tanggal_mulai'   => $validated['tanggal_cuti'],
            'tanggal_selesai' => $validated['tanggal_selesai'],
            'kategori_cuti'   => $validated['kategori_cuti'],
            'alasan'          => $validated['alasan'],
            'status_cuti'     => 'Menunggu',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Pengajuan cuti berhasil diajukan!',
            'data'    => $cuti,
        ], 201);
    }

    public function approve($id)
    {
        $cuti              = Cutis::findOrFail($id);
        $cuti->status_cuti = 'Diterima';
        $cuti->save();

        return response()->json([
            'success' => true,
            'message' => 'Cuti approved successfully.',
            'data'    => $cuti,
        ], 200);
    }

    public function reject($id)
    {
        $cuti              = Cutis::findOrFail($id);
        $cuti->status_cuti = 'Ditolak';
        $cuti->save();

        return response()->json([
            'success' => true,
            'message' => 'Cuti rejected successfully.',
            'data'    => $cuti,
        ], 200);
    }

    public function getNotifications()
    {
        $cutiNotifications = Cutis::where('status_cuti', 'Menunggu')->count();

        return response()->json([
            'success' => true,
            'count'   => $cutiNotifications,
        ], 200);
    }
}
