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
        Schema::table('teachers', function (Blueprint $table) {
            $table->boolean('can_teach_labs')
                ->default(false)
                ->nullable()
                ->after('temporary_password')
                ->comment('Whether teacher can conduct lab sessions');

            // Add max lectures per day after 'can_teach_labs'
            $table->unsignedSmallInteger('max_lectures_per_day')
                ->default(4)
                ->nullable()
                ->after('can_teach_labs')
                ->comment('Maximum lectures per day based on rank: Lecturer/Asst Prof:4, Assoc Prof:3, Professor:2');

            // Add max lectures per week after 'max_lectures_per_day'
            $table->unsignedSmallInteger('max_lectures_per_week')
                ->default(24)
                ->nullable()
                ->after('max_lectures_per_day')
                ->comment('Maximum lectures per week based on rank: Lecturer/Asst Prof:24, Assoc Prof:18, Professor:12');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('teachers', function (Blueprint $table) {
            $table->dropColumn([
                'max_lectures_per_day',
                'max_lectures_per_week',
                'can_teach_labs'
            ]);
        });
    }
};
