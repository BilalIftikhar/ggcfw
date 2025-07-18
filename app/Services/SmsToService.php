<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SmsToService
{
    protected string $apiKey;
    protected string $url;

    public function __construct()
    {
        $this->apiKey = config('services.smsto.api_key', '<your_api_key_here>');
        $this->url = 'https://api.sms.to/sms/send';
    }

    /**
     * Send an SMS via SMS.to
     *
     * @param string $to E.g. +35799999999999
     * @param string $message The text message
     * @param string $senderId Sender name (default: SMSto)
     * @param string|null $callbackUrl Optional callback URL for delivery status
     * @return bool|string Response content or false on failure
     */
    public function send(string $to, string $message, string $senderId = 'SMSto', string $callbackUrl = null): bool|string
    {
        $payload = [
            'message' => $message,
            'to' => $to,
            'bypass_optout' => true,
            'sender_id' => $senderId,
        ];

        if ($callbackUrl) {
            $payload['callback_url'] = $callbackUrl;
        }

        try {
            $response = Http::withToken($this->apiKey)
                ->withHeaders(['Content-Type' => 'application/json'])
                ->post($this->url, $payload);

            if ($response->successful()) {
                Log::info('SMS.to message sent', ['to' => $to, 'response' => $response->body()]);
                return $response->body();
            }

            Log::error('SMS.to message failed', ['status' => $response->status(), 'body' => $response->body()]);
            return false;

        } catch (\Exception $e) {
            Log::error('SMS.to exception', ['error' => $e->getMessage()]);
            return false;
        }
    }
}
