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
        Schema::create('enrollments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->onDelete('cascade');
            $table->foreignId('program_id')->constrained()->onDelete('cascade');
            $table->foreignId('program_class_id')->constrained()->onDelete('cascade'); // semester or year
            $table->foreignId('academic_session_id')->constrained()->onDelete('cascade');
            $table->foreignId('examination_session_id')->constrained()->onDelete('cascade');
            $table->date('enrolled_on')->nullable();
            $table->enum('status', ['enrolled', 'cancelled', 'completed'])->default('enrolled');
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
        Schema::dropIfExists('enrollments');
    }
};
