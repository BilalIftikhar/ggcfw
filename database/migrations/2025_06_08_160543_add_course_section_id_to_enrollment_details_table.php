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
        Schema::table('enrollment_details', function (Blueprint $table) {
            $table->foreignId('course_section_id')
                ->nullable()
                ->constrained()
                ->on('course_sections')
                ->onDelete('set null'); // Or use 'cascade' if you want to delete dependent enrollment details

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('enrollment_details', function (Blueprint $table) {
            $table->dropForeign(['course_section_id']);
            $table->dropColumn('course_section_id');
        });
    }
};
