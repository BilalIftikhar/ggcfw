<?php

    namespace Database\Seeders;

    use App\Models\Course;
    use App\Models\CourseSection;
    use App\Models\Program;
    use Illuminate\Database\Seeder;
    use Illuminate\Support\Facades\DB;
    use Illuminate\Support\Str;

    class CoursesSeeder extends Seeder
    {
        protected array $courseDefinitions = [
            'FSc Pre-Medical' => [
                'First (1st) Year' => [
                    ['name' => 'English', 'code' => 'FSC-ENG-101'],
                    ['name' => 'Urdu', 'code' => 'FSC-URD-101'],
                    ['name' => 'Islamic Studies/Ethics', 'code' => 'FSC-ISL-101'],
                    ['name' => 'Physics', 'code' => 'FSC-PHY-101', 'has_lab' => true],
                    ['name' => 'Chemistry', 'code' => 'FSC-CHM-101', 'has_lab' => true],
                    ['name' => 'Biology', 'code' => 'FSC-BIO-101', 'has_lab' => true],
                    ['name' => 'Pakistan Studies', 'code' => 'FSC-PKS-101'],
                ],
                'Second (2nd) Year' => [
                    ['name' => 'English', 'code' => 'FSC-ENG-201'],
                    ['name' => 'Urdu', 'code' => 'FSC-URD-201'],
                    ['name' => 'Islamic Studies/Ethics', 'code' => 'FSC-ISL-201'],
                    ['name' => 'Physics', 'code' => 'FSC-PHY-201', 'has_lab' => true],
                    ['name' => 'Chemistry', 'code' => 'FSC-CHM-201', 'has_lab' => true],
                    ['name' => 'Biology', 'code' => 'FSC-BIO-201', 'has_lab' => true],
                    ['name' => 'Pakistan Studies', 'code' => 'FSC-PKS-201'],
                ],
            ],
            'FSc Pre-Engineering' => [
                'First (1st) Year' => [
                    ['name' => 'English', 'code' => 'FSE-ENG-101'],
                    ['name' => 'Urdu', 'code' => 'FSE-URD-101'],
                    ['name' => 'Islamic Studies/Ethics', 'code' => 'FSE-ISL-101'],
                    ['name' => 'Physics', 'code' => 'FSE-PHY-101', 'has_lab' => true],
                    ['name' => 'Chemistry', 'code' => 'FSE-CHM-101', 'has_lab' => true],
                    ['name' => 'Mathematics', 'code' => 'FSE-MTH-101'],
                    ['name' => 'Pakistan Studies', 'code' => 'FSE-PKS-101'],
                ],
                'Second (2nd) Year' => [
                    ['name' => 'English', 'code' => 'FSE-ENG-201'],
                    ['name' => 'Urdu', 'code' => 'FSE-URD-201'],
                    ['name' => 'Islamic Studies/Ethics', 'code' => 'FSE-ISL-201'],
                    ['name' => 'Physics', 'code' => 'FSE-PHY-201', 'has_lab' => true],
                    ['name' => 'Chemistry', 'code' => 'FSE-CHM-201', 'has_lab' => true],
                    ['name' => 'Mathematics', 'code' => 'FSE-MTH-201'],
                    ['name' => 'Pakistan Studies', 'code' => 'FSE-PKS-201'],
                ],
            ],
            'BS Computer Science' => [
                'First (1st) Semester' => [
                    ['name' => 'Programming Fundamentals', 'code' => 'BSCS-101', 'has_lab' => true],
                    ['name' => 'Introduction to Computing', 'code' => 'BSCS-102', 'has_lab' => true],
                    ['name' => 'English Composition', 'code' => 'BSCS-ENG-101'],
                    ['name' => 'Calculus and Analytical Geometry', 'code' => 'BSCS-MTH-101'],
                    ['name' => 'Pakistan Studies', 'code' => 'BSCS-PKS-101'],
                ],
                'Second (2nd) Semester' => [
                    ['name' => 'Object Oriented Programming', 'code' => 'BSCS-201', 'has_lab' => true],
                    ['name' => 'Digital Logic Design', 'code' => 'BSCS-202', 'has_lab' => true],
                    ['name' => 'Communication Skills', 'code' => 'BSCS-ENG-201'],
                    ['name' => 'Linear Algebra', 'code' => 'BSCS-MTH-201'],
                    ['name' => 'Islamic Studies', 'code' => 'BSCS-ISL-201'],
                ],
                'Third (3rd) Semester' => [
                    ['name' => 'Data Structures', 'code' => 'BSCS-301', 'has_lab' => true],
                    ['name' => 'Computer Organization & Assembly', 'code' => 'BSCS-302', 'has_lab' => true],
                    ['name' => 'Discrete Structures', 'code' => 'BSCS-303'],
                    ['name' => 'Probability & Statistics', 'code' => 'BSCS-MTH-301'],
                    ['name' => 'Professional Ethics', 'code' => 'BSCS-HUM-301'],
                ],
                'Fourth (4th) Semester' => [
                    ['name' => 'Database Systems', 'code' => 'BSCS-401', 'has_lab' => true],
                    ['name' => 'Operating Systems', 'code' => 'BSCS-402', 'has_lab' => true],
                    ['name' => 'Software Engineering', 'code' => 'BSCS-403'],
                    ['name' => 'Theory of Automata', 'code' => 'BSCS-404'],
                    ['name' => 'Numerical Computing', 'code' => 'BSCS-MTH-401'],
                ],
                'Fifth (5th) Semester' => [
                    ['name' => 'Computer Networks', 'code' => 'BSCS-501', 'has_lab' => true],
                    ['name' => 'Web Engineering', 'code' => 'BSCS-502', 'has_lab' => true],
                    ['name' => 'Design & Analysis of Algorithms', 'code' => 'BSCS-503'],
                    ['name' => 'Artificial Intelligence', 'code' => 'BSCS-504', 'has_lab' => true],
                    ['name' => 'Technical Writing', 'code' => 'BSCS-ENG-501'],
                ],
                'Sixth (6th) Semester' => [
                    ['name' => 'Computer Architecture', 'code' => 'BSCS-601'],
                    ['name' => 'Compiler Construction', 'code' => 'BSCS-602', 'has_lab' => true],
                    ['name' => 'Mobile Application Development', 'code' => 'BSCS-603', 'has_lab' => true],
                    ['name' => 'Cloud Computing', 'code' => 'BSCS-604', 'has_lab' => true],
                    ['name' => 'Software Project Management', 'code' => 'BSCS-605'],
                ],
                'Seventh (7th) Semester' => [
                    ['name' => 'Final Year Project I', 'code' => 'BSCS-701', 'has_lab' => true],
                    ['name' => 'Information Security', 'code' => 'BSCS-702'],
                    ['name' => 'Machine Learning', 'code' => 'BSCS-703', 'has_lab' => true],
                    ['name' => 'Data Mining', 'code' => 'BSCS-704', 'has_lab' => true],
                    ['name' => 'Professional Practices', 'code' => 'BSCS-705'],
                ],
                'Eighth (8th) Semester' => [
                    ['name' => 'Final Year Project II', 'code' => 'BSCS-801', 'has_lab' => true],
                    ['name' => 'Deep Learning', 'code' => 'BSCS-802', 'has_lab' => true],
                    ['name' => 'Parallel & Distributed Computing', 'code' => 'BSCS-803'],
                    ['name' => 'Big Data Analytics', 'code' => 'BSCS-804', 'has_lab' => true],
                    ['name' => 'Human Computer Interaction', 'code' => 'BSCS-805'],
                ],
            ],
            'ADP Computer Science' => [
                'First (1st) Semester' => [
                    ['name' => 'Programming Fundamentals', 'code' => 'ADP-CS-101', 'has_lab' => true],
                    ['name' => 'Introduction to Computing', 'code' => 'ADP-CS-102', 'has_lab' => true],
                    ['name' => 'English Composition', 'code' => 'ADP-ENG-101'],
                    ['name' => 'Basic Mathematics', 'code' => 'ADP-MTH-101'],
                    ['name' => 'Islamic Studies', 'code' => 'ADP-ISL-101'],
                ],
                'Second (2nd) Semester' => [
                    ['name' => 'Object Oriented Programming', 'code' => 'ADP-CS-201', 'has_lab' => true],
                    ['name' => 'Web Development', 'code' => 'ADP-CS-202', 'has_lab' => true],
                    ['name' => 'Communication Skills', 'code' => 'ADP-ENG-201'],
                    ['name' => 'Discrete Mathematics', 'code' => 'ADP-MTH-201'],
                    ['name' => 'Pakistan Studies', 'code' => 'ADP-PKS-201'],
                ],
                'Third (3rd) Semester' => [
                    ['name' => 'Data Structures', 'code' => 'ADP-CS-301', 'has_lab' => true],
                    ['name' => 'Database Systems', 'code' => 'ADP-CS-302', 'has_lab' => true],
                    ['name' => 'Software Engineering', 'code' => 'ADP-CS-303'],
                    ['name' => 'Computer Networks', 'code' => 'ADP-CS-304', 'has_lab' => true],
                    ['name' => 'Technical Writing', 'code' => 'ADP-ENG-301'],
                ],
                'Fourth (4th) Semester' => [
                    ['name' => 'Mobile App Development', 'code' => 'ADP-CS-401', 'has_lab' => true],
                    ['name' => 'Operating Systems', 'code' => 'ADP-CS-402', 'has_lab' => true],
                    ['name' => 'Project', 'code' => 'ADP-CS-403', 'has_lab' => true],
                    ['name' => 'Professional Ethics', 'code' => 'ADP-HUM-401'],
                    ['name' => 'Information Security', 'code' => 'ADP-CS-404'],
                ],
            ],
        ];

        public function run(): void
        {
            $programs = Program::with('classes')->get();
            //DB::table('courses')->truncate();
            DB::table('courses')->delete();
            DB::statement('ALTER TABLE courses AUTO_INCREMENT = 1');

            foreach ($programs as $program) {
                // Debug output to see all program names
                echo "Processing Program: ID {$program->id} - {$program->name}\n";

                // More flexible matching - look for "ADP" anywhere in the name
                $baseProgramName = collect(array_keys($this->courseDefinitions))
                    ->first(function($key) use ($program) {
                        // Try exact match first
                        if (trim($program->name) === $key) return true;

                        // Then try case-insensitive contains
                        return Str::contains(Str::lower($program->name), Str::lower($key)) ||
                            Str::contains(Str::lower($key), Str::lower($program->name));
                    });

                // Debug output for matching
                echo "Matched Definition: " . ($baseProgramName ?? 'NONE') . "\n";

                $programCourses = $baseProgramName ? $this->courseDefinitions[$baseProgramName] : [];

                foreach ($program->classes as $class) {
                    // More flexible class name matching
                    $classCourses = collect($programCourses)
                        ->first(function($courses, $className) use ($class) {
                            return Str::contains(Str::lower($class->name), Str::lower($className)) ||
                                Str::contains(Str::lower($className), Str::lower($class->name));
                        }, []);

                    // Debug output for classes
                    echo "Processing Class: {$class->name} - Found " . count($classCourses) . " courses\n";

                    foreach ($classCourses as $courseData) {
                        $isHigherEducation = Str::contains($program->name, ['BS', 'ADP', 'Bachelor', 'Master', 'PhD']);

                        $baseAttributes = Course::factory()
                            ->{$isHigherEducation ? 'higherEducation' : 'intermediate'}()
                            ->raw();

                        if (!empty($courseData['has_lab'])) {
                            $baseAttributes = array_merge($baseAttributes, Course::factory()->withLab()->raw());
                        }

                        Course::updateOrCreate(
                            [
                                'code' => $courseData['code'],
                                'program_id' => $program->id // Also include program_id in unique check
                            ],
                            array_merge($baseAttributes, [
                                'name' => $courseData['name'],
                                'program_id' => $program->id,
                                'class_id' => $class->id,
                            ])
                        );
                    }
                }
            }
        }


    }
