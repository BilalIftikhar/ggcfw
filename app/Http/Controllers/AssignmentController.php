<?php

namespace App\Http\Controllers;

use App\Helpers\UserHelper;
use App\Models\AcademicSession;
use App\Models\Assignment;
use App\Models\AssignmentSubmission;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AssignmentController extends Controller
{

    public function index()
    {
        if (!auth()->user()->hasPermissionTo('view_assignment')) {
            abort(403, 'You are not authorized to view assignments.');
        }
        $user = auth()->user();

        if ($user->hasRole('student')) {
            $assignments = UserHelper::getStudentAssignments($user);
        } elseif ($user->hasRole('teacher')) {
            $assignments = UserHelper::getTeacherAssignments($user);
        } else {
            // For admins/other roles, show all assignments
            $assignments = Assignment::with(['teacher', 'course'])->latest()->get();
        }


        $academicSessions = AcademicSession::all();
        $students = Student::with('user')->get();


        return view('assignments.index', compact('assignments', 'academicSessions', 'students'));
    }

    public function create(Request $request)
    {
        if (!$request->user()->hasPermissionTo('create_assignment')) {
            abort(403, 'You are not authorized to create assignments.');
        }

        $academicSessions = AcademicSession::active()->get(); // Only active sessions
        return view('assignments.create', compact('academicSessions'));
    }

    public function store(Request $request)
    {
        if (!auth()->user()->hasPermissionTo('create_assignment')) {
            abort(403, 'Unauthorized.');
        }

       // dd($request->all());
        try {
            $validated = $request->validate([
                'program_id' => 'required|exists:programs,id',
                'program_class_id' => 'required|exists:program_classes,id',
                'course_id' => 'required|exists:courses,id',
                'course_section_id' => 'required|exists:course_sections,id',
                'teacher_id' => 'required|exists:teachers,id',
                'title' => 'required|string|max:255',
                'description' => 'nullable|string',
                'due_date' => 'required|date|after_or_equal:today',
                'attachment' => 'nullable|file|max:10240', // 10MB
            ]);

            DB::beginTransaction();

            $assignment = Assignment::create([
                'program_id' => $validated['program_id'],
                'program_class_id' => $validated['program_class_id'],
                'course_id' => $validated['course_id'],
                'course_section_id' => $validated['course_section_id'],
                'teacher_id' => $validated['teacher_id'],
                'title' => $validated['title'],
                'description' => $validated['description'] ?? null,
                'due_date' => $validated['due_date'],
            ]);

            if ($request->hasFile('attachment')) {
                $assignment->addMediaFromRequest('attachment')->toMediaCollection('attachment');
            }

            DB::commit();

            return redirect()->route('assignments.index')->with([
                'toastr' => [
                    'type' => 'success',
                    'message' => 'Assignment created successfully.',
                    'title' => 'Success!',
                    'options' => ['timeOut' => 2000, 'progressBar' => true, 'closeButton' => true]
                ]
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with([
                'toastr' => [
                    'type' => 'error',
                    'message' => 'Error: ' . $e->getMessage(),
                    'title' => 'Error!',
                    'options' => ['timeOut' => 3000, 'progressBar' => true, 'closeButton' => true]
                ]
            ]);
        }
    }

    public function show(Assignment $assignment)
    {
        if (!auth()->user()->hasPermissionTo('view_assignment')) {
            abort(403, 'You are not authorized to view assignments.');
        }

        $assignment->load([
            'program',
            'course',
            'section',
            'teacher',
        ]);

        $user = auth()->user();

        $hasAccess = false;
        $alreadySubmitted = false;
        $submittedFileUrl = null;

        // STUDENT ACCESS CHECK
        if ($user->hasRole('student')) {
            $studentAssignments = UserHelper::getStudentAssignments($user);

            if ($studentAssignments->contains('id', $assignment->id)) {
                $hasAccess = true;

                $student = Student::where('user_id', $user->id)
                    ->where('status', 'studying')
                    ->where('is_active', true)
                    ->first();

                $submission = AssignmentSubmission::where('assignment_id', $assignment->id)
                    ->where('student_id', $student->id)
                    ->first();

                if ($submission) {
                    $alreadySubmitted = true;
                    $submittedFileUrl = $submission->getFirstMediaUrl('submission');
                }
            }
        }
        // TEACHER ACCESS CHECK
        elseif ($user->hasRole('teacher')) {
            $teacherAssignments = UserHelper::getTeacherAssignments($user);

            if ($teacherAssignments->contains('id', $assignment->id)) {
                $hasAccess = true;
            }
        }
        // ADMIN or STAFF
        else {
            $hasAccess = true;
        }

        if (!$hasAccess) {
            abort(403, 'You are not authorized to view this assignment.');
        }

        return view('assignments.show', compact('assignment', 'alreadySubmitted', 'submittedFileUrl'));
    }


    public function edit(Assignment $assignment)
    {
        if (!auth()->user()->hasPermissionTo('update_assignment')) {
            abort(403, 'You are not authorized to edit assignments.');
        }

        $assignment->load([
            'program.academicSession',
            'program.studyLevel',
            'course.class', // Add this if programClass is accessed through course
            'section',
            'teacher'
        ]);
       // dd($assignment);
        $academicSessions = AcademicSession::active()->get();
        return view('assignments.edit', compact('assignment', 'academicSessions'));
    }


    public function update(Request $request, Assignment $assignment)
    {
        if (!auth()->user()->hasPermissionTo('update_assignment')) {
            abort(403, 'Unauthorized.');
        }

        try {
            $validated = $request->validate([
                'program_id' => 'required|exists:programs,id',
                'program_class_id' => 'required|exists:program_classes,id',
                'course_id' => 'required|exists:courses,id',
                'course_section_id' => 'required|exists:course_sections,id',
                'teacher_id' => 'required|exists:teachers,id',
                'title' => 'required|string|max:255',
                'description' => 'nullable|string',
                'due_date' => 'required|date|after_or_equal:today',
                'attachment' => 'nullable|file|max:10240', // 10MB
            ]);

            DB::beginTransaction();

            $assignment->update([
                'program_id' => $validated['program_id'],
                'program_class_id' => $validated['program_class_id'],
                'course_id' => $validated['course_id'],
                'course_section_id' => $validated['course_section_id'],
                'teacher_id' => $validated['teacher_id'],
                'title' => $validated['title'],
                'description' => $validated['description'] ?? null,
                'due_date' => $validated['due_date'],
            ]);

            // Replace attachment if a new one is uploaded
            if ($request->hasFile('attachment')) {
                $assignment->clearMediaCollection('attachment');
                $assignment->addMediaFromRequest('attachment')->toMediaCollection('attachment');
            }

            DB::commit();

            return redirect()->route('assignments.index')->with([
                'toastr' => [
                    'type' => 'success',
                    'message' => 'Assignment updated successfully.',
                    'title' => 'Updated!',
                    'options' => ['timeOut' => 2000, 'progressBar' => true, 'closeButton' => true]
                ]
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with([
                'toastr' => [
                    'type' => 'error',
                    'message' => 'Update Failed: ' . $e->getMessage(),
                    'title' => 'Error!',
                    'options' => ['timeOut' => 3000, 'progressBar' => true, 'closeButton' => true]
                ]
            ]);
        }
    }


    public function destroy(Request $request, Assignment $assignment)
    {
        if (!auth()->user()->hasPermissionTo('delete_assignment')) {
            abort(403, 'You are not authorized to delete assignments.');
        }

        // Delete associated media (attachment) first
        $assignment->clearMediaCollection('attachment');

        // Then delete the assignment
        $assignment->delete();

        return redirect()->route('assignments.index')->with([
            'toastr' => [
                'type' => 'success',
                'message' => 'Assignment deleted successfully.',
                'title' => 'Deleted!',
                'options' => [
                    'timeOut' => 2000,
                    'progressBar' => true,
                    'closeButton' => true,
                ]
            ]
        ]);
    }
}
