<?php

namespace App\Http\Controllers;

use App\Models\{AcademicSession,
    EnrollmentDetail,
    ExaminationMark,
    ExaminationSession,
    ExaminationTerm,
    Program,
    ProgramClass,
    Course,
    CourseSection,
    Student,
    StudyLevel};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Auth;

class ExaminationMarkController extends Controller
{

    public function index(Request $request)
    {
        if (!$request->user()->hasPermissionTo('view_examination_marks')) {
            abort(403, 'Unauthorized.');
        }

        $user = auth()->user();
        $examMarks = collect(); // Initialize empty collection

        // Check if any filter is applied (excluding academic_session_id which is used for cascading dropdowns)
        $hasFilters = $request->anyFilled([
            'examination_session_id',
            'examination_term_id',
            'study_level_id',
            'program_id',
            'program_class_id',
            'course_id',
            'course_section_id'
        ]);

        // Only query marks if filters are applied or user is a student
        if ($hasFilters || $user->roles()->where('is_student', true)->exists()) {
            $query = ExaminationMark::with([
                'course',
                'courseSection',
                'student',
                'program',
                'class',
                'dateSheet',
                'markedBy',
                'updatedBy',
            ]);

            // Role-based data access
            if ($user->roles()->where('is_student', true)->exists()) {
                $student = Student::where('user_id', $user->id)->first();
                if ($student) {
                    $query->where('student_id', $student->id);
                } else {
                    $query->whereNull('id'); // No matching student, return nothing
                }
            } else {
                // Apply filters for non-student users
                if ($request->filled('examination_session_id')) {
                    $query->where('examination_session_id', $request->examination_session_id);
                }

                if ($request->filled('examination_term_id')) {
                    $query->where('examination_term_id', $request->examination_term_id);
                }

                if ($request->filled('study_level_id')) {
                    $programIds = Program::where('study_level_id', $request->study_level_id)->pluck('id');
                    $query->whereIn('program_id', $programIds);
                }

                if ($request->filled('program_id')) {
                    $query->where('program_id', $request->program_id);
                }

                if ($request->filled('program_class_id')) {
                    $query->where('program_class_id', $request->program_class_id);
                }

                if ($request->filled('course_id')) {
                    $query->where('course_id', $request->course_id);
                }

                if ($request->filled('course_section_id')) {
                    $query->where('course_section_id', $request->course_section_id);
                }
            }

            $examMarks = $query->orderBy('program_id')->orderBy('course_id')->get();
        }

        // Load cascading dropdown data
        $academicSessions = AcademicSession::all();
        $examinationSessions = ExaminationSession::active()->get();
        $examinationTerms = collect();
        $studyLevels = collect();
        $programs = collect();
        $programClasses = collect();
        $courses = collect();
        $courseSections = collect();

        // Load dropdown options based on selected filters
        if ($request->filled('academic_session_id')) {
            $studyLevels = StudyLevel::where('academic_session_id', $request->academic_session_id)->get();
        }

        if ($request->filled('study_level_id')) {
            $programs = Program::where('study_level_id', $request->study_level_id)->get();
        }

        if ($request->filled('program_id')) {
            $programClasses = ProgramClass::where('program_id', $request->program_id)->get();
            $courses = Course::where('program_id', $request->program_id)->get();
        }

        if ($request->filled('course_id')) {
            $courseSections = CourseSection::where('course_id', $request->course_id)->get();
        }

        if ($request->filled('examination_session_id')) {
            $examinationTerms = ExaminationTerm::where('examination_session_id', $request->examination_session_id)->get();
        }

        return view('examination_marks.index', [
            'examMarks' => $examMarks,
            'academicSessions' => $academicSessions,
            'examinationSessions' => $examinationSessions,
            'examinationTerms' => $examinationTerms,
            'studyLevels' => $studyLevels,
            'programs' => $programs,
            'programClasses' => $programClasses,
            'courses' => $courses,
            'courseSections' => $courseSections,
            'filters' => $request->all(),
            'hasFilters' => $hasFilters,
        ]);
    }


    public function create(Request $request)
    {
        if (!$request->user()->hasPermissionTo('create_examination_marks')) {
            abort(403, 'Unauthorized.');
        }

        // Get all necessary data for the create form
        $academicSessions = AcademicSession::active()->get();
        $examinationSessions = ExaminationSession::active()->get();
        $studyLevels = StudyLevel::all();
        $programs = Program::all();
        $programClasses = ProgramClass::all();
        $courses = Course::all();
        $courseSections = CourseSection::all();
        $examinationTerms = ExaminationTerm::all();

        return view('examination_marks.create', [
            'academicSessions' => $academicSessions,
            'examinationSessions' => $examinationSessions,
            'examinationTerms' => $examinationTerms,
            'studyLevels' => $studyLevels,
            'programs' => $programs,
            'programClasses' => $programClasses,
            'courses' => $courses,
            'courseSections' => $courseSections,
        ]);
    }

    public function prepareMarkSheet(Request $request)
    {
        if (!$request->user()->hasPermissionTo('create_examination_marks')) {
            abort(403, 'Unauthorized.');
        }

        $validated = $request->validate([
            'academic_session_id' => 'required|exists:academic_sessions,id',
            'examination_session_id' => 'required|exists:examination_sessions,id',
            'examination_term_id' => 'required|exists:examination_terms,id',
            'study_level_id' => 'required|exists:study_levels,id',
            'program_id' => 'required|exists:programs,id',
            'program_class_id' => 'required|exists:program_classes,id',
            'course_id' => 'required|exists:courses,id',
            'course_section_id' => 'nullable|exists:course_sections,id',
        ]);

        // Get enrolled students through Enrollment → EnrollmentDetail
        $enrolledStudents = EnrollmentDetail::with(['enrollment.student'])
            ->whereHas('enrollment', function ($query) use ($validated) {
                $query->where('program_id', $validated['program_id'])
                    ->where('program_class_id', $validated['program_class_id'])
                    ->where('academic_session_id', $validated['academic_session_id'])
                    ->where('status', 'enrolled');
            })
            ->where('course_id', $validated['course_id'])
            ->when($validated['course_section_id'], function ($query) use ($validated) {
                $query->where('course_section_id', $validated['course_section_id']);
            })
            ->where('status', 'enrolled')
            ->get();

        // Get existing marks for these students if they exist
        $existingMarks = [];
        if ($enrolledStudents->isNotEmpty()) {
            $studentIds = $enrolledStudents->pluck('enrollment.student_id')->toArray();

            $existingMarks = ExaminationMark::where([
                'examination_session_id' => $validated['examination_session_id'],
                'examination_term_id' => $validated['examination_term_id'],
                'course_id' => $validated['course_id'],
            ])
                ->when($validated['course_section_id'], function ($query) use ($validated) {
                    $query->where('course_section_id', $validated['course_section_id']);
                })
                ->whereIn('student_id', $studentIds)
                ->get()
                ->keyBy('student_id');
        }

        $selectedCourse = Course::find($validated['course_id']);
        $selectedExaminationTerm = ExaminationTerm::find($validated['examination_term_id']);
        $enable_sessional = $selectedExaminationTerm?->enable_sessional ?? false;

        // Get dropdown data
        $academicSessions = AcademicSession::active()->get();
        $examinationSessions = ExaminationSession::active()->get();
        $examinationTerms = ExaminationTerm::all();
        $studyLevels = StudyLevel::all();
        $programs = Program::all();
        $programClasses = ProgramClass::all();
        $courses = Course::all();
        $courseSections = CourseSection::all();

        return view('examination_marks.create', compact(
            'academicSessions',
            'examinationSessions',
            'examinationTerms',
            'studyLevels',
            'programs',
            'programClasses',
            'courses',
            'courseSections',
            'enrolledStudents',
            'selectedCourse',
            'existingMarks',
            'selectedExaminationTerm',
            'enable_sessional'
        ))->with([
            'academic_session_id' => $validated['academic_session_id'],
            'examination_session_id' => $validated['examination_session_id'],
            'examination_term_id' => $validated['examination_term_id'],
            'study_level_id' => $validated['study_level_id'],
            'program_id' => $validated['program_id'],
            'program_class_id' => $validated['program_class_id'],
            'course_id' => $validated['course_id'],
            'course_section_id' => $validated['course_section_id'] ?? null,
        ]);
    }


    public function store(Request $request)
    {
        if (!$request->user()->hasPermissionTo('create_examination_marks')) {
            abort(403, 'Unauthorized. Permission: create_examination_marks required.');
        }

        try {
            $validated = $request->validate([
                'examination_session_id' => 'required|exists:examination_sessions,id',
                'examination_term_id' => 'required|exists:examination_terms,id',
                'program_id' => 'required|exists:programs,id',
                'program_class_id' => 'required|exists:program_classes,id',
                'course_id' => 'required|exists:courses,id',
                'course_section_id' => 'nullable|exists:course_sections,id',
                'students' => 'required|array',
                'students.*.student_id' => 'required|exists:students,id',
                'students.*.marks_obtained' => 'required|numeric|min:0',
                'students.*.total_marks' => 'required|numeric|min:0',
                'students.*.passing_marks' => 'nullable|numeric|min:0',
                'students.*.sessional_marks' => 'nullable|numeric|min:0',
                'students.*.mark_id' => 'nullable|exists:examination_marks,id',
            ]);

            // Check if sessional marks are enabled
            $selectedTerm = ExaminationTerm::find($validated['examination_term_id']);
            $enableSessional = $selectedTerm?->enable_sessional ?? false;

            DB::beginTransaction();

            foreach ($validated['students'] as $studentData) {
                $markData = [
                    'examination_session_id' => $validated['examination_session_id'],
                    'examination_term_id' => $validated['examination_term_id'],
                    'program_id' => $validated['program_id'],
                    'program_class_id' => $validated['program_class_id'],
                    'course_id' => $validated['course_id'],
                    'course_section_id' => $validated['course_section_id'],
                    'student_id' => $studentData['student_id'],
                    'marks_obtained' => $studentData['marks_obtained'],
                    'total_marks' => $studentData['total_marks'],
                    'passing_marks' => $studentData['passing_marks'] ?? null,
                    'marked_by' => auth()->id(),
                ];

                if ($enableSessional && isset($studentData['sessional_marks'])) {
                    $markData['sessional_marks'] = $studentData['sessional_marks'];
                }

                if (!empty($studentData['mark_id'])) {
                    ExaminationMark::where('id', $studentData['mark_id'])->update($markData);
                } else {
                    ExaminationMark::create($markData);
                }
            }

            DB::commit();

            return redirect()->route('examination-marks.index')->with([
                'toastr' => [
                    'type' => 'success',
                    'message' => 'Examination marks saved successfully.',
                    'title' => 'Success',
                    'options' => [
                        'timeOut' => 3000,
                        'progressBar' => true,
                        'closeButton' => true,
                    ],
                ]
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withErrors($e->validator)->withInput();
        } catch (\Exception $e) {
            DB::rollBack();
            report($e);
            return back()->with([
                'toastr' => [
                    'type' => 'error',
                    'message' => 'An error occurred while saving marks. Please try again.',
                    'title' => 'Error',
                    'options' => [
                        'timeOut' => 5000,
                        'progressBar' => true,
                        'closeButton' => true,
                    ],
                ]
            ])->withInput();
        }
    }


    public function destroy(ExaminationMark $examinationMark)
    {
        if (!Auth::user()->hasPermissionTo('delete_marks')) {
            return back()->with([
                'toastr' => [
                    'type' => 'error',
                    'message' => 'Unauthorized action.',
                    'title' => 'Unauthorized',
                    'options' => [
                        'timeOut' => 3000,
                        'progressBar' => true,
                        'closeButton' => true,
                    ],
                ]
            ]);
        }

        try {
            $examinationMark->delete();

            return redirect()->route('examination-marks.index')->with([
                'toastr' => [
                    'type' => 'success',
                    'message' => 'Mark deleted successfully.',
                    'title' => 'Deleted!',
                    'options' => [
                        'timeOut' => 3000,
                        'progressBar' => true,
                        'closeButton' => true,
                    ],
                ]
            ]);
        } catch (\Exception $e) {
            return back()->with([
                'toastr' => [
                    'type' => 'error',
                    'message' => 'Failed to delete mark: ' . $e->getMessage(),
                    'title' => 'Error',
                    'options' => [
                        'timeOut' => 3000,
                        'progressBar' => true,
                        'closeButton' => true,
                    ],
                ]
            ]);
        }
    }
}
