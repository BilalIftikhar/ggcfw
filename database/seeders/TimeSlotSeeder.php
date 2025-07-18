<?php

namespace Database\Seeders;

use App\Models\WorkingDay;
use App\Models\TimeSlot;
use Illuminate\Database\Seeder;

class TimeSlotSeeder extends Seeder
{
    public function run(): void
    {
        // Only fetch working days
        $workingDays = WorkingDay::where('is_working', true)->get();

        foreach ($workingDays as $workingDay) {
            $this->createTimeSlotsForDay($workingDay);
        }
    }

    protected function createTimeSlotsForDay(WorkingDay $workingDay): void
    {
        // Optional: Delete existing time slots for a clean re-seed
        TimeSlot::where('working_day_id', $workingDay->id)->delete();

        $slots = [];
        $breakIndex = null;

        if ($workingDay->day === 'Friday') {
            // Friday — shorter schedule
            $slots = [
                ['08:00', '08:40'],
                ['08:45', '09:25'],
                ['09:30', '10:10'],
                ['10:15', '10:45'], // Break
                ['10:50', '11:30'],
                ['13:35', '14:15'],
            ];
            $breakIndex = 3;
        } else {
            // Regular working days
            $slots = [
                ['08:00', '08:45'],
                ['08:50', '09:35'],
                ['09:40', '10:25'],
                ['10:30', '11:15'],
                ['11:20', '12:05'],
                ['12:10', '12:40'], // Break
                ['12:45', '13:30'],
                ['13:35', '14:20'],
            ];
            $breakIndex = 5;
        }

        foreach ($slots as $index => [$start, $end]) {
            TimeSlot::create([
                'name' => 'Slot ' . ($index + 1),
                'start_time' => $start,
                'end_time' => $end,
                'is_break' => $index === $breakIndex ? 1 : 0,
                'sort_order' => $index + 1,
                'working_day_id' => $workingDay->id,
            ]);
        }
    }
}
