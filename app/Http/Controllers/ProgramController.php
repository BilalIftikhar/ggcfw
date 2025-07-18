<?php

namespace App\Http\Controllers;

use App\Models\Program;
use App\Models\ProgramClass;
use App\Models\StudyLevel;
use App\Models\Teacher;
use App\Models\WorkingDay;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class ProgramController extends Controller
{

    public function index(Request $request)
    {
        if (!$request->user()->can('view_program')) {
            abort(403, 'Unauthorized action.');
        }
        $activeWorkingDays = WorkingDay::where('is_working', true)->pluck('day')->toArray();

        $levelId = $request->input('level_id');
        if ($levelId) {
            $studyLevel = StudyLevel::with('academicSession')->findOrFail($levelId);
            $programs = Program::with(['academicSession', 'studyLevel'])
                ->where('study_level_id', $levelId)
                ->get();

            return view('programs.index', compact('programs', 'studyLevel','activeWorkingDays'));
        }

        $programs = Program::with(['academicSession', 'studyLevel'])->get();

        return view('programs.index', compact('programs'));
    }

    public function store(Request $request)
    {
        if (!$request->user()->can('create_program')) {
            abort(403, 'Unauthorized action.');
        }

        //dd($request->all());
        try {
            $validated = $request->validate([
                'name' => [
                    'required',
                    'string',
                    'max:255',
                    Rule::unique('programs')->where(function ($query) use ($request) {
                        return $query->where('study_level_id', $request->study_level_id);
                    }),
                ],
                'study_level_id' => 'required|exists:study_levels,id',
                'academic_session_id' => 'required|exists:academic_sessions,id',
                'is_semester' => 'nullable|boolean',
                'number_of_years' => 'nullable|integer|min:0',
                'number_of_semesters' => 'nullable|integer|min:0',
                'credit_hour_system' => 'nullable|boolean',
                'teaching_days_per_week' => 'required|integer|min:1|max:7',
                'period_duration' => 'required|integer|min:30|max:120',
                'max_periods_per_day' => 'required|integer|min:1|max:10',
                'labs_on_separate_days' => 'nullable|boolean',
                'preferred_lab_days' => 'nullable|array',
                'preferred_lab_days.*' => 'string|in:monday,tuesday,wednesday,thursday,friday,saturday,sunday',
                'attendance_threshold' => 'required|integer|min:1|max:100',
                'is_active' => 'nullable|boolean',
                'admission_enabled' => 'nullable|boolean',
            ]);

            // Convert preferred_lab_days array to comma-separated string
            $preferredLabDays = $request->has('preferred_lab_days')
                ? implode(',', $validated['preferred_lab_days'])
                : null;

            $program = Program::create([
                'name' => $validated['name'],
                'study_level_id' => $validated['study_level_id'],
                'academic_session_id' => $validated['academic_session_id'],
                'is_semester' => $request->has('is_semester'),
                'number_of_years' => $request->input('number_of_years', 0),
                'number_of_semesters' => $request->input('number_of_semesters', 0),
                'credit_hour_system' => $request->has('credit_hour_system'),
                'teaching_days_per_week' => $validated['teaching_days_per_week'],
                'period_duration' => $validated['period_duration'],
                'max_periods_per_day' => $validated['max_periods_per_day'],
                'labs_on_separate_days' => $request->has('labs_on_separate_days'),
                'preferred_lab_days' => $preferredLabDays,
                'attendance_threshold' => $validated['attendance_threshold'],
                'is_active' => $request->has('is_active'),
                'admission_enabled' => $request->has('admission_enabled'),
                'created_by' => Auth::id(),
                'updated_by' => Auth::id(),
            ]);

            // Create Program Classes
            $count = $program->is_semester ? $program->number_of_semesters : $program->number_of_years;

            $prefixes = [
                1 => 'First',
                2 => 'Second',
                3 => 'Third',
                4 => 'Fourth',
                5 => 'Fifth',
                6 => 'Sixth',
                7 => 'Seventh',
                8 => 'Eighth',
                9 => 'Ninth',
                10 => 'Tenth',
                11 => 'Eleventh',
                12 => 'Twelfth',
            ];

            for ($i = 1; $i <= $count; $i++) {
                $word = $prefixes[$i] ?? "{$i}th";
                $ordinal = match ($i) {
                    1 => '1st',
                    2 => '2nd',
                    3 => '3rd',
                    default => $i . 'th',
                };

                $suffix = $program->is_semester ? 'Semester' : 'Year';
                $className = "{$word} ({$ordinal}) {$suffix}";

                ProgramClass::create([
                    'name' => $className,
                    'program_id' => $program->id,
                    'is_active' => $i === 1, // First class is active, rest are inactive
                ]);
            }

            $request->session()->flash('toastr', [
                'type' => 'success',
                'message' => 'Program and classes created successfully.',
                'title' => 'Success!',
                'options' => [
                    'timeOut' => 2000,
                    'progressBar' => true,
                    'closeButton' => true,
                ],
            ]);

            return back();

        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withInput()->with([
                'toastr' => [
                    'type' => 'error',
                    'message' => $e->validator->errors()->first(),
                    'title' => 'Validation Error!',
                    'options' => [
                        'timeOut' => 3000,
                        'progressBar' => true,
                        'closeButton' => true,
                    ],
                ]
            ]);
        }
    }

    public function update(Request $request, Program $program)
    {
        if (!$request->user()->can('update_program')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $validated = $request->validate([
                'name' => [
                    'required',
                    'string',
                    'max:255',
                    Rule::unique('programs')
                        ->ignore($program->id)
                        ->where(function ($query) use ($request) {
                            return $query->where('study_level_id', $request->study_level_id);
                        }),
                ],
                'study_level_id' => 'required|exists:study_levels,id',
                'academic_session_id' => 'required|exists:academic_sessions,id',
                'is_semester' => 'nullable|boolean',
                'number_of_years' => 'nullable|integer|min:0',
                'number_of_semesters' => 'nullable|integer|min:0',
                'credit_hour_system' => 'nullable|boolean',
                'teaching_days_per_week' => 'required|integer|min:1|max:7',
                'period_duration' => 'required|integer|min:30|max:120',
                'max_periods_per_day' => 'required|integer|min:1|max:10',
                'labs_on_separate_days' => 'nullable|boolean',
                'preferred_lab_days' => 'nullable|array',
                'preferred_lab_days.*' => 'string|in:monday,tuesday,wednesday,thursday,friday,saturday,sunday',
                'attendance_threshold' => 'required|integer|min:1|max:100',
                'is_active' => 'nullable|boolean',
                'admission_enabled' => 'nullable|boolean',
            ]);

            // Convert preferred_lab_days array to comma-separated string
            $preferredLabDays = $request->has('preferred_lab_days')
                ? implode(',', $validated['preferred_lab_days'])
                : null;

            $program->update([
                'name' => $validated['name'],
                'study_level_id' => $validated['study_level_id'],
                'academic_session_id' => $validated['academic_session_id'],
                'is_semester' => $request->has('is_semester'),
                'number_of_years' => $request->input('number_of_years', 0),
                'number_of_semesters' => $request->input('number_of_semesters', 0),
                'credit_hour_system' => $request->has('credit_hour_system'),
                'teaching_days_per_week' => $validated['teaching_days_per_week'],
                'period_duration' => $validated['period_duration'],
                'max_periods_per_day' => $validated['max_periods_per_day'],
                'labs_on_separate_days' => $request->has('labs_on_separate_days'),
                'preferred_lab_days' => $preferredLabDays,
                'attendance_threshold' => $validated['attendance_threshold'],
                'is_active' => $request->has('is_active'),
                'admission_enabled' => $request->has('admission_enabled'),
                'updated_by' => Auth::id(),
            ]);

            $request->session()->flash('toastr', [
                'type' => 'success',
                'message' => 'Program updated successfully.',
                'title' => 'Updated!',
                'options' => [
                    'timeOut' => 2000,
                    'progressBar' => true,
                    'closeButton' => true,
                ],
            ]);

            return redirect()->route('programs.index', ['level_id' => $program->study_level_id]);

        } catch (ValidationException $e) {
            return back()->withInput()->with([
                'toastr' => [
                    'type' => 'error',
                    'message' => $e->validator->errors()->first(),
                    'title' => 'Validation Error!',
                    'options' => [
                        'timeOut' => 3000,
                        'progressBar' => true,
                        'closeButton' => true,
                    ],
                ]
            ]);
        }
    }

    public function destroy(Program $program, Request $request)
    {
        if (!auth()->user()->can('delete_program')) {
            abort(403, 'Unauthorized action.');
        }

        // Check if program has any courses
        if ($program->courses()->exists()) {
            $request->session()->flash('toastr', [
                'type' => 'error',
                'message' => 'Cannot delete program because it has associated courses.',
                'title' => 'Error!',
                'options' => [
                    'timeOut' => 3000,
                    'progressBar' => true,
                    'closeButton' => true,
                ],
            ]);
            return redirect()->route('programs.index', ['level_id' => $program->study_level_id]);
        }

        try {
            // Delete all related classes first
            $program->classes()->delete();

            // Then delete the program itself
            $program->delete();

            $request->session()->flash('toastr', [
                'type' => 'success',
                'message' => 'Program and related classes deleted successfully.',
                'title' => 'Deleted!',
                'options' => [
                    'timeOut' => 2000,
                    'progressBar' => true,
                    'closeButton' => true,
                ],
            ]);
        } catch (\Exception $e) {
            $request->session()->flash('toastr', [
                'type' => 'error',
                'message' => 'Failed to delete program. It may have associated records.',
                'title' => 'Error!',
                'options' => [
                    'timeOut' => 3000,
                    'progressBar' => true,
                    'closeButton' => true,
                ],
            ]);
        }

        return redirect()->route('programs.index', ['level_id' => $program->study_level_id]);
    }

    public function showClasses(Request $request)
    {
        if (!$request->user()->can('view_program')) {
            abort(403, 'Unauthorized action.');
        }

        $programId = $request->input('program_id');

        if ($programId) {
            $program = Program::with(['classes', 'academicSession', 'studyLevel'])->findOrFail($programId);
            $classes = $program->classes()->orderBy('id')->get();
            $teachers = Teacher::where('is_active', true)
                ->where('working_status', 'working')
                ->get();
            return view('programs.classes', compact('program','classes','programId','teachers'));
        }

        return redirect()->route('programs.index')->with([
            'toastr' => [
                'type' => 'error',
                'message' => 'Program not found.',
                'title' => 'Error!',
                'options' => [
                    'timeOut' => 3000,
                    'progressBar' => true,
                    'closeButton' => true,
                ],
            ],
        ]);
    }

    public function showProgramCoursesPath(Request $request)
    {
        if (!$request->user()->can('view_program')) {
            abort(403, 'Unauthorized action.');
        }

        $programId = $request->input('program_id');

        if (!$programId) {
            return redirect()->route('programs.index')->with([
                'toastr' => [
                    'type' => 'error',
                    'message' => 'Program ID is required.',
                    'title' => 'Error!',
                    'options' => [
                        'timeOut' => 3000,
                        'progressBar' => true,
                        'closeButton' => true,
                    ],
                ],
            ]);
        }

        $program = Program::with(['academicSession', 'studyLevel', 'classes.courses'])->find($programId);

        if (!$program) {
            return redirect()->route('programs.index')->with([
                'toastr' => [
                    'type' => 'error',
                    'message' => 'Program not found.',
                    'title' => 'Error!',
                    'options' => [
                        'timeOut' => 3000,
                        'progressBar' => true,
                        'closeButton' => true,
                    ],
                ],
            ]);
        }
     //   dd($program,$program->classes);
        return view('programs.course_path', ['program' => $program, 'classes' => $program->classes,]);
    }

    public function toggleStatus(Request $request, ProgramClass $class)
    {
        try {
            $class->is_active = !$class->is_active;
            $class->save();

            return response()->json([
                'status' => 'success',
                'message' => 'Class status updated successfully.',
                'is_active' => $class->is_active,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to update class status. Please try again.'
            ], 500);
        }
    }



}
