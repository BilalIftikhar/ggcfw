<?php

namespace App\Http\Controllers;

use App\Models\WorkingDay;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class WorkingDayController extends Controller
{

    public function index(Request $request)
    {
        if (!$request->user()->hasPermissionTo('view_working_days')) {
            abort(403, 'You are not authorized to view working days.');
        }

        $workingDays = WorkingDay::orderByRaw("FIELD(day, 'monday','tuesday','wednesday','thursday','friday','saturday','sunday')")->get();

        return view('working_days.index', compact('workingDays'));
    }


    public function toggle(Request $request)
    {

        if (!$request->user()->hasPermissionTo('updated_working_days')) {
            return response()->json([
                'status' => 'error',
                'message' => 'You are not authorize to update working days',
            ], 403);
        }


        $request->validate([
            'id' => 'required|exists:working_days,id',
            'is_working' => 'required|boolean',
        ]);

        DB::beginTransaction();

        try {
            $day = WorkingDay::findOrFail($request->id);
            $day->update([
                'is_working' => $request->is_working,
                'updated_by' => Auth::id(),
            ]);

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Day status updated successfully.',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'Toggle failed: ' . $e->getMessage(),
            ], 500);
        }
    }



}
