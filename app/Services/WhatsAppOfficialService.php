<?php

namespace App\Services;

use App\Models\Setting;
use Netflie\WhatsAppCloudApi\WhatsAppCloudApi;
use Illuminate\Support\Facades\Log;

class WhatsAppOfficialService
{
    protected WhatsAppCloudApi $wa;
    protected bool $enabled;

    public function __construct()
    {
        $settings = Setting::first();

        $this->enabled = (bool) ($settings->whatsapp_active ?? false);

        if ($this->enabled) {
            $this->wa = new WhatsAppCloudApi([
                'from_phone_number_id' => $settings->whatsapp_phone_id,
                'access_token'         => $settings->whatsapp_api_key,
            ]);
        }
    }

    /**
     * Send a text message.
     *
     * @param string $to Phone number in international format (no +)
     * @param string $body Message body
     * @return array|false
     */
    public function sendText(string $to, string $body)
    {
        if (!$this->enabled) {
            Log::warning('WhatsApp official API disabled in settings.');
            return false;
        }

        try {
            $response = $this->wa->sendTextMessage($to, $body);
            Log::info('WhatsApp message sent', ['to' => $to, 'response' => $response]);
            return $response;
        } catch (\Throwable $e) {
            Log::error('WhatsApp send failed', [
                'to' => $to,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }
}
