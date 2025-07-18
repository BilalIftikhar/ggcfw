<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubjectAttendance extends Model
{
    protected $fillable = [
        'student_id',
        'timetable_id',
        'attendance_date',
        'course_section_id',
        'marked_by',
        'status',
    ];

    public const STATUS_MAP = [
        'P' => 'present',
        'A' => 'absent',
        'L' => 'late',
        'H' => 'leave'
    ];

    public const ENUM_STATUSES = ['present', 'absent', 'leave', 'late'];

    protected $casts = [
        'attendance_date' => 'date',
    ];


    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function timetable()
    {
        return $this->belongsTo(Timetable::class);
    }

    public function markedBy()
    {
        return $this->belongsTo(User::class, 'marked_by');
    }

    public function scopeForStudent($query, $studentId)
    {
        return $query->where('student_id', $studentId);
    }

    public function scopeForDate($query, $date)
    {
        return $query->whereDate('attendance_date', $date);
    }

    public function courseSection()
    {
        return $this->timetable?->courseSection(); // optional chaining
    }

    public function scopeForTimetable($query, $timetableId)
    {
        return $query->where('timetable_id', $timetableId);
    }

}
