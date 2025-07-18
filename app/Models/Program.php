<?php

namespace App\Models;

use App\Traits\HasUserStamps;
use Illuminate\Database\Eloquent\Model;

class Program extends Model
{
    use HasUserStamps;

    protected $fillable = [
        'name',
        'is_semester',
        'number_of_years',
        'number_of_semesters',
        'is_active',
        'admission_enabled',
        'study_level_id',
        'academic_session_id',
        'credit_hour_system',
        'teaching_days_per_week',
        'period_duration',
        'max_periods_per_day',
        'labs_on_separate_days',
        'preferred_lab_days',
        'attendance_threshold',
        'created_by',
        'updated_by',
    ];

    public function studyLevel()
    {
        return $this->belongsTo(StudyLevel::class);
    }

    public function academicSession()
    {
        return $this->belongsTo(AcademicSession::class);
    }

    public function classes()
    {
        return $this->hasMany(ProgramClass::class);
    }

    public function courses()
    {
        return $this->hasMany(Course::class);
    }

}
