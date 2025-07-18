<?php

namespace App\Services;

use App\Models\Program;
use App\Models\CourseSection;
use App\Models\TimeSlot;
use App\Models\Room;
use App\Models\Teacher;
use App\Models\Timetable;
use App\Models\ScheduleLog;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class ScheduleGenerator
{
    protected int $examinationSessionId;

    public function __construct(int $examinationSessionId)
    {
        $this->examinationSessionId = $examinationSessionId;
    }

    public function generateForProgram(Program $program): void
    {
        if (!$program->credit_hour_system) {
            return; // Only process credit-hour–based programs
        }

        $classes = $program->courses()->distinct('class_id')->pluck('class_id');

        foreach ($classes as $classId) {
            $this->scheduleClassSections($program->id, $classId);
        }
        $this->retryUnscheduledSections();
    }

    protected function scheduleClassSections(int $programId, int $classId): void
    {
        $sections = CourseSection::with(['teachers', 'course'])
            ->where('program_id', $programId)
            ->whereHas('course', fn($q) => $q->where('class_id', $classId))
            ->get();
        foreach ($sections as $section) {
            $this->scheduleSection($section);
        }
    }

    protected function scheduleSection(CourseSection $section): void
    {
        if ($this->isSectionFullyScheduled($section)) {
            return;
        }

        if ($section->teacher_id === null) {
            Log::info("Scheduling section without assigned teacher", ['section_id' => $section->id]);
        }

        if ($section->required_minutes_theory_weekly > 0) {
            $this->scheduleTheory($section);
        }

        if ($section->has_lab && $section->required_minutes_lab_weekly > 0) {
            $this->scheduleLab($section);
        }
    }

    protected function isSectionFullyScheduled(CourseSection $section): bool
    {
        $scheduled = Timetable::where('course_section_id', $section->id)
            ->where('examination_session_id', $this->examinationSessionId)
            ->get();
        $totalTheory = $scheduled->where('is_lab', false)->sum(fn($t) => $this->getSlotDurationInMinutes($t->time_slot_id));
        $totalLab = $scheduled->where('is_lab', true)->sum(fn($t) => $this->getSlotDurationInMinutes($t->time_slot_id));
        return $totalTheory >= $section->required_minutes_theory_weekly && $totalLab >= $section->required_minutes_lab_weekly;
    }

    protected function scheduleTheory(CourseSection $section): void
    {
        $minutesRemaining = $section->required_minutes_theory_weekly;

        Log::info("Scheduling THEORY for section ID {$section->id}", [
            'initial_required_minutes' => $minutesRemaining,
        ]);

        $timeSlots = TimeSlot::with('workingDay')
            ->whereHas('workingDay', fn($q) => $q->where('is_working', true))
            ->where('is_break', false)
            ->orderBy('start_time')
            ->get()
            ->sortBy(fn($slot) =>
                array_search($slot->workingDay->day, ['monday','tuesday','wednesday', 'thursday', 'saturday', 'friday']) ?? 999
            )
            ->values();

        $usedDays = [];
        $usedSlots = collect();

        foreach ($timeSlots as $slot) {
            if ($minutesRemaining <= 0) break;

            $day = $slot->workingDay->day;
            if (in_array($day, $usedDays)) continue;
            if ($usedSlots->contains(fn($s) => $this->areAdjacent($s, $slot))) continue;
            if ($this->isSlotOccupiedByAnotherCourse($section->course_id, $slot->id)) continue;

            $room = $this->findAvailableRoom($slot->id, 'lecture_hall');
            if (!$room) {
                $this->logUnavailability($section, $slot->id, 'No available lecture hall');
                continue;
            }

            if ($section->teacher_id !== null && !$this->isTeacherAvailable($section->teacher_id, $slot->id)) {
                $alternativeSlots = $this->getAlternativeSlots($slot, $timeSlots);

                foreach ($alternativeSlots as $altSlot) {
                    if ($this->isSlotOccupiedByAnotherCourse($section->course_id, $altSlot->id)) continue;
                    if ($section->teacher_id !== null && !$this->isTeacherAvailable($section->teacher_id, $altSlot->id)) continue;

                    $altRoom = $this->findAvailableRoom($altSlot->id, 'lecture_hall');
                    if (!$altRoom) continue;

                    if ($this->createTimetableEntry($section, $altSlot, $altRoom->id, false)) {
                        $usedDays[] = $altSlot->workingDay->day;
                        $usedSlots->push($altSlot);
                        $minutesRemaining -= $this->getSlotDurationInMinutes($altSlot->id);

                        Log::info("Scheduled THEORY (via fallback slot)", [
                            'section_id' => $section->id,
                            'slot_id' => $altSlot->id,
                            'slot_day' => $altSlot->workingDay->day,
                            'fallback_from' => $slot->id,
                            'remaining_minutes' => $minutesRemaining,
                        ]);
                        break 1;
                    }
                }

                continue;
            }

            if ($this->createTimetableEntry($section, $slot, $room->id, false)) {
                $usedDays[] = $day;
                $usedSlots->push($slot);
                $minutesRemaining -= $this->getSlotDurationInMinutes($slot->id);

                Log::info("Scheduled THEORY (spread phase)", [
                    'section_id' => $section->id,
                    'slot_id' => $slot->id,
                    'slot_day' => $day,
                    'remaining_minutes' => $minutesRemaining,
                ]);
            }
        }

        // Fallback phase
        if ($minutesRemaining > 0) {
            foreach ($timeSlots as $slot) {
                if ($minutesRemaining <= 0) break;
                $day = $slot->workingDay->day;
                if ($usedSlots->contains(fn($s) => $s->id === $slot->id)) continue;

                $existing = Timetable::where('course_section_id', $section->id)
                    ->where('day_of_week', $day)
                    ->where('examination_session_id', $this->examinationSessionId)
                    ->count();
                if ($existing >= 2) continue;

                if ($this->isSlotOccupiedByAnotherCourse($section->course_id, $slot->id)) continue;

                $room = $this->findAvailableRoom($slot->id, 'lecture_hall');
                if (!$room) {
                    $this->logUnavailability($section, $slot->id, 'No available lecture hall');
                    continue;
                }

                if ($section->teacher_id !== null && !$this->isTeacherAvailable($section->teacher_id, $slot->id)) {
                    $alternativeSlots = $this->getAlternativeSlots($slot, $timeSlots);

                    foreach ($alternativeSlots as $altSlot) {
                        if ($this->isSlotOccupiedByAnotherCourse($section->course_id, $altSlot->id)) continue;
                        if ($section->teacher_id !== null && !$this->isTeacherAvailable($section->teacher_id, $altSlot->id)) continue;

                        $altRoom = $this->findAvailableRoom($altSlot->id, 'lecture_hall');
                        if (!$altRoom) continue;

                        if ($this->createTimetableEntry($section, $altSlot, $altRoom->id, false)) {
                            $usedSlots->push($altSlot);
                            $minutesRemaining -= $this->getSlotDurationInMinutes($altSlot->id);

                            Log::info("Scheduled THEORY (fallback with alternative slot)", [
                                'section_id' => $section->id,
                                'slot_id' => $altSlot->id,
                                'fallback_from' => $slot->id,
                                'remaining_minutes' => $minutesRemaining,
                            ]);
                            break 1;
                        }
                    }

                    continue;
                }

                if ($this->createTimetableEntry($section, $slot, $room->id, false)) {
                    $usedSlots->push($slot);
                    $minutesRemaining -= $this->getSlotDurationInMinutes($slot->id);

                    Log::info("Scheduled THEORY (fallback phase)", [
                        'section_id' => $section->id,
                        'slot_id' => $slot->id,
                        'slot_day' => $day,
                        'remaining_minutes' => $minutesRemaining,
                    ]);
                }
            }
        }

        if ($minutesRemaining > 0) {
            ScheduleLog::create([
                'course_section_id' => $section->id,
                'examination_session_id' => $this->examinationSessionId,
                'reason' => "Theory not fully scheduled. Remaining: {$minutesRemaining} minutes.",
            ]);
        }

        Log::info("Completed scheduling THEORY for section {$section->id}", [
            'final_remaining_minutes' => $minutesRemaining,
        ]);
    }

    protected function scheduleLab(CourseSection $section): void
    {
        if (!$section->has_lab || $section->required_minutes_lab_weekly <= 0) {
            return;
        }

        $minutesRemaining = $section->required_minutes_lab_weekly;

        Log::info("Scheduling LAB for section ID {$section->id}", [
            'requires_continuous_slots' => $section->requires_continuous_slots,
            'initial_required_minutes' => $minutesRemaining,
        ]);

        $preferredDayOrder = ['monday','tuesday', 'wednesday', 'thursday', 'friday', 'saturday'];

        $timeSlots = TimeSlot::with('workingDay')
            ->whereHas('workingDay', fn($q) => $q->where('is_working', true))
            ->where('is_break', false)
            ->orderBy('start_time')
            ->get()
            ->sortBy(fn($slot) =>
                array_search($slot->workingDay->day, $preferredDayOrder) ?? 999
            )
            ->values();

        $usedSlots = collect();
        $scheduled = false;

        if ($section->requires_continuous_slots) {
            $scheduled = $this->tryContinuousLab($section, $timeSlots, $usedSlots, $minutesRemaining);
        } else {
            $scheduled = $this->trySpreadLab($section, $timeSlots, $usedSlots, $minutesRemaining);
        }

        if (!$scheduled && $minutesRemaining > 0) {
            if ($section->requires_continuous_slots) {
                $scheduled = $this->trySpreadLab($section, $timeSlots, $usedSlots, $minutesRemaining);
            } else {
                $scheduled = $this->tryContinuousLab($section, $timeSlots, $usedSlots, $minutesRemaining);
            }
        }

        if ($minutesRemaining > 0) {
            ScheduleLog::create([
                'course_section_id' => $section->id,
                'examination_session_id' => $this->examinationSessionId,
                'reason' => "Lab not fully scheduled. Remaining: {$minutesRemaining} minutes. Possibly due to slot, teacher, or room conflicts.",
            ]);
        }

        Log::info("Completed scheduling LAB for section {$section->id}", [
            'final_remaining_minutes' => $minutesRemaining,
        ]);
    }

    protected function getSlotDurationInMinutes(int $slotId): int
    {
        $slot = TimeSlot::find($slotId);

        if (!$slot) return 0;

        $start = Carbon::parse($slot->start_time);
        $end = Carbon::parse($slot->end_time);
        return $start->diffInMinutes($end);
    }

    protected function createTimetableEntry(CourseSection $section, TimeSlot $slot, int $roomId, bool $isLab): bool
    {
        if (Timetable::where('course_section_id', $section->id)
            ->where('time_slot_id', $slot->id)
            ->where('examination_session_id', $this->examinationSessionId)
            ->exists()) {
            return false;
        }

        $program = Program::find($section->program_id);
        if (!$program) {
            return false;
        }

        Timetable::create([
            'examination_session_id' => $this->examinationSessionId,
            'academic_session_id' => $program->academic_session_id,
            'program_id' => $section->program_id,
            'program_class_id' => $section->course->class_id,
            'course_id' => $section->course_id,
            'course_section_id' => $section->id,
            'teacher_id' => $section->teacher_id, // Can be null
            'room_id' => $roomId,
            'time_slot_id' => $slot->id,
            'day_of_week' => $slot->workingDay->day,
            'is_lab' => $isLab,
        ]);

        return true;
    }

    protected function findAvailableRoom(int $slotId, string $roomType): ?Room
    {
        return Room::where('room_type', $roomType)
            ->whereDoesntHave('timetables', fn($q) =>
            $q->where('time_slot_id', $slotId)
                ->where('examination_session_id', $this->examinationSessionId)
            )
            ->inRandomOrder()
            ->first();
    }

    protected function isTeacherAvailable(?int $teacherId, int $slotId): bool
    {
        // If no teacher is assigned, consider them "available"
        if ($teacherId === null) {
            return true;
        }

        return !Timetable::where('teacher_id', $teacherId)
            ->where('time_slot_id', $slotId)
            ->where('examination_session_id', $this->examinationSessionId)
            ->exists();
    }

    protected function areAdjacent(TimeSlot $a, TimeSlot $b): bool
    {
        return $a->working_day_id === $b->working_day_id &&
            (
                abs(strtotime($a->start_time) - strtotime($b->end_time)) <= 300 ||
                abs(strtotime($b->start_time) - strtotime($a->end_time)) <= 300
            );
    }

    protected function isSlotOccupiedByAnotherCourse(int $courseId, int $slotId): bool
    {
        return Timetable::where('time_slot_id', $slotId)
            ->where('examination_session_id', $this->examinationSessionId)
            ->where('course_id', '!=', $courseId)
            ->exists();
    }

    protected function logUnavailability(CourseSection $section, int $slotId, string $reason): void
    {
        ScheduleLog::create([
            'course_section_id' => $section->id,
            'examination_session_id' => $this->examinationSessionId,
            'reason' => "{$reason} at slot ID: {$slotId}",
        ]);
    }

    protected function trySpreadLab(CourseSection $section, $timeSlots, $usedSlots, &$minutesRemaining): bool
    {
        foreach ($timeSlots as $slot) {
            if ($minutesRemaining <= 0) break;
            if ($usedSlots->contains(fn($s) => $s->id === $slot->id)) continue;
            if ($usedSlots->contains(fn($s) => $this->areAdjacent($s, $slot))) continue;

            if ($this->isSlotOccupiedByAnotherCourse($section->course_id, $slot->id)) continue;

            if ($section->teacher_id !== null && !$this->isTeacherAvailable($section->teacher_id, $slot->id)) {
                $fallbacks = $this->getAlternativeSlots($slot, $timeSlots);
                foreach ($fallbacks as $altSlot) {
                    if ($usedSlots->contains(fn($s) => $s->id === $altSlot->id)) continue;
                    if ($section->teacher_id !== null && !$this->isTeacherAvailable($section->teacher_id, $altSlot->id)) continue;
                    if ($this->isSlotOccupiedByAnotherCourse($section->course_id, $altSlot->id)) continue;

                    $altRoom = $this->findAvailableRoom($altSlot->id, 'lab');
                    if (!$altRoom) continue;

                    if ($this->createTimetableEntry($section, $altSlot, $altRoom->id, true)) {
                        $usedSlots->push($altSlot);
                        $slotMinutes = $this->getSlotDurationInMinutes($altSlot->id);
                        $minutesRemaining -= $slotMinutes;

                        Log::info("Scheduled LAB (spread fallback)", [
                            'section_id' => $section->id,
                            'slot_id' => $altSlot->id,
                            'from_slot' => $slot->id,
                            'slot_day' => $altSlot->workingDay->day,
                            'remaining_minutes' => $minutesRemaining,
                        ]);
                        break 1;
                    }
                }
                continue;
            }

            $room = $this->findAvailableRoom($slot->id, 'lab');
            if (!$room) {
                $this->logUnavailability($section, $slot->id, 'No available lab');
                continue;
            }

            if ($this->createTimetableEntry($section, $slot, $room->id, true)) {
                $usedSlots->push($slot);
                $slotMinutes = $this->getSlotDurationInMinutes($slot->id);
                $minutesRemaining -= $slotMinutes;

                Log::info("Scheduled LAB (spread)", [
                    'section_id' => $section->id,
                    'slot_id' => $slot->id,
                    'slot_day' => $slot->workingDay->day,
                    'remaining_minutes' => $minutesRemaining,
                ]);
            }
        }

        return $minutesRemaining <= 0;
    }

    protected function tryContinuousLab(CourseSection $section, $timeSlots, $usedSlots, &$minutesRemaining): bool
    {
        $preferredDayOrder = ['tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'monday'];

        foreach ($preferredDayOrder as $dayName) {
            $daySlots = $timeSlots->filter(fn($slot) => $slot->workingDay->day === $dayName)->values();

            for ($i = 0; $i < $daySlots->count(); $i++) {
                $group = collect();
                $totalMinutes = 0;

                for ($j = $i; $j < $daySlots->count(); $j++) {
                    $slot = $daySlots[$j];

                    if ($group->isNotEmpty() && !$this->areAdjacent($group->last()[0], $slot)) break;
                    if ($usedSlots->contains(fn($s) => $s->id === $slot->id)) continue;
                    if ($this->isSlotOccupiedByAnotherCourse($section->course_id, $slot->id)) continue;

                    if ($section->teacher_id !== null && !$this->isTeacherAvailable($section->teacher_id, $slot->id)) {
                        $fallbacks = $this->getAlternativeSlots($slot, $daySlots);
                        foreach ($fallbacks as $altSlot) {
                            if ($usedSlots->contains(fn($s) => $s->id === $altSlot->id)) continue;
                            if ($section->teacher_id !== null && !$this->isTeacherAvailable($section->teacher_id, $altSlot->id)) continue;
                            if ($this->isSlotOccupiedByAnotherCourse($section->course_id, $altSlot->id)) continue;

                            $altRoom = $this->findAvailableRoom($altSlot->id, 'lab');
                            if (!$altRoom) continue;

                            $group->push([$altSlot, $altRoom]);
                            $totalMinutes += $this->getSlotDurationInMinutes($altSlot->id);

                            if ($totalMinutes >= $minutesRemaining) break;
                        }
                        break;
                    }

                    $room = $this->findAvailableRoom($slot->id, 'lab');
                    if (!$room) {
                        $this->logUnavailability($section, $slot->id, 'No available lab');
                        continue;
                    }

                    $group->push([$slot, $room]);
                    $totalMinutes += $this->getSlotDurationInMinutes($slot->id);

                    if ($totalMinutes >= $minutesRemaining) break;
                }

                if ($totalMinutes >= $minutesRemaining) {
                    foreach ($group as [$slot, $room]) {
                        if ($this->createTimetableEntry($section, $slot, $room->id, true)) {
                            $usedSlots->push($slot);
                            $slotMinutes = $this->getSlotDurationInMinutes($slot->id);
                            $minutesRemaining -= $slotMinutes;

                            Log::info("Scheduled LAB (continuous)", [
                                'section_id' => $section->id,
                                'slot_id' => $slot->id,
                                'slot_day' => $slot->workingDay->day,
                                'remaining_minutes' => $minutesRemaining,
                            ]);
                        }
                    }
                    return true;
                }
            }
        }

        return false;
    }

    protected function getAlternativeSlots(TimeSlot $originalSlot, $timeSlots): Collection
    {
        return $timeSlots->filter(function ($slot) use ($originalSlot) {
            return $slot->id !== $originalSlot->id &&
                $slot->working_day_id === $originalSlot->working_day_id &&
                abs(strtotime($slot->start_time) - strtotime($originalSlot->start_time)) <= 7200;
        })->sortBy('start_time')->values();
    }

    protected function retryTheory(CourseSection $section): void
    {
        $preferredDayOrder = ['monday', 'tuesday', 'wednesday', 'thursday', 'saturday', 'friday'];

        $minutesRemaining = $section->required_minutes_theory_weekly -
            Timetable::where('course_section_id', $section->id)
                ->where('is_lab', false)
                ->where('examination_session_id', $this->examinationSessionId)
                ->get()
                ->sum(fn($t) => $this->getSlotDurationInMinutes($t->time_slot_id));

        if ($minutesRemaining <= 0) return;

        $timeSlots = TimeSlot::with('workingDay')
            ->whereHas('workingDay', fn($q) => $q->where('is_working', true))
            ->where('is_break', false)
            ->orderBy('start_time')
            ->get()
            ->sortBy(fn($slot) =>
                array_search(strtolower($slot->workingDay->day), $preferredDayOrder) ?? 999
            )
            ->values();

        foreach ($timeSlots as $slot) {
            if ($minutesRemaining <= 0) break;

            if ($this->isSlotOccupiedByAnotherCourse($section->course_id, $slot->id)) continue;

            if ($section->teacher_id !== null && !$this->isTeacherAvailable($section->teacher_id, $slot->id)) continue;

            $room = Room::where('room_type', 'lecture_hall')
                ->inRandomOrder()
                ->get()
                ->first(fn($r) => !$r->timetables()
                    ->where('time_slot_id', $slot->id)
                    ->where('examination_session_id', $this->examinationSessionId)
                    ->exists());

            if (!$room) continue;

            if ($this->createTimetableEntry($section, $slot, $room->id, false)) {
                $minutesRemaining -= $this->getSlotDurationInMinutes($slot->id);

                Log::info("Retry scheduled THEORY slot", [
                    'section_id' => $section->id,
                    'slot_id' => $slot->id,
                    'remaining_minutes' => $minutesRemaining
                ]);
            }
        }
    }

    protected function retryUnscheduledSections(): void
    {
        $unscheduledSections = CourseSection::with(['course', 'teachers'])
            ->whereHas('scheduleLogs', function ($q) {
                $q->where('examination_session_id', $this->examinationSessionId);
            })
            ->get();

        foreach ($unscheduledSections as $section) {
            Log::info("Retrying UNSCHEDULED section ID {$section->id}");

            if ($section->required_minutes_theory_weekly > 0) {
                $this->retryTheory($section);

                $remainingTheory = $section->required_minutes_theory_weekly - Timetable::where('course_section_id', $section->id)
                        ->where('is_lab', false)
                        ->where('examination_session_id', $this->examinationSessionId)
                        ->get()
                        ->sum(fn($t) => $this->getSlotDurationInMinutes($t->time_slot_id));

                if ($remainingTheory > 0) {
                    $this->assignToAnyFreeSlot($section, $remainingTheory, false);
                }
            }

            if ($section->has_lab && $section->required_minutes_lab_weekly > 0) {
                $this->retryLab($section);

                $remainingLab = $section->required_minutes_lab_weekly - Timetable::where('course_section_id', $section->id)
                        ->where('is_lab', true)
                        ->where('examination_session_id', $this->examinationSessionId)
                        ->get()
                        ->sum(fn($t) => $this->getSlotDurationInMinutes($t->time_slot_id));

                if ($remainingLab > 0) {
                    $this->assignToAnyFreeSlot($section, $remainingLab, true);
                }
            }

            $finalTheory = Timetable::where('course_section_id', $section->id)
                ->where('is_lab', false)
                ->where('examination_session_id', $this->examinationSessionId)
                ->get()
                ->sum(fn($t) => $this->getSlotDurationInMinutes($t->time_slot_id));

            $finalLab = Timetable::where('course_section_id', $section->id)
                ->where('is_lab', true)
                ->where('examination_session_id', $this->examinationSessionId)
                ->get()
                ->sum(fn($t) => $this->getSlotDurationInMinutes($t->time_slot_id));

            $isTheoryComplete = $finalTheory >= $section->required_minutes_theory_weekly;
            $isLabComplete = !$section->has_lab || $finalLab >= $section->required_minutes_lab_weekly;

            if ($isTheoryComplete && $isLabComplete) {
                ScheduleLog::where('course_section_id', $section->id)
                    ->where('examination_session_id', $this->examinationSessionId)
                    ->delete();

                Log::info("🧹 Cleaned up schedule logs for fully scheduled section ID {$section->id}");
            }
        }
    }

    protected function retryLab(CourseSection $section): void
    {
        $minutesRemaining = $section->required_minutes_lab_weekly -
            Timetable::where('course_section_id', $section->id)
                ->where('is_lab', true)
                ->where('examination_session_id', $this->examinationSessionId)
                ->get()
                ->sum(fn($t) => $this->getSlotDurationInMinutes($t->time_slot_id));

        if ($minutesRemaining <= 0) return;

        Log::info("Retrying LAB for section ID {$section->id}", [
            'initial_remaining_minutes' => $minutesRemaining,
            'requires_continuous_slots' => $section->requires_continuous_slots,
        ]);

        $allSlots = TimeSlot::with('workingDay')
            ->whereHas('workingDay', fn($q) => $q->where('is_working', true))
            ->where('is_break', false)
            ->orderBy('start_time')
            ->get()
            ->sortBy(fn($slot) =>
                array_search($slot->workingDay->day, ['tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'monday']) ?? 999
            )
            ->values();

        $usedSlots = collect();

        if ($section->requires_continuous_slots) {
            if ($this->tryContinuousLab($section, $allSlots, $usedSlots, $minutesRemaining)) return;
        }

        if ($this->trySpreadLab($section, $allSlots, $usedSlots, $minutesRemaining)) return;

        foreach ($allSlots as $slot) {
            if ($minutesRemaining <= 0) break;
            if ($usedSlots->contains(fn($s) => $s->id === $slot->id)) continue;

            $alreadyScheduled = Timetable::where('course_section_id', $section->id)
                ->where('day_of_week', $slot->workingDay->day)
                ->where('examination_session_id', $this->examinationSessionId)
                ->count();
            if ($alreadyScheduled >= 3) continue;

            if ($this->isSlotOccupiedByAnotherCourse($section->course_id, $slot->id)) continue;

            $room = $this->findAvailableRoom($slot->id, 'lab')
                ?? $this->findAvailableRoom($slot->id, 'lecture_hall');

            if (!$room) {
                $this->logUnavailability($section, $slot->id, 'No lab or lecture hall (fallback)');
                continue;
            }

            if ($section->teacher_id !== null && !$this->isTeacherAvailable($section->teacher_id, $slot->id)) {
                $this->logUnavailability($section, $slot->id, 'Teacher unavailable (fallback)');
                continue;
            }

            if ($this->createTimetableEntry($section, $slot, $room->id, true)) {
                $usedSlots->push($slot);
                $slotMinutes = $this->getSlotDurationInMinutes($slot->id);
                $minutesRemaining -= $slotMinutes;

                Log::info("Retry scheduled LAB (fallback-any-room)", [
                    'section_id' => $section->id,
                    'slot_id' => $slot->id,
                    'slot_day' => $slot->workingDay->day,
                    'slot_duration' => $slotMinutes,
                    'room_type' => $room->room_type,
                    'remaining_minutes' => $minutesRemaining,
                ]);
            }
        }

        if ($minutesRemaining > 0) {
            ScheduleLog::create([
                'course_section_id' => $section->id,
                'examination_session_id' => $this->examinationSessionId,
                'reason' => "Lab still not fully scheduled after retry. Remaining: {$minutesRemaining} mins.",
            ]);
        }
    }

    protected function assignToAnyFreeSlot(CourseSection $section, int &$minutesRemaining, bool $isLab = false): void
    {
        $roomType = $isLab ? 'lab' : 'lecture_hall';

        $randomRoom = Room::where('room_type', $roomType)->inRandomOrder()->first();
        if (!$randomRoom) return;

        $timeSlots = TimeSlot::with('workingDay')
            ->whereHas('workingDay', fn($q) => $q->where('is_working', true))
            ->where('is_break', false)
            ->orderBy('start_time')
            ->get();

        foreach ($timeSlots as $slot) {
            if ($minutesRemaining <= 0) break;

            $isClassBusy = Timetable::where('program_class_id', $section->course->class_id)
                ->where('time_slot_id', $slot->id)
                ->where('examination_session_id', $this->examinationSessionId)
                ->exists();

            if ($isClassBusy) continue;

            $slotMinutes = $this->getSlotDurationInMinutes($slot->id);

            if ($this->isTeacherAvailable($section->teacher_id, $slot->id)) {
                $this->createTimetableEntry($section, $slot, $randomRoom->id, $isLab);
                $minutesRemaining -= $slotMinutes;

                Log::warning("Final fallback scheduled (teacher available)", [
                    'section_id' => $section->id,
                    'slot_id' => $slot->id,
                    'room_id' => $randomRoom->id,
                    'remaining_minutes' => $minutesRemaining,
                    'teacher_conflict' => false
                ]);
            }

            if ($minutesRemaining > 0) {
                $this->createTimetableEntry($section, $slot, $randomRoom->id, $isLab);
                $minutesRemaining -= $slotMinutes;

                Log::warning("Final fallback scheduled (teacher conflict)", [
                    'section_id' => $section->id,
                    'slot_id' => $slot->id,
                    'room_id' => $randomRoom->id,
                    'remaining_minutes' => $minutesRemaining,
                    'teacher_conflict' => true
                ]);
            }
        }
    }

    public function logSummary(): array
    {
        $logs = ScheduleLog::where('examination_session_id', $this->examinationSessionId)->get();

        $sections = CourseSection::with(['course', 'program'])
            ->whereHas('course')
            ->whereHas('program', fn($q) => $q->where('credit_hour_system', true))
            ->get();

        $summary = [
            'fully_scheduled' => 0,
            'partially_scheduled' => 0,
            'not_scheduled' => 0,
            'total_sections' => $sections->count(),
            'reasons' => [],
        ];

        foreach ($sections as $section) {
            $scheduled = Timetable::where('course_section_id', $section->id)
                ->where('examination_session_id', $this->examinationSessionId)
                ->get();
            $totalTheory = $scheduled->where('is_lab', false)->sum(fn($t) => $this->getSlotDurationInMinutes($t->time_slot_id));
            $totalLab = $scheduled->where('is_lab', true)->sum(fn($t) => $this->getSlotDurationInMinutes($t->time_slot_id));

            $isTheoryComplete = $totalTheory >= $section->required_minutes_theory_weekly;
            $isLabComplete = !$section->has_lab || $totalLab >= $section->required_minutes_lab_weekly;

            if ($scheduled->isEmpty()) {
                $summary['not_scheduled']++;

                if (!$logs->contains('course_section_id', $section->id)) {
                    ScheduleLog::create([
                        'course_section_id' => $section->id,
                        'examination_session_id' => $this->examinationSessionId,
                        'reason' => 'Section was not scheduled at all. Likely due to unavailability of room, teacher, or slot.',
                    ]);
                }
            } elseif ($isTheoryComplete && $isLabComplete) {
                $summary['fully_scheduled']++;
            } else {
                $summary['partially_scheduled']++;

                if (!$logs->contains('course_section_id', $section->id)) {
                    $remainingTheory = $section->required_minutes_theory_weekly - $totalTheory;
                    $remainingLab = $section->required_minutes_lab_weekly - $totalLab;

                    $reason = 'Section partially scheduled. ';
                    if ($remainingTheory > 0) {
                        $reason .= "Remaining theory minutes: {$remainingTheory}. ";
                    }
                    if ($section->has_lab && $remainingLab > 0) {
                        $reason .= "Remaining lab minutes: {$remainingLab}. ";
                    }

                    ScheduleLog::create([
                        'course_section_id' => $section->id,
                        'examination_session_id' => $this->examinationSessionId,
                        'reason' => trim($reason),
                    ]);
                }
            }
        }

        $logs = ScheduleLog::where('examination_session_id', $this->examinationSessionId)->get();
        $groupedReasons = $logs->groupBy('reason')->map(fn($group) => $group->count())->sortDesc();
        $summary['reasons'] = $groupedReasons->toArray();

        Log::info("Scheduling Summary for Exam Session {$this->examinationSessionId}", $summary);

        return $summary;
    }
}
