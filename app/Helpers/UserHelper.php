<?php

namespace App\Helpers;

use App\Models\Assignment;
use App\Models\CourseSection;
use App\Models\Enrollment;
use App\Models\EnrollmentDetail;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\Timetable;
use Illuminate\Support\Facades\Auth;

class UserHelper
{
    public static function getTeachingScope()
    {
        $user = auth()->user();
        // Check if user has a teaching role
        if (!$user || !$user->roles()->where('is_teaching', true)->exists()) {
            return null;
        }

        // Get the teacher record based on user_id
        $teacher = Teacher::where('user_id', $user->id)->first();

        if (!$teacher) {
            return null;
        }

        // Fetch all course sections taught by this teacher
        $sections = CourseSection::with([
            'course.class.program.studyLevel.academicSession'
        ])->where('teacher_id', $teacher->id)->get();

        $scope = [
            'academic_session_ids' => [],
            'study_level_ids' => [],
            'program_ids' => [],
            'class_ids' => [],
            'course_ids' => [],
            'section_ids' => [],
        ];

        foreach ($sections as $section) {
            $course = $section->course;
            $class = $course?->class;
            $program = $course?->program;
            $studyLevel = $program?->studyLevel;
            $academicSession = $program?->academicSession;

            $scope['section_ids'][] = $section->id;
            $scope['course_ids'][] = $course?->id;
            $scope['class_ids'][] = $class?->id;
            $scope['program_ids'][] = $program?->id;
            $scope['study_level_ids'][] = $studyLevel?->id;
            $scope['academic_session_ids'][] = $academicSession?->id;
        }

        // Remove nulls and duplicates
        foreach ($scope as $key => $values) {
            $scope[$key] = array_values(array_unique(array_filter($values)));
        }

        return $scope;
    }

    public static function getStudentScope()
    {
        $user = auth()->user();

        // Ensure the user has a student role
        if (!$user || !$user->roles()->where('is_student', true)->exists()) {
            return null;
        }

        // Get the student record linked to the user
        $student = Student::where('user_id', $user->id)
            ->where('status', 'studying')
            ->where('is_active', true)
            ->first();

        if (!$student) {
            return null;
        }

        // Fetch only 'enrolled' enrollments
        $enrollments = Enrollment::with([
            'program.studyLevel.academicSession',
            'programClass',
            'details.course.class',
            'details.courseSection'
        ])
            ->where('student_id', $student->id)
            ->where('status', 'enrolled') // Matches enum
            ->get();

        $scope = [
            'academic_session_ids' => [],
            'study_level_ids' => [],
            'program_ids' => [],
            'class_ids' => [],
            'course_ids' => [],
            'section_ids' => [],
        ];

        foreach ($enrollments as $enrollment) {
            $program = $enrollment->program;
            $class = $enrollment->programClass;
            $studyLevel = $program?->studyLevel;
            $academicSession = $program?->academicSession;

            $scope['program_ids'][] = $program?->id;
            $scope['class_ids'][] = $class?->id;
            $scope['study_level_ids'][] = $studyLevel?->id;
            $scope['academic_session_ids'][] = $academicSession?->id;

            foreach ($enrollment->details as $detail) {
                if ($detail->status === 'enrolled') { // Matches enum
                    $scope['course_ids'][] = $detail->course_id;
                    $scope['section_ids'][] = $detail->course_section_id;
                }
            }
        }

        // Clean nulls and duplicates
        foreach ($scope as $key => $values) {
            $scope[$key] = array_values(array_unique(array_filter($values)));
        }

        return $scope;
    }

    public static function getUserScope()
    {
        $user = auth()->user();

        if (!$user) return null;

        if ($user->roles()->where('is_teaching', true)->exists()) {
            return self::getTeachingScope();
        }

        if ($user->roles()->where('is_student', true)->exists()) {
            return self::getStudentScope();
        }

        return null;
    }

    public static function getStudentAssignments($user)
    {
        // Check if user is a student
        if (!$user->roles()->where('is_student', true)->exists()) {
            return null;
        }

        // Get active student record
        $student = Student::where('user_id', $user->id)
            ->where('status', 'studying')
            ->where('is_active', true)
            ->first();

        if (!$student) {
            return collect(); // Return empty collection if no valid student
        }

        // Get enrolled course and section IDs
        $enrollmentDetails = EnrollmentDetail::whereHas('enrollment', function($query) use ($student) {
            $query->where('student_id', $student->id)
                ->where('status', 'enrolled');
        })
            ->where('status', 'enrolled')
            ->get(['course_id', 'course_section_id']);

        $courseIds = $enrollmentDetails->pluck('course_id')->unique()->toArray();
        $sectionIds = $enrollmentDetails->pluck('course_section_id')->unique()->toArray();

        // Return filtered assignments
        return Assignment::where(function($query) use ($courseIds, $sectionIds) {
            $query->whereIn('course_id', $courseIds)
                ->orWhereIn('course_section_id', $sectionIds);
        })
            ->latest()
            ->get();
    }

    public static function getTeacherAssignments($user)
    {
        if (!$user->roles()->where('is_teaching', true)->exists()) {
            return null;
        }

        $teacher = Teacher::where('user_id', $user->id)
            ->where('is_active', true)
            ->first();

        if (!$teacher) {
            return collect();
        }

        return Assignment::where('teacher_id', $teacher->id)
            ->latest()
            ->get();
    }

    public static function getStudentTimetable($user)
    {
        if (!$user->roles()->where('is_student', true)->exists()) {
            return null;
        }

        $student = Student::where('user_id', $user->id)
            ->where('status', 'studying')
            ->where('is_active', true)
            ->first();

        if (!$student) {
            return collect();
        }

        // Fetch active, enrolled course-section IDs
        $enrollmentDetails = EnrollmentDetail::whereHas('enrollment', function ($query) use ($student) {
            $query->where('student_id', $student->id)
                ->where('status', 'enrolled');
        })->where('status', 'enrolled')->get(['course_id', 'course_section_id']);

        $courseIds = $enrollmentDetails->pluck('course_id')->unique()->toArray();
        $sectionIds = $enrollmentDetails->pluck('course_section_id')->unique()->toArray();

        return Timetable::with(['teacher', 'course', 'programClass', 'timeSlot', 'room'])
            ->where(function ($query) use ($courseIds, $sectionIds) {
                $query->whereIn('course_id', $courseIds)
                    ->orWhereIn('course_section_id', $sectionIds);
            })
            ->orderBy('day_of_week')
            ->orderBy('time_slot_id')
            ->get();
    }

    public static function getTeacherTimetable($user)
    {
        if (!$user->roles()->where('is_teaching', true)->exists()) {
            return null;
        }

        $teacher = Teacher::where('user_id', $user->id)
            ->where('is_active', true)
            ->first();

        if (!$teacher) {
            return collect();
        }

        return Timetable::with(['course', 'programClass', 'timeSlot', 'room'])
            ->where('teacher_id', $teacher->id)
            ->orderBy('day_of_week')
            ->orderBy('time_slot_id')
            ->get();
    }


}
