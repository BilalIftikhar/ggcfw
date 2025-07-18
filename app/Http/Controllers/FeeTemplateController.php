<?php

namespace App\Http\Controllers;

use App\Models\FeeTemplate;
use App\Models\Fee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class FeeTemplateController extends Controller
{
    /**
     * Display a listing of fee templates.
     */
    public function index(Request $request)
    {
        if (!$request->user()->hasPermissionTo('view_fee_template')) {
            abort(403, 'You are not authorized to view fee templates.');
        }

        $feeTemplates = FeeTemplate::latest()->get();

        $fees = Fee::with(['feeType.feeGroup'])->latest()->get();

        $feeGroups = $fees->groupBy(fn($fee) => optional($fee->feeType->feeGroup)->name ?? 'Ungrouped');

        return view('fee_templates.index', compact('feeTemplates', 'fees', 'feeGroups'));
    }




    /**
     * Store a newly created fee template.
     */
    public function store(Request $request)
    {
        if (!$request->user()->hasPermissionTo('create_fee_template')) {
            abort(403, 'You are not authorized to create fee templates.');
        }

        try {
            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'nullable|string|max:1000',
                'fee_ids' => 'required|array',
                'fee_ids.*' => 'exists:fees,id',
            ]);

            $validated['fee_ids'] = json_encode($validated['fee_ids']);
            $validated['created_by'] = Auth::id();
            $validated['updated_by'] = Auth::id();

            FeeTemplate::create($validated);

            $request->session()->flash('toastr', [
                'type' => 'success',
                'message' => 'Fee template created successfully.',
                'title' => 'Created!',
                'options' => [
                    'timeOut' => 3000,
                    'progressBar' => true,
                    'closeButton' => true,
                    'positionClass' => 'toast-top-right',
                    'newestOnTop' => true,
                    'preventDuplicates' => true,
                    'showDuration' => 300,
                    'hideDuration' => 1000,
                    'extendedTimeOut' => 1000,
                    'showEasing' => 'swing',
                    'hideEasing' => 'linear',
                    'showMethod' => 'fadeIn',
                    'hideMethod' => 'fadeOut',
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
                        'positionClass' => 'toast-top-right',
                        'newestOnTop' => true,
                        'preventDuplicates' => true,
                        'showDuration' => 300,
                        'hideDuration' => 1000,
                        'extendedTimeOut' => 1000,
                        'showEasing' => 'swing',
                        'hideEasing' => 'linear',
                        'showMethod' => 'fadeIn',
                        'hideMethod' => 'fadeOut',
                    ],
                ]
            ]);
        }

        return redirect()->route('fee-templates.index');
    }

    /**
     * Update the specified fee template.
     */
    public function update(Request $request, FeeTemplate $feeTemplate)
    {
        if (!$request->user()->hasPermissionTo('update_fee_template')) {
            abort(403, 'You are not authorized to update fee templates.');
        }

        try {
            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'nullable|string|max:1000',
                'fee_ids' => 'required|array',
                'fee_ids.*' => 'exists:fees,id',
            ]);

            $validated['fee_ids'] = json_encode($validated['fee_ids']);
            $validated['updated_by'] = Auth::id();

            $feeTemplate->update($validated);

            $request->session()->flash('toastr', [
                'type' => 'success',
                'message' => 'Fee template updated successfully.',
                'title' => 'Updated!',
                'options' => [
                    'timeOut' => 3000,
                    'progressBar' => true,
                    'closeButton' => true,
                    'positionClass' => 'toast-top-right',
                    'newestOnTop' => true,
                    'preventDuplicates' => true,
                    'showDuration' => 300,
                    'hideDuration' => 1000,
                    'extendedTimeOut' => 1000,
                    'showEasing' => 'swing',
                    'hideEasing' => 'linear',
                    'showMethod' => 'fadeIn',
                    'hideMethod' => 'fadeOut',
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
                        'positionClass' => 'toast-top-right',
                        'newestOnTop' => true,
                        'preventDuplicates' => true,
                        'showDuration' => 300,
                        'hideDuration' => 1000,
                        'extendedTimeOut' => 1000,
                        'showEasing' => 'swing',
                        'hideEasing' => 'linear',
                        'showMethod' => 'fadeIn',
                        'hideMethod' => 'fadeOut',
                    ],
                ]
            ]);
        }

        return redirect()->route('fee-templates.index');
    }

    /**
     * Remove the specified fee template.
     */
    public function destroy(Request $request, FeeTemplate $feeTemplate)
    {
        if (!$request->user()->hasPermissionTo('delete_fee_template')) {
            abort(403, 'You are not authorized to delete fee templates.');
        }

        $feeTemplate->delete();

        $request->session()->flash('toastr', [
            'type' => 'success',
            'message' => 'Fee template deleted successfully.',
            'title' => 'Deleted!',
            'options' => [
                'timeOut' => 3000,
                'progressBar' => true,
                'closeButton' => true,
                'positionClass' => 'toast-top-right',
                'newestOnTop' => true,
                'preventDuplicates' => true,
                'showDuration' => 300,
                'hideDuration' => 1000,
                'extendedTimeOut' => 1000,
                'showEasing' => 'swing',
                'hideEasing' => 'linear',
                'showMethod' => 'fadeIn',
                'hideMethod' => 'fadeOut',
            ],
        ]);

        return redirect()->route('fee-templates.index');
    }

    /**
     * Show detailed view of a fee template with grouped fees under tabs.
     */
    public function show(Request $request, FeeTemplate $feeTemplate)
    {
        if (!$request->user()->hasPermissionTo('view_fee_template')) {
            abort(403, 'You are not authorized to view fee templates.');
        }
        //dd($feeTemplate);
        $feeIds = json_decode($feeTemplate->fee_ids, true);
        if (!is_array($feeIds)) {
            $feeIds = [];
        }
        $fees = Fee::with(['feeType.feeGroup'])
            ->whereIn('id', $feeIds)
            ->get();
        //dd($fees);
        // Group fees by FeeGroup name for tabs
        $feesGroupedByGroup = $fees->groupBy(fn($fee) => optional($fee->feeType->feeGroup)->name ?? 'Ungrouped');

       // dd($feesGroupedByGroup);
        return view('fee_templates.show', compact('feeTemplate', 'feesGroupedByGroup'));
    }

}
