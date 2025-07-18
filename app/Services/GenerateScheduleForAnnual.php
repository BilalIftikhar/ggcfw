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

class GenerateScheduleForAnnual
{
    protected int $examinationSessionId;

    public function __construct(int $examinationSessionId)
    {
        $this->examinationSessionId = $examinationSessionId;
    }

    public function generateForProgram(Program $program): void
    {
        if ($program->is_semester_based) {
            return; // Only process annual programs
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

        if ($section->weekly_lectures > 0) {
            $this->scheduleLectures($section);
        }

        if ($section->has_lab && $section->weekly_labs > 0) {
            $this->scheduleLabs($section);
        }
    }

    protected function isSectionFullyScheduled(CourseSection $section): bool
    {
        $scheduled = Timetable::where('course_section_id', $section->id)
            ->where('examination_session_id', $this->examinationSessionId)
            ->get();

        $totalLectures = $scheduled->where('is_lab', false)->count();
        $totalLabs = $scheduled->where('is_lab', true)->count();

        return $totalLectures >= $section->weekly_lectures && $totalLabs >= $section->weekly_labs;
    }

    protected function scheduleLectures(CourseSection $section): void
    {
        $lecturesRemaining = $section->weekly_lectures - Timetable::where('course_section_id', $section->id)
                ->where('is_lab', false)
                ->where('examination_session_id', $this->examinationSessionId)
                ->count();

        if ($lecturesRemaining <= 0) return;

        Log::info("Scheduling LECTURES for section ID {$section->id}", [
            'lectures_remaining' => $lecturesRemaining,
        ]);

        $timeSlots = $this->getSortedTimeSlots();

        $usedDays = [];
        $usedSlots = collect();

        foreach ($timeSlots as $slot) {
            if ($lecturesRemaining <= 0) break;

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
                        $lecturesRemaining--;

                        Log::info("Scheduled LECTURE (via fallback slot)", [
                            'section_id' => $section->id,
                            'slot_id' => $altSlot->id,
                            'slot_day' => $altSlot->workingDay->day,
                            'fallback_from' => $slot->id,
                            'lectures_remaining' => $lecturesRemaining,
                        ]);
                        break 1;
                    }
                }
                continue;
            }

            if ($this->createTimetableEntry($section, $slot, $room->id, false)) {
                $usedDays[] = $day;
                $usedSlots->push($slot);
                $lecturesRemaining--;

                Log::info("Scheduled LECTURE (spread phase)", [
                    'section_id' => $section->id,
                    'slot_id' => $slot->id,
                    'slot_day' => $day,
                    'lectures_remaining' => $lecturesRemaining,
                ]);
            }
        }

        // Fallback phase - try to schedule remaining lectures without day restrictions
        if ($lecturesRemaining > 0) {
            foreach ($timeSlots as $slot) {
                if ($lecturesRemaining <= 0) break;
                if ($usedSlots->contains(fn($s) => $s->id === $slot->id)) continue;

                $existing = Timetable::where('course_section_id', $section->id)
                    ->where('day_of_week', $slot->workingDay->day)
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
                    continue;
                }

                if ($this->createTimetableEntry($section, $slot, $room->id, false)) {
                    $usedSlots->push($slot);
                    $lecturesRemaining--;

                    Log::info("Scheduled LECTURE (fallback phase)", [
                        'section_id' => $section->id,
                        'slot_id' => $slot->id,
                        'slot_day' => $day,
                        'lectures_remaining' => $lecturesRemaining,
                    ]);
                }
            }
        }

        if ($lecturesRemaining > 0) {
            ScheduleLog::create([
                'course_section_id' => $section->id,
                'examination_session_id' => $this->examinationSessionId,
                'reason' => "Lectures not fully scheduled. Remaining: {$lecturesRemaining} lectures.",
            ]);
        }
    }

    protected function scheduleLabs(CourseSection $section): void
    {
        $labsRemaining = $section->weekly_labs - Timetable::where('course_section_id', $section->id)
                ->where('is_lab', true)
                ->where('examination_session_id', $this->examinationSessionId)
                ->count();

        if ($labsRemaining <= 0) return;

        Log::info("Scheduling LABS for section ID {$section->id}", [
            'labs_remaining' => $labsRemaining,
        ]);

        $timeSlots = $this->getSortedTimeSlots('lab');

        $usedSlots = collect();

        foreach ($timeSlots as $slot) {
            if ($labsRemaining <= 0) break;
            if ($usedSlots->contains(fn($s) => $s->id === $slot->id)) continue;

            if ($this->isSlotOccupiedByAnotherCourse($section->course_id, $slot->id)) continue;

            $room = $this->findAvailableRoom($slot->id, 'lab');
            if (!$room) {
                $this->logUnavailability($section, $slot->id, 'No available lab');
                continue;
            }

            if ($section->teacher_id !== null && !$this->isTeacherAvailable($section->teacher_id, $slot->id)) {
                $alternativeSlots = $this->getAlternativeSlots($slot, $timeSlots);

                foreach ($alternativeSlots as $altSlot) {
                    if ($this->isSlotOccupiedByAnotherCourse($section->course_id, $altSlot->id)) continue;
                    if ($section->teacher_id !== null && !$this->isTeacherAvailable($section->teacher_id, $altSlot->id)) continue;

                    $altRoom = $this->findAvailableRoom($altSlot->id, 'lab');
                    if (!$altRoom) continue;

                    if ($this->createTimetableEntry($section, $altSlot, $altRoom->id, true)) {
                        $usedSlots->push($altSlot);
                        $labsRemaining--;

                        Log::info("Scheduled LAB (via fallback slot)", [
                            'section_id' => $section->id,
                            'slot_id' => $altSlot->id,
                            'fallback_from' => $slot->id,
                            'labs_remaining' => $labsRemaining,
                        ]);
                        break 1;
                    }
                }
                continue;
            }

            if ($this->createTimetableEntry($section, $slot, $room->id, true)) {
                $usedSlots->push($slot);
                $labsRemaining--;

                Log::info("Scheduled LAB", [
                    'section_id' => $section->id,
                    'slot_id' => $slot->id,
                    'labs_remaining' => $labsRemaining,
                ]);
            }
        }

        if ($labsRemaining > 0) {
            ScheduleLog::create([
                'course_section_id' => $section->id,
                'examination_session_id' => $this->examinationSessionId,
                'reason' => "Labs not fully scheduled. Remaining: {$labsRemaining} labs.",
            ]);
        }
    }

    protected function getSortedTimeSlots(string $type = 'lecture'): Collection
    {
        $preferredDayOrder = $type === 'lab'
            ? ['tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'monday']
            : ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday'];

        return TimeSlot::with('workingDay')
            ->whereHas('workingDay', fn($q) => $q->where('is_working', true))
            ->where('is_break', false)
            ->orderBy('start_time')
            ->get()
            ->sortBy(fn($slot) =>
                array_search($slot->workingDay->day, $preferredDayOrder) ?? 999
            )
            ->values();
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
            'teacher_id' => $section->teacher_id,
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
        if ($teacherId === null) return true;

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
            'examination_session_id', $this->examinationSessionId,
            'reason' => "{$reason} at slot ID: {$slotId}",
        ]);
    }

    protected function getAlternativeSlots(TimeSlot $originalSlot, $timeSlots): Collection
    {
        return $timeSlots->filter(function ($slot) use ($originalSlot) {
            return $slot->id !== $originalSlot->id &&
                $slot->working_day_id === $originalSlot->working_day_id &&
                abs(strtotime($slot->start_time) - strtotime($originalSlot->start_time)) <= 7200;
        })->sortBy('start_time')->values();
    }

    protected function retryUnscheduledSections(): void
    {
        $unscheduledSections = CourseSection::with(['course', 'teachers'])
            ->whereHas('scheduleLogs', function ($q) {
                $q->where('examination_session_id', $this->examinationSessionId);
            })
            ->get();

        foreach ($unscheduledSections as $section) {
            $this->retryScheduling($section);
        }
    }

    protected function retryScheduling(CourseSection $section): void
    {
        $lecturesRemaining = $section->weekly_lectures - Timetable::where('course_section_id', $section->id)
                ->where('is_lab', false)
                ->where('examination_session_id', $this->examinationSessionId)
                ->count();

        $labsRemaining = $section->has_lab
            ? $section->weekly_labs - Timetable::where('course_section_id', $section->id)
                ->where('is_lab', true)
                ->where('examination_session_id', $this->examinationSessionId)
                ->count()
            : 0;

        if ($lecturesRemaining > 0) {
            $this->retryLectures($section, $lecturesRemaining);
        }

        if ($labsRemaining > 0) {
            $this->retryLabs($section, $labsRemaining);
        }

        // Clean up logs if fully scheduled
        $finalLectures = Timetable::where('course_section_id', $section->id)
            ->where('is_lab', false)
            ->where('examination_session_id', $this->examinationSessionId)
            ->count();

        $finalLabs = $section->has_lab
            ? Timetable::where('course_section_id', $section->id)
                ->where('is_lab', true)
                ->where('examination_session_id', $this->examinationSessionId)
                ->count()
            : 0;

        if ($finalLectures >= $section->weekly_lectures && $finalLabs >= $section->weekly_labs) {
            ScheduleLog::where('course_section_id', $section->id)
                ->where('examination_session_id', $this->examinationSessionId)
                ->delete();
        }
    }

    protected function retryLectures(CourseSection $section, int &$lecturesRemaining): void
    {
        $timeSlots = $this->getSortedTimeSlots();

        foreach ($timeSlots as $slot) {
            if ($lecturesRemaining <= 0) break;

            if ($this->isSlotOccupiedByAnotherCourse($section->course_id, $slot->id)) continue;

            $room = $this->findAvailableRoom($slot->id, 'lecture_hall');
            if (!$room) continue;

            if ($section->teacher_id !== null && !$this->isTeacherAvailable($section->teacher_id, $slot->id)) continue;

            if ($this->createTimetableEntry($section, $slot, $room->id, false)) {
                $lecturesRemaining--;
                Log::info("Retry scheduled LECTURE", [
                    'section_id' => $section->id,
                    'slot_id' => $slot->id,
                    'lectures_remaining' => $lecturesRemaining
                ]);
            }
        }
    }

    protected function retryLabs(CourseSection $section, int &$labsRemaining): void
    {
        $timeSlots = $this->getSortedTimeSlots('lab');

        foreach ($timeSlots as $slot) {
            if ($labsRemaining <= 0) break;

            if ($this->isSlotOccupiedByAnotherCourse($section->course_id, $slot->id)) continue;

            $room = $this->findAvailableRoom($slot->id, 'lab');
            if (!$room) continue;

            if ($section->teacher_id !== null && !$this->isTeacherAvailable($section->teacher_id, $slot->id)) continue;

            if ($this->createTimetableEntry($section, $slot, $room->id, true)) {
                $labsRemaining--;
                Log::info("Retry scheduled LAB", [
                    'section_id' => $section->id,
                    'slot_id' => $slot->id,
                    'labs_remaining' => $labsRemaining
                ]);
            }
        }
    }

    public function logSummary(): array
    {
        $sections = CourseSection::with(['course', 'program'])
            ->whereHas('course')
            ->whereHas('program', fn($q) => $q->where('is_semester_based', false))
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

            $lecturesScheduled = $scheduled->where('is_lab', false)->count();
            $labsScheduled = $scheduled->where('is_lab', true)->count();

            $isLecturesComplete = $lecturesScheduled >= $section->weekly_lectures;
            $isLabsComplete = !$section->has_lab || $labsScheduled >= $section->weekly_labs;

            if ($scheduled->isEmpty()) {
                $summary['not_scheduled']++;
            } elseif ($isLecturesComplete && $isLabsComplete) {
                $summary['fully_scheduled']++;
            } else {
                $summary['partially_scheduled']++;
            }
        }

        $logs = ScheduleLog::where('examination_session_id', $this->examinationSessionId)->get();
        $groupedReasons = $logs->groupBy('reason')->map(fn($group) => $group->count())->sortDesc();
        $summary['reasons'] = $groupedReasons->toArray();

        return $summary;
    }
}
