<?php

namespace App\Http\Controllers;

use App\Helpers\UserHelper;
use App\Models\Course;
use App\Models\CourseSection;
use App\Models\ExaminationTerm;
use App\Models\FeeType;
use App\Models\Program;
use App\Models\ProgramClass;
use App\Models\StudyLevel;
use App\Models\Teacher;
use App\Models\Timetable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class AjaxController extends Controller
{
    public function studyLevels(Request $request)
    {
        $query = StudyLevel::query();

        if ($request->has('academic_session_id')) {
            $query->where('academic_session_id', $request->academic_session_id);
        }

        $user = Auth::user();
        $scope = UserHelper::getUserScope(); // auto-detect student/teacher

        if ($scope && !empty($scope['study_level_ids'])) {
            $query->whereIn('id', $scope['study_level_ids']);
        } elseif ($scope !== null) {
            return response()->json([]);
        }

        return $query->get(['id', 'name']);
    }

    public function programs(Request $request)
    {
        $query = Program::query();

        if ($request->has('study_level_id')) {
            $query->where('study_level_id', $request->study_level_id);
        }

        $user = Auth::user();
        $scope = UserHelper::getUserScope();

        if ($scope && !empty($scope['program_ids'])) {
            $query->whereIn('id', $scope['program_ids']);
        } elseif ($scope !== null) {
            return response()->json([]);
        }

        return $query->get(['id', 'name']);
    }


    public function courses(Request $request)
    {
        $query = Course::query();

        if ($request->has('program_id')) {
            $query->where('program_id', $request->program_id);
        }

        if ($request->has('program_class_id')) {
            $query->where('class_id', $request->program_class_id);
        }

        $query->where('is_active', true);

        $user = Auth::user();
        $scope = UserHelper::getUserScope();

        if ($scope && !empty($scope['course_ids'])) {
            $query->whereIn('id', $scope['course_ids']);
        } elseif ($scope !== null) {
            return response()->json([]);
        }

        return $query->get(['id', 'name', 'is_mandatory']);
    }


    public function programClasses(Request $request)
    {
        $query = ProgramClass::active(); // Only get active classes

        if ($request->has('program_id')) {
            $query->where('program_id', $request->program_id);
        }

        $user = Auth::user();
        $scope = UserHelper::getUserScope();

        if ($scope && !empty($scope['class_ids'])) {
            $query->whereIn('id', $scope['class_ids']);
        } elseif ($scope !== null) {
            return response()->json([]);
        }

        return $query->orderBy('id')->get(['id', 'name']);
    }

    public function courseSections(Request $request)
    {
        $query = CourseSection::query();

        if ($request->has('course_id')) {
            $query->where('course_id', $request->course_id);
        }
        $user = Auth::user();
        $scope = UserHelper::getUserScope();

        if ($scope && !empty($scope['section_ids'])) {
            $query->whereIn('id', $scope['section_ids']);
        } elseif ($scope !== null) {
            return response()->json([]);
        }

        return $query->orderBy('name')->get(['id', 'name']);
    }

    public function timetables(Request $request)
    {
        $request->validate([
            'course_section_id' => 'required|exists:course_sections,id',
            'program_class_id' => 'required|exists:program_classes,id',
        ]);

        $timetables = Timetable::where('course_section_id', $request->course_section_id)
            ->where('program_class_id', $request->program_class_id)
            ->with(['room', 'timeSlot', 'courseSection.course'])
            ->get()
            ->map(function ($timetable) {
                $courseName = optional($timetable->courseSection->course)->name ?? 'N/A';
                $day = ucfirst($timetable->day_of_week ?? 'N/A');
                $start = optional($timetable->timeSlot)->start_time ?? '??:??';
                $end = optional($timetable->timeSlot)->end_time ?? '??:??';
                $room = optional($timetable->room)->room_number ?? 'N/A';

                return [
                    'id' => $timetable->id,
                    'text' => "{$courseName} - {$day} {$start} to {$end} (Room: {$room})",
                ];
            });

        return response()->json($timetables);
    }

    public function examinationTerms(Request $request)
    {
        $request->validate([
            'examination_session_id' => 'required|exists:examination_sessions,id',
        ]);

        $terms = ExaminationTerm::where('examination_session_id', $request->examination_session_id)
            ->orderBy('title')
            ->get(['id', 'title']);

        return response()->json($terms);
    }

    public function sectionTeacher(Request $request) {

        $user = Auth::user();
        $section = null;

        if ($request->has('course_section_id')) {

            $sectionId = $request->input('course_section_id');
            $scope = UserHelper::getTeachingScope();

            if ($scope !== null && $user->roles()->where('is_teaching', true)->exists()) {
                if (!in_array($sectionId, $scope['course_section_ids'])) {
                    return response()->json([]);
                }
            }

            $section = CourseSection::with('teachers')->find($sectionId);
        }

        if ($section && $section->teachers) {
            return response()->json([
                'id' => $section->teachers->id,
                'name' => $section->teachers->name
            ]);
        }

        return response()->json([]);
    }

    public function feeTypes(Request $request)
    {
        $query = FeeType::query();

        if ($request->has('fee_group_id')) {
            $query->where('fee_group_id', $request->fee_group_id);
        }

        return $query->orderBy('name')->get(['id', 'name']);
    }


}
