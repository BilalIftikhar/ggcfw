<?php

namespace App\Models;

use App\Traits\HasUserStamps;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Student extends Model implements HasMedia
{
    use HasUserStamps, SoftDeletes, InteractsWithMedia;

    protected $fillable = [
        // PERSONAL
        'name',
        'cnic',
        'father_name',
        'father_cnic',
        'address',
        'religion',
        'blood_group',
        'gender',
        'student_contact',
        'parent_contact',
        'whatsapp_no',
        'email',
        'date_of_birth',
        'picture',


        // ACADEMIC INFO
        'matric_passing_year',
        'matric_roll_no',
        'matric_board',
        'matric_obtained_marks',
        'matric_total_marks',
        'matric_group',

        'inter_passing_year',
        'inter_roll_no',
        'inter_board',
        'inter_obtained_marks',
        'inter_total_marks',
        'inter_group',

        'grad_passing_year',
        'grad_reg_no',
        'grad_board',
        'grad_obtained_marks',
        'grad_total_marks',
        'grad_group',

        // OTHERS
        'is_hafiz',
        'father_job',
        'father_department',
        'father_designation',

        //user infor
        'user_id',
        'temporary_password',

        // Core
        'roll_number',
        'registration_number',
        'status',
        'academic_session_id',
        'study_level_id',
        'program_id',
        'is_active',
        'created_by',
        'updated_by',
    ];

    // RELATIONSHIPS
    public function academicSession()
    {
        return $this->belongsTo(AcademicSession::class);
    }

    public function studyLevel()
    {
        return $this->belongsTo(StudyLevel::class);
    }

    public function program()
    {
        return $this->belongsTo(Program::class);
    }

    public function enrollments()
    {
        return $this->hasMany(Enrollment::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function submissions()
    {
        return $this->hasMany(AssignmentSubmission::class);
    }


    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeStudying($query)
    {
        return $query->where('status', 'studying');
    }

}
