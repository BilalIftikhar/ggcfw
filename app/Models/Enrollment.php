<?php

namespace App\Models;

use App\Traits\HasUserStamps;
use Illuminate\Database\Eloquent\Model;

class Enrollment extends Model
{
   use HasUserStamps;
    protected $fillable = [
        'student_id',
        'program_id',
        'program_class_id',
        'academic_session_id',
        'examination_session_id',
        'enrolled_on',
        'status',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'enrolled_on' => 'date',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function program()
    {
        return $this->belongsTo(Program::class);
    }

    public function academicSession()
    {
        return $this->belongsTo(AcademicSession::class);
    }

    public function programClass()
    {
        return $this->belongsTo(ProgramClass::class);
    }

    public function details()
    {
        return $this->hasMany(EnrollmentDetail::class);
    }

    public function examinationSession()
    {
        return $this->belongsTo(ExaminationSession::class, 'examination_session_id');
    }

}
