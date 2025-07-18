<?php

namespace App\Http\Controllers;

use App\Models\Postal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class PostalController extends Controller
{
    /**
     * Display a listing of postal records.
     */
    public function index(Request $request)
    {
        if (!$request->user()->hasPermissionTo('view_post_log')) {
            abort(403, 'You are not authorized to view postal records.');
        }

        $postals = Postal::latest()->get();

        return view('postals.index', compact('postals'));
    }

    /**
     * Store a newly created postal record.
     */
    public function store(Request $request)
    {
        if (!$request->user()->hasPermissionTo('create_post_log')) {
            abort(403, 'You are not authorized to create postal records.');
        }

        try {
            $validated = $request->validate([
                'type' => 'required|in:dispatch,receive',
                'reference_number' => 'required|string|max:255',
                'to_title' => 'nullable|string|max:255',
                'from_title' => 'nullable|string|max:255',
                'address' => 'nullable|string|max:255',
                'tracking_no' => 'nullable|string|max:255',
                'courier' => 'nullable|string|max:255',
                'note' => 'nullable|string|max:1000',
                'date' => 'required|date',
            ]);


            $validated['created_by'] = Auth::id();
            $validated['updated_by'] = Auth::id();

            Postal::create($validated);

            $request->session()->flash('toastr', [
                'type' => 'success',
                'message' => 'Postal record created successfully.',
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

        return redirect()->route('postals.index');
    }

    /**
     * Update the specified postal record.
     */
    public function update(Request $request, Postal $postal)
    {
        if (!$request->user()->hasPermissionTo('udpate_post_log')) {
            abort(403, 'You are not authorized to update postal records.');
        }

        try {
            $validated = $request->validate([
                'type' => 'required|in:dispatch,receive',
                'reference_number' => 'required|string|max:255',
                'to_title' => 'nullable|string|max:255',
                'from_title' => 'nullable|string|max:255',
                'address' => 'nullable|string|max:255',
                'tracking_no' => 'nullable|string|max:255',
                'courier' => 'nullable|string|max:255',
                'note' => 'nullable|string|max:1000',
                'date' => 'required|date',
            ]);

            $validated['updated_by'] = Auth::id();

            $postal->update($validated);

            $request->session()->flash('toastr', [
                'type' => 'success',
                'message' => 'Postal record updated successfully.',
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

        return redirect()->route('postals.index');
    }

    /**
     * Remove the specified postal record.
     */
    public function destroy(Request $request, Postal $postal)
    {
        if (!$request->user()->hasPermissionTo('delete_post_log')) {
            abort(403, 'You are not authorized to delete postal records.');
        }

        $postal->delete();

        $request->session()->flash('toastr', [
            'type' => 'success',
            'message' => 'Postal record deleted successfully.',
            'title' => 'Deleted!',
            'options' => [
                'timeOut' => 2000,
                'progressBar' => true,
                'closeButton' => true,
            ],
        ]);

        return redirect()->route('postals.index');
    }
}
