<?php

namespace App\Http\Controllers;

use App\Models\ExaminationSession;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class ExaminationSessionController extends Controller
{
    public function index(Request $request)
    {
        if (!$request->user()->hasPermissionTo('view_examination_session')) {
            abort(403, 'You are not authorized to view examination sessions.');
        }

        $sessions = ExaminationSession::latest()->get();
        return view('examination_sessions.index', compact('sessions'));
    }

    public function store(Request $request)
    {
        if (!$request->user()->hasPermissionTo('create_examination_session')) {
            abort(403, 'You are not authorized to create examination sessions.');
        }

        try {
            $validated = $request->validate([
                'title' => 'required|string|max:255|unique:examination_sessions,title',
                'start_date' => 'required|date',
                'end_date' => 'required|date|after_or_equal:start_date',
                'description' => 'nullable|string',
            ]);

            if ($request->has('is_active')) {
                ExaminationSession::query()->update(['is_active' => false]);
            }

            ExaminationSession::create([
                'title' => $validated['title'],
                'start_date' => $validated['start_date'],
                'end_date' => $validated['end_date'],
                'description' => $validated['description'] ?? null,
                'is_active' => $request->has('is_active'),
                'is_running' => $request->has('is_running'),
                'is_examination_taken' => $request->has('is_examination_taken'),
                'created_by' => Auth::id(),
            ]);

            $request->session()->flash('toastr', [
                'type' => 'success',
                'message' => 'Examination session created successfully.',
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
                ],
            ]);
        }

        return redirect()->route('examination-session.index');
    }

    public function update(Request $request, ExaminationSession $examination_session)
    {
        if (!$request->user()->hasPermissionTo('update_examination_session')) {
            abort(403, 'You are not authorized to update examination sessions.');
        }

        try {
            $validated = $request->validate([
                'title' => 'required|string|max:255|unique:examination_sessions,title,' . $examination_session->id,
                'start_date' => 'required|date',
                'end_date' => 'required|date|after_or_equal:start_date',
                'description' => 'nullable|string',
            ]);

            if ($request->has('is_active')) {
                ExaminationSession::where('id', '!=', $examination_session->id)
                    ->update(['is_active' => false]);
            }

            $examination_session->update([
                'title' => $validated['title'],
                'start_date' => $validated['start_date'],
                'end_date' => $validated['end_date'],
                'description' => $validated['description'] ?? null,
                'is_active' => $request->has('is_active'),
                'is_running' => $request->has('is_running'),
                'is_examination_taken' => $request->has('is_examination_taken'),
                'updated_by' => Auth::id(),
            ]);

            $request->session()->flash('toastr', [
                'type' => 'success',
                'message' => 'Examination session updated successfully.',
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
                ],
            ]);
        }

        return redirect()->route('examination-session.index');
    }

    public function destroy(Request $request, ExaminationSession $examination_session)
    {
        if (!$request->user()->hasPermissionTo('delete_examination_session')) {
            abort(403, 'You are not authorized to delete examination sessions.');
        }

        if ($examination_session->enrollments()->exists()) {
            return back()->with([
                'toastr' => [
                    'type' => 'error',
                    'message' => 'Cannot delete session with existing enrollments.',
                    'title' => 'Deletion Failed!',
                    'options' => [
                        'timeOut' => 3000,
                        'progressBar' => true,
                        'closeButton' => true,
                    ],
                ],
            ]);
        }

        $examination_session->delete();

        $request->session()->flash('toastr', [
            'type' => 'success',
            'message' => 'Examination session deleted successfully.',
            'title' => 'Deleted!',
            'options' => [
                'timeOut' => 2000,
                'progressBar' => true,
                'closeButton' => true,
            ],
        ]);

        return redirect()->route('examination-session.index');
    }


    /**
     * Toggle the is_running status of an examination session.
     */
    public function toggleRunning(Request $request, ExaminationSession $examination_session)
    {
        if (!$request->user()->hasPermissionTo('update_examination_session')) {
            abort(403, 'You are not authorized to perform this action.');
        }

        try {
            $examination_session->is_running = !$examination_session->is_running;
            $examination_session->updated_by = $request->user()->id;
            $examination_session->save();

            return back()->with([
                'toastr' => [
                    'type' => 'success',
                    'message' => 'Session running status updated successfully.',
                    'title' => 'Updated!',
                    'options' => [
                        'timeOut' => 2000,
                        'progressBar' => true,
                        'closeButton' => true,
                    ],
                ]
            ]);
        } catch (\Exception $e) {
            return back()->with([
                'toastr' => [
                    'type' => 'error',
                    'message' => 'Failed to update running status: ' . $e->getMessage(),
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

}
