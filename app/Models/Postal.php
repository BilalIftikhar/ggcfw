<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class Postal extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'reference_number',
        'to_title',
        'from_title',
        'address',
        'tracking_no',
        'courier',
        'note',
        'date',
        'attached_document',
        'created_by',
        'updated_by',
    ];

    /**
     * Get the user who created the postal entry.
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user who last updated the postal entry.
     */
    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Scope for dispatch records.
     */
    public function scopeDispatch($query)
    {
        return $query->where('type', 'dispatch');
    }

    /**
     * Scope for receive records.
     */
    public function scopeReceive($query)
    {
        return $query->where('type', 'receive');
    }
}
