<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            // Reference to student (assuming 'students' table exists)
            $table->foreignId('student_id')->constrained()->onDelete('cascade');
            // Reference to timetable (the lecture info)
            $table->foreignId('timetable_id')->constrained()->onDelete('cascade');
            // Reference to course (optional, but can be redundant as timetable has course_id)
            $table->foreignId('course_id')->constrained()->onDelete('cascade');
            // Attendance status (present/absent)
            $table->enum('status', ['present', 'absent', 'late', 'excused'])->default('absent');
            // Date of attendance (useful for recording attendance on a specific day)
            $table->date('attendance_date');
            // Add created_by and updated_by foreign keys
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendances');
    }
};
