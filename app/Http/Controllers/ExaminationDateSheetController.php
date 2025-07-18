<?php

namespace App\Http\Controllers;

use App\Models\AcademicSession;
use App\Models\Course;
use App\Models\CourseSection;
use App\Models\ExaminationDateSheet;
use App\Models\ExaminationSession;
use App\Models\ExaminationTerm;
use App\Models\Program;
use App\Models\ProgramClass;
use App\Models\Room;
use App\Models\StudyLevel;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class ExaminationDateSheetController extends Controller
{

    // 1. List all date sheets with required related data

    public function index(Request $request)
    {
        if (!$request->user()->hasPermissionTo('view_date_sheet')) {
            abort(403, 'You are not authorized to view date sheets.');
        }

        $sessions = AcademicSession::active()->get();
        $examSessions = ExaminationSession::active()->get();

        // Get terms if examination_session_id is present
        $terms = collect();
        if ($request->filled('examination_session_id')) {
            $terms = ExaminationTerm::where('examination_session_id', $request->examination_session_id)->get();
        }

        // Get study levels if academic_session_id is present
        $studyLevels = collect();
        if ($request->filled('academic_session_id')) {
            $studyLevels = StudyLevel::where('academic_session_id', $request->academic_session_id)->get();
        }

        // Get programs if study_level_id is present
        $programs = collect();
        if ($request->filled('study_level_id')) {
            $programs = Program::where('study_level_id', $request->study_level_id)->get();
        }

        // Get classes if program_id is present
        $classes = collect();
        if ($request->filled('program_id')) {
            $classes = ProgramClass::where('program_id', $request->program_id)->get();
        }

        $dateSheets = collect();
        $groupedSheets = collect();

        if ($request->filled(['academic_session_id', 'examination_session_id', 'examination_term_id', 'program_id', 'program_class_id'])) {
            $dateSheets = ExaminationDateSheet::with([
                'term', 'session', 'program', 'class', 'course', 'courseSection', 'room'
            ])
                ->where('examination_session_id', $request->examination_session_id)
                ->where('examination_term_id', $request->examination_term_id)
                ->where('program_id', $request->program_id)
                ->where('program_class_id', $request->program_class_id)
                ->latest()
                ->get();

            $groupedSheets = $dateSheets->groupBy(function ($sheet) {
                return $sheet->program?->name ?? 'Unknown Program';
            });
        }

        return view('examination_date_sheets.index', compact(
            'sessions', 'examSessions', 'terms', 'groupedSheets',
            'studyLevels', 'programs', 'classes'
        ));
    }


    public function create(Request $request)
    {
        if (!$request->user()->hasPermissionTo('create_date_sheet')) {
            abort(403, 'You are not authorized to create date sheets.');
        }

        try {
            if ($request->isMethod('post')) {
                $validated = $request->validate([
                    'academic_session_id' => 'required|exists:academic_sessions,id',
                    'examination_session_id' => 'required|exists:examination_sessions,id',
                    'examination_term_id' => 'required|exists:examination_terms,id',
                    'study_level_id' => 'required|exists:study_levels,id',
                    'program_id' => 'required|exists:programs,id',
                    'program_class_id' => 'required|exists:program_classes,id',
                ], [
                    'academic_session_id.required' => 'Please select an academic session',
                    'examination_session_id.required' => 'Please select an examination session',
                    'examination_term_id.required' => 'Please select an examination term',
                    'study_level_id.required' => 'Please select a study level',
                    'program_id.required' => 'Please select a program',
                    'program_class_id.required' => 'Please select a class',
                ]);

                // Fetch courses with sections
                $courses = Course::where('program_id', $validated['program_id'])
                    ->where('class_id', $validated['program_class_id'])
                    ->with('sections')
                    ->get()
                    ->filter(fn($course) => $course->sections->isNotEmpty());

                if ($courses->isEmpty()) {
                    $request->session()->flash('toastr', [
                        'type' => 'warning',
                        'message' => 'No courses with sections found for the selected criteria. Please try different filters.',
                        'title' => 'No Courses Found!',
                        'options' => [
                            'timeOut' => 3000,
                            'progressBar' => true,
                            'closeButton' => true,
                        ],
                    ]);

                    return back()->withInput();
                }

                $rooms = Room::all();

                // Fetch existing date sheet entries (for pre-fill)
                $existingSheets = ExaminationDateSheet::where('examination_session_id', $validated['examination_session_id'])
                    ->where('examination_term_id', $validated['examination_term_id'])
                    ->where('program_id', $validated['program_id'])
                    ->where('program_class_id', $validated['program_class_id'])
                    ->get()
                    ->keyBy(fn($item) => $item->course_id . '_' . $item->course_section_id);

                return view('examination_date_sheets.create', [
                    'academicSessionId' => $validated['academic_session_id'],
                    'examinationSessionId' => $validated['examination_session_id'],
                    'examinationTermId' => $validated['examination_term_id'],
                    'studyLevelId' => $validated['study_level_id'],
                    'programId' => $validated['program_id'],
                    'classId' => $validated['program_class_id'],
                    'courses' => $courses,
                    'rooms' => $rooms,
                    'existingSheets' => $existingSheets,
                    'sessions' => AcademicSession::active()->get(),
                    'examSessions' => ExaminationSession::active()->get(),
                    'studyLevels' => StudyLevel::where('academic_session_id', $validated['academic_session_id'])->get(),
                    'programs' => Program::where('study_level_id', $validated['study_level_id'])->get(),
                    'programClasses' => ProgramClass::where('program_id', $validated['program_id'])->get(),
                    'examinationTerms' => ExaminationTerm::where('examination_session_id', $validated['examination_session_id'])->get(),
                ]);
            }

            $sessions = AcademicSession::active()->get();
            $examSessions = ExaminationSession::active()->get();
            $rooms = Room::all();

            $studyLevels = collect();
            $programs = collect();
            $programClasses = collect();

            if ($request->filled('academic_session_id')) {
                $studyLevels = StudyLevel::where('academic_session_id', $request->academic_session_id)->get();
            }

            if ($request->filled('study_level_id')) {
                $programs = Program::where('study_level_id', $request->study_level_id)->get();
            }

            if ($request->filled('program_id')) {
                $programClasses = ProgramClass::where('program_id', $request->program_id)->get();
            }

            return view('examination_date_sheets.create', compact(
                'sessions',
                'examSessions',
                'rooms',
                'studyLevels',
                'programs',
                'programClasses'
            ));
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
        } catch (\Throwable $e) {
            return back()->withInput()->with([
                'toastr' => [
                    'type' => 'error',
                    'message' => 'An unexpected error occurred: ' . $e->getMessage(),
                    'title' => 'Error!',
                    'options' => [
                        'timeOut' => 3000,
                        'progressBar' => true,
                        'closeButton' => true,
                    ],
                ]
            ]);
        }
    }

    // 2. Store a new date sheet
    public function store(Request $request)
    {
        if (!$request->user()->hasPermissionTo('create_date_sheet')) {
            abort(403, 'Unauthorized');
        }

        $validated = $request->validate([
            'course_id' => 'required|array',
            'course_section_id' => 'required|array',
            'exam_date' => 'required|array',
            'start_time' => 'required|array',
            'end_time' => 'required|array',
            'room_id' => 'required|array',
        ]);

        foreach ($request->course_id as $index => $courseId) {
            $sheetData = [
                'examination_term_id'     => $request->examination_term_id,
                'examination_session_id' => $request->examination_session_id,
                'program_id'             => $request->program_id,
                'program_class_id'       => $request->program_class_id,
                'course_id'              => $courseId,
                'course_section_id'      => $request->course_section_id[$index],
                'exam_date'              => $request->exam_date[$index],
                'start_time'             => $request->start_time[$index],
                'end_time'               => $request->end_time[$index],
                'room_id'                => $request->room_id[$index],
            ];

            // If ID exists, update
            if (!empty($request->existing_id[$index])) {
                ExaminationDateSheet::where('id', $request->existing_id[$index])->update($sheetData);
            } else {
                ExaminationDateSheet::create($sheetData);
            }
        }

        return redirect()->route('examination-date-sheet.index')->with('toastr', [
            'type' => 'success',
            'title' => 'Date Sheet Saved!',
            'message' => 'Examination date sheet entries have been saved successfully.',
            'options' => [
                'timeOut' => 3000,
                'progressBar' => true,
                'closeButton' => true,
            ]
        ]);
    }



    // 3. Update an existing date sheet
    public function update(Request $request, ExaminationDateSheet $examination_date_sheet)
    {
        if (!$request->user()->hasPermissionTo('update_date_sheet')) {
            abort(403, 'You are not authorized to update date sheets.');
        }

        try {
            $validated = $request->validate([
                'examination_term_id' => 'required|exists:examination_terms,id',
                'examination_session_id' => 'required|exists:examination_sessions,id',
                'program_id' => 'required|exists:programs,id',
                'program_class_id' => 'required|exists:program_classes,id',
                'course_id' => 'required|exists:courses,id',
                'course_section_id' => 'required|exists:course_sections,id',
                'exam_date' => 'required|date',
                'start_time' => 'required|date_format:H:i',
                'end_time' => 'required|date_format:H:i|after:start_time',
                'room_id' => 'nullable|string|max:255',
            ]);

            $examination_date_sheet->update($validated);

            return redirect()->route('examination-date-sheet.index')->with([
                'toastr' => [
                    'type' => 'success',
                    'message' => 'Examination date sheet updated successfully.',
                    'title' => 'Updated!',
                    'options' => [
                        'timeOut' => 2000,
                        'progressBar' => true,
                        'closeButton' => true,
                    ],
                ],
            ]);
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
                ],
            ]);
        }
    }


    // 4. Delete a date sheet
    public function destroy(Request $request, ExaminationDateSheet $examination_date_sheet)
    {
        if (!$request->user()->hasPermissionTo('delete_date_sheet')) {
            abort(403, 'You are not authorized to delete date sheets.');
        }

        $examination_date_sheet->delete();

        return redirect()->route('examination-date-sheet.index')->with([
            'toastr' => [
                'type' => 'success',
                'message' => 'Examination date sheet deleted successfully.',
                'title' => 'Deleted!',
                'options' => [
                    'timeOut' => 2000,
                    'progressBar' => true,
                    'closeButton' => true,
                ],
            ],
        ]);
    }


}
