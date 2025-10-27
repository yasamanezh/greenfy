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
        Schema::create('website_sms_templates', function (Blueprint $table) {
            $table->id();
             $table->foreignId('user_website_id')->constrained()->onDelete('cascade');
            $table->foreignId('sms_template_id')->constrained()->onDelete('cascade');
            $table->text('custom_content')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->unique(['user_website_id', 'sms_template_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('website_sms_templates');
    }
};
