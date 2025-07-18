<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Setting extends Model implements HasMedia
{
    use InteractsWithMedia;

    protected $fillable = [
        'institute_name',
        'tagline',
        'institute_email',
        'institute_phone',
        'institute_website',
        'institute_address',
        'whatsapp_api_key',
        'whatsapp_number',
        'whatsapp_url',
        'whatsapp_active',
        'smtp_host',
        'smtp_port',
        'smtp_username',
        'smtp_password',
        'smtp_encryption',
        'smtp_from_address',
        'smtp_from_name',
        'voucher_footer_note',
        'default_currency',
        'timezone',
        'academic_year_start',
        'academic_year_end',
        'maintenance_mode',
    ];

    /**
     * Register media collections.
     */
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('institute_logo')->singleFile();
        $this->addMediaCollection('voucher_logo')->singleFile();
    }

    /**
     * Accessor for institute logo URL.
     */
    public function getInstituteLogoUrlAttribute()
    {
        return $this->getFirstMediaUrl('institute_logo');
    }

    /**
     * Accessor for voucher logo URL.
     */
    public function getVoucherLogoUrlAttribute()
    {
        return $this->getFirstMediaUrl('voucher_logo');
    }
}
