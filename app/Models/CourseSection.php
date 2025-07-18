<?php

namespace App\Models;

use App\Traits\HasUserStamps;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CourseSection extends Model
{
    use HasFactory, HasUserStamps, SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'program_id',
        'course_id',
        'teacher_id',
        'is_active',
        'no_of_students_allowed',
        'no_of_students_enrolled',
        'has_lab',
        'lab_credit_hours',
        'credit_hours',
        'requires_continuous_slots',
        'required_minutes_theory_weekly',
        'required_minutes_lab_weekly',
        'weekly_lectures',            // Added
        'weekly_labs',                // Added
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'weekly_lectures' => 'integer',
        'weekly_labs' => 'integer',
    ];

    // Scope to get only active sections
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // Relationships
    public function program()
    {
        return $this->belongsTo(Program::class);
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function teachers()
    {
        return $this->belongsTo(Teacher::class, 'teacher_id');
    }

    public function enrollmentDetails()
    {
        return $this->hasMany(EnrollmentDetail::class);
    }

    public function scheduleLogs()
    {
        return $this->hasMany(ScheduleLog::class);
    }


}
