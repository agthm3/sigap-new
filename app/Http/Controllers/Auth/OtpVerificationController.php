<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Notifications\SendOtpNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class OtpVerificationController extends Controller
{
    /**
     * Memproses verifikasi kode OTP yang di-input user.
     */
    public function verify(Request $request): RedirectResponse
    {
        $request->validate([
            'otp' => ['required', 'numeric', 'digits:6'],
        ]);

        $user = $request->user();

        // Validasi ketersediaan kode OTP
        if (! $user->otp_code || $user->otp_code !== $request->otp) {
            return back()->withErrors(['otp' => 'Kode OTP yang Anda masukkan salah.']);
        }

        // Validasi kadaluarsa
        if (now()->greaterThan($user->otp_expires_at)) {
            return back()->withErrors(['otp' => 'Kode OTP telah kadaluarsa. Silakan minta kode OTP baru.']);
        }

        // Tandai email terverifikasi & bersihkan OTP
        $user->markEmailAsVerified();
        $user->update([
            'otp_code' => null,
            'otp_expires_at' => null,
        ]);

        return redirect()->intended(route('home', absolute: false));
    }

    /**
     * Kirim ulang kode OTP baru.
     */
    public function resend(Request $request): RedirectResponse
    {
        $user = $request->user();

        if ($user->hasVerifiedEmail()) {
            return redirect()->intended(route('home', absolute: false));
        }

        $otp = (string) random_int(100000, 999999);

        $user->update([
            'otp_code' => $otp,
            'otp_expires_at' => now()->addMinutes(10),
        ]);

        $user->notify(new SendOtpNotification($otp));

        return back()->with('status', 'Kode OTP baru telah berhasil dikirim ke alamat email Anda.');
    }
}