<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsappService
{
    protected $token;
    protected $url;
    protected $isEnabled;

    public function __construct()
    {
        $setting = Setting::first(); // Get the global settings row

        $this->token = $setting->whatsapp_api_key ?? null;
        $this->url = $setting->whatsapp_url ?? null;
        $this->isEnabled = $setting->whatsapp_active ?? false;
    }

    /**
     * Send WhatsApp message(s)
     *
     * @param array $contacts Format:
     * [
     *   'contact' => [
     *     ['number' => '92300xxxxxxx', 'message' => 'Hello!'],
     *     ...
     *   ]
     * ]
     * @return array|false
     */
    public function send(array $contacts)
    {
        if (!$this->isEnabled || !$this->token || !$this->url) {
            Log::warning('WhatsApp send blocked: Disabled or missing config');
            return false;
        }

        $response = Http::withHeaders([
            'Api-key' => $this->token,
            'Content-Type' => 'application/json'
        ])->post($this->url, $contacts);

        if ($response->successful()) {
            Log::info('WhatsApp message sent successfully.', [
                'response' => $response->json()
            ]);
            return $response->json();
        }

        Log::error('WhatsApp send failed.', [
            'status' => $response->status(),
            'body' => $response->body()
        ]);

        return false;
    }
}
