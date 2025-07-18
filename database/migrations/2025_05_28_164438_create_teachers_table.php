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
        Schema::create('teachers', function (Blueprint $table) {
            $table->id();
            $table->string('cnic')->unique();
            $table->string('seniority_no')->nullable();
            $table->string('name');
            $table->string('father_name');
            $table->string('designation');
            $table->string('bps')->nullable();
            $table->date('dob')->nullable();
            $table->string('domicile')->nullable();
            $table->date('retirement_date')->nullable();
            $table->string('subject')->nullable();
            $table->text('qualification')->nullable();
            $table->date('govt_entry_date')->nullable();
            $table->enum('employee_mode', ['regular', 'contract', 'adhoc'])->nullable();
            $table->string('quota')->nullable();
            $table->date('joining_date_adhoc_lecturer')->nullable();
            $table->date('joining_date_regular_lecturer')->nullable();
            $table->date('joining_date_assistant_prof')->nullable();
            $table->date('joining_date_associate_prof')->nullable();
            $table->date('joining_date_professor')->nullable();
            $table->date('joining_date_principal')->nullable();
            $table->string('qualifying_service')->nullable(); // e.g., "25.3.10"
            $table->date('joining_date_present_station')->nullable();
            $table->string('cadre')->nullable();
            $table->text('home_address')->nullable();
            $table->string('work_contact')->nullable();
            $table->string('home_contact')->nullable();


            // Status and Auditing
            $table->boolean('is_active')->default(true);
            $table->unsignedBigInteger('user_id')->nullable(); // FK to users table
            $table->string('temporary_password')->nullable(); // Temporarily store initial password
            $table->enum('working_status', ['working', 'retired', 'fired', 'other'])->default('working');
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();

            $table->timestamps();
            $table->softDeletes();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('teachers');
    }
};
