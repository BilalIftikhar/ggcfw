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
        Schema::create('settings', function (Blueprint $table) {
            $table->id();

            // Required: Set default for required field
            $table->string('institute_name')->default('Your Institute Name');

            // Optional: Nullable fields (no default needed)
            $table->string('tagline')->nullable();
            $table->string('institute_email')->nullable();
            $table->string('institute_phone')->nullable();
            $table->string('institute_website')->nullable();
            $table->text('institute_address')->nullable();

            // WhatsApp defaults
            $table->string('whatsapp_api_key')->nullable();
            $table->string('whatsapp_number')->nullable();
            $table->string('whatsapp_url')->nullable();
            $table->boolean('whatsapp_active')->default(false);

            // Email defaults
            $table->string('smtp_host')->default('smtp.example.com');
            $table->integer('smtp_port')->default(587);
            $table->string('smtp_username')->default('username@example.com');
            $table->string('smtp_password')->default('password');
            $table->string('smtp_encryption')->default('tls');
            $table->string('smtp_from_address')->default('noreply@example.com');
            $table->string('smtp_from_name')->default('Your Institute');

            // Media (set as nullable, managed by media library)
            $table->string('institute_logo')->nullable();
            $table->string('voucher_logo')->nullable();

            // Optional Text Defaults
            $table->text('voucher_footer_note')->nullable();

            // Other Defaults
            $table->string('default_currency')->default('PKR');
            $table->string('timezone')->default('Asia/Karachi');
            $table->date('academic_year_start')->nullable();
            $table->date('academic_year_end')->nullable();
            $table->boolean('maintenance_mode')->default(false);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
