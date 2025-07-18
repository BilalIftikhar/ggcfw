<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AttendanceRule extends Model
{
    protected $table = 'attendance_rules';

    protected $fillable = [
        'attendance_type',
        'allowed_before_minutes',
        'allowed_after_minutes',
        'restrict_to_first_half',
        'mark_once_per_slot',
        'restrict_backdate',
        'restrict_future_date',
        'allow_admin_override',
        'max_daily_absences',
        'auto_mark_present_on_login',
        'requires_reason_for_absent',
        'requires_location_validation',
    ];

    protected $casts = [
        'allowed_before_minutes' => 'integer',
        'allowed_after_minutes' => 'integer',
        'restrict_to_first_half' => 'boolean',
        'mark_once_per_slot' => 'boolean',
        'restrict_backdate' => 'boolean',
        'restrict_future_date' => 'boolean',
        'allow_admin_override' => 'boolean',
        'max_daily_absences' => 'integer',
        'auto_mark_present_on_login' => 'boolean',
        'requires_reason_for_absent' => 'boolean',
        'requires_location_validation' => 'boolean',
    ];

    // Optional: helper to get applicable rules
    public static function forType(string $type = 'subject'): ?self
    {
        return self::where('attendance_type', $type)->first();
    }
}
