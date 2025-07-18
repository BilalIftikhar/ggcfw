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
            $table->boolean('is_mandatory')->default(false)->after('lab_credit_hours');
            $table->unsignedInteger('no_of_sections')->default(1)->after('is_mandatory');
            $table->unsignedInteger('students_per_section')->default(0)->after('no_of_sections');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            $table->dropColumn([
                'is_mandatory',
                'no_of_sections',
                'students_per_section'
            ]);
        });
    }
};
