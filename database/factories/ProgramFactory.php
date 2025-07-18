<?php

namespace Database\Factories;

use App\Models\Program;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProgramFactory extends Factory
{
    protected $model = Program::class;

    public function definition(): array
    {
        return [
            'is_active' => true,
            'admission_enabled' => true,
            'created_by' => 1,
            'updated_by' => 1,
        ];
    }

    public function intermediate()
    {
        return $this->state(function (array $attributes) {
            return [
                'is_semester' => false,
                'number_of_years' => 2,
                'number_of_semesters' => 0,
                'credit_hour_system' => false,
                'teaching_days_per_week' => 6,
                'period_duration' => 45,
                'max_periods_per_day' => 6,
                'labs_on_separate_days' => false,
                'preferred_lab_days' => null,
                'attendance_threshold' => 85,
            ];
        });
    }

    public function higherEducation()
    {
        return $this->state(function (array $attributes) {
            return [
                'is_semester' => true,
                'credit_hour_system' => true,
                'teaching_days_per_week' => 5,
                'period_duration' => 60,
                'max_periods_per_day' => 4,
                'labs_on_separate_days' => true,
                'preferred_lab_days' => 'Thursday,Friday',
                'attendance_threshold' => 75,
            ];
        });
    }
}
