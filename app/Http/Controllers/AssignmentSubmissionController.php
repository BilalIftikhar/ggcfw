<?php

namespace App\Http\Controllers;

use App\Models\Assignment;
use App\Models\AssignmentSubmission;
use App\Models\EnrollmentDetail;
use App\Models\Student;
use App\Models\Teacher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AssignmentSubmissionController extends Controller
{
    public function store(Request $request)
    {
        if (!auth()->user()->hasPermissionTo('submit_assignment')) {
            abort(403, 'Unauthorized.');
        }

        $request->validate([
            'assignment_id' => 'required|exists:assignments,id',
            'file' => 'required|file|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx|max:10240', // 10MB
        ]);

        DB::beginTransaction();

        try {
            $user = auth()->user();

            $student = Student::where('user_id', $user->id)
                ->where('status', 'studying')
                ->where('is_active', true)
                ->firstOrFail();

            $alreadySubmitted = AssignmentSubmission::where('assignment_id', $request->assignment_id)
                ->where('student_id', $student->id)
                ->exists();

            if ($alreadySubmitted) {
                return redirect()->back()->with([
                    'toastr' => [
                        'type' => 'error',
                        'message' => 'You have already submitted this assignment.',
                        'title' => 'Submission Blocked',
                        'options' => ['timeOut' => 3000, 'progressBar' => true, 'closeButton' => true]
                    ]
                ]);
            }

            $submission = new AssignmentSubmission();
            $submission->assignment_id = $request->assignment_id;
            $submission->student_id = $student->id;
            $submission->attempts = 1;
            $submission->submitted_at = now();
            $submission->created_by = $user->id;
            $submission->updated_by = $user->id;
            $submission->save();

            if ($request->hasFile('file')) {
                $submission->addMediaFromRequest('file')
                    ->usingFileName(time() . '_' . $request->file('file')->getClientOriginalName())
                    ->toMediaCollection('submission');
            }

            DB::commit();

            return redirect()->route('assignments.show', $request->assignment_id)->with([
                'toastr' => [
                    'type' => 'success',
                    'message' => 'Assignment submitted successfully.',
                    'title' => 'Success!',
                    'options' => ['timeOut' => 2000, 'progressBar' => true, 'closeButton' => true]
                ]
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()->withInput()->with([
                'toastr' => [
                    'type' => 'error',
                    'message' => 'Error: ' . $e->getMessage(),
                    'title' => 'Submission Failed!',
                    'options' => ['timeOut' => 3000, 'progressBar' => true, 'closeButton' => true]
                ]
            ]);
        }
    }

    public function submissions(Assignment $assignment)
    {
        // Double check if the logged-in user is the teacher of this assignment
        $user = auth()->user();

        if (!auth()->user()->hasPermissionTo('view_submission')) {
            abort(403, 'Unauthorized.');
        }


        if ($user->roles()->where('is_teaching', true)->exists()) {
            $teacher = Teacher::where('user_id', $user->id)->where('is_active', true)->first();

            if (!$teacher || $assignment->teacher_id !== $teacher->id) {
                abort(403, 'You are not authorized to view submissions for this assignment.');
            }
        }

        // Get enrolled students based on course and section
        $studentIds = EnrollmentDetail::where('course_id', $assignment->course_id)
            ->where('course_section_id', $assignment->course_section_id)
            ->where('status', 'enrolled')
            ->whereHas('enrollment', function ($q) {
                $q->where('status', 'enrolled');
            })
            ->with('enrollment') // Eager-load enrollment to access student_id
            ->get()
            ->pluck('enrollment.student_id')
            ->unique(); // optional: to avoid duplicate IDs


        $students = Student::whereIn('id', $studentIds)
            ->with(['user', 'submissions' => function ($q) use ($assignment) {
                $q->where('assignment_id', $assignment->id);
            }])
            ->get();

        return view('assignments.submissions', compact('assignment', 'students'));
    }


}
