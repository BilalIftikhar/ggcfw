<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class VisitorsLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'contact_number',
        'address',
        'purpose',
        'date_of_visit',
        'in_time',
        'out_time',
        'person_to_meet',
        'note',
        'created_by',
        'updated_by',
    ];

    /**
     * The user who created the visitor log.
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * The user who last updated the visitor log.
     */
    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
