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
        Schema::table('course_sections', function (Blueprint $table) {
            // Replace is_lab with has_lab after is_active
            $table->boolean('has_lab')
                ->default(false)
                ->after('is_active')
                ->comment('Whether this section has a lab component');

            // Lecture credit hours
            $table->unsignedSmallInteger('credit_hours')
                ->default(0)
                ->after('has_lab')
                ->comment('Lecture credit hours for this section');

            // Lab credit hours
            $table->unsignedSmallInteger('lab_credit_hours')
                ->default(0)
                ->comment('Lab credit hours for this section');

            // Weekly required minutes
            $table->unsignedInteger('required_minutes_theory_weekly')
                ->comment('Total required minutes per week (calculated)');

            $table->unsignedInteger('required_minutes_lab_weekly')
                ->comment('Total required minutes per week (calculated)');

            // Continuous slot requirement (for labs)
            $table->boolean('requires_continuous_slots')
                ->default(false)
                ->comment('For labs: whether time slots must be continuous');
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('course_sections', function (Blueprint $table) {
            $table->dropColumn(['has_lab',
                'credit_hours',
                'lab_credit_hours',
                'required_minutes_theory_weekly',
                'required_minutes_lab_weekly',
                'requires_continuous_slots']);
        });
    }
};
