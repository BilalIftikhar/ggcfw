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
        Schema::table('time_slots', function (Blueprint $table) {
            $table->boolean('is_break')->default(false)->after('end_time'); // or wherever appropriate
            $table->integer('sort_order')->default(0)->after('is_break');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('time_slots', function (Blueprint $table) {
            $table->dropColumn(['is_break', 'sort_order']);
        });
    }
};
