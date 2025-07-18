<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Fee extends Model
{
    //

    protected $fillable = [
        'program_id',
        'academic_session_id',
        'fee_type_id',
        'fee_mode',
        'amount',
        'per_credit_hour_rate',
    ];


    public function program()
    {
        return $this->belongsTo(Program::class);
    }

    public function academicSession()
    {
        return $this->belongsTo(AcademicSession::class);
    }

    public function feeType()
    {
        return $this->belongsTo(FeeType::class);
    }

    public function feeGroup()
    {
        return $this->hasOneThrough(FeeGroup::class, FeeType::class, 'id', 'id', 'fee_type_id', 'fee_group_id');
    }
}
