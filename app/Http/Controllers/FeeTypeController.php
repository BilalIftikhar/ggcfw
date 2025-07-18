<?php

namespace App\Http\Controllers;

use App\Models\FeeGroup;
use App\Models\FeeType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class FeeTypeController extends Controller
{
    /**
     * Display a listing of the fee types.
     */
    public function index(Request $request)
    {
        if (!$request->user()->hasPermissionTo('view_fee_type')) {
            abort(403, 'You are not authorized to view fee types.');
        }

        $feeGroups = FeeGroup::orderBy('name')->get();
        $feeTypes = FeeType::with('feeGroup')->latest()->get();

        return view('fee_types.index', compact('feeTypes', 'feeGroups'));
    }

    /**
     * Store a newly created fee type in storage.
     */
    public function store(Request $request)
    {
        if (!$request->user()->hasPermissionTo('create_fee_type')) {
            abort(403, 'You are not authorized to create fee types.');
        }

        try {
            $validated = $request->validate([
                'fee_group_id' => 'required|exists:fee_groups,id',
                'name' => 'required|string|max:255',
                'account_code' => 'nullable|string|max:255',
                'bank_name' => 'nullable|string|max:255',
                'description' => 'nullable|string|max:1000',
            ]);

            FeeType::create($validated);

            $request->session()->flash('toastr', [
                'type' => 'success',
                'message' => 'Fee type created successfully.',
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

        return redirect()->route('fee-type.index');
    }

    /**
     * Update the specified fee type in storage.
     */
    public function update(Request $request, FeeType $feeType)
    {
        if (!$request->user()->hasPermissionTo('update_fee_type')) {
            abort(403, 'You are not authorized to update fee types.');
        }

        try {
            $validated = $request->validate([
                'fee_group_id' => 'required|exists:fee_groups,id',
                'name' => 'required|string|max:255',
                'account_code' => 'nullable|string|max:255',
                'bank_name' => 'nullable|string|max:255',
                'description' => 'nullable|string|max:1000',
            ]);

            $feeType->update($validated);

            $request->session()->flash('toastr', [
                'type' => 'success',
                'message' => 'Fee type updated successfully.',
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

        return redirect()->route('fee-type.index');
    }

    /**
     * Remove the specified fee type from storage.
     */
    public function destroy(Request $request, FeeType $feeType)
    {
        if (!$request->user()->hasPermissionTo('delete_fee_type')) {
            abort(403, 'You are not authorized to delete fee types.');
        }

        if ($feeType->fees()->exists()) {
            return back()->with([
                'toastr' => [
                    'type' => 'error',
                    'message' => 'Cannot delete fee type with existing associated fees.',
                    'title' => 'Deletion Failed!',
                    'options' => [
                        'timeOut' => 3000,
                        'progressBar' => true,
                        'closeButton' => true,
                    ],
                ]
            ]);
        }

        $feeType->delete();

        $request->session()->flash('toastr', [
            'type' => 'success',
            'message' => 'Fee type deleted successfully.',
            'title' => 'Deleted!',
            'options' => [
                'timeOut' => 2000,
                'progressBar' => true,
                'closeButton' => true,
            ],
        ]);

        return redirect()->route('fee-type.index');
    }
}
