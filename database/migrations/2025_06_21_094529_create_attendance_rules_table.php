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
        Schema::create('attendance_rules', function (Blueprint $table) {
            $table->id();
            $table->enum('attendance_type', ['daily', 'subject'])->default('subject');

            $table->unsignedInteger('allowed_before_minutes')->default(15);
            $table->unsignedInteger('allowed_after_minutes')->default(15);

            $table->boolean('restrict_to_first_half')->default(false);
            $table->boolean('mark_once_per_slot')->default(true);
            $table->boolean('restrict_backdate')->default(true);
            $table->boolean('restrict_future_date')->default(true);

            $table->boolean('allow_admin_override')->default(true);
            $table->unsignedTinyInteger('max_daily_absences')->nullable();
            $table->boolean('auto_mark_present_on_login')->default(false);
            $table->boolean('requires_reason_for_absent')->default(false);
            $table->boolean('requires_location_validation')->default(false);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendance_rules');
    }
};
