<?php

namespace App\Models;

use App\Traits\HasUserStamps;
use Illuminate\Database\Eloquent\Model;

class ExaminationSession extends Model
{
    //
    use HasUserStamps;
    protected $fillable = [
        'title',
        'start_date',
        'end_date',
        'is_examination_taken',
        'is_active',
        'is_running',
        'description', // if used
        'created_by',
        'updated_by',
    ];
    public function enrollments()
    {
        return $this->hasMany(Enrollment::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeRunning($query)
    {
        return $query->where('is_running', true);
    }

}
