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
        Schema::create('examination_marks', function (Blueprint $table) {
            $table->id();

            $table->foreignId('examination_term_id')->constrained()->onDelete('cascade');
            $table->foreignId('examination_session_id')->constrained()->onDelete('cascade');
            $table->foreignId('program_id')->constrained()->onDelete('cascade');
            $table->foreignId('program_class_id')->constrained()->onDelete('cascade');
            $table->foreignId('course_id')->constrained()->onDelete('cascade');
            $table->foreignId('course_section_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('student_id')->constrained()->onDelete('cascade');

            $table->foreignId('examination_date_sheet_id')->nullable()->constrained()->onDelete('set null');

            $table->decimal('marks_obtained', 6, 2)->nullable();
            $table->decimal('total_marks', 6, 2)->nullable();

            $table->foreignId('marked_by')->nullable()->constrained('users')->onDelete('set null'); // Created by
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null'); // Last updated by

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('examination_marks');
    }
};
