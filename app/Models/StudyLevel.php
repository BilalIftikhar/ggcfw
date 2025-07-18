<?php

namespace App\Models;

use App\Traits\HasUserStamps;
use Illuminate\Database\Eloquent\Model;

class StudyLevel extends Model
{
    use HasUserStamps;
    protected $fillable = [
        'name',
        'academic_session_id',
        'created_by',
        'updated_by',
    ];
    // Each study level belongs to an academic session
    public function academicSession()
    {
        return $this->belongsTo(AcademicSession::class);
    }

    public function programs()
    {
        return $this->hasMany(Program::class);
    }

}
