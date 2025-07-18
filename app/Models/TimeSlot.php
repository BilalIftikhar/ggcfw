<?php

namespace App\Models;

use App\Traits\HasUserStamps;
use Illuminate\Database\Eloquent\Model;

class TimeSlot extends Model
{
    use HasUserStamps;
    protected $fillable = ['name', 'start_time', 'end_time', 'is_break','sort_order', 'working_day_id','created_by','updated_by'];

    public function timetables()
    {
        return $this->hasMany(Timetable::class);
    }

    public function workingDay()
    {
        return $this->belongsTo(WorkingDay::class);
    }
}
