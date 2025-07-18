<?php

namespace App\Http\Controllers;

use App\Models\Teacher;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Spatie\Permission\Models\Role;

class TeacherController extends Controller
{
    //

    public function index(Request $request)
    {
        // Permission check
        if (!$request->user()->hasPermissionTo('view_teacher')) {
            abort(403, 'You are not authorized to view teachers.');
        }

        $query = Teacher::query();

        // Filter by employee mode (Regular, Contract, Adhoc)
        if ($request->filled('employee_mode')) {
            $query->where('employee_mode', $request->employee_mode);
        }

        // Filter by working status (working, retired, fired, other)
        if ($request->filled('working_status')) {
            $query->where('working_status', $request->working_status);
        }

        // Filter by active/inactive
        if ($request->filled('is_active')) {
            $query->where('is_active', $request->is_active);
        }

        // Search by name, father_name, cnic, designation
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('father_name', 'like', "%{$search}%")
                    ->orWhere('cnic', 'like', "%{$search}%")
                    ->orWhere('designation', 'like', "%{$search}%");
            });
        }

        // Fetch paginated data
        $teachers = $query->latest()->paginate(20);

//        foreach ($teachers as $teacher) {
//            // Check if media exists in 'employee' collection
//            $hasMedia = $teacher->hasMedia('employee');
//
//            // Get URLs of all media in that collection
//            $mediaUrls = $teacher->getMedia('employee')->map(function($media) {
//                return $media->getUrl();
//            });
//
//            dump([
//                'teacher_id' => $teacher->id,
//                'name' => $teacher->name,
//                'hasMedia' => $hasMedia,
//                'mediaUrls' => $mediaUrls,
//            ]);
//        }
//
//        // Stop here to inspect the dump
//        dd();
        return view('teachers.index', compact('teachers'));
    }

    public function create(Request $request)
    {
        if (!$request->user()->hasPermissionTo('create_employee')) {
            abort(403, 'You are not authorized to create employees.');
        }

        $roles = Role::where('is_teaching', true)->get();
        return view('teachers.create', compact('roles'));
    }

    public function store(Request $request)
    {
        if (!$request->user()->hasPermissionTo('create_employee')) {
            abort(403, 'You are not authorized to create employees.');
        }

        $rules = [
            'cnic' => 'required|unique:teachers|max:15',
            'seniority_no' => 'nullable|max:50',
            'name' => 'required|max:100',
            'father_name' => 'nullable|max:100',
            'designation' => 'required|max:100',
            'bps' => 'nullable|integer|min:1|max:22',
            'dob' => 'nullable|date',
            'domicile' => 'nullable|max:100',
            'retirement_date' => 'nullable|date',
            'subject' => 'nullable|max:100',
            'qualification' => 'nullable|max:255',
            'govt_entry_date' => 'nullable|date',
            'employee_mode' => 'nullable|in:regular,contract,adhoc',
            'quota' => 'nullable|max:50',
            'joining_date_adhoc_lecturer' => 'nullable|date',
            'joining_date_regular_lecturer' => 'nullable|date',
            'joining_date_assistant_prof' => 'nullable|date',
            'joining_date_associate_prof' => 'nullable|date',
            'joining_date_professor' => 'nullable|date',
            'joining_date_principal' => 'nullable|date',
            'qualifying_service' => 'nullable|max:50',
            'joining_date_present_station' => 'nullable|date',
            'cadre' => 'nullable|max:100',
            'home_address' => 'nullable|max:255',
            'work_contact' => 'nullable|max:20',
            'home_contact' => 'nullable|max:20',
            'email' => 'nullable|email|unique:teachers',
            'is_active' => 'boolean',
            'working_status' => 'nullable|in:working,retired,fired,other',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'generateLogin' => 'sometimes|accepted',
            'username' => 'required_with:generateLogin|nullable|max:100|unique:users,username',
            'password' => 'required_with:generateLogin|nullable|min:6',
            'role_id' => 'required_with:generateLogin|exists:roles,id',
            // New fields for timetable configuration
            'can_teach_labs' => 'nullable|boolean',
            'max_lectures_per_day' => 'nullable|integer|min:1|max:8',
            'max_lectures_per_week' => 'nullable|integer|min:1|max:40',

        ];

        try {
            $validated = $request->validate($rules);
        } catch (ValidationException $e) {
            dd($e->errors());
        }

        DB::beginTransaction();

        try {
            $teacherData = $request->except(['photo', 'createLogin', 'username', 'password', 'role_id']);
            $teacherData['created_by'] = auth()->id();
            $teacherData['working_status'] = $request->working_status ?? 'working';
            $teacherData['is_active'] = $request->boolean('is_active', true);
            $teacherData['can_teach_labs'] = $request->boolean('can_teach_labs', false);
            $teacherData['can_teach_labs'] = $request->boolean('can_teach_labs', false);
            $teacherData['max_lectures_per_day'] = $request->max_lectures_per_day;
            $teacherData['max_lectures_per_week'] = $request->max_lectures_per_week;



            $teacher = Teacher::create($teacherData);

            if ($request->hasFile('photo')) {
                $file = $request->file('photo');
                $filename = $request->cnic . '_' . str_replace(' ', '_', $request->name);
                $teacher->addMedia($file)
                    ->usingFileName($filename . '.' . $file->getClientOriginalExtension())
                    ->toMediaCollection('employee');
            }
            if ($request->has('generateLogin')) {
                try {
                    // Create user
                    $user = User::create([
                        'username' => $request->username,
                        'email' => $teacher->email ?? null,
                        'password' => Hash::make($request->password),
                        'name' => $teacher->name,
                    ]);
                } catch (\Exception $ex) {
                    DB::rollBack();
                   // dd($ex->getMessage());

                    $request->session()->flash('toastr', [
                        'type' => 'error',
                        'message' => 'Teacher created but user account creation failed: ' . $ex->getMessage(),
                        'title' => 'User Creation Error!',
                        'options' => [
                            'timeOut' => 4000,
                            'progressBar' => true,
                            'closeButton' => true,
                        ],
                    ]);

                    return redirect()->route('teachers.index');
                }

                try {
                    // Assign role
                    $role = Role::findOrFail($request->role_id);
                    $user->assignRole($role->name);

                    // Link teacher with user
                    $teacher->user_id = $user->id;
                    $teacher->temporary_password = base64_encode($request->password);
                    $teacher->save();
                } catch (\Exception $ex) {
                    DB::rollBack();
                    $request->session()->flash('toastr', [
                        'type' => 'error',
                        'message' => 'User created but failed to assign role or link to teacher: ' . $ex->getMessage(),
                        'title' => 'Role Assignment Error!',
                        'options' => [
                            'timeOut' => 4000,
                            'progressBar' => true,
                            'closeButton' => true,
                        ],
                    ]);

                    return redirect()->route('teachers.index');
                }
            }


            DB::commit();

            $request->session()->flash('toastr', [
                'type' => 'success',
                'message' => 'Teacher created successfully.',
                'title' => 'Success!',
                'options' => [
                    'timeOut' => 2000,
                    'progressBar' => true,
                    'closeButton' => true,
                ],
            ]);

            return redirect()->route('teachers.index');

        } catch (\Exception $e) {
            DB::rollBack();

            $request->session()->flash('toastr', [
                'type' => 'error',
                'message' => 'Failed to create teacher: ' . $e->getMessage(),
                'title' => 'Error!',
                'options' => [
                    'timeOut' => 4000,
                    'progressBar' => true,
                    'closeButton' => true,
                ],
            ]);

            return redirect()->back()->withInput();
        }
    }

    public function edit(Request $request, Teacher $teacher)
    {
        if (!$request->user()->hasPermissionTo('update_teacher')) {
            abort(403, 'You are not authorized to edit employees.');
        }

        $roles = Role::where('is_teaching', true)->get();
        return view('teachers.edit', compact('teacher', 'roles'));
    }

    public function update(Request $request, Teacher $teacher)
    {
        if (!$request->user()->hasPermissionTo('update_teacher')) {
            abort(403, 'You are not authorized to update employees.');
        }

        $rules = [
            'cnic' => 'required|max:15|unique:teachers,cnic,' . $teacher->id,
            'seniority_no' => 'nullable|max:50',
            'name' => 'required|max:100',
            'father_name' => 'nullable|max:100',
            'designation' => 'required|max:100',
            'bps' => 'nullable|integer|min:1|max:22',
            'dob' => 'nullable|date',
            'domicile' => 'nullable|max:100',
            'retirement_date' => 'nullable|date',
            'subject' => 'nullable|max:100',
            'qualification' => 'nullable|max:255',
            'govt_entry_date' => 'nullable|date',
            'employee_mode' => 'nullable|in:regular,contract,adhoc',
            'quota' => 'nullable|max:50',
            'joining_date_adhoc_lecturer' => 'nullable|date',
            'joining_date_regular_lecturer' => 'nullable|date',
            'joining_date_assistant_prof' => 'nullable|date',
            'joining_date_associate_prof' => 'nullable|date',
            'joining_date_professor' => 'nullable|date',
            'joining_date_principal' => 'nullable|date',
            'qualifying_service' => 'nullable|max:50',
            'joining_date_present_station' => 'nullable|date',
            'cadre' => 'nullable|max:100',
            'home_address' => 'nullable|max:255',
            'work_contact' => 'nullable|max:20',
            'home_contact' => 'nullable|max:20',
            'email' => 'nullable|email|unique:teachers,email,' . $teacher->id,
            'is_active' => 'boolean',
            'working_status' => 'nullable|in:working,retired,fired,other',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            // New fields for timetable configuration
            'can_teach_labs' => 'nullable|boolean',
            'max_lectures_per_day' => 'nullable|integer|min:1|max:8',
            'max_lectures_per_week' => 'nullable|integer|min:1|max:40',
        ];

        $validated = $request->validate($rules);

        DB::beginTransaction();

        try {
            $teacherData = $request->except(['photo']);
            $teacherData['is_active'] = $request->boolean('is_active', true);
            $teacherData['working_status'] = $request->working_status ?? $teacher->working_status;
            $teacherData['can_teach_labs'] = $request->boolean('can_teach_labs', false);
            $teacherData['max_lectures_per_day'] = $request->max_lectures_per_day;
            $teacherData['max_lectures_per_week'] = $request->max_lectures_per_week;

            $teacher->update($teacherData);

            if ($request->hasFile('photo')) {
                $file = $request->file('photo');
                $filename = $request->cnic . '_' . str_replace(' ', '_', $request->name);

                // Remove old photo if any
                $teacher->clearMediaCollection('employee');

                $teacher->addMedia($file)
                    ->usingFileName($filename . '.' . $file->getClientOriginalExtension())
                    ->toMediaCollection('employee');
            }

            DB::commit();

            $request->session()->flash('toastr', [
                'type' => 'success',
                'message' => 'Teacher updated successfully.',
                'title' => 'Success!',
                'options' => [
                    'timeOut' => 2000,
                    'progressBar' => true,
                    'closeButton' => true,
                ],
            ]);

            return redirect()->route('teachers.index');

        } catch (\Exception $e) {
            DB::rollBack();

            $request->session()->flash('toastr', [
                'type' => 'error',
                'message' => 'Failed to update teacher: ' . $e->getMessage(),
                'title' => 'Error!',
                'options' => [
                    'timeOut' => 4000,
                    'progressBar' => true,
                    'closeButton' => true,
                ],
            ]);

            return redirect()->back()->withInput();
        }
    }



    public function destroy(Teacher $teacher, Request $request)
    {
        // Soft delete the teacher
        $teacher->delete();

        // Disable linked user if exists
        if ($teacher->user_id) {
            $user = User::find($teacher->user_id);
            if ($user) {
                $user->is_active = false;
                $user->save();
            }
        }

        // Toastr success message
        $request->session()->flash('toastr', [
            'type' => 'success',
            'message' => 'Teacher deleted successfully and user account disabled.',
            'title' => 'Success!',
            'options' => [
                'timeOut' => 2000,
                'progressBar' => true,
                'closeButton' => true,
            ],
        ]);

        return redirect()->route('teachers.index');
    }


    /**
     * Restore the specified soft deleted resource.
     */
    public function restore($id, Request $request)
    {
        $teacher = Teacher::withTrashed()->findOrFail($id);
        $teacher->restore();

        $request->session()->flash('toastr', [
            'type' => 'success',
            'message' => 'Teacher restored successfully.',
            'title' => 'Success!',
            'options' => [
                'timeOut' => 2000,
                'progressBar' => true,
                'closeButton' => true,
            ],
        ]);

        return redirect()->route('teachers.index');
    }


    /**
     * Permanently delete the specified resource.
     */
    public function forceDelete($id)
    {
        $teacher = Teacher::withTrashed()->findOrFail($id);

        // Delete photo if exists (using media library)
        if ($teacher->hasMedia('employee')) {
            $teacher->clearMediaCollection('employee');
        }

        $teacher->forceDelete();

        return redirect()->route('teachers.index')
            ->with('success', 'Teacher permanently deleted.');
    }

    public function show(Teacher $teacher)
    {
        if (!auth()->user()->can('view_teacher')) {
            abort(403, 'You are not authorized to edit employees.');
        }
        return view('teachers.show', compact('teacher'));
    }

}
