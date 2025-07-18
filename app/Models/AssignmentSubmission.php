<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class AssignmentSubmission extends Model implements HasMedia
{

    use InteractsWithMedia;


    protected $fillable = [
        'assignment_id',
        'student_id',
        'attempts',
        'submitted_at',
        'created_by',
        'updated_by',
        // 'file_path' // Uncomment if you're planning to store file paths here instead of media library
    ];

    public function assignment()
    {
        return $this->belongsTo(Assignment::class);
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('submission')->singleFile(); // or multiple if needed
    }

}
