<?php

namespace Database\Seeders;

use App\Models\AcademicSession;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class AcademicSessionSeeder extends Seeder
{
    public function run(): void
    {
        $totalSessionsToCreate = 2; // 👈 Change this to generate more or fewer sessions
        $currentDate = Carbon::now();

        // Determine the current or next academic year start (based on if before or after September)
        $currentAcademicYear = $currentDate->month >= 9
            ? $currentDate->year
            : $currentDate->year - 1;

        // Start one session in the past
        $startingYear = $currentAcademicYear - 1;

        for ($i = 0; $i < $totalSessionsToCreate; $i++) {
            $year = $startingYear + $i;
            $startDate = Carbon::create($year, 9, 1); // Academic year starts in September
            $endDate = Carbon::create($year + 1, 8, 31); // Ends next year in August
            $admissionEndDate = Carbon::create($year, 8, 15); // Admission ends mid-August

            AcademicSession::create([
                'name' => $year . '/' . ($year + 1) . ' Academic Session',
                'start_date' => $startDate,
                'end_date' => $endDate,
                'admission_end_date' => $admissionEndDate,
                'allow_admission' => rand(0, 1),
                'is_active' => $startDate->isSameYear($currentDate) || $startDate->isFuture(),
                'created_by' => 1,
                'updated_by' => 1,
            ]);
        }
    }
}
