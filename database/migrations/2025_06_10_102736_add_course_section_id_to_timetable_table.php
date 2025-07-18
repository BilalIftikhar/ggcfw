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
        Schema::table('timetables', function (Blueprint $table) {
            $table->unsignedBigInteger('course_section_id')->nullable()->after('course_id');

            // Optional: Add foreign key constraint
            $table->foreign('course_section_id')
                ->references('id')
                ->on('course_sections')
                ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('timetables', function (Blueprint $table) {
            $table->dropForeign(['course_section_id']);
            $table->dropColumn('course_section_id');
        });
    }
};
