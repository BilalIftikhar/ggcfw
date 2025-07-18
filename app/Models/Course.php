<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Course extends Model
{

    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'description',
        'credit_hours',
        'has_lab',
        'lab_credit_hours',
        'program_id',
        'class_id',
        'teacher_id',
        'is_active',
        //Time Table Fields
        'requires_continuous_slots',
        'required_minutes_theory_weekly',
        'required_minutes_lab_weekly',
        'weekly_lectures',            // <-- new
        'weekly_labs',                // <-- new
        'is_mandatory',
        'no_of_sections',
        'students_per_section',
        'created_by',
        'updated_by',
    ];
    protected $casts = [
        'has_lab' => 'boolean',
        'is_active' => 'boolean',
        'weekly_lectures' => 'integer',
        'weekly_labs' => 'integer',

    ];

    public function program()
    {
        return $this->belongsTo(Program::class);
    }

    public function class()
    {
        return $this->belongsTo(ProgramClass::class, 'class_id');
    }


    public function sections()
    {
        return $this->hasMany(CourseSection::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
