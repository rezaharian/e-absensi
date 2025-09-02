<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */

    public function store(Request $request): RedirectResponse
    {
        // Validasi input
        $request->validate([
            'no_payroll' => 'required',
            'tgl_lahir' => 'required|date',
        ]);

        // Cari user berdasarkan no_payroll dan tgl_lahir
        $user = User::where('no_payroll', $request->no_payroll)
            ->where('tgl_lahir', $request->tgl_lahir)
            ->first();

        if ($user) {
            // Login user langsung
            Auth::login($user);

            // Regenerate session
            $request->session()->regenerate();

            // Redirect sesuai role
            if ($user->bagian === 'admin') {
                return redirect('/admin');
            } else {
                return redirect('/user');
            }
        }

        return back()->withErrors([
            'no_payroll' => 'Data tidak cocok.',
        ]);
    }
    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
