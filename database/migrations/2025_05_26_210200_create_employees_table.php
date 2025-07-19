<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('employees', function (Blueprint $table) {
            $table->id();

            // Personal Details
            $table->string('cnic_no')->unique()->comment('CNIC.NO');
            $table->string('name')->comment('Name of Official/Officer');
            $table->string('father_name')->comment('Father\'s Name');
            $table->string('designation')->nullable();
            $table->string('cadre')->nullable();
            $table->unsignedTinyInteger('bps')->nullable();

            $table->date('date_of_birth')->nullable()->comment('Date of Birth');
            $table->string('domicile')->nullable()->comment('Domicile');

            $table->date('date_of_retirement')->nullable();

            $table->string('qualification')->nullable();

            $table->date('date_of_first_entry')->nullable()->comment('Date of 1st entry in Govt. service');

            $table->string('quota')->nullable()->comment('Recruited against Quota');

            $table->date('date_of_joining_contract')->nullable();
            $table->date('date_of_joining_regular')->nullable();

            $table->enum('status', ['Regular', 'Contract'])->nullable();

            $table->enum('working_status', ['working', 'retired', 'fired', 'other'])->default('working')->comment('Working Status');

            // Designation History
            $table->date('date_of_joining_junior_clerk')->nullable();
            $table->date('date_of_joining_senior_clerk')->nullable();
            $table->date('date_of_joining_lab_supervisor')->nullable();
            $table->date('date_of_joining_head_clerk')->nullable();
            $table->date('date_of_joining_superintendent')->nullable();
            $table->date('date_of_joining_senior_bursar')->nullable();

            $table->string('qualifying_service')->nullable()->comment('Qualifying Service Y.M.D');

            $table->date('date_of_joining_current_station')->nullable()->comment('Date of joining at Present Station');

            $table->text('home_address')->nullable();

            // Contact numbers
            $table->string('home_contact')->nullable();
            $table->string('work_contact')->nullable();

            // Auditing & system fields
            $table->boolean('is_active')->default(true);
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('temporary_password')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');

            $table->softDeletes();
            $table->timestamps();

        });

        Schema::table('employees', function (Blueprint $table) {
            $table->index('cnic_no');
            $table->index('designation');
            $table->index('bps');
            $table->index('status');
            $table->index('working_status');
        });
    }



    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};
