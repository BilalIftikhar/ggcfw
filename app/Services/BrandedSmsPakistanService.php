<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class BrandedSmsPakistanService
{
    protected string $email;
    protected string $key;
    protected string $mask;
    protected string $endpoint;

    public function __construct()
    {
        // You can set these in config/services.php or .env
        $this->email = config('services.branded_sms.email', 'hello@brandedsmspakistan.com');
        $this->key = config('services.branded_sms.key', 'your-api-key');
        $this->mask = config('services.branded_sms.mask', 'H3 TECHS');
        $this->endpoint = 'https://secure.h3techs.com/sms/api/send';
    }

    /**
     * Send an SMS using Branded SMS Pakistan API
     *
     * @param string $to Full international number (e.g. 923151231015)
     * @param string $message Message to send
     * @return string|bool Response from API or false on failure
     */
    public function send(string $to, string $message): string|bool
    {
        $payload = http_build_query([
            'email' => $this->email,
            'key' => $this->key,
            'mask' => $this->mask,
            'to' => $to,
            'message' => $message,
        ]);

        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/x-www-form-urlencoded'
            ])
                ->timeout(10)
                ->post($this->endpoint, $payload);

            if ($response->successful()) {
                Log::info('Branded SMS sent successfully', ['to' => $to, 'response' => $response->body()]);
                return $response->body();
            }

            Log::error('Branded SMS failed', ['to' => $to, 'status' => $response->status(), 'body' => $response->body()]);
            return false;
        } catch (\Exception $e) {
            Log::error('Branded SMS Exception', ['message' => $e->getMessage()]);
            return false;
        }
    }
}
