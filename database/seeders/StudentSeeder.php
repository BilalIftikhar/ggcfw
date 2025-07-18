<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Faker\Factory as Faker;
use Spatie\Permission\Models\Role;
use App\Models\{
    Student, Program, ProgramClass, Course, Enrollment, EnrollmentDetail, CourseSection, User
};

class StudentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $faker = Faker::create();

        // Get a student role if login is needed
        $studentRole = Role::where('is_student', 1)->first();

        // Loop to generate multiple students
        for ($i = 0; $i < 100; $i++) {
            DB::beginTransaction();

            try {
                // Pick a random program with academic session loaded
                $program = Program::with('academicSession')->inRandomOrder()->first();

                if (!$program || !$program->academicSession) {
                    continue;
                }

                $programClass = ProgramClass::where('program_id', $program->id)
                    ->where('name', 'like', '%First%')
                    ->orderBy('id')
                    ->first();

                if (!$programClass) {
                    continue;
                }

                $student = Student::create([
                    'name' => $faker->name,
                    'cnic' => $faker->unique()->numerify('#####-#######-#'),
                    'father_name' => $faker->name('male'),
                    'father_cnic' => $faker->numerify('#####-#######-#'),
                    'gender' => $faker->randomElement(['female', 'transgender']),
                    'date_of_birth' => $faker->date(),
                    'religion' => $faker->randomElement(['Islam', 'Christianity', 'Hinduism', 'Other']),
                    'blood_group' => $faker->randomElement(['A+', 'B+', 'O+', 'AB+']),
                    'student_contact' => $faker->phoneNumber,
                    'whatsapp_no' => $faker->phoneNumber,
                    'parent_contact' => $faker->phoneNumber,
                    'email' => $faker->unique()->safeEmail,

                    'registration_number' => strtoupper(Str::random(6)),
                    'roll_number' => strtoupper(Str::random(6)),
                    'status' => 'studying',
                    'academic_session_id' => $program->academicSession->id,
                    'study_level_id' => $program->study_level_id,
                    'program_id' => $program->id,
                   // 'examination_session_id' => 1, // or random if needed

                    'is_hafiz' => $faker->boolean,
                    'father_job' => $faker->boolean,
                    'father_department' => $faker->company,
                    'father_designation' => $faker->jobTitle,
                    'created_by' => 1,
                    'is_active' => true,
                ]);

                $enrollment = Enrollment::create([
                    'student_id' => $student->id,
                    'program_id' => $program->id,
                    'program_class_id' => $programClass->id,
                    'academic_session_id' => $program->academicSession->id,
                    'examination_session_id' => 1, // or set accordingly
                    'enrolled_on' => now(),
                    'status' => 'enrolled',
                    'created_by' => 1,
                ]);

                // Enroll in courses
                $mandatoryCourses = $programClass->courses()
                    ->where('is_active', true)
                    ->where('is_mandatory', true)
                    ->get();


                foreach ($mandatoryCourses as $course) {
                    $section = $this->assignCourseSection($course);
                    EnrollmentDetail::create([
                        'enrollment_id' => $enrollment->id,
                        'course_id' => $course->id,
                        'is_mandatory' => true,
                        'course_section_id' => optional($section)->id,
                        'status' => 'enrolled',
                    ]);

                    if ($section) {
                        $section->increment('no_of_students_enrolled');
                    }
                }

                // Optional login
                if ($studentRole) {
                    $username = strtolower(Str::slug($student->name)) . rand(1000, 9999);
                    $password = 'student123';

                    $user = User::create([
                        'username' => $username,
                        'email' => $student->email,
                        'password' => Hash::make($password),
                        'name' => $student->name,
                    ]);

                    $user->assignRole($studentRole->name);

                    $student->update([
                        'user_id' => $user->id,
                        'temporary_password' => base64_encode($password),
                    ]);
                }

                DB::commit();
            } catch (\Exception $e) {
                DB::rollBack();
                dump("Failed to create student: " . $e->getMessage());
            }
        }
    }

    protected function assignCourseSection(Course $course): ?CourseSection
    {
        $sections = CourseSection::active()
            ->where('course_id', $course->id)
            ->where('program_id', $course->program_id)
            ->get();

        foreach ($sections as $section) {
            if ($section->no_of_students_allowed == 0 ||
                $section->no_of_students_enrolled < $section->no_of_students_allowed) {
                return $section;
            }
        }

        $sectionCount = $sections->count();
        $maxSections = $course->no_of_sections;

        if ($maxSections == 0 || $sectionCount < $maxSections) {
            return CourseSection::create([
                'name' => $course->name . ' - Section ' . ($sectionCount + 1),
                'description' => 'Auto-generated',
                'program_id' => $course->program_id,
                'course_id' => $course->id,
                'teacher_id' => $course->teacher_id,
                'is_active' => true,
                'no_of_students_allowed' => $course->students_per_section ?? 30,
                'no_of_students_enrolled' => 0,
                'required_minutes_theory_weekly' => $course->required_minutes_theory_weekly,
                'required_minutes_lab_weekly' => $course->required_minutes_lab_weekly,
                'credit_hours' => $course->credit_hours,
                'lab_credit_hours' => $course->lab_credit_hours,
                'has_lab' => $course->has_lab,
                'requires_continuous_slots' => $course->requires_continuous_slots,
                'created_by' => 1,
                'updated_by' => 1,
            ]);

        }

        return null;
    }
}
