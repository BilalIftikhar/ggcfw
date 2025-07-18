<?php

use App\Http\Controllers\AcademicSessionController;
use App\Http\Controllers\AjaxController;
use App\Http\Controllers\AssignmentController;
use App\Http\Controllers\AssignmentSubmissionController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\CourseSectionController;
use App\Http\Controllers\DailyAttendanceController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\ExaminationDateSheetController;
use App\Http\Controllers\ExaminationMarkController;
use App\Http\Controllers\ExaminationSessionController;
use App\Http\Controllers\ExaminationTermController;
use App\Http\Controllers\FeeController;
use App\Http\Controllers\FeeGroupController;
use App\Http\Controllers\FeeTemplateController;
use App\Http\Controllers\FeeTypeController;
use App\Http\Controllers\PostalController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProgramController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\StudyLevelController;
use App\Http\Controllers\TeacherController;
use App\Http\Controllers\TimeSlotController;
use App\Http\Controllers\TimetableController;
use App\Http\Controllers\UserManagementController;
use App\Http\Controllers\VisitorsLogController;
use App\Http\Controllers\WorkingDayController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('auth.login');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {

        Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
        Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');


        // Roles Controller
        Route::get('/roles', [RoleController::class, 'index'])->name('roles.index');
        Route::post('/roles', [RoleController::class, 'store'])->name('roles.store');
        Route::put('/roles/{role}', [RoleController::class, 'update'])->name('roles.update');
        Route::delete('/roles/{role}', [RoleController::class, 'destroy'])->name('roles.destroy');


        // Permission assigning routes
        Route::get('/roles/{role}/permissions', [RoleController::class, 'assignPermissions'])->name('roles.permissions');
        Route::patch('/roles/{role}/permissions', [RoleController::class, 'updatePermissions'])->name('roles.permissions.update');

         //Users Routes
         Route::get('/users', [UserManagementController::class, 'index'])->name('users.index');
         Route::patch('/users/{user}/update-username', [UserManagementController::class, 'updateUsername'])->name('users.update-username');
         Route::patch('/users/{user}/update-password', [UserManagementController::class, 'updatePassword'])->name('users.update-password');
         Route::patch('/users/{user}/change-status', [UserManagementController::class, 'changeStatus'])->name('users.change-status');
         Route::get('/users/{id}/impersonate', [UserManagementController::class, 'impersonate'])->name('users.impersonate');
         Route::get('/users/impersonate/leave', [UserManagementController::class, 'leaveImpersonation'])->name('users.impersonate.leave');



       // Academic Session Routes
        Route::get('/academic-sessions', [AcademicSessionController::class, 'index'])->name('academic-session.index');
        Route::post('/academic-sessions', [AcademicSessionController::class, 'store'])->name('academic-session.store');
        Route::put('/academic-sessions/{academic_session}', [AcademicSessionController::class, 'update'])->name('academic-session.update');
        Route::delete('/academic-sessions/{academic_session}', [AcademicSessionController::class, 'destroy'])->name('academic-session.destroy');
        Route::get('/academic-sessions/transfer', [AcademicSessionController::class, 'transferSessionForm'])->name('academic-session.transfer.form');
        Route::delete('/academic-sessions/transfer', [AcademicSessionController::class, 'transferSessionData'])->name('academic-session.transfer');

        //Assignment Controller
        Route::get('/assignments', [AssignmentController::class, 'index'])->name('assignments.index');
        Route::get('/assignments/create', [AssignmentController::class, 'create'])->name('assignments.create');
        Route::post('/assignments', [AssignmentController::class, 'store'])->name('assignments.store');
        Route::get('/assignments/{assignment}', [AssignmentController::class, 'show'])->name('assignments.show');
        Route::get('/assignments/{assignment}/edit', [AssignmentController::class, 'edit'])->name('assignments.edit');
        Route::put('/assignments/{assignment}', [AssignmentController::class, 'update'])->name('assignments.update');
        Route::delete('/assignments/{assignment}', [AssignmentController::class, 'destroy'])->name('assignments.destroy');

        //Assignment Submission
        Route::post('/assignment-submissions', [AssignmentSubmissionController::class, 'store'])->name('assignment-submissions.store');
        Route::get('/assignments/{assignment}/submissions', [AssignmentSubmissionController::class, 'submissions'])->name('assignments.submissions');

        //Study Level Routes
        Route::get('/study-levels', [StudyLevelController::class, 'index'])->name('study-levels.index');
        Route::post('/study-levels', [StudyLevelController::class, 'store'])->name('study-levels.store');
        Route::put('/study-levels/{studyLevel}', [StudyLevelController::class, 'update'])->name('study-levels.update');
        Route::delete('/study-levels/{studyLevel}', [StudyLevelController::class, 'destroy'])->name('study-levels.destroy');

        // Programs Routes
        Route::get('/programs', [ProgramController::class, 'index'])->name('programs.index');
        Route::post('/programs', [ProgramController::class, 'store'])->name('programs.store');
        Route::put('/programs/{program}', [ProgramController::class, 'update'])->name('programs.update');
        Route::delete('/programs/{program}', [ProgramController::class, 'destroy'])->name('programs.destroy');
        Route::get('/programs/classes', [ProgramController::class, 'showClasses'])->name('programs.classes');
        Route::get('/programs/course-path', [ProgramController::class, 'showProgramCoursesPath'])->name('programs.course_path');


        //Course Routes
        Route::get('/courses', [CourseController::class, 'index'])->name('courses.index');
        Route::post('/courses', [CourseController::class, 'store'])->name('courses.store');
        Route::put('/courses/{course}', [CourseController::class, 'update'])->name('courses.update');
        Route::delete('/courses/{course}', [CourseController::class, 'destroy'])->name('courses.destroy');

        //Sections route
        Route::get('/course-sections', [CourseSectionController::class, 'index'])->name('course_sections.index');
        Route::post('/course-sections', [CourseSectionController::class, 'store'])->name('course_sections.store');
        Route::put('/course-sections/{id}', [CourseSectionController::class, 'update'])->name('course_sections.update');
        Route::delete('/course-sections/{id}', [CourseSectionController::class, 'destroy'])->name('course_sections.destroy');


        // Teacher Routes
        Route::get('/teachers', [TeacherController::class, 'index'])->name('teachers.index');
        Route::get('/teachers/create', [TeacherController::class, 'create'])->name('teachers.create');
        Route::post('/teachers', [TeacherController::class, 'store'])->name('teachers.store');
        Route::get('/teachers/{teacher}/edit', [TeacherController::class, 'edit'])->name('teachers.edit');
        Route::put('/teachers/{teacher}', [TeacherController::class, 'update'])->name('teachers.update');
        Route::delete('/teachers/{teacher}', [TeacherController::class, 'destroy'])->name('teachers.destroy');
        Route::get('teachers/{teacher}', [TeacherController::class, 'show'])->name('teachers.show');


        // Employee Routes
        Route::get('/employees', [EmployeeController::class, 'index'])->name('employees.index');
        Route::get('/employees/create', [EmployeeController::class, 'create'])->name('employees.create');
        Route::post('/employees', [EmployeeController::class, 'store'])->name('employees.store');
        Route::get('/employees/{employee}/edit', [EmployeeController::class, 'edit'])->name('employees.edit');
        Route::put('/employees/{employee}', [EmployeeController::class, 'update'])->name('employees.update');
        Route::delete('/employees/{employee}', [EmployeeController::class, 'destroy'])->name('employees.destroy');
        Route::get('employees/{employee}', [EmployeeController::class, 'show'])->name('employees.show');

        // Student Routes
        Route::get('/students', [StudentController::class, 'index'])->name('students.index');
        Route::get('/students/create', [StudentController::class, 'create'])->name('students.create');
        Route::post('/students', [StudentController::class, 'store'])->name('students.store');
        Route::get('/students/{student}/edit', [StudentController::class, 'edit'])->name('students.edit');
        Route::put('/students/{student}', [StudentController::class, 'update'])->name('students.update');
        Route::delete('/students/{student}', [StudentController::class, 'destroy'])->name('students.destroy');
        Route::get('/students/{student}', [StudentController::class, 'show'])->name('students.show');
        Route::get('/students/{student}/courses/edit', [StudentController::class, 'editCourse'])->name('students.courses.edit');
        Route::put('/students/{student}/courses', [StudentController::class, 'updateCourse'])->name('students.courses.update');
        Route::get('/students/{student}/change-program', [StudentController::class, 'changeProgramForm'])->name('students.changeProgram');
        Route::put('/students/{student}/change-program', [StudentController::class, 'changeProgramUpdate'])->name('students.changeProgram.update');


       // Soft delete restore and force delete
        Route::post('/students/{id}/restore', [StudentController::class, 'restore'])->name('students.restore');
        Route::delete('/students/{id}/force-delete', [StudentController::class, 'forceDelete'])->name('students.force-delete');

        //Working Days Routes
        Route::get('working-days', [WorkingDayController::class, 'index'])->name('working-days.index');
        Route::post('/working-days/toggle', [WorkingDayController::class, 'toggle'])->name('working-days.toggle');

        //Time Slot Routes
        Route::get('/time-slots', [TimeSlotController::class, 'index'])->name('time-slots.index');
        Route::post('/time-slots', [TimeSlotController::class, 'store'])->name('time-slots.store');
        Route::put('/time-slots/{timeSlot}', [TimeSlotController::class, 'update'])->name('time-slots.update');
        Route::post('/time-slots/copy', [TimeSlotController::class, 'copy'])->name('time-slots.copy');
        Route::delete('/time-slots/{timeSlot}', [TimeSlotController::class, 'destroy'])->name('time-slots.destroy');

        //Time Table Routes
        Route::get('timetable', [TimetableController::class, 'index'])->name('timetable.index');
        Route::get('timetable/create', [TimetableController::class, 'create'])->name('timetable.create');
        Route::get('/timetables/setup', [TimetableController::class, 'setup'])->name('timetable.setup');
        Route::any('timetables/edit', [TimetableController::class, 'edit'])->name('timetable.edit');
        Route::put('timetables/{timetable}', [TimetableController::class, 'update'])->name('timetable.update'); // Add this
        Route::delete('timetable/{timetable}', [TimetableController::class, 'destroy'])->name('timetable.delete');


        //Time Table Ajax Methods
        Route::get('/timetables/data', [TimetableController::class, 'getData']);
        Route::get('/timetables/{id}/edit-modal', [TimetableController::class, 'editModal']);
        Route::get('/timetables/create-modal', [TimetableController::class, 'createModal']);
        Route::put('/timetables/{id}/move', [TimetableController::class, 'moveEntry']);


        // Attendance Routes
        Route::get('attendance', [DailyAttendanceController::class, 'index'])->name('attendance.index');
        Route::post('attendance/daily/search', [DailyAttendanceController::class, 'searchDaily'])->name('attendance.daily.search');
        Route::post('attendance/subject/search', [DailyAttendanceController::class, 'searchSubject'])->name('attendance.subject.search');
        Route::get('create-attendance', [DailyAttendanceController::class, 'create'])->name('attendance.create');
        Route::any('create-attendance-daily', [DailyAttendanceController::class, 'storeDaily'])->name('attendance.daily.store');
        Route::any('create-attendance-subject', [DailyAttendanceController::class, 'storeSubject'])->name('attendance.subject.store');
        Route::post('create-attendance-update', [DailyAttendanceController::class, 'updateDaily'])->name('attendance.daily.update');
        Route::post('create-attendance-update-subject', [DailyAttendanceController::class, 'updateSubject'])->name('attendance.subject.update');


        // Examination Term Routes
        Route::get('/examination-term', [ExaminationTermController::class, 'index'])->name('examination-term.index');
        Route::post('/examination-term', [ExaminationTermController::class, 'store'])->name('examination-term.store');
        Route::put('/examination-term/{examination_term}', [ExaminationTermController::class, 'update'])->name('examination-term.update');
        Route::delete('/examination-term/{examination_term}', [ExaminationTermController::class, 'destroy'])->name('examination-term.destroy');
        Route::patch('/examination-term/{term}/toggle-sessional', [ExaminationTermController::class, 'toggleSessional'])->name('examination-term.toggle-sessional');


        // Examination Date Sheet
        Route::get('/examination-date-sheets', [ExaminationDateSheetController::class, 'index'])->name('examination-date-sheet.index');
        Route::match(['get', 'post'], '/examination-date-sheets/create', [ExaminationDateSheetController::class, 'create'])->name('examination-date-sheet.create');
        Route::post('/examination-date-sheets/store', [ExaminationDateSheetController::class, 'store'])->name('examination-date-sheet.store');
        Route::put('/examination-date-sheets/{examination_date_sheet}', [ExaminationDateSheetController::class, 'update'])->name('examination-date-sheet.update');
        Route::delete('/examination-date-sheets/{examination_date_sheet}', [ExaminationDateSheetController::class, 'destroy'])->name('examination-date-sheet.destroy');


        // Examination Marks

         Route::get('/examination-marks', [ExaminationMarkController::class, 'index'])->name('examination-marks.index');
         Route::get('/examination-marks/create', [ExaminationMarkController::class, 'create'])->name('examination-marks.create');
         Route::any('/examination-marks/prepare', [ExaminationMarkController::class, 'prepareMarkSheet'])->name('examination-marks.prepare');
         Route::post('/examination-marks', [ExaminationMarkController::class, 'store'])->name('examination-marks.store');

         //Change status of program class
         Route::patch('/classes/{class}/toggle-status', [ProgramController::class, 'toggleStatus'])->name('classes.toggle_status');


        // Settings Route
        Route::prefix('settings')->name('settings.')->middleware(['auth'])->group(function () {
        Route::get('institute', [SettingController::class, 'indexInstitute'])->name('institute');
        Route::get('email', [SettingController::class, 'indexEmail'])->name('email');
        Route::get('whatsapp', [SettingController::class, 'indexWhatsapp'])->name('whatsapp');
        });

        Route::post('update-institute', [SettingController::class, 'updateInstitute'])->name('update.institute');
        Route::post('update-email', [SettingController::class, 'updateEmail'])->name('update.email');
        Route::post('update-whatsapp', [SettingController::class, 'updateWhatsapp'])->name('update.whatsapp');

        //Examination Session Controller
        Route::get('examination-sessions', [ExaminationSessionController::class, 'index'])->name('examination-session.index');
        Route::post('examination-sessions', [ExaminationSessionController::class, 'store'])->name('examination-session.store');
        Route::put('examination-sessions/{examination_session}', [ExaminationSessionController::class, 'update'])->name('examination-session.update');
        Route::delete('examination-sessions/{examination_session}', [ExaminationSessionController::class, 'destroy'])->name('examination-session.destroy');
        Route::patch('examination-sessions/{examination_session}/toggle-running', [ExaminationSessionController::class, 'toggleRunning'])->name('examination-session.toggle-running');

        // Rooms Routes
        Route::get('rooms', [RoomController::class, 'index'])->name('rooms.index');
        Route::post('rooms', [RoomController::class, 'store'])->name('rooms.store');
        Route::put('rooms/{room}', [RoomController::class, 'update'])->name('rooms.update');
        Route::delete('rooms/{room}', [RoomController::class, 'destroy'])->name('rooms.destroy');

        // Fee Group Routes
        Route::get('/fee-groups', [FeeGroupController::class, 'index'])->name('fee-group.index');
        Route::post('/fee-groups', [FeeGroupController::class, 'store'])->name('fee-group.store');
        Route::put('/fee-groups/{fee_group}', [FeeGroupController::class, 'update'])->name('fee-group.update');
        Route::delete('/fee-groups/{fee_group}', [FeeGroupController::class, 'destroy'])->name('fee-group.destroy');

        //Fee Type Routes
        Route::get('/fee-types', [FeeTypeController::class, 'index'])->name('fee-type.index');
        Route::post('/fee-types', [FeeTypeController::class, 'store'])->name('fee-type.store');
        Route::put('/fee-types/{feeType}', [FeeTypeController::class, 'update'])->name('fee-type.update');
        Route::delete('/fee-types/{feeType}', [FeeTypeController::class, 'destroy'])->name('fee-type.destroy');

        // Fees (Fee Setup)
        Route::get('/fees', [FeeController::class, 'index'])->name('fee.index');
        Route::post('/fees', [FeeController::class, 'store'])->name('fee.store');
        Route::put('/fees/{fee}', [FeeController::class, 'update'])->name('fee.update');
        Route::delete('/fees/{fee}', [FeeController::class, 'destroy'])->name('fee.destroy');

        //Fee Template
        Route::get('/fee-templates', [FeeTemplateController::class, 'index'])->name('fee-templates.index');
        Route::post('/fee-templates', [FeeTemplateController::class, 'store'])->name('fee-templates.store');
        Route::put('/fee-templates/{feeTemplate}', [FeeTemplateController::class, 'update'])->name('fee-templates.update');
        Route::delete('/fee-templates/{feeTemplate}', [FeeTemplateController::class, 'destroy'])->name('fee-templates.destroy');
        Route::get('/fee-templates/{feeTemplate}', [FeeTemplateController::class, 'show'])->name('fee-templates.show');

        //Visitor Log Routes
        Route::get('/visitor-logs', [VisitorsLogController::class, 'index'])->name('visitor-logs.index');
        Route::post('/visitor-logs', [VisitorsLogController::class, 'store'])->name('visitor-logs.store');
        Route::put('/visitor-logs/{visitor_log}', [VisitorsLogController::class, 'update'])->name('visitor-logs.update');
        Route::delete('/visitor-logs/{visitor_log}', [VisitorsLogController::class, 'destroy'])->name('visitor-logs.destroy');

        // Postal Routes
        Route::get('/postals', [PostalController::class, 'index'])->name('postals.index');
        Route::post('/postals', [PostalController::class, 'store'])->name('postals.store');
        Route::put('/postals/{postal}', [PostalController::class, 'update'])->name('postals.update');
        Route::delete('/postals/{postal}', [PostalController::class, 'destroy'])->name('postals.destroy');



        //Ajax Routes
        Route::get('/ajax-study-levels', [AjaxController::class, 'studyLevels']);
        Route::get('/ajax-programs', [AjaxController::class, 'programs']);
        Route::get('/ajax-courses', [AjaxController::class, 'courses']);
        Route::get('/program-classes', [AjaxController::class, 'programClasses']);
        Route::get('/ajax-course-sections', [AjaxController::class, 'courseSections']);
        Route::get('/section-teachers', [AjaxController::class, 'sectionTeacher']);
        Route::get('/ajax-timetables', [AjaxController::class, 'timetables']);
        Route::get('/ajax-examination-terms', [AjaxController::class, 'examinationTerms']);
        Route::get('/ajax-fee-types', [AjaxController::class, 'feeTypes']);



});

require __DIR__.'/auth.php';
