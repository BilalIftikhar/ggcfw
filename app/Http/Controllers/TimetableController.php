<?php

namespace App\Http\Controllers;

use App\Helpers\UserHelper;
use App\Models\AcademicSession;
use App\Models\Course;
use App\Models\CourseSection;
use App\Models\ExaminationSession;
use App\Models\Program;
use App\Models\ProgramClass;
use App\Models\Room;
use App\Models\StudyLevel;
use App\Models\Teacher;
use App\Models\TimeSlot;
use App\Models\Timetable;
use App\Models\WorkingDay;
use App\Services\GenerateScheduleForAnnual;
use App\Services\ScheduleGenerator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class TimetableController extends Controller
{

    public function index(Request $request)
    {
        // Permission check
        if (!auth()->user()->hasPermissionTo('view_time_table')) {
            abort(403, 'You are not authorized to view timetable.');
        }

        $user = auth()->user();
        $days = WorkingDay::where('is_working', true)
            ->orderByRaw("FIELD(day, 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday')")
            ->pluck('day')
            ->toArray();

        $timeSlots = TimeSlot::orderBy('sort_order')->get();
        $structuredData = [];

        if ($user->roles()->where('is_student', true)->exists()) {
            $timetables = UserHelper::getStudentTimetable($user);
            $structuredData = $this->structureTimetableData($timetables, $days, $timeSlots);
        } elseif ($user->roles()->where('is_teaching', true)->exists()) {
            $timetables = UserHelper::getTeacherTimetable($user);
            $structuredData = $this->structureTimetableData($timetables, $days, $timeSlots);
        } else {
            // Admin or coordinator
            $query = Timetable::with(['teacher', 'course', 'program', 'programClass', 'timeSlot', 'room', 'courseSection']);

            if ($request->filled('academic_session_id')) {
                $query->where('academic_session_id', $request->academic_session_id);
            }

            if ($request->filled('examination_session_id')) {
                $query->where('examination_session_id', $request->examination_session_id);
            }
            if ($request->filled('study_level_id')) {
                $programs = Program::where('study_level_id', $request->study_level_id)->get();
                $programIds = $programs->pluck('id');
                $query->whereIn('program_id', $programIds);
            }

            if ($request->filled('program_id')) {
                $query->where('program_id', $request->program_id);
            }

            if ($request->filled('program_class_id')) {
                $query->where('program_class_id', $request->program_class_id);
            }

            $timetables = $query->orderBy('day_of_week')->orderBy('time_slot_id')->get();
            $structuredData = $this->structureTimetableData($timetables, $days, $timeSlots);
        }

        // Always load sessions
        $academicSessions = AcademicSession::all();
        $examinationSessions = ExaminationSession::active()->get();

        // Load dependent dropdown values based on current filters
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

        return view('time_table.index', [
            'structuredData' => $structuredData,
            'timeSlots' => $timeSlots,
            'days' => $days,
            'academicSessions' => $academicSessions,
            'examinationSessions' => $examinationSessions,
            'studyLevels' => $studyLevels,
            'programs' => $programs,
            'programClasses' => $programClasses,
            'filters' => $request->all(),
        ]);
    }



    /**
     * Structure timetable data for frontend display with support for multiple sections
     *
     * @param \Illuminate\Support\Collection $timetables
     * @param array $days Array of working days
     * @param \Illuminate\Support\Collection $timeSlots
     * @return array
     */
    protected function structureTimetableData($timetables, $days, $timeSlots)
    {
        $structuredData = [];

        // Load working days with their time slots ordered by sort_order
        $workingDays = WorkingDay::with(['timeSlots' => function ($q) {
            $q->orderBy('sort_order');
        }])
            ->where('is_working', true)
            ->orderByRaw("FIELD(day, 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday')")
            ->get();

        // Map day name to full WorkingDay model
        $dayToWorkingDay = $workingDays->keyBy(function ($day) {
            return strtolower($day->day);
        });

        // dd($dayToWorkingDay); // 🔍 Check mapping: 'monday' => WorkingDay model

        $groupedByProgram = $timetables->groupBy('program_id');

        // dd($groupedByProgram); // 🔍 Timetables grouped by program

        foreach ($groupedByProgram as $programId => $programTimetables) {
            $program = $programTimetables->first()->program ?? null;
            if (!$program) continue;

            $groupedByClass = $programTimetables->groupBy('program_class_id');
            $classesData = [];

            foreach ($groupedByClass as $classId => $classTimetables) {
                $programClass = $classTimetables->first()->programClass ?? null;
                if (!$programClass) continue;

                $timetableData = [];

                foreach ($days as $day) {
                    $day = strtolower($day);
                    $dayData = [];

                    $workingDay = $dayToWorkingDay[$day] ?? null;
                    if (!$workingDay) continue;

                    $slotsForDay = $workingDay->timeSlots ?? collect();

                    // dd($slotsForDay); // 🔍 Slots for this working day

                    foreach ($slotsForDay as $timeSlot) {
                        $entries = $classTimetables->filter(function ($entry) use ($day, $timeSlot) {
                            return strtolower($entry->day_of_week) === $day
                                && $entry->time_slot_id == $timeSlot->id;
                        });

                        // dd($entries); // 🔍 Entries for this time slot + day

                        $groupedEntries = $entries->groupBy('course_id');
                        $formattedEntries = [];

                        foreach ($groupedEntries as $courseId => $courseEntries) {
                            $course = $courseEntries->first()->course ?? null;

                            $sections = $courseEntries->map(function ($entry) {
                                return [
                                    'section_id' => $entry->course_section_id,
                                    'section_name' => $entry->courseSection->name ?? 'N/A',
                                    'teacher' => $entry->teacher->name ?? 'N/A',
                                    'teacher_id' => $entry->teacher_id,
                                    'room' => $entry->room->room_number ?? 'N/A',
                                    'room_id' => $entry->room_id,
                                    'is_lab' => $entry->is_lab,
                                    'timetable_id' => $entry->id
                                ];
                            });

                            $formattedEntries[] = [
                                'course_id' => $courseId,
                                'course_name' => $course?->name ?? 'N/A',
                                'course_code' => $course?->code ?? '',
                                'type' => $courseEntries->first()->is_lab ? 'lab' : 'lecture',
                                'sections' => $sections->toArray(),
                                'has_multiple_sections' => $sections->count() > 1
                            ];
                        }

                        $dayData[$timeSlot->id] = [
                            'time_display' => $timeSlot->start_time . ' - ' . $timeSlot->end_time,
                            'is_working_day' => true,
                            'entries' => $formattedEntries,
                            'has_multiple_courses' => count($formattedEntries) > 1
                        ];
                    }

                    $timetableData[$day] = $dayData;

                     //dd($timetableData); // 🔍 Inspect this class's timetable for the day
                }

                $classesData[] = [
                    'class_id' => $classId,
                    'class_name' => $programClass->name,
                    'timetable' => $timetableData
                ];

                // dd($classesData); // 🔍 Finished one class
            }

            $structuredData[] = [
                'program_id' => $programId,
                'program_name' => $program->name,
                'classes' => $classesData
            ];

            // dd($structuredData); // 🔍 Finished one program
        }

        return $structuredData;
    }





    public function create()
    {
        if (!auth()->user()->hasPermissionTo('create_time_table')) {
            abort(403, 'You are not authorized to create timetable.');
        }

        $examinationSession = ExaminationSession::active()->get();

        return view('time_table.create', compact('examinationSession'));
    }

    /**
     * Generate timetable based on examination session and program type
     */

    public function setup(Request $request)
    {
        try {
            if (!auth()->user()->hasPermissionTo('create_time_table')) {
                abort(403, 'You are not authorized to create timetable.');
            }

            $validated = $request->validate([
                'examination_session_id' => 'required|exists:examination_sessions,id',
                'program_type' => 'required|in:semester,annual'
            ]);

            $examinationSessionId = $validated['examination_session_id'];

            if ($validated['program_type'] === 'semester') {
                $programs = Program::with('courses')
                    ->where('credit_hour_system', true)
                    ->get();
                $generator = new ScheduleGenerator($examinationSessionId);
                foreach ($programs as $program) {
                    $generator->generateForProgram($program);
                }
                $generator->logSummary();
            } else {
                // Optional: Hook in your fixed-period logic here later
                $programs = Program::with('courses')
                    ->where('credit_hour_system', false)
                    ->get();

                $generator = new GenerateScheduleForAnnual($examinationSessionId);
                foreach ($programs as $program) {
                    $generator->generateForProgram($program);
                }
            }

            Log::info('Timetable Generated Successfully', [
                'examination_session_id' => $examinationSessionId,
                'program_type' => $validated['program_type'],
                'user_id' => auth()->id()
            ]);

            return redirect()->route('timetable.index', [
                'examination_session_id' => $examinationSessionId
            ])->with('toastr', [
                'type' => 'success',
                'title' => 'Success',
                'message' => ucfirst($validated['program_type']) . ' timetable generated successfully',
                'options' => [
                    'closeButton' => true,
                    'progressBar' => true,
                    'timeOut' => 5000
                ]
            ]);

        } catch (ValidationException $e) {
            Log::warning('Timetable Generation Validation Failed', [
                'errors' => $e->errors(),
                'input' => $request->all(),
                'user_id' => auth()->id()
            ]);

            return back()->withErrors($e->errors())->with('toastr', [
                'type' => 'error',
                'title' => 'Validation Error',
                'message' => 'Invalid input parameters',
                'options' => [
                    'timeOut' => 8000
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Timetable Generation Failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'input' => $request->all(),
                'user_id' => auth()->id()
            ]);

            return back()->with('toastr', [
                'type' => 'error',
                'title' => 'Generation Failed',
                'message' => 'Timetable generation failed: ' . $e->getMessage(),
                'options' => [
                    'closeButton' => true,
                    'timeOut' => 10000
                ]
            ])->withInput();
        }
    }


    public function edit(Request $request)
    {
        if (!auth()->user()->hasPermissionTo('update_time_table')) {
            abort(403, 'You are not authorized to edit timetable.');
        }

        $academicSessions = AcademicSession::all();
        $examinationSessions = ExaminationSession::all();
        $teachers = Teacher::active()->get();
        $rooms = Room::all();
        $timeSlots = TimeSlot::with('workingDay')->orderBy('sort_order')->get();

        $days = WorkingDay::where('is_working', true)
            ->orderByRaw("FIELD(day, 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday')")
            ->pluck('day')
            ->toArray();

        $timetables = collect();
        $structuredData = [];
        $selectedExamSessionId = null;

        $studyLevels = collect();
        $programs = collect();
        $programClasses = collect();

        if ($request->isMethod('post') && $request->filled('examination_session_id')) {
            $request->validate([
                'examination_session_id' => 'required|exists:examination_sessions,id'
            ]);

            $selectedExamSessionId = $request->input('examination_session_id');

            $query = Timetable::with(['teacher', 'course', 'program', 'programClass', 'timeSlot', 'room', 'courseSection'])
                ->where('examination_session_id', $selectedExamSessionId);

            if ($request->filled('academic_session_id')) {
                $query->where('academic_session_id', $request->academic_session_id);
                $studyLevels = StudyLevel::where('academic_session_id', $request->academic_session_id)->get();
            }

            if ($request->filled('study_level_id')) {
                $programs = Program::where('study_level_id', $request->study_level_id)->get();
                $programIds = $programs->pluck('id');

                $query->whereIn('program_id', $programIds);
            }

            if ($request->filled('program_id')) {
                $programClasses = ProgramClass::where('program_id', $request->program_id)->get();
                $query->where('program_id', $request->program_id);
            }

            if ($request->filled('program_class_id')) {
                $query->where('program_class_id', $request->program_class_id);
            }

            $timetables = $query->orderBy('day_of_week')->orderBy('time_slot_id')->get();
            $structuredData = $this->structureTimetableData($timetables, $days, $timeSlots);
        }

        return view('time_table.edit', compact(
            'academicSessions',
            'examinationSessions',
            'studyLevels',
            'programs',
            'programClasses',
            'teachers',
            'rooms',
            'timeSlots',
            'timetables',
            'structuredData',
            'days',
            'selectedExamSessionId'
        ));
    }




    public function update(Request $request, $id)
    {
        if (!auth()->user()->hasPermissionTo('update_time_table')) {
            abort(403, 'You are not authorized to update timetable.');
        }

        try {
            $validated = $request->validate([
                'academic_session_id' => 'nullable|exists:academic_sessions,id',
                'examination_session_id' => 'required|exists:examination_sessions,id',
                'program_id' => 'required|exists:programs,id',
                'program_class_id' => 'required|exists:program_classes,id',
                'course_id' => 'required|exists:courses,id',
                'course_section_id' => 'required|exists:course_sections,id',
                'teacher_id' => 'required|exists:teachers,id',
                'time_slot_id' => 'required|exists:time_slots,id',
                'day_of_week' => 'required|string|in:monday,tuesday,wednesday,thursday,friday,saturday',
                'room_id' => 'required|exists:rooms,id',
                'is_lab' => 'required|boolean',
            ]);

            $timetable = Timetable::findOrFail($id);
            $timetable->update($validated);

            return redirect()->route('timetable.index')->with('toastr', [
                'type' => 'success',
                'message' => 'Timetable entry updated successfully.',
                'title' => 'Updated!',
                'options' => [
                    'timeOut' => 3000,
                    'progressBar' => true,
                    'closeButton' => true,
                ],
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withInput()->with('toastr', [
                'type' => 'error',
                'message' => $e->validator->errors()->first(),
                'title' => 'Validation Error!',
                'options' => [
                    'timeOut' => 3000,
                    'progressBar' => true,
                    'closeButton' => true,
                ],
            ]);
        } catch (\Throwable $e) {
            return back()->withInput()->with('toastr', [
                'type' => 'error',
                'message' => 'An unexpected error occurred while updating the timetable.',
                'title' => 'Error!',
                'options' => [
                    'timeOut' => 3000,
                    'progressBar' => true,
                    'closeButton' => true,
                ],
            ]);
        }
    }


    public function getData(Request $request)
    {
        $validated = $request->validate([
            'academic_session_id' => 'required|exists:academic_sessions,id',
            'examination_session_id' => 'required|exists:examination_sessions,id'
        ]);

        $cacheKey = "timetable_data_{$validated['academic_session_id']}_{$validated['examination_session_id']}";

        $data = Cache::remember($cacheKey, now()->addHours(6), function() use ($validated) {
            return [
                'timetable' => Timetable::with([
                    'teacher',
                    'course',
                    'program',
                    'programClass',
                    'timeSlot',
                    'room',
                    'courseSection'
                ])
                    ->where('academic_session_id', $validated['academic_session_id'])
                    ->where('examination_session_id', $validated['examination_session_id'])
                    ->get()
                    ->groupBy(['time_slot_id', 'day_of_week']),

                'time_slots' => TimeSlot::orderBy('start_time')->get(),

                'meta' => [
                    'academic_session' => AcademicSession::find($validated['academic_session_id']),
                    'examination_session' => ExaminationSession::find($validated['examination_session_id'])
                ]
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }

    /**
     * Load edit modal content
     */
    public function editModal($id)
    {
        if (!auth()->user()->hasPermissionTo('update_time_table')) {
            abort(403, 'You are not authorized to update timetable.');
        }
        $timetable = Timetable::with([
            'teacher',
            'course',
            'program',
            'programClass',
            'timeSlot',
            'room',
            'courseSection',
            'academicSession',
            'examinationSession'
        ])->findOrFail($id);

        return view('time_table.modals.edit', [
            'timetable' => $timetable,
            'programs' => Program::all(),
            'programClasses' => ProgramClass::where('program_id', $timetable->program_id)->get(),
            'courses' => Course::where('class_id', $timetable->program_class_id)->get(),
            'courseSections' => CourseSection::where('course_id', $timetable->course_id)->get(),
            'teachers' => Teacher::active()->get(),
            'rooms' => Room::all(),
            'timeSlots' => TimeSlot::with('workingDay')->get(),
            'academicSessions' => AcademicSession::all(),
            'examinationSessions' => ExaminationSession::all()
        ]);
    }

    /**
     * Load create modal content
     */
    public function createModal(Request $request)
    {
        if (!auth()->user()->hasPermissionTo('create_time_table')) {
            abort(403, 'You are not authorized to update timetable.');
        }

        $validated = $request->validate([
            'day' => 'required|in:monday,tuesday,wednesday,thursday,friday,saturday',
            'slot' => 'required|exists:time_slots,id',
            'academic_session_id' => 'required|exists:academic_sessions,id',
            'examination_session_id' => 'required|exists:examination_sessions,id'
        ]);

        return view('time_table.modals.create', [
            'day_of_week' => $validated['day'],
            'time_slot_id' => $validated['slot'],
            'academic_session_id' => $validated['academic_session_id'],
            'examination_session_id' => $validated['examination_session_id'],
            'programs' => Program::all(),
            'programClasses' => ProgramClass::active()->get(),
            'courses' => Course::all(),
            'courseSections' => CourseSection::all(),
            'teachers' => Teacher::active()->get(),
            'rooms' => Room::all(),
            'timeSlots' => TimeSlot::with('workingDay')->get(),
            'academicSessions' => AcademicSession::all(),
            'examinationSessions' => ExaminationSession::all()
        ]);
    }

    /**
     * Handle timetable entry movement
     */
    public function moveEntry(Request $request, $id)
    {
        if (!auth()->user()->hasPermissionTo('update_time_table')) {
            return response()->json([
                'success' => false,
                'message' => 'You are not authorized to update timetable.',
            ], 403);
        }

        $validated = $request->validate([
            'day_of_week' => 'required|in:monday,tuesday,wednesday,thursday,friday,saturday',
            'time_slot_id' => 'required|exists:time_slots,id'
        ]);

        try {
            DB::beginTransaction();

            $timetable = Timetable::findOrFail($id);

            // Check for conflicts with just the new day and time slot
            $conflicts = $this->checkConflicts(array_merge($validated, [
                'room_id' => $timetable->room_id,
                'teacher_id' => $timetable->teacher_id
            ]), $timetable);

            if ($conflicts['has_conflict']) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => $this->getConflictMessage($conflicts),
                    'conflicts' => $conflicts,
                ], 409);
            }

            // Only update day and time slot
            $timetable->update([
                'day_of_week' => $validated['day_of_week'],
                'time_slot_id' => $validated['time_slot_id']
            ]);

            // Clear relevant cache
            Cache::forget("timetable_data_{$timetable->academic_session_id}_{$timetable->examination_session_id}");

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Timetable entry moved successfully',
                'data' => $timetable->fresh()->load(['teacher', 'course', 'room', 'timeSlot']),
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Timetable move failed: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'An error occurred while moving the timetable entry',
            ], 500);
        }
    }

    /**
     * Check for scheduling conflicts
     */
    protected function checkConflicts($data, $timetable)
    {
        $roomConflict = Timetable::where('room_id', $data['room_id'])
            ->where('time_slot_id', $data['time_slot_id'])
            ->where('day_of_week', $data['day_of_week'])
            ->where('id', '!=', $timetable->id)
            ->exists();

        $teacherConflict = Timetable::where('teacher_id', $timetable->teacher_id)
            ->where('time_slot_id', $data['time_slot_id'])
            ->where('day_of_week', $data['day_of_week'])
            ->where('id', '!=', $timetable->id)
            ->exists();

        return [
            'has_conflict' => $roomConflict || $teacherConflict,
            'room' => $roomConflict,
            'teacher' => $teacherConflict
        ];
    }

    /**
     * Generate conflict message
     */
    protected function getConflictMessage($conflicts)
    {
        if ($conflicts['room'] && $conflicts['teacher']) {
            return 'Conflict detected: Both room and teacher are already booked for this time slot';
        }
        return $conflicts['room']
            ? 'Conflict detected: Room is already booked for this time slot'
            : 'Conflict detected: Teacher is already booked for this time slot';
    }


}
