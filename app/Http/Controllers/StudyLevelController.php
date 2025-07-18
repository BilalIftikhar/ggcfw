<?php

namespace App\Http\Controllers;

use App\Models\AcademicSession;
use App\Models\StudyLevel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class StudyLevelController extends Controller
{

    public function index(Request $request)
    {
        $sessionId = $request->input('session_id');

        if ($sessionId) {
            $studyLevels = StudyLevel::with('academicSession')
                ->where('academic_session_id', $sessionId)
                ->get();
            $academicSession = AcademicSession::findOrFail($sessionId);
            return view('study_levels.index', compact('studyLevels', 'academicSession'));
        }

        $studyLevels = StudyLevel::with('academicSession')->get();
        return view('study_levels.index', compact('studyLevels'));
    }

    public function store(Request $request)
    {
        if (!$request->user()->can('create_study_level')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $validated = $request->validate([
                'name' => [
                    'required',
                    'string',
                    'max:255',
                    Rule::unique('study_levels')->where(function ($query) use ($request) {
                        return $query->where('academic_session_id', $request->academic_session_id);
                    })
                ],
                'academic_session_id' => 'required|exists:academic_sessions,id',
            ]);

            StudyLevel::create([
                'name' => $validated['name'],
                'academic_session_id' => $validated['academic_session_id'],
                'created_by' => Auth::id(),
                'updated_by' => Auth::id(),
            ]);

            $request->session()->flash('toastr', [
                'type' => 'success',
                'message' => 'Study level created successfully.',
                'title' => 'Success!',
                'options' => [
                    'timeOut' => 2000,
                    'progressBar' => true,
                    'closeButton' => true,
                ],
            ]);

            return back(); // Redirects back instead of to index

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

    /**
     * Update the specified study level.
     */
    public function update(Request $request, StudyLevel $studyLevel)
    {
        if (!$request->user()->can('update_study_level')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $validated = $request->validate([
                'name' => [
                    'required',
                    'string',
                    'max:255',
                    Rule::unique('study_levels')
                        ->ignore($studyLevel->id)
                        ->where(function ($query) use ($request) {
                            return $query->where('academic_session_id', $request->academic_session_id);
                        })
                ],
                'academic_session_id' => 'required|exists:academic_sessions,id',
            ]);

            $studyLevel->update([
                'name' => $validated['name'],
                'academic_session_id' => $validated['academic_session_id'],
                'updated_by' => Auth::id(),
            ]);

            $request->session()->flash('toastr', [
                'type' => 'success',
                'message' => 'Study level updated successfully.',
                'title' => 'Updated!',
                'options' => [
                    'timeOut' => 2000,
                    'progressBar' => true,
                    'closeButton' => true,
                ],
            ]);

            return redirect()->route('study-levels.index');

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


    /**
     * Remove the specified study level.
     */
    public function destroy(Request $request, StudyLevel $studyLevel)
    {
        if (!$request->user()->hasPermissionTo('delete_study_level')) {
            abort(403, 'You are not authorized to delete study levels.');
        }

        // Check for related programs
        if ($studyLevel->programs()->exists()) {
            return back()->with([
                'toastr' => [
                    'type' => 'error',
                    'message' => 'Cannot delete study level with existing programs.',
                    'title' => 'Deletion Failed!',
                    'options' => [
                        'timeOut' => 3000,
                        'progressBar' => true,
                        'closeButton' => true,
                    ],
                ]
            ]);
        }

        $studyLevel->delete();

        return redirect()->route('study-levels.index')->with([
            'toastr' => [
                'type' => 'success',
                'message' => 'Study level deleted successfully.',
                'title' => 'Deleted!',
                'options' => [
                    'timeOut' => 2000,
                    'progressBar' => true,
                    'closeButton' => true,
                ],
            ]
        ]);
    }



}
