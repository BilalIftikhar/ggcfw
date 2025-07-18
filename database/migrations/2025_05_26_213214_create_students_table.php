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
        Schema::create('students', function (Blueprint $table) {
            $table->id();

            // PERSONAL
            $table->string('name'); // Full Name
            $table->string('cnic')->unique();
            $table->string('father_name');
            $table->string('father_cnic')->nullable();
            $table->text('address')->nullable();
            $table->string('religion')->nullable();
            $table->string('blood_group')->nullable();
            $table->enum('gender', ['female', 'transgender'])->nullable();
            $table->string('student_contact')->nullable();
            $table->string('parent_contact')->nullable();
            $table->string('whatsapp_no')->nullable();
            $table->string('email')->nullable()->unique();
            $table->date('date_of_birth')->nullable();
            $table->string('picture')->nullable(); // Path to uploaded image
            $table->string('roll_number');
            $table->string('registration_number');
            $table->enum('status', ['studying', 'passed_out', 'graduated', 'dropped', 'expelled'])->default('studying');


            // ACADEMIC INFORMATION
            $table->string('matric_passing_year')->nullable();
            $table->string('matric_roll_no')->nullable();
            $table->string('matric_board')->nullable();
            $table->integer('matric_obtained_marks')->nullable();
            $table->integer('matric_total_marks')->nullable();
            $table->string('matric_group')->nullable();

            $table->string('inter_passing_year')->nullable();
            $table->string('inter_roll_no')->nullable();
            $table->string('inter_board')->nullable();
            $table->integer('inter_obtained_marks')->nullable();
            $table->integer('inter_total_marks')->nullable();
            $table->string('inter_group')->nullable();

            $table->string('grad_passing_year')->nullable();
            $table->string('grad_reg_no')->nullable();
            $table->string('grad_board')->nullable();
            $table->integer('grad_obtained_marks')->nullable();
            $table->integer('grad_total_marks')->nullable();
            $table->string('grad_group')->nullable();

            // OTHERS
            $table->boolean('is_hafiz')->default(false);
            $table->boolean('father_job')->default(false);
            $table->string('father_department')->nullable();
            $table->string('father_designation')->nullable();

            // Foreign Keys for relationships
            $table->foreignId('academic_session_id')->constrained()->onDelete('cascade');
            $table->foreignId('study_level_id')->constrained()->onDelete('cascade');
            $table->foreignId('program_id')->constrained()->onDelete('cascade');

            // User login related fields
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('temporary_password')->nullable();

            // Auditing & Soft Delete
            $table->boolean('is_active')->default(true);
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
        Schema::dropIfExists('students');
    }
};
