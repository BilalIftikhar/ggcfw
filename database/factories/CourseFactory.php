<?php

namespace Database\Factories;

use App\Models\Course;
use Illuminate\Database\Eloquent\Factories\Factory;

class CourseFactory extends Factory
{
    protected $model = Course::class;

    public function definition(): array
    {
        return [
            'is_active' => true,
            'is_mandatory' => true,
            'no_of_sections' => fake()->numberBetween(2, 4),
            'students_per_section' => fake()->numberBetween(35, 50),
            'created_by' => 1,
            'updated_by' => 1,
        ];
    }

    public function intermediate()
    {
        return $this->state(function (array $attributes) {
            return [
                'credit_hours' => 0,
                'required_minutes_theory_weekly' => fake()->numberBetween(100, 250),
                'required_minutes_lab_weekly' => 0,
            ];
        });
    }

    public function higherEducation()
    {
        return $this->state(function (array $attributes) {
            return [
                'credit_hours' => 3,
                'required_minutes_theory_weekly' => fake()->numberBetween(100, 250),
                'required_minutes_lab_weekly' => 0,
            ];
        });
    }

    public function withLab()
    {
        return $this->state(function (array $attributes) {
            $creditHours = $attributes['credit_hours'] ?? 0;
            $isHigherEd = $creditHours === 3;

            return [
                'has_lab' => true,
                'lab_credit_hours' => 1,
                'required_minutes_lab_weekly' => $isHigherEd ? 180 : 135,
                'requires_continuous_slots' => true,
            ];
        });
    }
}
