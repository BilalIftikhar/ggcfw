<?php

namespace App\Http\Controllers;

use App\Models\Module;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{

    public function index()
    {
        $roles = Role::all();
        return view('roles.index', compact('roles'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|unique:roles,name'
        ]);

        Role::create(['name' => $request->name, 'guard_name' => 'web']);

        return redirect()->route('roles.index')->with('success', 'Role created successfully.');
    }


    public function update(Request $request, $id)
    {
        if ($request->user()->hasPermissionTo('edit_role')) {
            $request->validate([
                'name' => 'required|string|unique:roles,name,' . $id,
            ]);

            $role = Role::findOrFail($id);
            $role->update([
                'name' => $request->name
            ]);

            $request->session()->flash('toastr', [
                'type' => 'success',
                'message' => 'Role updated successfully.',
                'title' => 'Updated!',
                'options' => [
                    'timeOut' => 2000,
                    'progressBar' => true,
                    'closeButton' => true,
                ],
            ]);

            return redirect()->back();
        } else {
            abort(403, 'You are not authorized to perform this action.');
        }
    }


    public function destroy(Request $request, $id)
    {
        if (!$request->user()->hasPermissionTo('delete_role')) {
            abort(403, 'You are not authorized to perform this action.');
        }

        $role = Role::findOrFail($id);

        // Prevent deletion if system role
        if ($role->is_system) {
            $request->session()->flash('toastr', [
                'type' => 'error',
                'message' => 'System roles cannot be deleted.',
                'title' => 'Error!',
                'options' => [
                    'timeOut' => 3000,
                    'progressBar' => true,
                    'closeButton' => true,
                ],
            ]);
            return redirect()->back();
        }

        // Prevent deletion if role has users attached
        if ($role->users()->count() > 0) {
            $request->session()->flash('toastr', [
                'type' => 'error',
                'message' => 'Role cannot be deleted because it is assigned to users.',
                'title' => 'Error!',
                'options' => [
                    'timeOut' => 3000,
                    'progressBar' => true,
                    'closeButton' => true,
                ],
            ]);
            return redirect()->back();
        }

        $role->delete();

        $request->session()->flash('toastr', [
            'type' => 'success',
            'message' => 'Role deleted successfully.',
            'title' => 'Deleted!',
            'options' => [
                'timeOut' => 2000,
                'progressBar' => true,
                'closeButton' => true,
            ],
        ]);

        return redirect()->back();
    }



    // Placeholder for future implementation
    public function assignPermissions(Role $role, Request $request)
    {
        if (!$request->user()->hasPermissionTo('assign_permission')) {
            abort(403, 'You are not authorized to perform this action.');
        }

        $permissions = Permission::all();

        $permissionsGroupedByModule = $permissions->groupBy(function ($permission) {
            $module = Module::find($permission->module_id);
            return $module ? $module->name : 'Uncategorized';
        })->sortBy(function ($group, $key) {
            $module = Module::where('name', $key)->first();
            return $module ? $module->id : 0;
        });

        return view('roles.assign', compact('role', 'permissionsGroupedByModule'));
    }

    public function updatePermissions(Request $request, Role $role)
    {
        if (!$request->user()->hasPermissionTo('assign_permission')) {
            abort(403, 'You are not authorized to perform this action.');
        }

        // Validate input — permissions array is optional (can be empty)
        $data = $request->validate([
            'permissions' => 'array',
            'permissions.*' => 'string|exists:permissions,name',
        ]);

        // Sync permissions to the role
        $permissions = $data['permissions'] ?? [];
        $role->syncPermissions($permissions);

        // Flash success message
        $request->session()->flash('toastr', [
            'type' => 'success',
            'message' => 'Permissions updated successfully.',
            'title' => 'Success!',
            'options' => [
                'timeOut' => 2000,
                'progressBar' => true,
                'closeButton' => true,
            ],
        ]);

        return redirect()->route('roles.index');
    }





}
