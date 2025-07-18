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
        Schema::table('roles', function (Blueprint $table) {
            $table->boolean('is_admin')->default(false)->after('is_system');
            $table->boolean('is_teaching')->default(false)->after('is_admin');
            $table->boolean('is_student')->default(false)->after('is_teaching');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('roles', function (Blueprint $table) {
            $table->dropColumn(['is_admin', 'is_teaching','is_student']);

        });
    }
};
