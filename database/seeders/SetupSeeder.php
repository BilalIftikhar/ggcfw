<?php

namespace Database\Seeders;

use App\Models\Module;
use App\Models\User;
use App\Models\WorkingDay;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class SetupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1️⃣ Create system roles with proper flags
        $superadminRole = Role::firstOrCreate(
            ['name' => 'superadmin'],
            ['is_system' => true, 'is_admin' => true]
        );

        $teacherRole = Role::firstOrCreate(
            ['name' => 'teacher'],
            ['is_system' => true, 'is_teaching' => true]
        );

        $studentRole = Role::firstOrCreate(
            ['name' => 'student'],
            ['is_system' => true, 'is_student' => true]
        );

        // Ensure flags are updated if roles already existed
        $superadminRole->update(['is_system' => true, 'is_admin' => true]);
        $teacherRole->update(['is_system' => true, 'is_teaching' => true]);
        $studentRole->update(['is_system' => true, 'is_student' => true]);

        // 2️⃣ Create Superadmin user
        $superadminUser = User::firstOrCreate(
            ['email' => 'superadmin@example.com'],
            [
                'name' => 'Super Admin',
                'username' => 'superadmin',
                'password' => Hash::make('12345678'),
                'email_verified_at' => now(),
            ]
        );

        if (!$superadminUser->hasRole('superadmin')) {
            $superadminUser->assignRole($superadminRole);
        }

        // 3️⃣ Define clean, modular permission groups
        $modules = [
            // 📌 Roles Management
            'roles management' => [
                'create_role',
                'edit_role',
                'view_role',
                'delete_role',
                'assign_permission',
            ],

            'users management' => [
                'view_users',
                'change_status',
                'change_password',
                'change_username',
                'user_impersonate',
            ],

            // 📌 Institute Management
            'institute management' => [
                'edit_institute',
                'view_institute',
            ],

            // 📌 Settings Management
            'settings management' => [
                'view_institute_settings',
                'update_institute_settings',
                'view_whatsapp_settings',
                'update_whatsapp_settings',
                'view_email_settings',
                'update_email_settings',
            ],

            // 📌 Academic Management
            'academic management' => [
                'create_academic_session',
                'view_academic_session',
                'update_academic_session',
                'delete_academic_session',

                'create_study_level',
                'view_study_level',
                'update_study_level',
                'delete_study_level',

                'create_program',
                'view_program',
                'update_program',
                'delete_program',

                'create_course',
                'view_course',
                'update_course',
                'delete_course',
            ],

            // 📌 Attendance Management
            'attendance management' => [
                'create_attendance',
                'view_attendance',
                'update_attendance',
                'delete_attendance',
                'take_attendance',
            ],

            // 📌 Course Sections
            'course sections' => [
                'create_course_section',
                'view_course_section',
                'update_course_section',
                'delete_course_section',
            ],

            // 📌 Assignments
            'assignments' => [
                'view_assignment',
                'create_assignment',
                'update_assignment',
                'delete_assignment',
                'submit_assignment',
                'view_submission',
            ],

            'examination sessions' => [
                'view_examination_session',
                'create_examination_session',
                'update_examination_session',
                'delete_examination_session',
            ],

            // 📌 Examination Terms
            'examination terms' => [
                'view_examination_term',
                'create_examination_term',
                'update_examination_term',
                'delete_examination_term',
            ],

            // 📌 Examination Date Sheets
            'examination date sheets' => [
                'view_date_sheet',
                'create_date_sheet',
                'update_date_sheet',
                'delete_date_sheet',
            ],

            // 📌 Examination Marks
            'examination marks' => [
                'view_examination_marks',
                'create_examination_marks',
                'update_examination_marks',
                'delete_examination_marks',
            ],

            // 📌 Working Days
            'working days' => [
                'view_working_days',
                'updated_working_days',
            ],

            // 📌 Time Slots
            'time slots' => [
                'view_time_slots',
                'updated_time_slots',
                'create_time_slot',
                'delete_time_slot',
            ],

            // 📌 Rooms
            'rooms' => [
                'view_room',
                'updated_room',
                'create_room',
                'delete_room',
            ],

            // 📌 Time Table
            'time table' => [
                'create_time_table',
                'delete_time_table',
                'view_time_table',
                'update_time_table',
            ],

            // 📌 Students
            'students' => [
                'create_student',
                'view_student',
                'update_student',
                'delete_student',
            ],

            // 📌 Employees
            'employees' => [
                'create_employee',
                'view_employee',
                'update_employee',
                'delete_employee',
            ],

            // 📌 Teachers
            'teachers' => [
                'create_teacher',
                'view_teacher',
                'update_teacher',
                'delete_teacher',
            ],

            'fee group' => [
                'view_fee_group',
                'create_fee_group',
                'update_fee_group',
                'delete_fee_group',
            ],

            'fee type' => [
                'view_fee_type',
                'create_fee_type',
                'update_fee_type',
                'delete_fee_type',
            ],

            'fee' => [
                'view_fee',
                'create_fee',
                'update_fee',
                'delete_fee',
            ],
            'fee template' => [
                'view_fee_template',
                'create_fee_template',
                'update_fee_template',
                'delete_fee_template',
            ],
            'visitor log' => [
                'view_visitor_log',
                'create_visitor_log',
                'update_visitor_log',
                'delete_visitor_log',
            ],
            'post log' => [
                'view_post_log',
                'create_post_log',
                'update_post_log',
                'delete_post_log',
            ],
        ];

        // 4️⃣ Create modules and permissions
        $allPermissionIds = [];

        foreach ($modules as $moduleName => $permissions) {
            $module = Module::firstOrCreate(['name' => $moduleName]);

            foreach ($permissions as $permissionName) {
                $permission = Permission::firstOrCreate(
                    ['name' => $permissionName, 'guard_name' => 'web'],
                    ['module_id' => $module->id]
                );

                $allPermissionIds[] = $permission->id;
            }
        }

        // 5️⃣ Sync all permissions with superadmin
        $superadminRole->syncPermissions($allPermissionIds);

        // 6️⃣ Populate working days
        $days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
        foreach ($days as $day) {
            WorkingDay::firstOrCreate(['day' => $day]);
        }
        // ✅ Log output to confirm
        $this->command->info('System setup completed: roles, permissions, modules, and superadmin seeded successfully.');
    }
}
