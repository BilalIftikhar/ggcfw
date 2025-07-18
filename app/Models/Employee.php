<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\HasUserStamps;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Employee extends Model implements HasMedia
{
    use HasFactory, HasUserStamps, SoftDeletes, InteractsWithMedia;

    protected $fillable = [
        // Identification
        'cnic_no',

        // Personal Info
        'name',
        'father_name',
        'domicile',
        'home_address',
        'photo_path',

        // Contact Info
        'home_contact',
        'work_contact',

        // Employment Details
        'designation',
        'bps',
        'status',           // 'Regular' or 'Contract'
        'working_status',   // 'working', 'retired', 'fired', 'other'
        'quota',
        'cadre',

        // Dates Timeline
        'date_of_first_entry',
        'date_of_joining_contract',
        'date_of_joining_regular',
        'date_of_joining_current_station',
        'date_of_retirement',

        // Position History
        'date_of_joining_junior_clerk',
        'date_of_joining_senior_clerk',
        'date_of_joining_lab_supervisor',
        'date_of_joining_head_clerk',
        'date_of_joining_superintendent',
        'date_of_joining_senior_bursar',

        // Academic Career
        'subject',
        'date_of_joining_as_lecturer_contract',
        'date_of_joining_as_lecturer_regular',
        'date_of_joining_as_assistant_prof',
        'date_of_joining_as_associate_prof',
        'date_of_joining_as_professor',
        'date_of_joining_as_principal',

        // Qualifications
        'qualification',
        'qualifying_service',

        // System Fields
        'is_active',
        'temporary_password',
        'user_id',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'date_of_first_entry' => 'date',
        'date_of_joining_contract' => 'date',
        'date_of_joining_regular' => 'date',
        'date_of_joining_current_station' => 'date',
        'date_of_retirement' => 'date',
        'date_of_joining_junior_clerk' => 'date',
        'date_of_joining_senior_clerk' => 'date',
        'date_of_joining_lab_supervisor' => 'date',
        'date_of_joining_head_clerk' => 'date',
        'date_of_joining_superintendent' => 'date',
        'date_of_joining_senior_bursar' => 'date',
        'date_of_joining_as_lecturer_contract' => 'date',
        'date_of_joining_as_lecturer_regular' => 'date',
        'date_of_joining_as_assistant_prof' => 'date',
        'date_of_joining_as_associate_prof' => 'date',
        'date_of_joining_as_professor' => 'date',
        'date_of_joining_as_principal' => 'date',
        'is_active' => 'boolean',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeRegular($query)
    {
        return $query->where('status', 'Regular');
    }
}
