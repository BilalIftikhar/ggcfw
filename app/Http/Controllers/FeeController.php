<?php

namespace App\Http\Controllers;

use App\Models\Fee;
use App\Models\FeeGroup;
use App\Models\Program;
use App\Models\AcademicSession;
use App\Models\FeeType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class FeeController extends Controller
{
    /**
     * Display a listing of the fees.
     */
    public function index(Request $request)
    {
        if (!$request->user()->hasPermissionTo('view_fee')) {
            abort(403, 'You are not authorized to view fees.');
        }

        $fees = Fee::latest()
            ->with(['program', 'academicSession', 'feeType.feeGroup'])
            ->get();

        $academicSessions = AcademicSession::orderByDesc('id')->get();

        $feeGroups = FeeGroup::orderBy('name')->get();

        return view('fees.index', compact('fees', 'academicSessions', 'feeGroups'));
    }



    /**
     * Store a newly created fee in storage.
     */
    public function store(Request $request)
    {
        if (!$request->user()->hasPermissionTo('create_fee')) {
            abort(403, 'You are not authorized to create fees.');
        }

        try {
            $validated = $request->validate([
                'program_id' => 'required|exists:programs,id',
                'academic_session_id' => 'required|exists:academic_sessions,id',
                'fee_type_id' => 'required|exists:fee_types,id',
                'fee_mode' => 'required|in:fixed,per_credit_hour',
                'amount' => 'nullable|numeric|min:0',
                'per_credit_hour_rate' => 'nullable|numeric|min:0',
            ]);

            Fee::create([
                'program_id' => $validated['program_id'],
                'academic_session_id' => $validated['academic_session_id'],
                'fee_type_id' => $validated['fee_type_id'],
                'fee_mode' => $validated['fee_mode'],
                'amount' => $validated['amount'] ?? 0,
                'per_credit_hour_rate' => $validated['per_credit_hour_rate'] ?? 0,
                'created_by' => auth()->id(),
                'updated_by' => auth()->id(),
            ]);

            $request->session()->flash('toastr', [
                'type' => 'success',
                'message' => 'Fee created successfully.',
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

        return redirect()->route('fee.index');
    }

    /**
     * Update the specified fee in storage.
     */
    public function update(Request $request, Fee $fee)
    {
        if (!$request->user()->hasPermissionTo('update_fee')) {
            abort(403, 'You are not authorized to update fees.');
        }

        try {
            $validated = $request->validate([
                'program_id' => 'required|exists:programs,id',
                'academic_session_id' => 'required|exists:academic_sessions,id',
                'fee_type_id' => 'required|exists:fee_types,id',
                'fee_mode' => 'required|in:fixed,per_credit_hour',
                'amount' => 'nullable|numeric|min:0',
                'per_credit_hour_rate' => 'nullable|numeric|min:0',
            ]);

            $fee->update([
                'program_id' => $validated['program_id'],
                'academic_session_id' => $validated['academic_session_id'],
                'fee_type_id' => $validated['fee_type_id'],
                'fee_mode' => $validated['fee_mode'],
                'amount' => $validated['amount'] ?? 0,
                'per_credit_hour_rate' => $validated['per_credit_hour_rate'] ?? 0,
                'updated_by' => auth()->id(),
            ]);

            $request->session()->flash('toastr', [
                'type' => 'success',
                'message' => 'Fee updated successfully.',
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

        return redirect()->route('fee.index');
    }

    /**
     * Remove the specified fee from storage.
     */
    public function destroy(Request $request, Fee $fee)
    {
        if (!$request->user()->hasPermissionTo('delete_fee')) {
            abort(403, 'You are not authorized to delete fees.');
        }

        $fee->delete();

        $request->session()->flash('toastr', [
            'type' => 'success',
            'message' => 'Fee deleted successfully.',
            'title' => 'Deleted!',
            'options' => [
                'timeOut' => 2000,
                'progressBar' => true,
                'closeButton' => true,
            ],
        ]);

        return redirect()->route('fee.index');
    }
}
