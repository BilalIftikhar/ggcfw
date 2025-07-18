<?php

namespace App\Http\Controllers;

use App\Models\TimeSlot;
use App\Models\WorkingDay;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class TimeSlotController extends Controller
{
    public function index()
    {
        if(!auth()->user()->hasPermissionTo('view_time_slots')){
            abort(403, 'You are not authorized to view time slots.');
        }
        $workingDays = WorkingDay::with(['timeSlots' => function ($query) {
            $query->orderBy('sort_order');
        }])->where('is_working', true)->get();

        return view('time_slots.index', compact('workingDays'));
    }

    public function store(Request $request)
    {
        if (!auth()->user()->hasPermissionTo('create_time_slot')) {
            abort(403, 'You are not authorized to view time slots.');
        }

        // Debug block (keep for future troubleshooting)
        // return response()->json([
        //     'all' => $request->all(),
        //     'has_is_break' => $request->has('is_break'),
        //     'is_break_value' => $request->input('is_break'),
        // ]);

        try {
            $request->validate([
                'working_day_id' => 'required|exists:working_days,id',
                'name' => 'required|string|max:100',
                'start_time' => 'required|date_format:H:i',
                'end_time' => 'required|date_format:H:i',
                'sort_order' => [
                    'required', 'integer',
                    Rule::unique('time_slots')->where(function ($query) use ($request) {
                        return $query->where('working_day_id', $request->working_day_id);
                    }),
                ],
                'is_break' => 'sometimes|in:on,1,true,false,0',
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation error occurred.',
                'errors' => $e->errors(),
            ], 422);
        }


        TimeSlot::create([
            'name' => $request->name,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'sort_order' => $request->sort_order,
            'is_break' => $request->has('is_break') ? 1 : 0, // Ensures 1 when present, 0 when not
            'working_day_id' => $request->working_day_id,
        ]);

        return response()->json(['message' => 'Time slot saved successfully.']);
    }

    public function update(Request $request, TimeSlot $timeSlot)
    {
        if (!auth()->user()->hasPermissionTo('updated_time_slots')) {
            abort(403, 'You are not authorized to update time slots.');
        }

        $request->validate([
            'name' => 'required|string|max:100',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'sort_order' => [
                'required', 'integer',
                Rule::unique('time_slots')->ignore($timeSlot->id)->where(function ($query) use ($request, $timeSlot) {
                    return $query->where('working_day_id', $timeSlot->working_day_id);
                }),
            ],
            'is_break' => 'nullable|boolean',
        ]);
        $timeSlot->update([
            'name' => $request->name,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'sort_order' => $request->sort_order,
            'is_break' => $request->is_break ?? 0,
        ]);

        return response()->json(['message' => 'Time slot updated successfully.']);


    }

    public function copy(Request $request)
    {
        $request->validate([
            'source_day' => 'required|exists:working_days,id',
            'destination_days' => 'required|array',
            'destination_days.*' => 'exists:working_days,id',
        ]);

        $sourceDayId = $request->source_day;
        $destinationDayIds = $request->destination_days;
        $overwrite = $request->boolean('overwrite');

        // Don't allow copying to the same day
        if (in_array($sourceDayId, $destinationDayIds)) {
            return back()->with('error', 'Source day cannot be one of the destination days.');
        }

        $sourceSlots = TimeSlot::where('working_day_id', $sourceDayId)->get();

        if ($sourceSlots->isEmpty()) {
            return response()->json(['message' => 'No time slots found in the source day.'], 422);
        }

        DB::beginTransaction();
        try {
            foreach ($destinationDayIds as $dayId) {
                if ($overwrite) {
                    // Delete existing slots if overwrite is enabled
                    TimeSlot::where('working_day_id', $dayId)->delete();
                }

                foreach ($sourceSlots as $slot) {
                    TimeSlot::create([
                        'working_day_id' => $dayId,
                        'name' => $slot->name,
                        'start_time' => $slot->start_time,
                        'end_time' => $slot->end_time,
                        'is_break' => $slot->is_break,
                        'sort_order' => $slot->sort_order,
                    ]);
                }
            }

            DB::commit();
            return response()->json(['message' => 'Time slots copied successfully!']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Failed to copy time slots: ' . $e->getMessage()], 500);
        }
    }

    public function destroy(TimeSlot $timeSlot)
    {
        if (!auth()->user()->hasPermissionTo('delete_time_slot')) {
            abort(403, 'You are not authorized to delete time slots.');
        }

        $timeSlot->delete();

        return response()->json(['message' => 'Time slot deleted successfully.']);
    }




}
