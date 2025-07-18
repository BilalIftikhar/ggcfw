<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('subject_attendances', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('student_id');
            $table->unsignedBigInteger('course_section_id');
            $table->unsignedBigInteger('timetable_id'); // references a specific slot
            $table->date('attendance_date');
            $table->enum('status', ['present', 'absent', 'leave', 'late'])->nullable();
            $table->unsignedBigInteger('marked_by')->nullable();

            $table->unique(['student_id', 'timetable_id', 'attendance_date'], 'unique_subject_attendance');

            $table->foreign('student_id')->references('id')->on('students')->onDelete('cascade');
            $table->foreign('course_section_id')->references('id')->on('course_sections')->onDelete('cascade');
            $table->foreign('timetable_id')->references('id')->on('timetables')->onDelete('cascade');
            $table->foreign('marked_by')->references('id')->on('users')->nullOnDelete();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subject_attendances');
    }
};
