<?php

namespace App\Http\Controllers;

use App\Models\AcademicSession;
use App\Models\Course;
use App\Models\CourseSection;
use App\Models\Enrollment;
use App\Models\EnrollmentDetail;
use App\Models\ExaminationSession;
use App\Models\Program;
use App\Models\ProgramClass;
use App\Models\Student;
use App\Models\StudyLevel;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Spatie\Permission\Models\Role;

class StudentController extends Controller
{

    public function index(Request $request)
    {
        if (!$request->user()->hasPermissionTo('view_student')) {
            abort(403, 'You are not authorized to view students.');
        }

        $query = Student::query();

        if ($request->filled('academic_session_id')) {
            $query->where('academic_session_id', $request->academic_session_id);
        }

        if ($request->filled('study_level_id')) {
            $query->where('study_level_id', $request->study_level_id);
        }

        if ($request->filled('program_id')) {
            $query->where('program_id', $request->program_id);
        }

        if ($request->filled('program_class_id')) {
            // Filter students based on enrollment conditions
            $academicSessionId = $request->academic_session_id;
            $programId = $request->program_id;
            $programClassId = $request->program_class_id;

            $query->whereHas('enrollments', function ($q) use ($academicSessionId, $programId, $programClassId) {
                $q->where('status', 'enrolled')
                    ->when($academicSessionId, fn($q) => $q->where('academic_session_id', $academicSessionId))
                    ->when($programId, fn($q) => $q->where('program_id', $programId))
                    ->where('program_class_id', $programClassId);
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('is_active')) {
            $query->where('is_active', $request->is_active);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('father_name', 'like', "%{$search}%")
                    ->orWhere('cnic', 'like', "%{$search}%")
                    ->orWhere('registration_number', 'like', "%{$search}%")
                    ->orWhere('roll_number', 'like', "%{$search}%");
            });
        }

        $students = $query->latest()->paginate(20);
        $sessions = AcademicSession::latest()->get();

        return view('students.index', compact('students', 'sessions'));
    }


    public function create(Request $request)
    {
        if (!$request->user()->hasPermissionTo('create_student')) {
            abort(403, 'You are not authorized to create students.');
        }

        $sessions = AcademicSession::active()->admission()->get();
        $examinationSessions = ExaminationSession::active()->running()->get();
        $levels = collect(); // Empty collection initially
        $programs = collect(); // Empty collection initially
        $roles = Role::where('is_student', true)->get();
        return view('students.create', compact('sessions', 'levels', 'programs', 'roles','examinationSessions'));
    }

    public function store(Request $request)
    {
        if (!$request->user()->hasPermissionTo('create_student')) {
            abort(403, 'You are not authorized to create students.');
        }


        $rules = [
            // Personal
            'name' => 'required|string|max:100',
            'cnic' => 'required|string|max:15|unique:students',
            'father_name' => 'required|string|max:100',
            'father_cnic' => 'nullable|string|max:15',
            'gender' => 'required|in:female,transgender',
            'date_of_birth' => 'nullable|date',
            'religion' => 'nullable|string|max:50',
            'blood_group' => 'nullable|string|max:5',
            'student_contact' => 'nullable|string|max:20',
            'whatsapp_no' => 'nullable|string|max:20',
            'parent_contact' => 'nullable|string|max:20',
            'whatsapp' => 'nullable|string|max:20',
            'email' => 'nullable|email|unique:students,email',

            // Academic
            'registration_number' => 'nullable|unique:students',
            'roll_number' => 'required|unique:students',
            'status' => 'required|in:studying,pass_out,graduated',
            'academic_session_id' => 'required|exists:academic_sessions,id',
            'study_level_id' => 'required|exists:study_levels,id',
            'program_id' => 'required|exists:programs,id',
            'examination_session_id' => 'required|exists:examination_sessions,id',
            'program_class_id'=> 'required|exists:program_classes,id',

            // Previous Education
            'matric_*' => 'nullable',
            'intermediate_*' => 'nullable',
            'graduation_*' => 'nullable',

            // Other
            'hafiz' => 'nullable|in:on,1,true,0,false',
            'father_job' => 'nullable|in:on,1,true,0,false',
            'father_department' => 'nullable|string|max:100',
            'father_designation' => 'nullable|string|max:100',


            // Optional login
            'generateLogin' => 'sometimes|accepted',
            'username' => 'required_with:generateLogin|nullable|max:100|unique:users,username',
            'password' => 'required_with:generateLogin|nullable|min:6',
            'role_id' => 'required_with:generateLogin|exists:roles,id',

            'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ];


        try {
            $validated = $request->validate($rules);
        } catch (ValidationException $e) {
            dd($e->errors());
        }


        DB::beginTransaction();

        try {
            $studentData = $request->except(['photo', 'generateLogin', 'username', 'password', 'role_id']);
            $studentData['created_by'] = auth()->id();
            $studentData['is_active'] = $request->boolean('is_active', true);

            $studentData['hafiz'] = $request->boolean('hafiz', false);
            $studentData['father_job'] = $request->boolean('father_job', false);
            //dd($studentData);
            $student = Student::create($studentData);

            if ($request->hasFile('photo')) {
                try {
                    $file = $request->file('photo');
                    $filename = $request->cnic . '_' . str_replace(' ', '_', $request->name);

                    $student->addMedia($file)
                        ->usingFileName($filename . '.' . $file->getClientOriginalExtension())
                        ->toMediaCollection('student');

                } catch (\Exception $e) {
                    Log::error('Student photo upload failed: ' . $e->getMessage(), [
                        'trace' => $e->getTraceAsString(),
                        'student_id' => $student->id ?? null,
                        'filename' => $filename ?? null,
                    ]);

                    $request->session()->flash('toastr', [
                        'type' => 'error',
                        'message' => 'Photo upload failed: ' . $e->getMessage(),
                        'title' => 'Error!',
                        'options' => [
                            'timeOut' => 4000,
                            'progressBar' => true,
                            'closeButton' => true,
                        ],
                    ]);

                    // Optional: You may choose to throw again if you want the entire transaction to rollback
                    // throw $e;
                }
            }


            $firstProgramClass = ProgramClass::where('program_id', $request->program_id)
                ->where('id', $request->program_class_id)
                ->orderBy('id', 'asc') // fallback if multiple "first" classes exist
                ->first();

            if (!$firstProgramClass) {
                throw new \Exception('No program class found for the selected program.');
            }

            $enrollment = Enrollment::create([
                'student_id' => $student->id,
                'program_id' => $request->program_id,
                'program_class_id' => $firstProgramClass->id,
                'academic_session_id' => $request->academic_session_id,
                'examination_session_id' => $request->examination_session_id,
                'enrolled_on' => now(),
                'status' => 'enrolled',
                'created_by' => auth()->id(),
            ]);


            $mandatoryCourses = $firstProgramClass->courses()
                ->where('is_active', true)
                ->where('is_mandatory', true)
                ->get();

            $optionalCourses = collect();

            if ($request->has('courses')) {
                $optionalCourses = Course::whereIn('id', $request->courses)
                    ->where('is_active', true)
                    ->where('is_mandatory', false)
                    ->get();
            }

            $allCourses = $mandatoryCourses->merge($optionalCourses);

            // 5. Enroll in each course and assign section
            foreach ($allCourses as $course) {
                try {
                    $section = $this->assignCourseSection($course);
                } catch (ValidationException $ex) {
                    // Rollback and show error toaster if section cannot be created/assigned
                    DB::rollBack();
                    return redirect()->back()
                        ->withInput()
                        ->with('toastr', [
                            'type' => 'error',
                            'message' => $ex->errors()['sections'][0] ?? 'Failed to assign course section.',
                            'title' => 'Section Assignment Error',
                            'options' => ['timeOut' => 5000, 'progressBar' => true, 'closeButton' => true],
                        ]);
                }
                EnrollmentDetail::create([
                    'enrollment_id' => $enrollment->id,
                    'course_id' => $course->id,
                    'is_mandatory' => $course->is_mandatory,
                    'course_section_id' => optional($section)->id,
                    'status'=> 'enrolled',
                ]);

                // Update enrolled count
                if ($section) {
                    $section->increment('no_of_students_enrolled');
                }
            }


            if ($request->has('generateLogin')) {
                $user = User::create([
                    'username' => $request->username,
                    'email' => $student->email ?? null,
                    'password' => Hash::make($request->password),
                    'name' => $student->name,
                ]);

                $role = Role::findOrFail($request->role_id);
                $user->assignRole($role->name);

                $student->update([
                    'user_id' => $user->id,
                    'temporary_password' => base64_encode($request->password),
                ]);
            }

            DB::commit();

            $request->session()->flash('toastr', [
                'type' => 'success',
                'message' => 'Student created successfully.',
                'title' => 'Success!',
                'options' => ['timeOut' => 3000, 'progressBar' => true, 'closeButton' => true],
            ]);

            return redirect()->route('students.index');

        } catch (\Exception $e) {
            DB::rollBack();
           // dd($e->getMessage());
            $request->session()->flash('toastr', [
                'type' => 'error',
                'message' => 'Failed to create student: ' . $e->getMessage(),
                'title' => 'Error!',
                'options' => ['timeOut' => 4000, 'progressBar' => true, 'closeButton' => true],
            ]);

            return redirect()->back()->withInput();
        }
    }

    protected function assignCourseSection(Course $course): ?CourseSection
    {
        // Get all active sections for this course and program
        $sections = CourseSection::active()
            ->where('course_id', $course->id)
            ->where('program_id', $course->program_id)
            ->get();

        // Try to find an available section first
        foreach ($sections as $section) {
            // If no limit (0 means unlimited), or capacity not yet full, assign this section
            if (
                $section->no_of_students_allowed == 0 ||
                $section->no_of_students_enrolled < $section->no_of_students_allowed
            ) {
                return $section;
            }
        }

        $sectionCount = $sections->count();
        $maxSections = $course->no_of_sections;

        // Check if new section can be created (0 means unlimited number of sections)
        if ($maxSections == 0 || $sectionCount < $maxSections) {
            return CourseSection::create([
                'name' => $course->name . ' - Section ' . ($sectionCount + 1),
                'description' => 'Auto-generated section',
                'program_id' => $course->program_id,
                'course_id' => $course->id,
                'teacher_id' => $course->teacher_id ?? null,
                'is_active' => true,
                'no_of_students_allowed' => $course->students_per_section ?? 30,
                'no_of_students_enrolled' => 0,
                'has_lab' => $course->has_lab ?? false,
                'requires_continuous_slots' => $course->requires_continuous_slots ?? false,
                'credit_hours' => $course->credit_hours ?? 0,
                'lab_credit_hours' => $course->lab_credit_hours ?? 0,
                'required_minutes_theory_weekly' => $course->required_minutes_theory_weekly ?? 0,
                'required_minutes_lab_weekly' => $course->required_minutes_lab_weekly ?? 0,
                'weekly_lectures' => $course->weekly_lectures ?? 0,
                'weekly_labs' => $course->weekly_labs ?? 0,
                'created_by' => auth()->id(),
                'updated_by' => auth()->id(),
            ]);

        }

        // No sections available and max sections already created
        throw ValidationException::withMessages([
            'courses.' . $course->id => ["All sections full for course: {$course->name}"]
        ]);
    }


    public function edit(Request $request, Student $student)
    {
        if (!$request->user()->hasPermissionTo('update_student')) {
            abort(403, 'You are not authorized to edit students.');
        }

        // Load all necessary data for the form
        $sessions = AcademicSession::active()->admission()->orderBy('name', 'desc')->get();
        $levels = StudyLevel::orderBy('name')->get();
        $programs = Program::orderBy('name')->get();

        return view('students.edit', compact('student', 'sessions', 'levels', 'programs'));
    }

    public function update(Request $request, Student $student)
    {
        if (!$request->user()->hasPermissionTo('update_student')) {
            abort(403, 'You are not authorized to update students.');
        }

        $rules = [
            // Personal
            'name' => 'required|string|max:100',
            'cnic' => 'required|string|max:15|unique:students,cnic,' . $student->id,
            'father_name' => 'required|string|max:100',
            'father_cnic' => 'nullable|string|max:15',
            'gender' => 'required|in:female,transgender',
            'date_of_birth' => 'nullable|date',
            'religion' => 'nullable|string|max:50',
            'blood_group' => 'nullable|string|max:5',
            'student_contact' => 'nullable|string|max:20',
            'whatsapp_no' => 'nullable|string|max:20',
            'parent_contact' => 'nullable|string|max:20',
            'whatsapp' => 'nullable|string|max:20',
            'email' => 'nullable|email|unique:students,email,' . $student->id,

            // Academic
            'registration_number' => 'nullable|unique:students,registration_number,' . $student->id,
            'roll_number' => 'required|unique:students,roll_number,' . $student->id,
            'status' => 'required|in:studying,pass_out,graduated',
//            'academic_session_id' => 'required|exists:academic_sessions,id',
//            'study_level_id' => 'required|exists:study_levels,id',
//            'program_id' => 'required|exists:programs,id',

            // Previous Education
            'matric_*' => 'nullable',
            'intermediate_*' => 'nullable',
            'graduation_*' => 'nullable',

            // Other
            'is_hafiz' => 'nullable|in:on,1,true,0,false',
            'father_job' => 'nullable|in:on,1,true,0,false',
            'father_department' => 'nullable|string|max:100',
            'father_designation' => 'nullable|string|max:100',

            // Photo
            'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ];
       // dd($request->all());
        $validated = $request->validate($rules);

        DB::beginTransaction();

        try {
            $data = $request->except(['photo', 'generateLogin', 'username', 'password', 'role_id']);

            // Normalize boolean values
            $data['is_hafiz'] = $request->has('is_hafiz') ? 1 : 0;
            $data['father_job'] = $request->has('father_job') ? 1 : 0;
            $data['is_active'] = $request->has('is_active') ? 1 : 0;
           // dd($data);
            $student->update($data);

            if ($request->hasFile('photo')) {
                $file = $request->file('photo');
                $filename = $request->cnic . '_' . str_replace(' ', '_', $request->name);

                try {
                    $student->addMedia($file)
                        ->usingFileName($filename . '.' . $file->getClientOriginalExtension())
                        ->toMediaCollection('student');
                } catch (\Exception $e) {
                    Log::error('Media upload failed: ' . $e->getMessage());
                    throw $e;
                }
            }

            DB::commit();

            $request->session()->flash('toastr', [
                'type' => 'success',
                'message' => 'Student updated successfully.',
                'title' => 'Success!',
                'options' => [
                    'timeOut' => 2000,
                    'progressBar' => true,
                    'closeButton' => true,
                ],
            ]);

            return redirect()->route('students.index');

        } catch (\Exception $e) {
            DB::rollBack();

            $request->session()->flash('toastr', [
                'type' => 'error',
                'message' => 'Failed to update student: ' . $e->getMessage(),
                'title' => 'Error!',
                'options' => [
                    'timeOut' => 4000,
                    'progressBar' => true,
                    'closeButton' => true,
                ],
            ]);
            return redirect()->back()->withInput();
        }
    }


    public function destroy(Student $student, Request $request)
    {
        if (!$request->user()->hasPermissionTo('delete_student')) {
            abort(403, 'You are not authorized to edit students.');
        }
        $student->delete();

        if ($student->user_id) {
            $user = User::find($student->user_id);
            if ($user) {
                $user->status = false;
                $user->save();
            }
        }

        $request->session()->flash('toastr', [
            'type' => 'success',
            'message' => 'Student deleted successfully and user account disabled.',
            'title' => 'Success!',
            'options' => [
                'timeOut' => 2000,
                'progressBar' => true,
                'closeButton' => true,
            ],
        ]);

        return redirect()->route('students.index');
    }

    public function restore($id, Request $request)
    {
        if (!$request->user()->hasPermissionTo('update_student')) {
            abort(403, 'You are not authorized to edit students.');
        }
        $student = Student::withTrashed()->findOrFail($id);
        $student->restore();

        $request->session()->flash('toastr', [
            'type' => 'success',
            'message' => 'Student restored successfully.',
            'title' => 'Success!',
            'options' => [
                'timeOut' => 2000,
                'progressBar' => true,
                'closeButton' => true,
            ],
        ]);

        return redirect()->route('students.index');
    }

    public function forceDelete($id, Request $request)
    {
        if (!$request->user()->hasPermissionTo('delete_student')) {
            abort(403, 'You are not authorized to edit students.');
        }
        $student = Student::withTrashed()->findOrFail($id);

        if ($student->hasMedia('student')) {
            $student->clearMediaCollection('student');
        }

        $student->forceDelete();

        return redirect()->route('students.index')
            ->with('success', 'Student permanently deleted.');
    }

    public function show(Student $student)
    {
        if (!auth()->user()->can('view_student')) {
            abort(403, 'You are not authorized to view students.');
        }

        $student->load([
            'enrollments.program',
            'enrollments.programClass',
            'enrollments.academicSession',
            'enrollments.examinationSession',
            'enrollments.details.course', // Load nested relationships
            'enrollments.details.courseSection', // Load nested relationships
            'enrollments.details.courseSection.teachers', // Load nested relationships
        ]);
        //dd($student);
        return view('students.show', compact('student'));
    }

    public function editCourse(Student $student)
    {
        $enrollment = Enrollment::with('details.course')
            ->where('student_id', $student->id)
            ->where('status', 'enrolled')
            ->latest()
            ->first();

       // dd($enrollment);
        if (!$enrollment) {
            abort(404, 'Student is not currently enrolled.');
        }

        $currentCourseIds = $enrollment->details->pluck('course_id')->toArray();

        $programClass = $enrollment->programClass;

        $mandatoryCourses = $programClass->courses()
            ->where('is_active', true)
            ->where('is_mandatory', true)
            ->get();

        $optionalCourses = $programClass->courses()
            ->where('is_active', true)
            ->where('is_mandatory', false)
            ->get();

        return view('students.course_edit', compact('student', 'enrollment', 'mandatoryCourses', 'optionalCourses', 'currentCourseIds'));
    }

    public function updateCourse(Request $request, Student $student)
    {
        if (!$request->user()->hasPermissionTo('update_student')) {
            abort(403, 'You are not authorized to update students.');
        }

        $request->validate([
            'optional_courses' => 'nullable|array',
            'optional_courses.*' => 'exists:courses,id',
        ]);

        DB::beginTransaction();

        try {
            $enrollment = $student->enrollments()
                ->where('program_id', $student->program_id)
                ->where('academic_session_id', $student->academic_session_id)
                ->where('status', 'enrolled')
                ->latest()
                ->first();

            if (!$enrollment) {
                throw new \Exception("Enrollment record not found.");
            }

            $existingDetails = $enrollment->details()->get();

            $existingMandatoryCourseIds = $existingDetails
                ->where('is_mandatory', true)
                ->pluck('course_id')
                ->toArray();

            $newOptionalCourseIds = collect($request->input('optional_courses', []))->map(fn($id) => (int) $id)->toArray();

            $currentOptionalCourseIds = $existingDetails
                ->where('is_mandatory', false)
                ->pluck('course_id')
                ->toArray();

            $toAdd = array_diff($newOptionalCourseIds, $currentOptionalCourseIds);
            $toRemove = array_diff($currentOptionalCourseIds, $newOptionalCourseIds);

            // Mark dropped optional courses
            if (!empty($toRemove)) {
                foreach ($toRemove as $courseId) {
                    $detail = $existingDetails->firstWhere('course_id', $courseId);
                    if ($detail && $detail->status !== 'dropped') {
                        // Decrement section enrollment if assigned
                        if ($detail->course_section_id) {
                            $section = CourseSection::find($detail->course_section_id);
                                if ($section && $section->no_of_students_enrolled > 0) {
                                    $section->decrement('no_of_students_enrolled');
                                }

                        }

                        $detail->update(['status' => 'dropped']);
                    }
                }
            }

            // Add newly selected optional courses
            foreach ($toAdd as $courseId) {
                $course = Course::active()
                    ->where('is_mandatory', false)
                    ->findOrFail($courseId);

                try {
                    $section = $this->assignCourseSection($course);
                } catch (ValidationException $ex) {
                    DB::rollBack();
                    return redirect()->back()
                        ->withInput()
                        ->with('toastr', [
                            'type' => 'error',
                            'message' => $ex->errors()['courses.' . $courseId][0] ?? 'Failed to assign section.',
                            'title' => 'Section Assignment Error',
                            'options' => ['timeOut' => 5000, 'progressBar' => true, 'closeButton' => true],
                        ]);
                }

                EnrollmentDetail::create([
                    'enrollment_id' => $enrollment->id,
                    'course_id' => $course->id,
                    'is_mandatory' => false,
                    'course_section_id' => optional($section)->id,
                    'status' => 'enrolled',
                ]);

                if ($section) {
                    $section->increment('no_of_students_enrolled');
                }
            }

            DB::commit();

            return redirect()->route('students.index')->with('toastr', [
                'type' => 'success',
                'message' => 'Student course enrollment updated successfully.',
                'title' => 'Success!',
                'options' => ['timeOut' => 3000, 'progressBar' => true, 'closeButton' => true],
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withInput()->with('toastr', [
                'type' => 'error',
                'message' => 'Failed to update course enrollment: ' . $e->getMessage(),
                'title' => 'Error!',
                'options' => ['timeOut' => 4000, 'progressBar' => true, 'closeButton' => true],
            ]);
        }
    }


    public function changeProgramForm(Student $student)
    {
        if (!auth()->user()->can('update_student')) {
            abort(403, 'Unauthorized');
        }

        $enrollment = $student->enrollments()
            ->where('status', 'enrolled')
            ->latest()->first();
        if (!$enrollment) {
            abort(404, 'Enrollment not found.');
        }

        if (!str_contains(strtolower($enrollment->programClass->name), 'first')) {
            return redirect()->route('students.index')->with('toastr', [
                'type' => 'error',
                'message' => 'Program change is only allowed in the first semester or year.',
                'title' => 'Not Allowed!',
                'options' => ['timeOut' => 3000, 'progressBar' => true, 'closeButton' => true],
            ]);
        }

        $academicSessionId = $student->academic_session_id;

        $sessions = AcademicSession::active()->admission()->get();
        $examinationSessions = ExaminationSession::active()->running()->get();

        return view('students.change_program', [
            'student' => $student,
            'enrollment' => $enrollment,
            'sessions' => $sessions,
            'examinationSessions'=>$examinationSessions,
        ]);
    }

    public function changeProgramUpdate(Request $request, Student $student)
    {
        // Ensure the user has permission to update student info
        if (!$request->user()->hasPermissionTo('update_student')) {
            abort(403, 'You are not authorized to update students.');
        }

        // Validate the input
        $request->validate([
            'program_id' => 'required|exists:programs,id',
            'academic_session_id' => 'required|exists:academic_sessions,id',
            'examination_session_id' => 'required|exists:examination_sessions,id',
            'courses' => 'nullable|array',
            'courses.*' => 'exists:courses,id',
        ]);

        DB::beginTransaction();

        try {
            // Cancel the student's previous enrollment for the given program & session
            $previousEnrollment = $student->enrollments()
                ->where('program_id', $student->program_id)
                ->where('academic_session_id', $student->academic_session_id)
                ->where('status', 'enrolled')
                ->latest()
                ->first();

            if ($previousEnrollment) {
                // Cancel all its enrollment details
                foreach ($previousEnrollment->details as $detail) {
                    if ($detail->course_section_id) {
                        $section = CourseSection::find($detail->course_section_id);
                        if ($section && $section->no_of_students_enrolled > 0) {
                            $section->decrement('no_of_students_enrolled');
                        }
                    }
                    $detail->update(['status' => 'cancelled']);
                }
            }

            $firstProgramClass = ProgramClass::where('program_id', $request->program_id)
                ->where('name', 'like', '%First%')
                ->orderBy('id', 'asc') // fallback if multiple "first" classes exist
                ->first();

            if (!$firstProgramClass) {
                throw new \Exception('No program class found for the selected program.');
            }

            // Create new enrollment
            $newEnrollment = Enrollment::create([
                'student_id' => $student->id,
                'program_id' => $request->program_id,
                'academic_session_id' => $request->academic_session_id,
                'program_class_id' => $firstProgramClass->id,
                'examination_session_id' => $request->examination_session_id,
                'status' => 'enrolled',
                'created_by' => $request->user()->id,
            ]);

            // Fetch all mandatory courses for the selected program
            $mandatoryCourses = Course::active()
                ->where('program_id', $request->program_id)
                ->where('program_class_id', $firstProgramClass->id)
                ->where('is_mandatory', true)
                ->get();

            // Enroll into mandatory courses
            foreach ($mandatoryCourses as $course) {
                try {
                    $section = $this->assignCourseSection($course);
                } catch (ValidationException $ex) {
                    DB::rollBack();
                    return redirect()->back()
                        ->withInput()
                        ->with('toastr', [
                            'type' => 'error',
                            'message' => $ex->errors()['courses.' . $course->id][0] ?? 'Failed to assign section.',
                            'title' => 'Section Assignment Error',
                            'options' => ['timeOut' => 5000, 'progressBar' => true, 'closeButton' => true],
                        ]);
                }



                EnrollmentDetail::create([
                    'enrollment_id' => $newEnrollment->id,
                    'course_id' => $course->id,
                    'is_mandatory' => true,
                    'course_section_id' => optional($section)->id,
                    'status' => 'enrolled',
                ]);

                if ($section) {
                    $section->increment('no_of_students_enrolled');
                }
            }

            // Handle optional courses passed from request
            $optionalCourseIds = collect($request->input('courses', []))->map(fn($id) => (int) $id)->toArray();

            foreach ($optionalCourseIds as $courseId) {
                $course = Course::active()
                    ->where('program_id', $request->program_id)
                    ->where('is_mandatory', false)
                    ->findOrFail($courseId);

                try {
                    $section = $this->assignCourseSection($course);
                } catch (ValidationException $ex) {
                    DB::rollBack();
                    return redirect()->back()
                        ->withInput()
                        ->with('toastr', [
                            'type' => 'error',
                            'message' => $ex->errors()['courses.' . $courseId][0] ?? 'Failed to assign section.',
                            'title' => 'Section Assignment Error',
                            'options' => ['timeOut' => 5000, 'progressBar' => true, 'closeButton' => true],
                        ]);
                }

                EnrollmentDetail::create([
                    'enrollment_id' => $newEnrollment->id,
                    'course_id' => $course->id,
                    'is_mandatory' => false,
                    'course_section_id' => optional($section)->id,
                    'status' => 'enrolled',
                ]);

                if ($section) {
                    $section->increment('no_of_students_enrolled');
                }
            }
            $student->update([
                'program_id' => $request->program_id,
                'academic_session_id' => $request->academic_session_id,
                'study_level_id' => $request->study_level_id,
            ]);
            $previousEnrollment->update(['status' => 'cancelled']);
            DB::commit(); // Everything is fine, commit transaction

            return redirect()->route('students.index')->with('toastr', [
                'type' => 'success',
                'message' => 'Student program and course enrollment updated successfully.',
                'title' => 'Success!',
                'options' => ['timeOut' => 3000, 'progressBar' => true, 'closeButton' => true],
            ]);
        } catch (\Exception $e) {
            DB::rollBack(); // On error, rollback everything
            return redirect()->back()->withInput()->with('toastr', [
                'type' => 'error',
                'message' => 'Failed to update enrollment: ' . $e->getMessage(),
                'title' => 'Error!',
                'options' => ['timeOut' => 4000, 'progressBar' => true, 'closeButton' => true],
            ]);
        }
    }



}
