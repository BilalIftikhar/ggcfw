<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        //        User::factory()->create([
        //            'name' => 'Test User',
        //            'email' => 'test@example.com',
        //        ]);
        $this->call([
        SetupSeeder::class,                 // ✅ Must come first
        AcademicSessionSeeder::class,
        CoursesSeeder::class,
        ExaminationSessionSeeder::class,
        ProgramSeeder::class,
        RoomSeeder::class,
        StudentSeeder::class,
        StudyLevelSeeder::class,
        TimeSlotSeeder::class,
    ]);
    }
}
