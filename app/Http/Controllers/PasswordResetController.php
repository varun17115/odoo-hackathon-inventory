<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\PasswordResetOtp;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class PasswordResetController extends Controller
{
    public function showForgotForm()
    {
        return view('auth.forgot-password');
    }

    public function sendOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users',
        ]);

        $user = User::where('email', $request->email)->first();
        $otp = PasswordResetOtp::createForUser($user);

        // For development, log the OTP. In production, send via email/SMS
        \Log::info("OTP for {$user->email}: {$otp->otp}");

        // Uncomment to send via email
        // Mail::send('emails.otp', ['otp' => $otp->otp, 'user' => $user], function ($message) use ($user) {
        //     $message->to($user->email)->subject('Password Reset OTP');
        // });

        return redirect()->route('password.verify-otp')->with('email', $user->email)->with('success', 'OTP sent to your email. Check your logs for development.');
    }

    public function showVerifyOtpForm()
    {
        return view('auth.verify-otp');
    }

    public function verifyOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users',
            'otp' => 'required|string|size:6',
        ]);

        $user = User::where('email', $request->email)->first();
        $otpRecord = PasswordResetOtp::where('user_id', $user->id)
            ->where('otp', $request->otp)
            ->first();

        if (!$otpRecord) {
            return back()->withErrors(['otp' => 'Invalid OTP.']);
        }

        if (!$otpRecord->isValid()) {
            return back()->withErrors(['otp' => 'OTP has expired or already used.']);
        }

        // Mark OTP as used
        $otpRecord->update(['is_used' => true]);

        return redirect()->route('password.reset-form', ['email' => $user->email, 'otp' => $request->otp])->with('success', 'OTP verified successfully.');
    }

    public function showResetForm(Request $request)
    {
        $email = $request->query('email');
        $otp = $request->query('otp');

        if (!$email || !$otp) {
            return redirect()->route('password.forgot')->withErrors(['error' => 'Invalid reset link.']);
        }

        return view('auth.reset-password', ['email' => $email, 'otp' => $otp]);
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users',
            'otp' => 'required|string|size:6',
            'password' => 'required|min:8|confirmed',
        ]);

        $user = User::where('email', $request->email)->first();
        $otpRecord = PasswordResetOtp::where('user_id', $user->id)
            ->where('otp', $request->otp)
            ->where('is_used', true)
            ->first();

        if (!$otpRecord) {
            return back()->withErrors(['error' => 'Invalid reset request.']);
        }

        // Update password
        $user->update(['password' => Hash::make($request->password)]);

        return redirect()->route('login')->with('success', 'Password reset successfully. Please login with your new password.');
    }
}
