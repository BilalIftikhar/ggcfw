<?php

namespace App\Models;

use App\Traits\HasUserStamps;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AcademicSession extends Model
{

    use SoftDeletes;
    use HasUserStamps;

    protected $fillable = [
        'name',
        'start_date',
        'end_date',
        'admission_end_date',
        'allow_admission',
        'created_by',
        'updated_by',
        'is_active',
    ];

    protected $dates = [
        'start_date',
        'end_date',
        'admission_end_date',
        'deleted_at',
    ];

    public function studyLevels()
    {
        return $this->hasMany(StudyLevel::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeAdmission($query)
    {
        return $query->where('allow_admission', true);
    }

}
