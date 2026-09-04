<?php

namespace App\Services;

use App\Models\SmsLog;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SmsService
{
    /**
     * Send an SMS and record the attempt.
     *
     * Default driver is "log": no real provider is configured, so the message
     * is written to the application log and recorded as sent - the same
     * convention Laravel's own "log" mail driver uses for local/dev environments.
     * Set SMS_DRIVER=http and SMS_API_URL/SMS_API_KEY in .env to send through a
     * real SMS gateway (Twilio, Vonage, a local Romanian provider, etc.) once
     * those credentials are available.
     */
    public function send(string $phone, string $message): SmsLog
    {
        $driver = config('services.sms.driver', 'log');

        if ($driver === 'http' && config('services.sms.api_url')) {
            return $this->sendViaHttp($phone, $message);
        }

        Log::info("[SMS:log] to {$phone}: {$message}");

        return SmsLog::create([
            'phone' => $phone,
            'message' => $message,
            'driver' => 'log',
            'status' => 'sent',
        ]);
    }

    private function sendViaHttp(string $phone, string $message): SmsLog
    {
        try {
            $response = Http::timeout(10)->post(config('services.sms.api_url'), [
                'to' => $phone,
                'message' => $message,
                'sender' => config('services.sms.sender'),
                'api_key' => config('services.sms.api_key'),
            ]);

            return SmsLog::create([
                'phone' => $phone,
                'message' => $message,
                'driver' => 'http',
                'status' => $response->successful() ? 'sent' : 'failed',
                'error' => $response->successful() ? null : $response->body(),
            ]);
        } catch (\Throwable $e) {
            return SmsLog::create([
                'phone' => $phone,
                'message' => $message,
                'driver' => 'http',
                'status' => 'failed',
                'error' => $e->getMessage(),
            ]);
        }
    }
}
