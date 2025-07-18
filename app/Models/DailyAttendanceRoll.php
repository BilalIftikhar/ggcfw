<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DailyAttendanceRoll extends Model
{
    protected $fillable = [
        'student_id',
        'program_class_id',
        'year',
        'month',
        // fillable for all 31 days
        'day_1', 'day_2', 'day_3', 'day_4', 'day_5', 'day_6', 'day_7', 'day_8', 'day_9', 'day_10',
        'day_11', 'day_12', 'day_13', 'day_14', 'day_15', 'day_16', 'day_17', 'day_18', 'day_19', 'day_20',
        'day_21', 'day_22', 'day_23', 'day_24', 'day_25', 'day_26', 'day_27', 'day_28', 'day_29', 'day_30', 'day_31'
    ];

    public const STATUS_MAP = [
        'P' => 'present',
        'A' => 'absent',
        'L' => 'late',
        'H' => 'leave'
    ];

    public const ENUM_STATUSES = ['present', 'absent', 'leave', 'late'];


    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function class()
    {
        return $this->belongsTo(ProgramClass::class, 'program_class_id');
    }

    public function markedBy()
    {
        return $this->belongsTo(User::class, 'marked_by');
    }

    public function getDay($day)
    {
        $key = 'day_' . intval($day);
        return $this->{$key} ?? null;
    }

    public function setDay($day, $status)
    {
        $key = 'day_' . intval($day);
        $this->{$key} = $status;
    }

    public function getMonthlyAttendancePercentage(): float
    {
        $attended = 0;
        $total = 0;

        for ($i = 1; $i <= 31; $i++) {
            $day = $this->{'day_' . $i} ?? null;
            if ($day !== null) {
                $total++;
                if ($day === 'present' || $day === 'late') {
                    $attended++;
                }
            }
        }

        return $total > 0 ? round(($attended / $total) * 100, 2) : 0.0;
    }


    public function getSummary(): array
    {
        $summary = ['present' => 0, 'absent' => 0, 'late' => 0, 'leave' => 0];

        for ($i = 1; $i <= 31; $i++) {
            $status = $this->{'day_' . $i} ?? null;
            if ($status && isset($summary[$status])) {
                $summary[$status]++;
            }
        }

        return $summary;
    }



    public function scopeForMonth($query, int $month, int $year)
    {
        return $query->where('month', $month)->where('year', $year);
    }

    public function scopeForStudent($query, int $studentId)
    {
        return $query->where('student_id', $studentId);
    }
}
