<?php

namespace App\Http\Controllers;

use App\Models\AcademicSession;
use App\Models\ExaminationSession;
use App\Models\ExaminationTerm;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class ExaminationTermController extends Controller
{
    // List all terms
    public function index(Request $request)
    {
        if (!$request->user()->hasPermissionTo('view_examination_term')) {
            abort(403, 'You are not authorized to view examination terms.');
        }
        $sessions = ExaminationSession::active()->get();
        $terms = ExaminationTerm::with('session')->latest()->get();
       // dd($terms);
        return view('examination_terms.index', compact('terms','sessions'));
    }

    // Store a new term
    public function store(Request $request)
    {
        if (!$request->user()->hasPermissionTo('create_examination_term')) {
            abort(403, 'You are not authorized to create examination terms.');
        }

        try {
            $validated = $request->validate([
                'examination_session_id' => 'required|exists:examination_sessions,id',
                'title' => 'required|string|max:255|unique:examination_terms,title,NULL,id,examination_session_id,' . $request->examination_session_id,
                'description' => 'nullable|string|max:500',
                'enable_sessional' => 'nullable|boolean',
            ]);


            ExaminationTerm::create([
                'examination_session_id' => $validated['examination_session_id'],
                'title' => $validated['title'],
                'description' => $validated['description'] ?? null,
                'enable_sessional' => $request->has('enable_sessional') ? 1 : 0,
            ]);

            return redirect()->route('examination-term.index')->with([
                'toastr' => [
                    'type' => 'success',
                    'message' => 'Examination term created successfully.',
                    'title' => 'Success!',
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
                ]
            ]);
        }
    }


    // Update an existing term
    public function update(Request $request, ExaminationTerm $examination_term)
    {
        if (!$request->user()->hasPermissionTo('update_examination_term')) {
            abort(403, 'You are not authorized to update examination terms.');
        }

        try {
            $validated = $request->validate([
                'examination_session_id' => 'required|exists:examination_sessions,id',
                'title' => 'required|string|max:255|unique:examination_terms,title,' . $examination_term->id . ',id,examination_session_id,' . $request->examination_session_id,
                'description' => 'nullable|string|max:500',
                'enable_sessional' => 'nullable|boolean',
            ]);

            $examination_term->update([
                'examination_session_id' => $validated['examination_session_id'],
                'title' => $validated['title'],
                'description' => $validated['description'] ?? null,
                'enable_sessional' => $request->has('enable_sessional') ? 1 : 0,
            ]);

            return redirect()->route('examination-term.index')->with([
                'toastr' => [
                    'type' => 'success',
                    'message' => 'Examination term updated successfully.',
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


    // Delete a term
    public function destroy(Request $request, ExaminationTerm $examination_term)
    {
        if (!$request->user()->hasPermissionTo('delete_examination_term')) {
            abort(403, 'You are not authorized to delete examination terms.');
        }

        // Optional: Check for related date sheets or marks
        if ($examination_term->dateSheets()->exists() || $examination_term->marks()->exists()) {
            return back()->with([
                'toastr' => [
                    'type' => 'error',
                    'message' => 'Cannot delete term with associated date sheets or marks.',
                    'title' => 'Deletion Failed!',
                    'options' => [
                        'timeOut' => 3000,
                        'progressBar' => true,
                        'closeButton' => true,
                    ],
                ]
            ]);
        }

        $examination_term->delete();

        return redirect()->route('examination-term.index')->with([
            'toastr' => [
                'type' => 'success',
                'message' => 'Examination term deleted successfully.',
                'title' => 'Deleted!',
                'options' => [
                    'timeOut' => 2000,
                    'progressBar' => true,
                    'closeButton' => true,
                ],
            ],
        ]);
    }

    public function toggleSessional(Request $request, ExaminationTerm $term)
    {
        if (!$request->user()->hasPermissionTo('update_examination_term')) {
            abort(403, 'You are not authorized to update examination terms.');
        }
        $term->enable_sessional = !$term->enable_sessional;
        $term->save();

        return back()->with('success', 'Sessional status updated successfully.');
    }

}
