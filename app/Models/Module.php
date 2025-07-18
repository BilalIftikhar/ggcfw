<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Models\Permission;

class Module extends Model
{

    protected $fillable = ['name','is_active'];

    public function permissions()
    {
        return $this->hasMany(Permission::class);
    }

}
