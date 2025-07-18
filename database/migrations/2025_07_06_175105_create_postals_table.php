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
        Schema::create('postals', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['dispatch', 'receive']);
            $table->string('reference_number');
            $table->string('to_title')->nullable();
            $table->string('from_title')->nullable();
            $table->string('address')->nullable();
            $table->string('tracking_no')->nullable();
            $table->string('courier')->nullable();
            $table->string('note')->nullable();
            $table->date('date');
            $table->string('attached_document')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('postals');
    }
};
