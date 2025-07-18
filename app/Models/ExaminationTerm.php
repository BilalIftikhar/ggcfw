<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExaminationTerm extends Model
{
    protected $fillable = [
        'examination_session_id',
        'title',
        'description',
        'status',
        'enable_sessional'
    ];

    public function session()
    {
        return $this->belongsTo(ExaminationSession::class,'examination_session_id');;
    }

    public function dateSheets()
    {
        return $this->hasMany(ExaminationDateSheet::class);
    }

    public function marks()
    {
        return $this->hasMany(ExaminationMark::class);
    }

}
