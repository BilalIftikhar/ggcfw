<?php

namespace App\Helpers;

use App\Models\AttendanceRule;
use App\Models\TimeSlot;
use App\Models\Timetable;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class AttendanceHelper
{
    /**
     * Validates attendance rules for daily attendance:
     * - Ensures system is set to 'daily' attendance type.
     * - Checks backdate and future date restrictions.
     * - Allows override for admin if configured.
     *
     * @param Carbon|string|\DateTime $dateToCheck
     * @return array ['allowed' => bool, 'message' => string (only if disallowed)]
     */
    public static function attendanceRulesValidationForDaily($dateToCheck): array
    {
        $rules = AttendanceRule::query()
            ->where('attendance_type', 'daily')
            ->first();

        if (!$rules) {
            return ['allowed' => true]; // No rules, allow marking.
        }

        $user = Auth::user();
        if ($rules->allow_admin_override && $user) {
            $isAdmin = $user->roles()->where('is_admin', true)->exists();
            if ($isAdmin) {
                return ['allowed' => true]; // Admin override, allow marking.
            }
        }

        $date = $dateToCheck instanceof Carbon ? $dateToCheck : Carbon::parse($dateToCheck);
        $today = Carbon::today();

        if ($rules->restrict_backdate && $date->lt($today)) {
            return [
                'allowed' => false,
                'message' => 'Backdate attendance marking is not allowed as per system settings.'
            ];
        }

        if ($rules->restrict_future_date && $date->gt($today)) {
            return [
                'allowed' => false,
                'message' => 'Future date attendance marking is not allowed as per system settings.'
            ];
        }

        return ['allowed' => true];
    }

    /**
     * Validates attendance rules for subject attendance with time window enforcement.
     *
     * @param Carbon|string|\DateTime $dateToCheck
     * @param int $timetableId
     * @return array ['allowed' => bool, 'message' => string (only if disallowed)]
     */
    public static function attendanceRulesValidationForSubject($dateToCheck, int $timetableId): array
    {
        $rules = AttendanceRule::query()
            ->where('attendance_type', 'subject')
            ->first();

        if (!$rules) {
            return ['allowed' => true]; // No rules, allow.
        }

        $user = Auth::user();
        if ($rules->allow_admin_override && $user) {
            $isAdmin = $user->roles()->where('is_admin', true)->exists();
            if ($isAdmin) {
                return ['allowed' => true]; // Admin override
            }
        }

        $date = $dateToCheck instanceof Carbon ? $dateToCheck : Carbon::parse($dateToCheck);
        $today = Carbon::today();

        if ($rules->restrict_backdate && $date->lt($today)) {
            return [
                'allowed' => false,
                'message' => 'Backdate attendance marking is not allowed as per system settings.'
            ];
        }

        if ($rules->restrict_future_date && $date->gt($today)) {
            return [
                'allowed' => false,
                'message' => 'Future date attendance marking is not allowed as per system settings.'
            ];
        }

        $timetable = Timetable::find($timetableId);
        if (!$timetable) {
            return [
                'allowed' => false,
                'message' => 'Invalid timetable reference for attendance marking.'
            ];
        }

        $timeSlot = TimeSlot::find($timetable->time_slot_id);
        if (!$timeSlot) {
            return [
                'allowed' => false,
                'message' => 'Associated time slot not found for the timetable.'
            ];
        }

        $slotDateTime = Carbon::parse($date->toDateString() . ' ' . $timeSlot->end_time);
        $startDateTime = Carbon::parse($date->toDateString() . ' ' . $timeSlot->start_time);

        $beforeMinutes = $rules->allowed_before_minutes ?? 0;
        $afterMinutes = $rules->allowed_after_minutes ?? 0;

        $windowStart = ($beforeMinutes > 0)
            ? $slotDateTime->copy()->subMinutes($beforeMinutes)
            : $startDateTime;

        $windowEnd = ($afterMinutes > 0)
            ? $slotDateTime->copy()->addMinutes($afterMinutes)
            : $slotDateTime;

        $now = now();

        if ($now->lt($windowStart) || $now->gt($windowEnd)) {
            $windowStartFormatted = $windowStart->format('h:i A');
            $windowEndFormatted = $windowEnd->format('h:i A');
            return [
                'allowed' => false,
                'message' => "Attendance can only be marked between {$windowStartFormatted} and {$windowEndFormatted}."
            ];
        }

        return ['allowed' => true];
    }

}
