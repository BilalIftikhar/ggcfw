<?php

namespace App\Models;

use App\Traits\HasUserStamps;
use Illuminate\Database\Eloquent\Model;

class Timetable extends Model
{
    use HasUserStamps;
    protected $fillable = [
        'academic_session_id',
        'examination_session_id',
        'program_id',
        'program_class_id',
        'course_id',
        'course_section_id',
        'teacher_id',
        'day_of_week',
        'start_time',
        'end_time',
        'room_number',
        'time_slot_id',
        'room_id',
        'is_lab',
        'created_by',
        'updated_by',
    ];
    public function academicSession()
    {
        return $this->belongsTo(AcademicSession::class);
    }

    public function program()
    {
        return $this->belongsTo(Program::class);
    }

    public function programClass()
    {
        return $this->belongsTo(ProgramClass::class);
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function teacher()
    {
        return $this->belongsTo(Teacher::class, 'teacher_id');
    }

    public function timeSlot()
    {
        return $this->belongsTo(TimeSlot::class);
    }

    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    public function courseSection()
    {
        return $this->belongsTo(CourseSection::class);
    }

    // Add this to your Timetable model
    public function examinationSession()
    {
        return $this->belongsTo(ExaminationSession::class, 'examination_session_id');
    }
}
