<?php

namespace App\Models;

use App\Traits\HasUserStamps;
use Illuminate\Database\Eloquent\Model;

class WorkingDay extends Model
{
    use HasUserStamps;
    protected $fillable = ['day', 'is_working','created_by','updated_by'];

    public function timeSlots()
    {
        return $this->hasMany(TimeSlot::class);
    }

}
