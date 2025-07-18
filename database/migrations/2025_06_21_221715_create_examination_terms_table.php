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
        Schema::create('examination_terms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('examination_session_id')->constrained()->onDelete('cascade');
            // Term fields
            $table->string('title'); // e.g. Mid, Final, Quiz
            $table->string('description')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('examination_terms');
    }
};
