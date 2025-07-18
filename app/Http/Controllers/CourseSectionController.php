<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\CourseSection;
use App\Models\Program;
use App\Models\Teacher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class CourseSectionController extends Controller
{
    // List all course sections
    public function index(Request $request)
    {
        if (!$request->user()->can('view_course_section')) {
            abort(403, 'Unauthorized action.');
        }

        // Get the course_id from the request
        $courseId = $request->input('course_id');

        // Find the course or fail
        $course = Course::findOrFail($courseId);

        // Get sections for this specific course with relationships
        $sections = CourseSection::with(['program', 'course', 'teachers'])
            ->where('course_id', $courseId)
            ->get();
        //dd($sections);
        // Get additional data needed for the form dropdowns
        $programs = Program::all(); // Assuming you need this for program dropdown
        $teachers = Teacher::where('is_active', true)->get(); // Adjust based on your teacher logic


        return view('course_sections.index', [
            'sections' => $sections,
            'course' => $course,
            'programs' => $programs,
            'teachers' => $teachers
        ]);
    }

    public function create()
    {
        // Load programs, courses, teachers for select inputs (not shown here)
        return view('course_sections.create');
    }

    // Save new section
    public function store(Request $request)
    {
        if (!$request->user()->hasPermissionTo('create_course_section')) {
            abort(403, 'You are not authorized to create course sections.');
        }

        $rules = [
            'name' => 'required|string|max:255',
            'program_id' => 'required|exists:programs,id',
            'course_id' => 'required|exists:courses,id',
            'teacher_id' => 'nullable|exists:teachers,id',
            'is_active' => 'sometimes|in:on,1,true,false,0',
            'no_of_students_allowed' => 'required|integer|min:0',
            'has_lab' => 'boolean',
            'requires_continuous_slots' => 'boolean',
            'credit_hours' => 'required|numeric|min:0|max:10',
            'lab_credit_hours' => 'required|numeric|min:0|max:10',
            'required_minutes_theory_weekly' => 'required|integer|min:0',
            'required_minutes_lab_weekly' => 'required|integer|min:0',
            'weekly_lectures'=> 'nullable|integer|min:0',
            'weekly_labs'=> 'nullable|integer|min:0',
        ];

        try {
            $validated = $request->validate($rules);
        } catch (ValidationException $e) {
            return redirect()->back()
                ->withInput()
                ->with([
                    'toastr' => [
                        'type' => 'error',
                        'message' => 'Validation errors:<br>' . $e->getMessage(),
                        'title' => 'Validation Failed!',
                        'options' => [
                            'timeOut' => 5000,
                            'progressBar' => true,
                            'closeButton' => true,
                            'escapeHtml' => false
                        ]
                    ]
                ]);

        }

        DB::beginTransaction();

        try {
            $course = Course::findOrFail($request->course_id);

            $sectionData = [
                'name' => $request->name,
                'program_id' => $request->program_id,
                'course_id' => $request->course_id,
                'teacher_id' => $request->teacher_id,
                'is_active' =>  $request->has('is_active'),
                'no_of_students_allowed' => $request->no_of_students_allowed,
                'no_of_students_enrolled' => 0,
                'has_lab' => $request->boolean('has_lab'),
                'requires_continuous_slots' => $request->boolean('requires_continuous_slots'),
                'credit_hours' => $request->credit_hours,
                'lab_credit_hours' => $request->lab_credit_hours,
                'required_minutes_theory_weekly' => $request->required_minutes_theory_weekly,
                'required_minutes_lab_weekly' => $request->required_minutes_lab_weekly,
                'weekly_lectures'=> $request->weekly_lecture ?? 0,
                'weekly_labs'=> $request->weekly_lab ?? 0,
                'created_by' => auth()->id(),
                'updated_by' => auth()->id(),
            ];

            CourseSection::create($sectionData);

            DB::commit();

            return redirect()->back()->with([
                'toastr' => [
                    'type' => 'success',
                    'message' => 'Course section created successfully.',
                    'title' => 'Success!',
                    'options' => [
                        'timeOut' => 3000,
                        'progressBar' => true,
                        'closeButton' => true
                    ]
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withInput()->with([
                'toastr' => [
                    'type' => 'error',
                    'message' => 'Failed to create course section. Please try again.' . $e->getMessage(),
                    'title' => 'Error!',
                    'options' => [
                        'timeOut' => 4000,
                        'progressBar' => true,
                        'closeButton' => true
                    ]
                ]
            ]);
        }
    }


    // Show form to edit a section
    public function edit($id)
    {
        $courseSection = CourseSection::findOrFail($id);
        return view('course_sections.edit', compact('courseSection'));
    }

    // Update the section
    public function update(Request $request, $id)
    {
        if (!$request->user()->hasPermissionTo('update_course_section')) {
            abort(403, 'You are not authorized to update course sections.');
        }

        $rules = [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'program_id' => 'required|exists:programs,id',
            'course_id' => 'required|exists:courses,id',
            'teacher_id' => 'nullable|exists:teachers,id',
            'is_active' => 'sometimes|in:on,1,true,false,0',
        ];

        try {
            $validated = $request->validate($rules);
        } catch (ValidationException $e) {
            return redirect()->back()
                ->withInput()
                ->with([
                    'toastr' => [
                        'type' => 'error',
                        'message' => 'Validation errors:<br>' . $e->getMessage(),
                        'title' => 'Validation Failed!',
                        'options' => [
                            'timeOut' => 5000,
                            'progressBar' => true,
                            'closeButton' => true,
                            'escapeHtml' => false
                        ]
                    ]
                ]);
        }

        DB::beginTransaction();

        try {
            $courseSection = CourseSection::findOrFail($id);
            $course = Course::findOrFail($request->course_id);

            $updateData = $request->only([
                'name', 'description', 'program_id', 'course_id', 'teacher_id'
            ]);

            $updateData['is_active'] = $request->has('is_active');
            $updateData['no_of_students_allowed'] = $course->students_per_section ?? $courseSection->no_of_students_allowed;
            $updateData['updated_by'] = auth()->id();

            // Capture old teacher_id before update
            $oldTeacherId = $courseSection->teacher_id;
            $newTeacherId = $request->teacher_id;

            $courseSection->update($updateData);

            // Update timetable.teacher_id if teacher has changed
            if ($newTeacherId != $oldTeacherId) {
                DB::table('timetables')
                    ->where('course_section_id', $courseSection->id)
                    ->update([
                        'teacher_id' => $newTeacherId,
                        'updated_at' => now(),
                    ]);
            }

            DB::commit();

            return redirect()->back()->with([
                'toastr' => [
                    'type' => 'success',
                    'message' => 'Course section updated successfully.',
                    'title' => 'Success!',
                    'options' => [
                        'timeOut' => 3000,
                        'progressBar' => true,
                        'closeButton' => true
                    ]
                ]
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()->withInput()->with([
                'toastr' => [
                    'type' => 'error',
                    'message' => 'Failed to update course section: ' . $e->getMessage(),
                    'title' => 'Error!',
                    'options' => [
                        'timeOut' => 4000,
                        'progressBar' => true,
                        'closeButton' => true
                    ]
                ]
            ]);
        }
    }



    // Delete section
    public function destroy(Request $request, $id)
    {
        if (!$request->user()->can('delete_course_section')) {
            abort(403, 'You are not authorized to delete course sections.');
        }

        DB::beginTransaction();

        try {
            $courseSection = CourseSection::findOrFail($id);
            $courseSection->delete();

            DB::commit();

            $request->session()->flash('toastr', [
                'type' => 'success',
                'message' => 'Course section deleted successfully.',
                'title' => 'Deleted!',
                'options' => ['timeOut' => 3000, 'progressBar' => true, 'closeButton' => true],
            ]);

            return redirect()->back();

        } catch (\Exception $e) {
            DB::rollBack();

            $request->session()->flash('toastr', [
                'type' => 'error',
                'message' => 'Failed to delete course section: ' . $e->getMessage(),
                'title' => 'Error!',
                'options' => ['timeOut' => 4000, 'progressBar' => true, 'closeButton' => true],
            ]);

            return redirect()->back();
        }
    }

}
