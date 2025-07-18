<?php

namespace App\Http\Controllers;

use App\Models\FeeGroup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class FeeGroupController extends Controller
{
    /**
     * Display a listing of fee groups.
     */
    public function index(Request $request)
    {
        if (!$request->user()->hasPermissionTo('view_fee_group')) {
            abort(403, 'You are not authorized to view fee groups.');
        }

        $feeGroups = FeeGroup::latest()->get();

        return view('fee_groups.index', compact('feeGroups'));
    }

    /**
     * Store a newly created fee group.
     */
    public function store(Request $request)
    {
        if (!$request->user()->hasPermissionTo('create_fee_group')) {
            abort(403, 'You are not authorized to create fee groups.');
        }

        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255|unique:fee_groups,name',
                'description' => 'nullable|string',
            ]);

            FeeGroup::create([
                'name' => $validated['name'],
                'description' => $validated['description'] ?? null,
                'created_by' => Auth::id(),
            ]);

            $request->session()->flash('toastr', [
                'type' => 'success',
                'message' => 'Fee group created successfully.',
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

        return redirect()->route('fee-group.index');
    }

    /**
     * Update an existing fee group.
     */
    public function update(Request $request, FeeGroup $fee_group)
    {
        if (!$request->user()->hasPermissionTo('update_fee_group')) {
            abort(403, 'You are not authorized to update fee groups.');
        }

        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255|unique:fee_groups,name,' . $fee_group->id,
                'description' => 'nullable|string',
            ]);

            $fee_group->update([
                'name' => $validated['name'],
                'description' => $validated['description'] ?? null,
                'updated_by' => Auth::id(),
            ]);

            $request->session()->flash('toastr', [
                'type' => 'success',
                'message' => 'Fee group updated successfully.',
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

        return redirect()->route('fee-group.index');
    }

    /**
     * Delete a fee group.
     */
    public function destroy(Request $request, FeeGroup $fee_group)
    {
        if (!$request->user()->hasPermissionTo('delete_fee_group')) {
            abort(403, 'You are not authorized to delete fee groups.');
        }

        // Check if fee group has associated fee types before deleting
        if ($fee_group->feeTypes()->exists()) {
            return back()->with([
                'toastr' => [
                    'type' => 'error',
                    'message' => 'Cannot delete fee group with associated fee types.',
                    'title' => 'Deletion Failed!',
                    'options' => [
                        'timeOut' => 3000,
                        'progressBar' => true,
                        'closeButton' => true,
                    ],
                ],
            ]);
        }

        $fee_group->delete();

        $request->session()->flash('toastr', [
            'type' => 'success',
            'message' => 'Fee group deleted successfully.',
            'title' => 'Deleted!',
            'options' => [
                'timeOut' => 2000,
                'progressBar' => true,
                'closeButton' => true,
            ],
        ]);

        return redirect()->route('fee-group.index');
    }
}
