<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use Illuminate\Mail\Message;

class EmailService
{
    protected $setting;

    public function __construct()
    {
        $this->setting = Setting::first(); // Global email settings

        if ($this->setting) {
            $this->configureMailSettings();
        }
    }

    /**
     * Apply SMTP config dynamically.
     */
    protected function configureMailSettings(): void
    {
        Config::set('mail.mailers.smtp.transport', 'smtp');
        Config::set('mail.mailers.smtp.host', $this->setting->smtp_host);
        Config::set('mail.mailers.smtp.port', $this->setting->smtp_port);
        Config::set('mail.mailers.smtp.encryption', $this->setting->smtp_encryption);
        Config::set('mail.mailers.smtp.username', $this->setting->smtp_username);
        Config::set('mail.mailers.smtp.password', $this->setting->smtp_password);
        Config::set('mail.from.address', $this->setting->smtp_from_address);
        Config::set('mail.from.name', $this->setting->smtp_from_name);
    }

    /**
     * Send email using settings from DB.
     *
     * @param string|array $to
     * @param string $subject
     * @param string $body (HTML or plain text)
     * @param array|null $attachments (file paths)
     * @return bool
     */
    public function send($to, string $subject, string $body, array $attachments = null): bool
    {
        try {
            Mail::send([], [], function (Message $message) use ($to, $subject, $body, $attachments) {
                $message->to($to)
                    ->subject($subject)
                    ->setBody($body, 'text/html'); // Change to 'text/plain' if needed

                if (!empty($attachments)) {
                    foreach ($attachments as $filePath) {
                        $message->attach($filePath);
                    }
                }
            });

            Log::info('Email sent', ['to' => $to, 'subject' => $subject]);
            return true;
        } catch (\Exception $e) {
            Log::error('Email sending failed', [
                'error' => $e->getMessage(),
                'to' => $to,
                'subject' => $subject,
            ]);
            return false;
        }
    }
}
