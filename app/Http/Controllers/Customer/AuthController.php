<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;
use Illuminate\Support\Str;
use App\Models\Customer;
use App\Mail\SendOtpMail;

class AuthController extends Controller
{

    public function sendOtp(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email',
        ]);

        $otp = rand(100000, 999999);
        $expiresAt = Carbon::now()->addSeconds(90);

        $customer = Customer::firstOrNew(['email' => $request->email]);
        $customer->first_name = $request->first_name;
        $customer->last_name = $request->last_name;
        $customer->otp = $otp;
        $customer->otp_expires_at = $expiresAt;
        $customer->save();

        Mail::to($customer->email)->send(new SendOtpMail($otp));

        return response()->json(['message' => 'OTP sent successfully.', 'user_details' => $customer]);
    }

    public function resendOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:customers,email',
        ]);

        $otp = rand(100000, 999999);
        $expiresAt = Carbon::now()->addSeconds(90);

        $customer = Customer::where('email', $request->email)->first();
        $customer->update([
            'otp' => $otp,
            'otp_expires_at' => $expiresAt,
        ]);

        Mail::to($customer->email)->send(new SendOtpMail($otp));

        return response()->json(['message' => 'OTP resent successfully.', 'user_detail'=> $customer]);
    }

    public function verifyOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:customers,email',
            'otp' => 'required|numeric',
        ]);

        $customer = Customer::where('email', $request->email)->first();

        if ($customer->otp !== $request->otp || Carbon::now()->greaterThan($customer->otp_expires_at)) {
            return response()->json(['message' => 'Invalid or expired OTP.'], 400);
        }

        $token = Str::random(64);
        $customer->update([
            'otp' => null,
            'otp_expires_at' => null,
            'token' => $token,
        ]);

        return response()->json(['message' => 'OTP verified successfully.', 'user_detail'=> $customer, 'token' => $token]);
    }

    public function logout(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:customers,email',
        ]);

        $customer = Customer::where('email', $request->email)->first();
        $customer->update(['token' => null]);

        return response()->json(['message' => 'Logged out successfully.']);
    }

}
