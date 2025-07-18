<?php

namespace App\Models;

use App\Traits\HasUserStamps;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    use HasUserStamps;
    protected $fillable = ['room_number', 'building', 'capacity', 'room_type','created_by','updated_by'];

    public function timetables()
    {
        return $this->hasMany(Timetable::class);
    }

}
