<?php

namespace Database\Seeders;

use App\Models\Teacher;
use App\Models\User;
use App\Models\CourseSection;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;

class TeacherSeeder extends Seeder
{
    public function run(): void
    {
        $teacherCount = 10; // Change this number to create more/less teachers
        $teacherIds = [];

        DB::transaction(function () use ($teacherCount, &$teacherIds) {
            // Get the first teaching role
            $teachingRole = Role::where('is_teaching', true)->first();

            if (!$teachingRole) {
                throw new \Exception('No role with is_teaching = true found.');
            }

            for ($i = 1; $i <= $teacherCount; $i++) {
                $name = fake()->name;
                $fatherName = fake()->name('male');
                $cnic = fake()->unique()->numerify('#####-#######-#');
                $email = Str::slug($name) . $i . '@example.com';

                // Create user account
                $user = User::create([
                    'name' => $name . $i,
                    'email' => $email,
                    'username' => Str::slug($name) . $i,
                    'password' => Hash::make('password'),
                ]);

                $user->assignRole($teachingRole->name);


                // Create teacher
                $teacher = Teacher::create([
                    'cnic' => $cnic,
                    'seniority_no' => str_pad($i, 4, '0', STR_PAD_LEFT),
                    'name' => $name,
                    'father_name' => $fatherName,
                    'designation' => fake()->randomElement(['Lecturer', 'Assistant Professor', 'Associate Professor']),
                    'bps' => fake()->randomElement([17, 18, 19, 20]),
                    'dob' => fake()->date('Y-m-d', '-30 years'),
                    'domicile' => fake()->city,
                    'retirement_date' => fake()->date('Y-m-d', '+20 years'),
                    'subject' => fake()->randomElement(['Math', 'Physics', 'English', 'Biology', 'Computer Science']),
                    'qualification' => fake()->randomElement(['MSc', 'MPhil', 'PhD']),
                    'govt_entry_date' => fake()->date('Y-m-d', '-10 years'),
                    'employee_mode' => 'Regular',
                    'quota' => fake()->randomElement(['Open Merit', 'Women', 'Minority']),
                    'joining_date_adhoc_lecturer' => fake()->optional()->date('Y-m-d', '-10 years'),
                    'joining_date_regular_lecturer' => fake()->optional()->date('Y-m-d', '-8 years'),
                    'joining_date_assistant_prof' => fake()->optional()->date('Y-m-d', '-6 years'),
                    'joining_date_associate_prof' => fake()->optional()->date('Y-m-d', '-4 years'),
                    'joining_date_professor' => fake()->optional()->date('Y-m-d', '-2 years'),
                    'joining_date_principal' => fake()->optional()->date('Y-m-d'),
                    'qualifying_service' => '10.0.0',
                    'joining_date_present_station' => fake()->date('Y-m-d', '-1 years'),
                    'cadre' => fake()->randomElement(['General', 'Technical']),
                    'home_address' => fake()->address,
                    'work_contact' => fake()->phoneNumber,
                    'home_contact' => fake()->phoneNumber,
                    'is_active' => true,
                    'user_id' => $user->id,
                    'temporary_password' => base64_encode('password'),
                    'can_teach_labs' => fake()->boolean,
                    'max_lectures_per_day' => rand(2, 5),
                    'max_lectures_per_week' => rand(10, 25),
                    'working_status' => 'Working',
                    'created_by' => 1,
                    'updated_by' => 1,
                ]);

                $teacherIds[] = $teacher->id;
            }

            // Assign a random teacher to each CourseSection
            CourseSection::all()->each(function ($section) use ($teacherIds) {
                $section->teacher_id = collect($teacherIds)->random();
                $section->save();
            });
        });

        echo "Seeded {$teacherCount} teachers and assigned them to course sections.\n";
    }
}
