<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FeeGroup extends Model
{
    //
    protected $fillable = [
        'name',
        'description',
    ];


    public function feeTypes()
    {
        return $this->hasMany(FeeType::class);
    }

}
