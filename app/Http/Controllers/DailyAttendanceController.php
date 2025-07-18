<?php

namespace App\Http\Controllers;

use App\Helpers\AttendanceHelper;
use App\Models\AcademicSession;
use App\Models\DailyAttendanceRoll;
use App\Models\Enrollment;
use App\Models\EnrollmentDetail;
use App\Models\Program;
use App\Models\ProgramClass;
use App\Models\Student;
use App\Models\StudyLevel;
use App\Models\SubjectAttendance;
use App\Models\Timetable;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class DailyAttendanceController extends Controller
{
    /**
     * Show the attendance records by class and month.
     */
    public function index()
    {
        return view('attendance.index', [
            'academicSessions' => AcademicSession::all(),
            'studyLevels' => StudyLevel::all(),
            'programs' => collect(),
            'classes' => collect(),
            'timetables' => collect(),
            'attendanceRecords' => collect(),
            'students' => collect(),
            'month' => null,
            'year' => null,
            'date' => null,
            'selectedFilters' => [],
            'attendanceType' => 'daily' // default to daily attendance
        ]);
    }

    public function searchDaily(Request $request)
    {
        $request->validate([
            'academic_session_id' => 'required|exists:academic_sessions,id',
            'study_level_id' => 'required|exists:study_levels,id',
            'program_id' => 'required|exists:programs,id',
            'program_class_id' => 'required|exists:program_classes,id',
            'year' => 'required|integer|min:2000|max:2100',
            'month' => 'nullable|integer|min:1|max:12',
        ]);

        $month = $request->input('month');
        $year = $request->input('year');
        $classId = $request->input('program_class_id');

        // Get attendance records with student and class relationships
        $query = DailyAttendanceRoll::with(['student', 'class'])
            ->where('program_class_id', $classId)
            ->where('year', $year);

        if ($month) {
            $query->where('month', $month);
        }

        $attendanceRecords = $query->get();

        // Transform records to include calculated percentages and summaries
        $transformedRecords = $attendanceRecords->map(function ($record) {
            return [
                'record' => $record,
                'percentage' => $record->getMonthlyAttendancePercentage(),
                'summary' => $record->getSummary(),
                'student' => $record->student,
            ];
        });

        return view('attendance.index', [
            'academicSessions' => AcademicSession::all(),
            'studyLevels' => StudyLevel::all(),
            'programs' => Program::where('study_level_id', $request->study_level_id)->get(),
            'classes' => ProgramClass::where('program_id', $request->program_id)->get(),
            'timetables' => collect(),
            'attendanceData' => $transformedRecords,
            'month' => $month,
            'year' => $year,
            'date' => null,
            'selectedFilters' => [
                'academic_session_id' => $request->academic_session_id,
                'study_level_id' => $request->study_level_id,
                'program_id' => $request->program_id,
                'program_class_id' => $classId,
            ],
            'attendanceType' => 'daily'
        ]);
    }

    public function searchSubject(Request $request)
    {
        $request->validate([
            'academic_session_id' => 'required|exists:academic_sessions,id',
            'study_level_id' => 'required|exists:study_levels,id',
            'program_id' => 'required|exists:programs,id',
            'program_class_id' => 'required|exists:program_classes,id',
            'timetable_id' => 'required|exists:timetables,id',
            'date' => 'required|date',
        ]);

        $date = $request->input('date');
        $classId = $request->input('program_class_id');
        $timetableId = $request->input('timetable_id');

        $students = Student::where('program_class_id', $classId)->get();

        $attendanceRecords = SubjectAttendance::where('timetable_id', $timetableId)
            ->whereDate('attendance_date', $date)
            ->get()
            ->keyBy('student_id');

        return view('attendance.index', [
            'academicSessions' => AcademicSession::all(),
            'studyLevels' => StudyLevel::all(),
            'programs' => Program::where('study_level_id', $request->study_level_id)->get(),
            'classes' => ProgramClass::where('program_id', $request->program_id)->get(),
            'timetables' => Timetable::where('program_class_id', $classId)->get(),
            'students' => $students,
            'attendanceRecords' => $attendanceRecords,
            'month' => null,
            'year' => null,
            'date' => $date,
            'selectedFilters' => [
                'academic_session_id' => $request->academic_session_id,
                'study_level_id' => $request->study_level_id,
                'program_id' => $request->program_id,
                'program_class_id' => $classId,
                'timetable_id' => $timetableId,
            ],
            'attendanceType' => 'subject'
        ]);
    }



    /**
     * Load form to take attendance for a specific date.
     */
    public function create()
    {
        return view('attendance.create', [
            'academicSessions' => AcademicSession::all(),
            'studyLevels' => StudyLevel::all(),
            'programs' => collect(),
            'classes' => collect(),
            'courses' => collect(),
            'sections' => collect(),
            'timetables' => collect(),
            'attendanceType' => 'daily' // default
        ]);
    }

    /**
]     * Store attendance for a specific day.
     */

    public function storeDaily(Request $request)
    {
        $request->validate([
            'academic_session_id' => 'required|exists:academic_sessions,id',
            'study_level_id' => 'required|exists:study_levels,id',
            'program_id' => 'required|exists:programs,id',
            'program_class_id' => 'required|exists:program_classes,id',
            'attendance_date' => 'required|date',
        ]);

        $classId = $request->program_class_id;
        $date = Carbon::parse($request->attendance_date);
        $month = $date->month;
        $year = $date->year;
        $day = $date->day;
        $dayKey = 'day_' . $day;

        // Get enrolled students
        $enrollments = Enrollment::with('student')
            ->where('program_class_id', $classId)
            ->where('academic_session_id', $request->academic_session_id)
            ->where('status', 'enrolled')
            ->whereHas('student') // ensures student is not soft-deleted
            ->get();

        // Fetch or create attendance sheet per student
        $attendanceRecords = collect();

        foreach ($enrollments as $enrollment) {
            $record = DailyAttendanceRoll::firstOrCreate(
                [
                    'student_id' => $enrollment->student_id,
                    'program_class_id' => $classId,
                    'month' => $month,
                    'year' => $year
                ],
                [
                    'marked_by' => auth()->id()
                ]
            );

            // No need to flag if marked or not — view will handle based on value presence
            $attendanceRecords->put($enrollment->student_id, $record);
        }
        //dd($attendanceRecords,$day);
        return view('attendance.create', [
            'academicSessions' => AcademicSession::all(),
            'studyLevels' => StudyLevel::all(),
            'programs' => Program::where('study_level_id', $request->study_level_id)->get(),
            'classes' => ProgramClass::where('program_id', $request->program_id)->get(),
            'attendanceType' => 'daily',
            'students' => $enrollments->pluck('student'),
            'attendanceRecords' => $attendanceRecords,
            'selectedDate' => $date,
            'selectedFilters' => [
                'academic_session_id' => $request->academic_session_id,
                'study_level_id' => $request->study_level_id,
                'program_id' => $request->program_id,
                'program_class_id' => $classId,
            ]
        ]);
    }

    public function storeSubject(Request $request)
    {
       // dd($request->all());
        $request->validate([
            'course_section_id' => 'required|exists:course_sections,id',
            'timetable_slot_id' => 'required|exists:timetables,id',
            'attendance_date' => 'required|date',
        ]);

        $sectionId = $request->course_section_id;
        $timetableId = $request->timetable_slot_id;
        $attendanceDate = Carbon::parse($request->attendance_date);

        // Load timetable and derive class ID
        $timetable = Timetable::with('courseSection.course')->findOrFail($timetableId);
        $classId = $timetable->program_class_id ?? $timetable->courseSection->program_class_id ?? null;

        //dd($sectionId);
        // Get enrollments filtered by section & status
        $enrollmentDetails = EnrollmentDetail::with(['enrollment.student'])
            ->where('course_section_id', $sectionId)
            ->where('status', 'enrolled')
            ->whereHas('enrollment', function ($query) {
                $query->where('status', 'enrolled');
            })
            ->get();

        $students = $enrollmentDetails
            ->map(fn($detail) => $detail->enrollment->student)
            ->filter() // Removes nulls
            ->unique('id')
            ->values();
        // Create attendance records
        $attendanceRecords = collect();

        foreach ($students as $student) {
           // dd($student->id,$sectionId,$timetableId,$attendanceDate->toDateString());
            $record = SubjectAttendance::firstOrCreate(
                [
                    'student_id' => $student->id,
                    'course_section_id' => $sectionId,
                    'timetable_id' => $timetableId,
                    'attendance_date' => $attendanceDate->toDateString(),
                ],
                [
                    'marked_by' => auth()->id(),
                    'status' => null,
                ]
            );

            $attendanceRecords->put($student->id, $record);
        }

        return view('attendance.create', [
            'academicSessions' => AcademicSession::all(),
            'studyLevels' => StudyLevel::all(),
            'programs' => Program::all(),
            'classes' => ProgramClass::all(),
            'attendanceType' => 'subject',
            'students' => $students,
            'attendanceRecords' => $attendanceRecords,
            'selectedDate' => $attendanceDate,
            'selectedFilters' => [
                'course_section_id' => $sectionId,
                'timetable_id' => $timetableId,
                'course_name' => optional($timetable->courseSection->course)->title,
                'section_name' => optional($timetable->courseSection)->section,
            ],
        ]);
    }


    public function updateDaily(Request $request)
    {
        $request->validate([
            'attendance_id' => 'required|exists:daily_attendance_rolls,id',
            'day' => 'required|integer|min:1|max:31',
            'status' => 'required|in:P,A,L,H'
        ]);

        $attendance = DailyAttendanceRoll::findOrFail($request->attendance_id);

        $year = $attendance->year ?? now()->year;
        $month = $attendance->month ?? now()->month;
        $dateToCheck = Carbon::createFromDate($year, $month, $request->day);

        $check = AttendanceHelper::attendanceRulesValidationForDaily($dateToCheck);

        if (!$check['allowed']) {
            return response()->json([
                'success' => false,
                'message' => $check['message']
            ], 403);
        }

        $status = DailyAttendanceRoll::STATUS_MAP[$request->status] ?? null;
        if (!$status || !in_array($status, DailyAttendanceRoll::ENUM_STATUSES)) {
            return response()->json(['error' => 'Invalid status'], 422);
        }

        $attendance->setDay($request->day, $status);
        $attendance->save();

        return response()->json([
            'success' => true,
            'percentage' => $attendance->getMonthlyAttendancePercentage(),
            'summary' => $attendance->getSummary()
        ]);
    }


    public function updateSubject(Request $request)
    {
        $request->validate([
            'attendance_id' => 'required|exists:subject_attendances,id',
            'status' => 'required|in:P,A,L,H', // Accept short codes
        ]);

        $attendance = SubjectAttendance::findOrFail($request->attendance_id);

        $dateToCheck = $attendance->date ?? now();

        //Log::info($dateToCheck);
        $check = AttendanceHelper::attendanceRulesValidationForSubject($dateToCheck, $attendance->timetable_id);

        if (!$check['allowed']) {
            return response()->json([
                'success' => false,
                'message' => $check['message']
            ], 403);
        }

        $fullStatus = SubjectAttendance::STATUS_MAP[$request->status] ?? null;

        if (!$fullStatus) {
            return response()->json(['error' => 'Invalid status mapping'], 422);
        }

        $attendance = SubjectAttendance::findOrFail($request->attendance_id);
        $attendance->update([
            'status' => $fullStatus,
            'marked_by' => auth()->id(),
        ]);

        return response()->json(['success' => true]);
    }




}
