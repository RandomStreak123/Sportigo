<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\TwilioService;
use Illuminate\Support\Facades\Validator;

class OtpController extends Controller
{
    protected $twilio;

    public function __construct(TwilioService $twilio)
    {
        $this->twilio = $twilio;
    }

    /**
     * Send OTP to the provided phone number
     */
    public function send(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone_number' => 'required|string|regex:/^\+[1-9]\d{1,14}$/'
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => 'Invalid phone number format. Use E.164 format (e.g. +1234567890).'], 422);
        }

        $success = $this->twilio->sendOtp($request->phone_number);

        if ($success) {
            return response()->json(['success' => true, 'message' => 'OTP sent successfully!']);
        }

        return response()->json(['success' => false, 'message' => 'Failed to send OTP. Please try again.'], 500);
    }

    /**
     * Verify the provided OTP
     */
    public function verify(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone_number' => 'required|string|regex:/^\+[1-9]\d{1,14}$/',
            'code' => 'required|string|size:6'
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => 'Invalid data provided.'], 422);
        }

        $isVerified = $this->twilio->verifyOtp($request->phone_number, $request->code);

        if ($isVerified) {
            // Here you would typically log the user in or mark their phone as verified
            return response()->json(['success' => true, 'message' => 'OTP verified successfully!']);
        }

        return response()->json(['success' => false, 'message' => 'Invalid or expired OTP.'], 401);
    }
}
