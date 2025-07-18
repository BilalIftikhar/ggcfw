<?php

namespace App\Http\Controllers;

use App\Models\AcademicSession;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class AcademicSessionController extends Controller
{

    // List all academic sessions (including soft deleted optionally)
    public function index(Request $request)
    {
        if (!$request->user()->hasPermissionTo('view_academic_session')) {
            abort(403, 'You are not authorized to view academic sessions.');
        }

        $sessions = AcademicSession::latest()->get();
        return view('academic_sessions.index', compact('sessions'));
    }



    public function store(Request $request)
    {
        if (!$request->user()->hasPermissionTo('create_academic_session')) {
            abort(403, 'You are not authorized to create academic sessions.');
        }

        try {
            $validated = $request->validate([
                'name' => 'required|string|unique:academic_sessions,name',
                'start_date' => 'required|date',
                'end_date' => 'required|date|after_or_equal:start_date',
                'admission_end_date' => 'required|date|after_or_equal:start_date|before_or_equal:end_date',
            ]);

            // If this new session is being set as active, deactivate all other sessions
            if ($request->has('is_active')) {
                AcademicSession::query()->update(['is_active' => false]);
            }

            AcademicSession::create([
                'name' => $validated['name'],
                'start_date' => $validated['start_date'],
                'end_date' => $validated['end_date'],
                'admission_end_date' => $validated['admission_end_date'],
                'allow_admission' => $request->has('allow_admission'),
                'is_active' => $request->has('is_active'),
                'created_by' => Auth::id(),
                'updated_by' => Auth::id(),
            ]);

            $request->session()->flash('toastr', [
                'type' => 'success',
                'message' => 'Academic session created successfully.',
                'title' => 'Success!',
                'options' => [
                    'timeOut' => 2000,
                    'progressBar' => true,
                    'closeButton' => true,
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
                ]
            ]);
        }

        return redirect()->route('academic-session.index');
    }

    public function update(Request $request, AcademicSession $academic_session)
    {
        if (!$request->user()->hasPermissionTo('update_academic_session')) {
            abort(403, 'You are not authorized to update academic sessions.');
        }

        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255|unique:academic_sessions,name,'.$academic_session->id,
                'start_date' => 'required|date',
                'end_date' => 'required|date|after_or_equal:start_date',
                'admission_end_date' => 'required|date|after_or_equal:start_date|before_or_equal:end_date',
            ]);

//            // If this session is being set as active, deactivate all other sessions
//            if ($request->has('is_active')) {
//                AcademicSession::where('id', '!=', $academic_session->id)
//                    ->update(['is_active' => false]);
//            }

            $academic_session->update([
                'name' => $validated['name'],
                'start_date' => $validated['start_date'],
                'end_date' => $validated['end_date'],
                'admission_end_date' => $validated['admission_end_date'],
                'allow_admission' => $request->has('allow_admission'),
                'is_active' => $request->has('is_active'),
                'updated_by' => Auth::id(),
            ]);

            $request->session()->flash('toastr', [
                'type' => 'success',
                'message' => 'Academic session updated successfully.',
                'title' => 'Updated!',
                'options' => [
                    'timeOut' => 2000,
                    'progressBar' => true,
                    'closeButton' => true,
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
                ]
            ]);
        }

        return redirect()->route('academic-session.index');
    }


    public function destroy(Request $request, AcademicSession $academic_session)
    {
        if (!$request->user()->hasPermissionTo('delete_academic_session')) {
            abort(403, 'You are not authorized to delete academic sessions.');
        }

        // Check for related study levels
        if ($academic_session->studyLevels()->exists()) {
            return back()->with([
                'toastr' => [
                    'type' => 'error',
                    'message' => 'Cannot delete session with existing study levels.',
                    'title' => 'Deletion Failed!',
                    'options' => [
                        'timeOut' => 3000,
                        'progressBar' => true,
                        'closeButton' => true,
                    ],
                ]
            ]);
        }

        $academic_session->delete();

        $request->session()->flash('toastr', [
            'type' => 'success',
            'message' => 'Academic session deleted successfully.',
            'title' => 'Deleted!',
            'options' => [
                'timeOut' => 2000,
                'progressBar' => true,
                'closeButton' => true,
            ],
        ]);

        return redirect()->route('academic-session.index');
    }


    /**
     * Transfer academic session data to a new session
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function transferSessionData(Request $request): \Illuminate\Http\RedirectResponse
    {
        if (!$request->user()->hasPermissionTo('create_academic_session')) {
            abort(403, 'You are not authorized to perform this action.');
        }

        try {
            $validated = $request->validate([
                'source_session_id' => [
                    'required',
                    'exists:academic_sessions,id',
                    'different:target_session_id'
                ],
                'target_session_id' => [
                    'required',
                    'exists:academic_sessions,id',
                    'different:source_session_id'
                ],
            ], [
                'source_session_id.different' => 'Source and Target sessions must be different.',
                'target_session_id.different' => 'Source and Target sessions must be different.',
            ]);

            // Additional manual validation
            if ($validated['source_session_id'] === $validated['target_session_id']) {
                throw ValidationException::withMessages([
                    'target_session_id' => 'Source and Target sessions cannot be the same.'
                ]);
            }

            DB::beginTransaction();

            $sourceSession = AcademicSession::findOrFail($validated['source_session_id']);
            $targetSession = AcademicSession::findOrFail($validated['target_session_id']);

            // Copy Study Levels
            foreach ($sourceSession->studyLevels as $sourceStudyLevel) {
                $newStudyLevel = $sourceStudyLevel->replicate();
                $newStudyLevel->academic_session_id = $targetSession->id;
                $newStudyLevel->created_by = Auth::id();
                $newStudyLevel->save();

                // Copy Programs for each Study Level
                foreach ($sourceStudyLevel->programs as $sourceProgram) {
                    $newProgram = $sourceProgram->replicate();
                    $newProgram->academic_session_id = $targetSession->id;
                    $newProgram->study_level_id = $newStudyLevel->id;
                    $newProgram->created_by = Auth::id();
                    $newProgram->save();

                    // Copy Program Classes and their courses in a single loop
                    foreach ($sourceProgram->classes as $sourceClass) {
                        // Create new class
                        $newClass = $sourceClass->replicate();
                        $newClass->program_id = $newProgram->id;
                        $newClass->save();

                        // Immediately copy all courses for this class
                        $courses = Course::where('class_id', $sourceClass->id)
                            ->where('program_id', $sourceProgram->id)
                            ->get();

                        foreach ($courses as $sourceCourse) {
                            $newCourse = $sourceCourse->replicate();
                            $newCourse->program_id = $newProgram->id;
                            $newCourse->class_id = $newClass->id;
                            $newCourse->created_by = Auth::id();
                            $newCourse->save();
                        }
                    }
                }
            }

            DB::commit();

            return redirect()->route('academic-session.index')->with([
                'toastr' => [
                    'type' => 'success',
                    'message' => 'Academic session data transferred successfully.',
                    'title' => 'Success!',
                    'options' => [
                        'timeOut' => 2000,
                        'progressBar' => true,
                        'closeButton' => true,
                    ],
                ],
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            return back()->with([
                'toastr' => [
                    'type' => 'error',
                    'message' => 'Failed to transfer academic session data: ' . $e->getMessage(),
                    'title' => 'Error!',
                    'options' => [
                        'timeOut' => 3000,
                        'progressBar' => true,
                        'closeButton' => true,
                    ],
                ],
            ]);
        }
    }

    public function transferSessionForm()
    {
        if (!auth()->user()->hasPermissionTo('create_academic_session')) {
            abort(403, 'You are not authorized to perform this action.');
        }

        $sessions = AcademicSession::orderBy('name', 'desc')->get();
        return view('academic_sessions.transfer', compact('sessions'));
    }
}
