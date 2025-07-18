<?php

namespace Database\Seeders;

use App\Models\AcademicSession;
use App\Models\StudyLevel;
use Illuminate\Database\Seeder;

class StudyLevelSeeder extends Seeder
{
    public function run(): void
    {
        // Common predefined study levels
        $studyLevelNames = [
            'Intermediate',
            'ADP (Associate Degree Program)',
            'BS (Bachelor of Science)',
            'MS (Master of Science)',
        ];

        // Fetch all academic sessions from DB
        $academicSessions = AcademicSession::all();
        foreach ($academicSessions as $academicSession) {
            foreach ($studyLevelNames as $name) {
                StudyLevel::create([
                    'name' => $name,
                    'academic_session_id' => $academicSession->id,
                    'created_by' => 1, // You can make this dynamic later
                    'updated_by' => 1,
                ]);
            }
        }
    }
}
