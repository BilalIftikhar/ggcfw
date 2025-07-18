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
        Schema::table('programs', function (Blueprint $table) {
            // Credit hour system flag after academic_session_id
            $table->boolean('credit_hour_system')
                ->default(false)
                ->after('academic_session_id')
                ->comment('Whether program uses credit hour system (true for BS/ADP, false for Intermediate)');

            // Teaching days configuration after credit_hour_system
            $table->unsignedTinyInteger('teaching_days_per_week')
                ->after('credit_hour_system')
                ->comment('Number of teaching days per week (6 for Intermediate, 4-5 for BS/ADP)');

            // Period configuration
            $table->unsignedSmallInteger('period_duration')
                ->default(45)
                ->after('teaching_days_per_week')
                ->comment('Duration of one period in minutes');

            $table->unsignedTinyInteger('max_periods_per_day')
                ->default(6)
                ->after('period_duration')
                ->comment('Maximum number of periods per day');

            // Lab configuration
            $table->boolean('labs_on_separate_days')
                ->default(false)
                ->after('max_periods_per_day')
                ->comment('Whether labs should be scheduled on separate days');

            $table->string('preferred_lab_days')
                ->nullable()
                ->after('labs_on_separate_days')
                ->comment('Preferred days for labs (e.g., "Friday,Saturday")');

            // Attendance configuration
            $table->unsignedTinyInteger('attendance_threshold')
                ->default(75)
                ->after('preferred_lab_days')
                ->comment('Minimum attendance percentage required (e.g., 75%)');
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('programs', function (Blueprint $table) {
            $table->dropColumn([
                'credit_hour_system',
                'teaching_days_per_week',
                'period_duration',
                'max_periods_per_day',
                'labs_on_separate_days',
                'preferred_lab_days',
                'attendance_threshold'
            ]);
        });
    }
};
