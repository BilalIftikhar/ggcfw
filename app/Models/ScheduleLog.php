<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ScheduleLog extends Model
{
    protected $fillable = [
        'course_section_id',
        'examination_session_id',
        'reason',
    ];

    public function courseSection()
    {
        return $this->belongsTo(CourseSection::class);
    }
}
