<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation;
use Illuminate\Validation\ValidationException;
use Spatie\Permission\Models\Role;

class EmployeeController extends Controller
{

    public function index(Request $request)
    {
        // Permission check
        if (!$request->user()->hasPermissionTo('view_employee')) {
            abort(403, 'You are not authorized to view employees.');
        }

        $query = Employee::query();

        // Filter by employee type/status (e.g. Regular, Contract, Adhoc)
        if ($request->filled('employee_type')) {
            $query->where('employee_type', $request->employee_type);
        }

        // Filter by working status (working, retired, fired, other)
        if ($request->filled('working_status')) {
            $query->where('working_status', $request->working_status);
        }

        // Filter by active/inactive
        if ($request->filled('is_active')) {
            $query->where('is_active', $request->is_active);
        }

        // Search by name, father_name, cnic_no, designation
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('father_name', 'like', "%{$search}%")
                    ->orWhere('cnic_no', 'like', "%{$search}%")
                    ->orWhere('designation', 'like', "%{$search}%");
            });
        }

        // Fetch paginated data
        $employees = $query->latest()->paginate(20);

        return view('employees.index', compact('employees'));
    }

    public function create(Request $request)
    {
        if (!$request->user()->hasPermissionTo('create_employee')) {
            abort(403, 'You are not authorized to create employees.');
        }


        // If you want to pass roles or any other related data, adjust accordingly.
        // Assuming roles are still related:
        $roles = Role::where('is_admin', false)
            ->where('is_student', false)
            ->where('is_teaching', false)
            ->get();
        return view('employees.create', compact('roles'));
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        if (!$request->user()->can('create_employee')) {
            abort(403, 'You are not authorized to create employees.');
        }

        $rules = [
            // Identification & Personal
            'cnic_no' => 'required|max:15|unique:employees',
            'name' => 'required|max:100',
            'father_name' => 'nullable|max:100',
            'designation' => 'nullable|max:100',
            'bps' => 'nullable|integer|min:1|max:22',
            'date_of_birth' => 'nullable|date',
            'domicile' => 'nullable|max:100',
            'date_of_retirement' => 'nullable|date',
            'qualification' => 'nullable|max:255',
            'date_of_first_entry' => 'nullable|date',
            'quota' => 'nullable|max:100',
            'status' => 'nullable|in:Regular,Contract',
            'working_status' => 'nullable|in:working,retired,fired,other',

            // Joining Dates
            'date_of_joining_contract' => 'nullable|date',
            'date_of_joining_regular' => 'nullable|date',
            'date_of_joining_current_station' => 'nullable|date',

            // Designation History
            'date_of_joining_junior_clerk' => 'nullable|date',
            'date_of_joining_senior_clerk' => 'nullable|date',
            'date_of_joining_lab_supervisor' => 'nullable|date',
            'date_of_joining_head_clerk' => 'nullable|date',
            'date_of_joining_superintendent' => 'nullable|date',
            'date_of_joining_senior_bursar' => 'nullable|date',

            // Academic Designations
            'date_of_joining_as_lecturer_contract' => 'nullable|date',
            'date_of_joining_as_lecturer_regular' => 'nullable|date',
            'date_of_joining_as_assistant_prof' => 'nullable|date',
            'date_of_joining_as_associate_prof' => 'nullable|date',
            'date_of_joining_as_professor' => 'nullable|date',
            'date_of_joining_as_principal' => 'nullable|date',

            // Other
            'qualifying_service' => 'nullable|max:100',
            'cadre' => 'nullable|max:100',
            'home_address' => 'nullable|max:500',
            'home_contact' => 'nullable|max:20',
            'work_contact' => 'nullable|max:20',
            'is_active' => 'boolean',

            // Media
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',

            // Optional login
            'generateLogin' => 'sometimes|accepted',
            'username' => 'required_with:generateLogin|nullable|max:100|unique:users,username',
            'password' => 'required_with:generateLogin|nullable|min:6',
            'role_id' => 'required_with:generateLogin|exists:roles,id',
        ];

        try {
            $validated = $request->validate($rules);
        } catch (ValidationException $e) {
            $request->session()->flash('toastr', [
                'type' => 'error',
                'message' => 'Validation failed: ' . collect($e->errors())->flatten()->first(),
                'title' => 'Validation Error',
                'options' => ['timeOut' => 4000, 'progressBar' => true, 'closeButton' => true],
            ]);

            return redirect()->back()->withErrors($e->errors())->withInput();
        }

        DB::beginTransaction();

        try {
            // Extract employee data only (ignore login & media fields)
            $employeeData = $request->except([
                'photo', 'generateLogin', 'username', 'password', 'role_id',
            ]);

            $employeeData['working_status'] = $request->input('working_status', 'working');
            $employeeData['is_active'] = $request->boolean('is_active', true);
            $employeeData['created_by'] = auth()->id(); // in case HasUserStamps isn't auto-handling

            $employee = Employee::create($employeeData);

            // Photo upload
            if ($request->hasFile('photo')) {
                $file = $request->file('photo');
                $filename = $request->cnic_no . '_' . str_replace(' ', '_', $request->name);
                $employee->addMedia($file)
                    ->usingFileName($filename . '.' . $file->getClientOriginalExtension())
                    ->toMediaCollection('employee');
            }

            // Optional login
            if ($request->has('generateLogin')) {
                $user = User::create([
                    'username' => $request->username,
                    'name' => $request->name,
                    'email' => null,
                    'password' => Hash::make($request->password),
                ]);

                $user->assignRole(Role::findOrFail($request->role_id)->name);

                $employee->update([
                    'user_id' => $user->id,
                    'temporary_password' => base64_encode($request->password),
                ]);
            }

            DB::commit();

            $request->session()->flash('toastr', [
                'type' => 'success',
                'message' => 'Employee created successfully.',
                'title' => 'Success!',
                'options' => ['timeOut' => 2000, 'progressBar' => true, 'closeButton' => true],
            ]);

            return redirect()->route('employees.index');

        } catch (\Exception $e) {
            DB::rollBack();

            $request->session()->flash('toastr', [
                'type' => 'error',
                'message' => 'Failed to create employee: ' . $e->getMessage(),
                'title' => 'Error!',
                'options' => ['timeOut' => 4000, 'progressBar' => true, 'closeButton' => true],
            ]);

            return redirect()->back()->withInput();
        }
    }


    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Employee $employee)
    {
        if (!auth()->user()->can('update_employee')) {
            abort(403, 'You are not authorized to edit employees.');
        }

        $roles = Role::where('is_admin', false)
            ->where('is_student', false)
            ->where('is_teaching', false)
            ->get();

        return view('employees.edit', compact('employee', 'roles'));
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Employee $employee)
    {
        if (!$request->user()->hasPermissionTo('update_employee')) {
            abort(403, 'You are not authorized to update employees.');
        }

        $rules = [
            'cnic_no' => ['required', 'max:15', Rule::unique('employees')->ignore($employee->id)],
            'name' => 'required|max:100',
            'father_name' => 'nullable|max:100',
            'designation' => 'nullable|max:100',
            'bps' => 'nullable|integer|min:1|max:22',
            'date_of_birth' => 'nullable|date',
            'domicile' => 'nullable|max:100',
            'date_of_retirement' => 'nullable|date',
            'qualification' => 'nullable|max:255',
            'date_of_first_entry' => 'nullable|date',
            'quota' => 'nullable|max:100',
            'status' => 'nullable|in:Regular,Contract',
            'working_status' => 'nullable|in:working,retired,fired,other',

            // Joining Dates
            'date_of_joining_contract' => 'nullable|date',
            'date_of_joining_regular' => 'nullable|date',
            'date_of_joining_current_station' => 'nullable|date',

            // Designation History
            'date_of_joining_junior_clerk' => 'nullable|date',
            'date_of_joining_senior_clerk' => 'nullable|date',
            'date_of_joining_lab_supervisor' => 'nullable|date',
            'date_of_joining_head_clerk' => 'nullable|date',
            'date_of_joining_superintendent' => 'nullable|date',
            'date_of_joining_senior_bursar' => 'nullable|date',

            // Academic Designations
            'date_of_joining_as_lecturer_contract' => 'nullable|date',
            'date_of_joining_as_lecturer_regular' => 'nullable|date',
            'date_of_joining_as_assistant_prof' => 'nullable|date',
            'date_of_joining_as_associate_prof' => 'nullable|date',
            'date_of_joining_as_professor' => 'nullable|date',
            'date_of_joining_as_principal' => 'nullable|date',

            // Other
            'qualifying_service' => 'nullable|max:100',
            'cadre' => 'nullable|max:100',
            'home_address' => 'nullable|max:500',
            'home_contact' => 'nullable|max:20',
            'work_contact' => 'nullable|max:20',
            'is_active' => 'boolean',

            // Optional photo
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ];

        $validated = $request->validate($rules);

        DB::beginTransaction();

        try {
            $employeeData = $request->except(['photo']);
            $employeeData['is_active'] = $request->boolean('is_active', true);
            $employeeData['working_status'] = $request->working_status ?? $employee->working_status;
            $employeeData['updated_by'] = auth()->id();

            $employee->update($employeeData);

            if ($request->hasFile('photo')) {
                $file = $request->file('photo');
                $filename = $request->cnic_no . '_' . str_replace(' ', '_', $request->name);

                $employee->clearMediaCollection('employee');

                $employee->addMedia($file)
                    ->usingFileName($filename . '.' . $file->getClientOriginalExtension())
                    ->toMediaCollection('employee');
            }

            DB::commit();

            $request->session()->flash('toastr', [
                'type' => 'success',
                'message' => 'Employee updated successfully.',
                'title' => 'Success!',
                'options' => [
                    'timeOut' => 2000,
                    'progressBar' => true,
                    'closeButton' => true,
                ],
            ]);

            return redirect()->route('employees.index');

        } catch (\Exception $e) {
            DB::rollBack();

            $request->session()->flash('toastr', [
                'type' => 'error',
                'message' => 'Failed to update employee: ' . $e->getMessage(),
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


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Employee $employee, Request $request)
    {
        if (!$request->user()->hasPermissionTo('delete_employee')) {
            abort(403, 'You are not authorized to delete employees.');
        }

        // Delete photo if exists
        if ($employee->photo_path) {
            Storage::disk('public')->delete($employee->photo_path);
        }

        // Deactivate associated user if exists
        if ($employee->user_id) {
            $user = User::find($employee->user_id);
            if ($user) {
                $user->status = false;
                $user->save();
            }
        }

        $employee->delete();

        $request->session()->flash('toastr', [
            'type' => 'success',
            'message' => 'Employee deleted successfully and user account disabled.',
            'title' => 'Success!',
            'options' => [
                'timeOut' => 2000,
                'progressBar' => true,
                'closeButton' => true,
            ],
        ]);

        return redirect()->route('employees.index');
    }


    /**
     * Restore the specified soft deleted resource.
     */
    public function restore($id)
    {
        $employee = Employee::withTrashed()->findOrFail($id);
        $employee->restore();

        return redirect()->route('employees.index')
            ->with('success', 'Employee restored successfully.');
    }

    /**
     * Permanently delete the specified resource.
     */
    public function forceDelete($id)
    {
        $employee = Employee::withTrashed()->findOrFail($id);

        // Delete photo if exists
        if ($employee->photo_path) {
            Storage::disk('public')->delete($employee->photo_path);
        }

        $employee->forceDelete();

        return redirect()->route('employees.index')
            ->with('success', 'Employee permanently deleted.');
    }


    /**
     * Display the specified resource.
     *
     * @param Employee $employee
     * @return \Illuminate\View\View
     */
    public function show(Employee $employee)
    {
        if (!auth()->user()->can('view_employee')) {
            abort(403, 'You are not authorized to edit employees.');
        }
        return view('employees.show', compact('employee'));
    }
}
