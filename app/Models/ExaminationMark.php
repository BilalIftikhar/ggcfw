<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExaminationMark extends Model
{
    protected $fillable = [
        'examination_term_id',
        'examination_session_id',
        'program_id',
        'program_class_id',
        'course_id',
        'course_section_id',
        'student_id',
        'examination_date_sheet_id',
        'marks_obtained',
        'total_marks',
        'sessional_marks',
        'passing_marks',
        'marked_by',
        'updated_by',
    ];

    public function term()
    {
        return $this->belongsTo(ExaminationTerm::class, 'examination_term_id');
    }

    public function session()
    {
        return $this->belongsTo(ExaminationSession::class, 'examination_session_id');
    }

    public function program()
    {
        return $this->belongsTo(Program::class);
    }

    public function class()
    {
        return $this->belongsTo(ProgramClass::class, 'program_class_id');
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function courseSection()
    {
        return $this->belongsTo(CourseSection::class);
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function dateSheet()
    {
        return $this->belongsTo(ExaminationDateSheet::class, 'examination_date_sheet_id');
    }

    public function markedBy()
    {
        return $this->belongsTo(User::class, 'marked_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
