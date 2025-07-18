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
            $table->enum('status', ['enrolled', 'dropped', 'cancelled', 'completed'])
                ->default('enrolled') ->after('is_mandatory');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('enrollment_details', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
};
