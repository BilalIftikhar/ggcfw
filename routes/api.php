<?php

use App\Helpers\UserHelper;
use App\Models\Course;
use App\Models\CourseSection;
use App\Models\Program;
use App\Models\ProgramClass;
use App\Models\StudyLevel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;


Route::get('/study-levels', function (Request $request) {

    $query = StudyLevel::query();

    if ($request->has('academic_session_id')) {
        $query->where('academic_session_id', $request->academic_session_id);
    }

    $user = Auth::user();
    dd($user);
    $scope = UserHelper::getTeachingScope();

    if ($scope !== null && $user->roles()->where('is_teaching', true)->exists()) {
        // If teaching scope is returned and user has a teaching role
        $query->whereIn('id', $scope['study_level_ids']);
    }

    return $query->get();
});

Route::get('/programs', function (Request $request) {
    $query = Program::query();
    if ($request->has('study_level_id')) {
        $query->where('study_level_id', $request->study_level_id);
    }
    $user = Auth::user();
    $scope = UserHelper::getTeachingScope();

    if ($scope !== null && $user->roles()->where('is_teaching', true)->exists()) {
        $query->whereIn('id', $scope['program_ids']);
    }
    return $query->get();
});

Route::get('/courses', function(Request $request) {
    $query = Course::query();

    if ($request->has('program_id')) {
        $query->where('program_id', $request->program_id);
    }
    if ($request->has('program_class_id')) {
        $query->where('class_id', $request->program_class_id);
    }
    // Only active courses
    $query->where('is_active', true);

    // Apply role-based filtering for teaching scope
    $user = Auth::user();
    $scope = UserHelper::getTeachingScope();

    if ($scope !== null && $user->roles()->where('is_teaching', true)->exists()) {
        $query->whereIn('id', $scope['course_ids']);
    }

    return $query->get(['id', 'name', 'is_mandatory']);
});

Route::get('/program-classes', function (Request $request) {
    $query = ProgramClass::query();

    if ($request->has('program_id')) {
        $query->where('program_id', $request->program_id);
    }

    // Apply role-based filtering for teaching scope
    $user = Auth::user();
    $scope = UserHelper::getTeachingScope();

    if ($scope !== null && $user->roles()->where('is_teaching', true)->exists()) {
        $query->whereIn('id', $scope['program_class_ids']);
    }


    return $query->orderBy('id')->get(['id', 'name']);
});

Route::get('/course-sections', function (Request $request) {
    $query = CourseSection::query();

    if ($request->has('course_id')) {
        $query->where('course_id', $request->course_id);
    }

    // Apply teaching scope filter if user is a teacher
    $user = Auth::user();
    $scope = UserHelper::getTeachingScope();

    if ($scope !== null && $user->roles()->where('is_teaching', true)->exists()) {
        $query->whereIn('id', $scope['course_section_ids']);
    }

    return $query->orderBy('name')->get(['id', 'name']);
});

Route::get('/section-teachers', function (Request $request) {
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

        $section = \App\Models\CourseSection::with('teachers')->find($sectionId);
    }

    if ($section && $section->teachers) {
        return response()->json([
            'id' => $section->teachers->id,
            'name' => $section->teachers->name
        ]);
    }

    return response()->json([]);
});




