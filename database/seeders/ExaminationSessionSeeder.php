<?php

namespace Database\Seeders;

use App\Models\ExaminationSession;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class ExaminationSessionSeeder extends Seeder
{
    public function run(): void
    {
        $totalSessionsToCreate = 2; // 👈 change this to any number you want
        $currentDate = Carbon::now();

        // Start from one session before the current date
        $startingSessionDate = $this->getPreviousSessionStartDate($currentDate);

        for ($i = 0; $i < $totalSessionsToCreate; $i++) {
            $sessionStart = $startingSessionDate->copy();
            $sessionEnd = $this->getSessionEndDate($sessionStart);

            $title = $this->getSessionTitle($sessionStart, $sessionEnd);
            $description = "Regular examination session from " . $sessionStart->format('F Y') . " to " . $sessionEnd->format('F Y');

            ExaminationSession::create([
                'title' => $title,
                'start_date' => $sessionStart,
                'end_date' => $sessionEnd,
                'is_examination_taken' => $sessionEnd->isPast(),
                'is_active' => !$sessionEnd->isPast(),
                'is_running' => $sessionStart->isPast() && !$sessionEnd->isPast(),
                'description' => $description,
                'created_by' => 1,
                'updated_by' => 1,
            ]);

            // Move to next session (6 months ahead)
            $startingSessionDate->addMonths(6);
        }
    }

    private function getPreviousSessionStartDate(Carbon $date): Carbon
    {
        $month = $date->month;

        if ($month >= 10 || $month <= 3) {
            // We're currently in October–March session
            return $month >= 10
                ? Carbon::create($date->year, 4, 1)->subMonths(6) // Go to previous April–Sept
                : Carbon::create($date->year - 1, 10, 1);          // Last year's Oct–Mar
        } else {
            // We're in April–September session
            return Carbon::create($date->year, 10, 1)->subYear(); // Previous October–March
        }
    }

    private function getSessionEndDate(Carbon $startDate): Carbon
    {
        if ($startDate->month === 10) {
            return $startDate->copy()->addMonths(5)->endOfMonth(); // Oct–Mar
        } else {
            return $startDate->copy()->addMonths(5)->endOfMonth(); // Apr–Sep
        }
    }

    private function getSessionTitle(Carbon $start, Carbon $end): string
    {
        return $start->format('F Y') . " - " . $end->format('F Y');
    }
}
