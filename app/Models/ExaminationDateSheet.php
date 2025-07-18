<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExaminationDateSheet extends Model
{
    protected $fillable = [
        'examination_term_id',
        'examination_session_id',
        'program_id',
        'program_class_id',
        'course_id',
        'course_section_id',
        'exam_date',
        'start_time',
        'end_time',
        'room_id',
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

    public function room()
    {
        return $this->belongsTo(Room::class, 'room_id','id');
    }

}
