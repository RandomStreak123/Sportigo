<?php

namespace App\Services;

use Twilio\Rest\Client;
use Illuminate\Support\Facades\Cache;

class TwilioService
{
    protected $client;
    protected $from;

    public function __construct()
    {
        $sid = env('TWILIO_SID');
        $token = env('TWILIO_TOKEN');
        $this->from = env('TWILIO_FROM');
        
        if ($sid && $token) {
            $this->client = new Client($sid, $token);
        }
    }

    /**
     * Send an OTP using Twilio SMS Messaging API
     */
    public function sendOtp($phoneNumber)
    {
        if (!$this->client) {
            throw new \Exception('Twilio credentials not configured.');
        }

        // Generate a random 6-digit code
        $code = rand(100000, 999999);

        try {
            // Store the code in cache for 10 minutes (keyed by phone number)
            Cache::put('otp_' . $phoneNumber, $code, now()->addMinutes(10));

            // Send the SMS
            $this->client->messages->create($phoneNumber, [
                'from' => $this->from,
                'body' => "Your verification code is: {$code}. It will expire in 10 minutes."
            ]);

            return true;
        } catch (\Exception $e) {
            \Log::error('Twilio Send OTP Error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Verify the provided OTP against the cached version
     */
    public function verifyOtp($phoneNumber, $code)
    {
        $cachedCode = Cache::get('otp_' . $phoneNumber);

        if ($cachedCode && $cachedCode == $code) {
            // Clear the cache after successful verification
            Cache::forget('otp_' . $phoneNumber);
            return true;
        }

        return false;
    }
}
