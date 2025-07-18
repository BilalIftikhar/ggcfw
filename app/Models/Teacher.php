<?php

namespace App\Models;

use App\Traits\HasUserStamps;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Teacher extends Model implements HasMedia
{
    use HasFactory, HasUserStamps,SoftDeletes,InteractsWithMedia;

    protected $fillable = [
        'cnic',
        'seniority_no',
        'name',
        'father_name',
        'designation',
        'bps',
        'dob',
        'domicile',
        'retirement_date',
        'subject',
        'qualification',
        'govt_entry_date',
        'employee_mode',
        'quota',
        'joining_date_adhoc_lecturer',
        'joining_date_regular_lecturer',
        'joining_date_assistant_prof',
        'joining_date_associate_prof',
        'joining_date_professor',
        'joining_date_principal',
        'qualifying_service',
        'joining_date_present_station',
        'cadre',
        'home_address',
        'work_contact',
        'home_contact',
        'is_active',
        'user_id',
        'temporary_password',
        'can_teach_labs',
        'max_lectures_per_day',
        'max_lectures_per_week',
        'working_status',
        'created_by',
        'updated_by',
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

    public function courses()
    {
        return $this->hasMany(Course::class, 'teacher_id');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

}
