<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SendpkSmsService
{
    protected string $username;
    protected string $password;
    protected string $url;

    public function __construct()
    {
        // Ideally load from .env or DB if required
        $this->username = config('services.sendpk.username', '923*****');
        $this->password = config('services.sendpk.password', '*****');
        $this->url = "https://sendpk.com/api/sms.php";
    }

    /**
     * Send SMS via SendPK API
     *
     * @param string $to E.g. 923001234567
     * @param string $message
     * @param string $sender Sender ID
     * @return bool|string Response text or false on failure
     */
    public function send(string $to, string $message, string $sender = 'SenderID'): bool|string
    {
        $postData = [
            'sender'  => urlencode($sender),
            'mobile'  => urlencode($to),
            'message' => urlencode($message),
        ];

        $fullUrl = $this->url . "?username={$this->username}&password={$this->password}";

        try {
            $response = Http::withHeaders([
                'User-Agent' => 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1)'
            ])->asForm()->post($fullUrl, $postData);

            if ($response->successful()) {
                Log::info('SendPK SMS sent', ['to' => $to, 'response' => $response->body()]);
                return $response->body();
            }

            Log::error('SendPK SMS failed', ['status' => $response->status(), 'body' => $response->body()]);
            return false;

        } catch (\Exception $e) {
            Log::error('SendPK SMS exception', ['error' => $e->getMessage()]);
            return false;
        }
    }
}
