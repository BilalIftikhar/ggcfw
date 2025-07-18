<?php

namespace App\Http\Controllers;

use App\Models\Room;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class RoomController extends Controller
{
    public function index(Request $request)
    {
        if (!$request->user()->hasPermissionTo('view_room')) {
            abort(403, 'You are not authorized to view rooms.');
        }

        $rooms = Room::latest()->get();

        return view('rooms.index', compact('rooms'));
    }

    public function store(Request $request)
    {
        if (!$request->user()->hasPermissionTo('create_room')) {
            abort(403, 'You are not authorized to create rooms.');
        }

        try {
            $validated = $request->validate([
                'room_number' => 'required|string|max:255|unique:rooms,room_number',
                'building' => 'nullable|string|max:255',
                'capacity' => 'nullable|integer|min:1',
                'room_type' => ['required', Rule::in(['lab', 'lecture_hall'])],
            ]);

            Room::create([
                'room_number' => $validated['room_number'],
                'building' => $validated['building'] ?? null,
                'capacity' => $validated['capacity'] ?? null,
                'room_type' => $validated['room_type'],
                'created_by' => Auth::id(),
            ]);

            $request->session()->flash('toastr', [
                'type' => 'success',
                'message' => 'Room created successfully.',
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

        return redirect()->route('rooms.index');
    }

    public function update(Request $request, Room $room)
    {
        if (!$request->user()->hasPermissionTo('updated_room')) {
            abort(403, 'You are not authorized to update rooms.');
        }

        try {
            $validated = $request->validate([
                'room_number' => [
                    'required',
                    'string',
                    'max:255',
                    Rule::unique('rooms', 'room_number')->ignore($room->id),
                ],
                'building' => 'nullable|string|max:255',
                'capacity' => 'nullable|integer|min:1',
                'room_type' => ['required', Rule::in(['lab', 'lecture_hall'])],
            ]);

            $room->update([
                'room_number' => $validated['room_number'],
                'building' => $validated['building'] ?? null,
                'capacity' => $validated['capacity'] ?? null,
                'room_type' => $validated['room_type'],
                'updated_by' => Auth::id(),
            ]);

            $request->session()->flash('toastr', [
                'type' => 'success',
                'message' => 'Room updated successfully.',
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

        return redirect()->route('rooms.index');
    }

    public function destroy(Request $request, Room $room)
    {
        if (!$request->user()->hasPermissionTo('delete_room')) {
            abort(403, 'You are not authorized to delete rooms.');
        }

        // Check relations if you want to block deletion when linked with timetables
        if ($room->timetables()->exists()) {
            return back()->with([
                'toastr' => [
                    'type' => 'error',
                    'message' => 'Cannot delete room with associated timetables.',
                    'title' => 'Deletion Failed!',
                    'options' => [
                        'timeOut' => 3000,
                        'progressBar' => true,
                        'closeButton' => true,
                    ],
                ],
            ]);
        }

        $room->delete();

        $request->session()->flash('toastr', [
            'type' => 'success',
            'message' => 'Room deleted successfully.',
            'title' => 'Deleted!',
            'options' => [
                'timeOut' => 2000,
                'progressBar' => true,
                'closeButton' => true,
            ],
        ]);

        return redirect()->route('rooms.index');
    }

}
