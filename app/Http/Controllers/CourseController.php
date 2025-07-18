<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\CourseSection;
use App\Models\Employee;
use App\Models\EnrollmentDetail;
use App\Models\ProgramClass;
use App\Models\Teacher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class CourseController extends Controller
{
    //

    public function index(Request $request)
    {
        if (!$request->user()->can('view_course')) {
            abort(403, 'Unauthorized action.');
        }

        $classId = $request->input('class_id');

        $query = Course::with(['program', 'class']);
        if ($classId) {
            $query->where('class_id', $classId);
        }

        $courses = $query->get();
        $teachers = Teacher::where('is_active', true)->get();

        // Pass selected class and program
        $class = null;
        $program = null;

        if ($classId) {
            $class = ProgramClass::with('program')->findOrFail($classId);
            $program = $class->program;
        }
        return view('courses.index', compact('courses', 'teachers', 'class', 'program'));
    }



    public function store(Request $request)
    {
        if (!$request->user()->can('create_course')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'code' => 'nullable|string|max:50',
                'credit_hours' => 'required|integer|min:0',

                'has_lab' => 'nullable',
                'is_active' => 'nullable',
                'is_mandatory' => 'nullable',
                'requires_continuous_slots' => 'nullable',

                'lab_credit_hours' => 'nullable|integer|min:0',
                'no_of_sections' => 'nullable|integer|min:0',
                'students_per_section' => 'nullable|integer|min:0',

                'weekly_lectures'=> 'nullable|integer|min:0',
                'weekly_labs'=> 'nullable|integer|min:0',

                'program_id' => 'required|exists:programs,id',
                'class_id' => 'required|exists:program_classes,id',
                'teacher_id' => 'nullable|exists:teachers,id',

                'description' => 'nullable|string|max:1000',
            ]);

            // Auto-calculate weekly required minutes
            $creditHours = $validated['credit_hours'];
            $labCreditHours = $request->filled('lab_credit_hours') ? $validated['lab_credit_hours'] : 0;

            $requiredMinutesTheoryWeekly = $creditHours * 60;
            $requiredMinutesLabWeekly = $labCreditHours * 135;

            $course = Course::create([
                'name' => $validated['name'],
                'code' => $validated['code'],
                'description' => $validated['description'] ?? null,
                'credit_hours' => $creditHours,

                'has_lab' => $request->has('has_lab') ? 1 : 0,
                'is_active' => $request->has('is_active') ? 1 : 0,
                'is_mandatory' => $request->has('is_mandatory') ? 1 : 0,
                'requires_continuous_slots' => $request->has('requires_continuous_slots') ? 1 : 0,

                'lab_credit_hours' => $labCreditHours,
                'no_of_sections' => $request->filled('no_of_sections') ? $validated['no_of_sections'] : 0,
                'students_per_section' => $request->filled('students_per_section') ? $validated['students_per_section'] : 0,

                'required_minutes_theory_weekly' => $requiredMinutesTheoryWeekly,
                'required_minutes_lab_weekly' => $requiredMinutesLabWeekly,

                'weekly_lectures'=> $request->filled('weekly_lectures') ? $validated['weekly_lectures'] : 0,
                'weekly_labs'=> $request->filled('weekly_labs') ? $validated['weekly_labs'] : 0,

                'program_id' => $validated['program_id'],
                'class_id' => $validated['class_id'],
                'teacher_id' => $validated['teacher_id'] ?? null,
                'created_by' => Auth::id(),
            ]);

            $request->session()->flash('toastr', [
                'type' => 'success',
                'message' => 'Course added successfully.',
                'title' => 'Success!',
                'options' => [
                    'timeOut' => 2000,
                    'progressBar' => true,
                    'closeButton' => true,
                ],
            ]);

            return back();

        } catch (ValidationException $e) {
            return back()->withInput()->with('toastr', [
                'type' => 'error',
                'message' => $e->validator->errors()->first(),
                'title' => 'Validation Error!',
                'options' => [
                    'timeOut' => 3000,
                    'progressBar' => true,
                    'closeButton' => true,
                ],
            ]);
        }
    }



    public function update(Request $request, Course $course)
    {
        if (!$request->user()->can('update_course')) {
            abort(403, 'Unauthorized action.');
        }

        $rules = [
            'name' => 'required|string|max:255',
            'code' => [
                'required',
                'string',
                'max:50',
                Rule::unique('courses')->ignore($course->id),
            ],
            'credit_hours' => 'required|integer|min:1',
            'lab_credit_hours' => 'nullable|integer|min:0',
            'program_id' => 'required|exists:programs,id',
            'class_id' => 'required|exists:program_classes,id',
            'teacher_id' => 'nullable|exists:employees,id',
            'no_of_sections' => 'nullable|integer|min:1',
            'students_per_section' => 'required|integer|min:0',
            'description' => 'nullable|string',
            'required_minutes_theory_weekly' => 'nullable|integer|min:0',
            'required_minutes_lab_weekly' => 'nullable|integer|min:0',
            'weekly_lectures'=> 'nullable|integer|min:0',
            'weekly_labs'=> 'nullable|integer|min:0',
        ];

        $validated = $request->validate($rules);
        $creditHours = $validated['credit_hours'];
        $labCreditHours = $request->filled('lab_credit_hours') ? $validated['lab_credit_hours'] : 0;

        $requiredMinutesTheoryWeekly = $creditHours * 60;
        $requiredMinutesLabWeekly = $labCreditHours * 135;

        // Prepare and normalize data
        $data = $validated;

        $data['required_minutes_theory_weekly'] = $requiredMinutesTheoryWeekly;
        $data['required_minutes_lab_weekly'] = $requiredMinutesLabWeekly;

        // Store the original value before update
        $originalIsMandatory = $course->is_mandatory;

        // Toggle switches
        $data['has_lab'] = $request->has('has_lab') ? 1 : 0;
        if ($data['has_lab'] == 0) {
            $data['lab_credit_hours'] = 0;
        }
        $data['is_mandatory'] = $request->has('is_mandatory') ? 1 : 0;
        $data['is_active'] = $request->has('is_active') ? 1 : 0;

        $data['auto_section_enabled'] = $request->has('auto_section_enabled') ? 1 : 0;
        $data['requires_continuous_slots'] = $request->has('requires_continuous_slots') ? 1 : 0;

        // Optional description
        $data['description'] = $request->filled('description') ? $request->input('description') : null;

        $data['updated_by'] = Auth::id();

        try {
            DB::beginTransaction();

            // Update the course
            $course->update($data);

            // Update related course sections student limits
            CourseSection::where('course_id', $course->id)
                ->update(['no_of_students_allowed' => $data['students_per_section']]);

            // If is_mandatory changed, update enrollment_details
            if ($originalIsMandatory != $data['is_mandatory']) {
                EnrollmentDetail::where('course_id', $course->id)
                    ->update(['is_mandatory' => $data['is_mandatory']]);
            }

            DB::commit();

            $request->session()->flash('toastr', [
                'type' => 'success',
                'message' => 'Course updated successfully.',
                'title' => 'Updated!',
                'options' => [
                    'timeOut' => 2000,
                    'progressBar' => true,
                    'closeButton' => true,
                ],
            ]);

            return back();

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('toastr', [
                'type' => 'error',
                'message' => 'Failed to update course: ' . $e->getMessage(),
                'title' => 'Error!',
                'options' => [
                    'timeOut' => 3000,
                    'progressBar' => true,
                    'closeButton' => true,
                ],
            ]);
        }
    }





    public function destroy(Course $course)
    {
        if (!auth()->user()->can('delete_course')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $course->delete();

            return back()->with('toastr', [
                'type' => 'success',
                'message' => 'Course deleted successfully.',
                'title' => 'Deleted!',
                'options' => [
                    'timeOut' => 2000,
                    'progressBar' => true,
                    'closeButton' => true,
                ],
            ]);

        } catch (\Exception $e) {
            return back()->with('toastr', [
                'type' => 'error',
                'message' => 'Failed to delete course. It may have associated records.',
                'title' => 'Error!',
                'options' => [
                    'timeOut' => 3000,
                    'progressBar' => true,
                    'closeButton' => true,
                ],
            ]);
        }
    }




}
