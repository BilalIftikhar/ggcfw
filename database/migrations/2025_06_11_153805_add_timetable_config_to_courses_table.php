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
        Schema::table('courses', function (Blueprint $table) {
            $table->boolean('requires_continuous_slots')
                ->default(false)
                ->after('has_lab')
                ->comment('For labs: whether time slots must be continuous');

            $table->unsignedInteger('required_minutes_theory_weekly')
                ->nullable()
                ->after('lab_credit_hours')
                ->comment('Required weekly minutes for theory sessions');

            $table->unsignedInteger('required_minutes_lab_weekly')
                ->nullable()
                ->after('required_minutes_theory_weekly')
                ->comment('Required weekly minutes for lab sessions');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            $table->dropColumn([
                'requires_continuous_slots',
                'required_minutes_theory_weekly',
                'required_minutes_lab_weekly',
            ]);
        });
    }
};
