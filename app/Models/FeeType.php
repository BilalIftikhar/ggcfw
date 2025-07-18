<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FeeType extends Model
{
    //

    protected $fillable = [
        'fee_group_id',
        'name',
        'account_code',
        'bank_name',
        'description',
    ];


    public function feeGroup()
    {
        return $this->belongsTo(FeeGroup::class);
    }

    public function fees()
    {
        return $this->hasMany(Fee::class);
    }

}
