<?php
namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class ImpersonateController extends Controller
{
    /**
     * Memulai impersonasi.
     *
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function startImpersonation($id)
    {
        // Pastikan hanya admin yang bisa memulai impersonasi
        if (Auth::user()->is_admin !== 1) {
            return redirect()->route('home')->with('error', 'Kamu tidak bisa melakukan impersonasi.');
        }

        // Cek apakah user yang akan diimpersonasi ada
        $user = User::findOrFail($id);

        // Pastikan admin tidak dapat impersonasi dirinya sendiri
        if (Auth::id() == $user->id) {
            return redirect()->route('home')->with('error', 'Kamu tidak bisa melakukan impersonasi pada akun kamu sendiri.');
        }

        // Simpan ID admin ke session untuk referensi saat stop impersonasi
        session(['impersonate' => Auth::id()]);

        // Login sebagai user yang diimpersonasi
        Auth::login($user);

        // Redirect ke halaman dashboard user yang diimpersonasi
        return redirect()->route('user.dashboard')->with('success', 'Impersonation dimulai. Selamat datang, ' . $user->name);
    }

    /**
     * Menghentikan impersonasi.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function stopImpersonation()
    {
        // Pastikan hanya admin yang bisa menghentikan impersonasi
        if (Auth::user()->is_admin !== 1) {
            return redirect()->route('home')->with('error', 'Kamu tidak bisa melakukan stop impersonasi.');
        }

        // Ambil ID admin dari session impersonasi
        $adminId = session('impersonate');

        // Pastikan ada session impersonasi yang valid
        if (! $adminId) {
            return redirect('/')->with('error', 'Tidak ada sesi impersonasi yang ditemukan.');
        }

        // Login kembali sebagai admin
        $admin = User::findOrFail($adminId);
        Auth::login($admin);

        // Hapus session impersonasi
        session()->forget('impersonate');

        // Arahkan kembali ke halaman admin atau halaman lain setelah menghentikan impersonasi
        return redirect()->route('pegawai.index')->with('success', 'Impersonation dihentikan. Selamat datang kembali, ' . $admin->name);
    }
}
