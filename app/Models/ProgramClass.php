<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProgramClass extends Model
{
    //

    protected $fillable = [
        'name',
        'program_id',
        'is_active',
    ];

    public function program()
    {
        return $this->belongsTo(Program::class);
    }
    public function courses()
    {
        return $this->hasMany(Course::class, 'class_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

}
