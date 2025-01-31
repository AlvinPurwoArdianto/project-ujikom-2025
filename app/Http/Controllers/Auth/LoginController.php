<?php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

// Pastikan model User diimpor

class LoginController extends Controller
{
    use AuthenticatesUsers;

    /**
     * Redirect users after login based on their role.
     */
    protected function redirectTo()
    {
        if (Auth::user()->is_admin === 1) {
            return 'admin/dashboard';
        } else {
            return 'user/dashboard';
        }
    }

    /**
     * Constructor for LoginController.
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
        $this->middleware('auth')->only('logout');
    }

    /**
     * Handle user after successful authentication.
     */
    protected function authenticated(Request $request, $user)
    {
        // Periksa apakah status_pegawai aktif
        if ($user->status_pegawai === 0) {
            Auth::logout();
            return redirect()->route('login')->with('error', 'Akun Anda tidak aktif. Silakan hubungi admin!');
        }
    }

    public function index()
    {
        if (Auth::check()) {
            return Auth::user()->is_admin ? redirect('admin/dashboard') : redirect('user/dashboard');
        }
        return view('auth.login');
    }

    /**
     * Override credentials to add status_pegawai validation.
     */
    protected function credentials(Request $request)
    {
        return [
            'email'    => $request->email,
            'password' => $request->password,
        ];
    }

    /**
     * Handle login attempt with email validation.
     */
    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        $user = User::whereRaw('BINARY email = ?', [$request->email])->first();
        if (! $user) {
            return redirect()->back()->with('error', 'Akun dengan email ini tidak terdaftar.');
        }

        if (Auth::attempt($this->credentials($request))) {
            $request->session()->regenerate();
            return $this->authenticated($request, Auth::user()) ?: redirect()->intended($this->redirectTo());
        }

        return redirect()->back()->with('error', 'Email atau password salah.');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }

}
