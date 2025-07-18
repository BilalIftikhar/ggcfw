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
        Schema::create('daily_attendance_rolls', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('student_id');
            $table->unsignedBigInteger('program_class_id');
            $table->year('year');
            $table->unsignedTinyInteger('month');
            $table->unsignedBigInteger('marked_by')->nullable();
            for ($i = 1; $i <= 31; $i++) {
                $table->enum("day_$i", ['present', 'absent', 'leave', 'late'])->nullable();
            }

            $table->timestamps();

            $table->unique(['student_id', 'program_class_id', 'year', 'month'], 'unique_attendance_per_month');

            $table->foreign('student_id')->references('id')->on('students')->onDelete('cascade');
            $table->foreign('program_class_id')->references('id')->on('program_classes')->onDelete('cascade');
            $table->foreign('marked_by')->references('id')->on('users')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('daily_attendance_rolls');
    }
};
