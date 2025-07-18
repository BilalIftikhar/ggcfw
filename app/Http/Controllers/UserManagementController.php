<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Role;

class UserManagementController extends Controller
{
    /**
     * Display a listing of users with their roles.
     */
    public function index()
    {
        if (!auth()->user()->can('view_users')) {
            abort(403, 'You do not have permission to view users.');
        }

        $roles = Role::with(['users' => function($query) {
            $query->with(['teacher', 'student', 'employee']);
        }])->get();

        return view('users.index', compact('roles'));
    }


    /**
     * Update the username of a user.
     */
    public function updateUsername(Request $request, User $user)
    {
        if (!auth()->user()->can('change_username')) {
            abort(403, 'You do not have permission to change usernames.');
        }

        $validated = $request->validate([
            'username' => ['required', 'string', 'max:255', Rule::unique('users')->ignore($user->id)],
        ]);

        $user->update([
            'username' => $validated['username'],
        ]);

        session()->flash('toastr', [
            'type' => 'success',
            'message' => 'Username updated successfully.',
            'title' => 'Success!',
            'options' => ['timeOut' => 2000, 'progressBar' => true, 'closeButton' => true],
        ]);

        return back();
    }

    /**
     * Update the password of a user.
     */
    public function updatePassword(Request $request, User $user)
    {
        if (!auth()->user()->can('change_password')) {
            abort(403, 'You do not have permission to change passwords.');
        }

        $validated = $request->validate([
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user->update([
            'password' => Hash::make($validated['password']),
        ]);

        session()->flash('toastr', [
            'type' => 'success',
            'message' => 'Password updated successfully.',
            'title' => 'Success!',
            'options' => ['timeOut' => 2000, 'progressBar' => true, 'closeButton' => true],
        ]);

        return back();
    }

    /**
     * Change the status (enable/disable) of a user (AJAX).
     */
    public function changeStatus(Request $request, User $user)
    {
        if (!auth()->user()->can('change_status')) {
            return response()->json([
                'status' => false,
                'message' => 'You do not have permission to change user status.'
            ], 403);
        }

        $validated = $request->validate([
            'status' => ['required', 'boolean'],
        ]);

        $user->update([
            'status' => $validated['status'],
        ]);

        return response()->json([
            'status' => true,
            'message' => 'User status updated successfully.',
            'new_status' => $user->status,
            'user_id' => $user->id,
        ]);
    }

    public function impersonate($id)
    {
        if (!auth()->user()->can('user_impersonate')) {
            abort(403, 'You do not have permission to impersonate users.');
        }

        $userToImpersonate = User::findOrFail($id);

        if (auth()->id() === $userToImpersonate->id) {
            session()->flash('toastr', [
                'type' => 'info',
                'message' => 'You cannot impersonate yourself.',
                'title' => 'Notice!',
                'options' => ['timeOut' => 2000, 'progressBar' => true, 'closeButton' => true],
            ]);
            return back();
        }

        auth()->user()->impersonate($userToImpersonate);

        session()->flash('toastr', [
            'type' => 'success',
            'message' => "Now impersonating {$userToImpersonate->username}.",
            'title' => 'Impersonation Started!',
            'options' => ['timeOut' => 2000, 'progressBar' => true, 'closeButton' => true],
        ]);

        return redirect()->route('dashboard');
    }


    public function leaveImpersonation()
    {
        if (!auth()->user()->isImpersonated()) {
            session()->flash('toastr', [
                'type' => 'info',
                'message' => 'You are not impersonating any user.',
                'title' => 'Notice!',
                'options' => ['timeOut' => 2000, 'progressBar' => true, 'closeButton' => true],
            ]);
            return redirect()->route('dashboard');
        }

        auth()->user()->leaveImpersonation();

        session()->flash('toastr', [
            'type' => 'success',
            'message' => 'Returned to your account successfully.',
            'title' => 'Impersonation Ended!',
            'options' => ['timeOut' => 2000, 'progressBar' => true, 'closeButton' => true],
        ]);

        return redirect()->route('dashboard');
    }



}
