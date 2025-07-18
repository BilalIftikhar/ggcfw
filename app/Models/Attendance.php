<?php

namespace App\Models;

use App\Traits\HasUserStamps;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    use HasUserStamps;
    protected $fillable = [
        'student_id',
        'timetable_id',
        'course_id',
        'status',
        'created_by',
        'updated_by',
        'attendance_date',
    ];
    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function timetable()
    {
        return $this->belongsTo(Timetable::class);
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

}
