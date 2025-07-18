<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EnrollmentDetail extends Model
{

    protected $fillable = [
        'enrollment_id', 'course_id', 'is_mandatory','status','course_section_id'
    ];

    public function enrollment()
    {
        return $this->belongsTo(Enrollment::class);
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function courseSection()
    {
        return $this->belongsTo(CourseSection::class);
    }


}
