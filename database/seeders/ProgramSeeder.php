<?php

namespace Database\Seeders;

use App\Models\Program;
use App\Models\ProgramClass;
use App\Models\StudyLevel;
use Illuminate\Database\Seeder;

class ProgramSeeder extends Seeder
{
    protected array $programDefinitions = [
        'Intermediate' => [
            'FSc Pre-Medical' => [
                'config' => [
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
                ],
                'groups' => ['A', 'B', 'C'],
                'shifts' => ['Morning', 'Evening'],
            ],
            'FSc Pre-Engineering' => [
                'config' => [
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
                ],
                'groups' => ['A', 'B', 'C'],
                'shifts' => ['Morning', 'Evening'],
            ],
            'ICS' => [
                'config' => [
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
                ],
                'groups' => ['A', 'B'],
                'shifts' => ['Morning'],
            ],
        ],
        'ADP (Associate Degree Program)' => [
            'ADP Computer Science' => [
                'config' => [
                    'is_semester' => true,
                    'number_of_years' => 2,
                    'number_of_semesters' => 4,
                    'credit_hour_system' => true,
                    'teaching_days_per_week' => 5,
                    'period_duration' => 60,
                    'max_periods_per_day' => 4,
                    'labs_on_separate_days' => true,
                    'preferred_lab_days' => 'Thursday,Friday',
                    'attendance_threshold' => 75,
                ],
                'groups' => ['A', 'B'],
                'shifts' => ['Morning', 'Evening'],
            ],
            'ADP Mathematics' => [
                'config' => [
                    'is_semester' => true,
                    'number_of_years' => 2,
                    'number_of_semesters' => 4,
                    'credit_hour_system' => true,
                    'teaching_days_per_week' => 5,
                    'period_duration' => 60,
                    'max_periods_per_day' => 4,
                    'labs_on_separate_days' => false,
                    'preferred_lab_days' => null,
                    'attendance_threshold' => 75,
                ],
                'groups' => ['A'],
                'shifts' => ['Morning'],
            ],
        ],
        'BS (Bachelor of Science)' => [
            'BS Computer Science' => [
                'config' => [
                    'is_semester' => true,
                    'number_of_years' => 4,
                    'number_of_semesters' => 8,
                    'credit_hour_system' => true,
                    'teaching_days_per_week' => 5,
                    'period_duration' => 60,
                    'max_periods_per_day' => 4,
                    'labs_on_separate_days' => true,
                    'preferred_lab_days' => 'Thursday,Friday',
                    'attendance_threshold' => 75,
                ],
                'groups' => ['A', 'B', 'C'],
                'shifts' => ['Morning', 'Evening'],
            ],
            'BS Mathematics' => [
                'config' => [
                    'is_semester' => true,
                    'number_of_years' => 4,
                    'number_of_semesters' => 8,
                    'credit_hour_system' => true,
                    'teaching_days_per_week' => 5,
                    'period_duration' => 60,
                    'max_periods_per_day' => 4,
                    'labs_on_separate_days' => false,
                    'preferred_lab_days' => null,
                    'attendance_threshold' => 75,
                ],
                'groups' => ['A', 'B'],
                'shifts' => ['Morning'],
            ],
            'BS Physics' => [
                'config' => [
                    'is_semester' => true,
                    'number_of_years' => 4,
                    'number_of_semesters' => 8,
                    'credit_hour_system' => true,
                    'teaching_days_per_week' => 5,
                    'period_duration' => 60,
                    'max_periods_per_day' => 4,
                    'labs_on_separate_days' => true,
                    'preferred_lab_days' => 'Thursday,Friday',
                    'attendance_threshold' => 75,
                ],
                'groups' => ['A'],
                'shifts' => ['Morning'],
            ],
            'BS Chemistry' => [
                'config' => [
                    'is_semester' => true,
                    'number_of_years' => 4,
                    'number_of_semesters' => 8,
                    'credit_hour_system' => true,
                    'teaching_days_per_week' => 5,
                    'period_duration' => 60,
                    'max_periods_per_day' => 4,
                    'labs_on_separate_days' => true,
                    'preferred_lab_days' => 'Thursday,Friday',
                    'attendance_threshold' => 75,
                ],
                'groups' => ['A'],
                'shifts' => ['Morning'],
            ],
        ],
    ];

    public function run(): void
    {
        $studyLevels = StudyLevel::all();

        foreach ($studyLevels as $studyLevel) {
            $programs = $this->programDefinitions[$studyLevel->name] ?? [];

            foreach ($programs as $programName => $settings) {
                $config = $settings['config'];

                $program = Program::create([
                    'name' => $programName,
                    'is_semester' => $config['is_semester'],
                    'number_of_years' => $config['number_of_years'],
                    'number_of_semesters' => $config['number_of_semesters'],
                    'is_active' => true,
                    'admission_enabled' => $studyLevel->academicSession->allow_admission,
                    'study_level_id' => $studyLevel->id,
                    'academic_session_id' => $studyLevel->academic_session_id,
                    'credit_hour_system' => $config['credit_hour_system'],
                    'teaching_days_per_week' => $config['teaching_days_per_week'],
                    'period_duration' => $config['period_duration'],
                    'max_periods_per_day' => $config['max_periods_per_day'],
                    'labs_on_separate_days' => $config['labs_on_separate_days'],
                    'preferred_lab_days' => $config['preferred_lab_days'],
                    'attendance_threshold' => $config['attendance_threshold'],
                    'created_by' => 1,
                    'updated_by' => 1,
                ]);

                $this->createProgramClasses($program);
            }
        }
    }


    protected function buildProgramName($baseName, $shift, $group): string
    {
        return trim("{$baseName} {$shift} {$group}");
    }

    protected function createProgramClasses(Program $program): void
    {
        $count = $program->is_semester ? $program->number_of_semesters : $program->number_of_years;

        $prefixes = [
            1 => 'First', 2 => 'Second', 3 => 'Third', 4 => 'Fourth',
            5 => 'Fifth', 6 => 'Sixth', 7 => 'Seventh', 8 => 'Eighth',
            9 => 'Ninth', 10 => 'Tenth', 11 => 'Eleventh', 12 => 'Twelfth'
        ];

        for ($i = 1; $i <= $count; $i++) {
            $word = $prefixes[$i] ?? "{$i}th";
            $ordinal = match ($i) {
                1 => '1st',
                2 => '2nd',
                3 => '3rd',
                default => $i . 'th',
            };

            $suffix = $program->is_semester ? 'Semester' : 'Year';
            $className = "{$word} ({$ordinal}) {$suffix}";

            ProgramClass::create([
                'name' => $className,
                'program_id' => $program->id,
                'is_active' => $i === 1, // First class is active, rest are inactive
                'created_by' => 1,
                'updated_by' => 1,
            ]);
        }
    }
}
