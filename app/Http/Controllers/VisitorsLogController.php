<?php

namespace App\Http\Controllers;

use App\Models\VisitorsLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class VisitorsLogController extends Controller
{
    /**
     * Display a listing of the visitor logs.
     */
    public function index(Request $request)
    {
        if (!$request->user()->hasPermissionTo('view_visitor_log')) {
            abort(403, 'You are not authorized to view visitor logs.');
        }

        $visitorLogs = VisitorsLog::latest()->get();

        return view('visitor_logs.index', compact('visitorLogs'));
    }

    /**
     * Store a newly created visitor log.
     */
    public function store(Request $request)
    {
        if (!$request->user()->hasPermissionTo('create_visitor_log')) {
            abort(403, 'You are not authorized to create visitor logs.');
        }

        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'contact_number' => 'nullable|string|max:255',
                'address' => 'nullable|string|max:500',
                'purpose' => 'nullable|string|max:500',
                'note' => 'nullable|string|max:500',
                'date_of_visit' => 'required|date',
            ]);

            VisitorsLog::create([
                'name' => $validated['name'],
                'contact_number' => $validated['contact_number'] ?? null,
                'address' => $validated['address'] ?? null,
                'purpose' => $validated['purpose'] ?? null,
                'note' => $validated['note'] ?? null,
                'date_of_visit' => $validated['date_of_visit'],
                'created_by' => Auth::id(),
                'updated_by' => Auth::id(),
            ]);

            $request->session()->flash('toastr', [
                'type' => 'success',
                'message' => 'Visitor log created successfully.',
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

        return redirect()->route('visitor-logs.index');
    }

    /**
     * Update the specified visitor log.
     */
    public function update(Request $request, VisitorsLog $visitorLog)
    {
        if (!$request->user()->hasPermissionTo('update_visitor_log')) {
            abort(403, 'You are not authorized to update visitor logs.');
        }

        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'contact_number' => 'nullable|string|max:255',
                'address' => 'nullable|string|max:500',
                'purpose' => 'nullable|string|max:500',
                'note' => 'nullable|string|max:500',
                'date_of_visit' => 'required|date',
            ]);

            $visitorLog->update([
                'name' => $validated['name'],
                'contact_number' => $validated['contact_number'] ?? null,
                'address' => $validated['address'] ?? null,
                'purpose' => $validated['purpose'] ?? null,
                'note' => $validated['note'] ?? null,
                'date_of_visit' => $validated['date_of_visit'],
                'updated_by' => Auth::id(),
            ]);

            $request->session()->flash('toastr', [
                'type' => 'success',
                'message' => 'Visitor log updated successfully.',
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

        return redirect()->route('visitor-logs.index');
    }

    /**
     * Remove the specified visitor log.
     */
    public function destroy(Request $request, VisitorsLog $visitorLog)
    {
        if (!$request->user()->hasPermissionTo('delete_visitor_log')) {
            abort(403, 'You are not authorized to delete visitor logs.');
        }

        $visitorLog->delete();

        $request->session()->flash('toastr', [
            'type' => 'success',
            'message' => 'Visitor log deleted successfully.',
            'title' => 'Deleted!',
            'options' => [
                'timeOut' => 2000,
                'progressBar' => true,
                'closeButton' => true,
            ],
        ]);

        return redirect()->route('visitor-logs.index');
    }
}
