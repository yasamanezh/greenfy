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
        Schema::create('website_sms_credits', function (Blueprint $table) {
            $table->id(); $table->foreignId('user_website_id')->constrained()->onDelete('cascade');
            $table->integer('remaining_sms')->default(0);
            $table->dateTime('expires_at')->nullable();
            $table->timestamps();
            
            $table->unique(['user_website_id']);
           
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('website_sms_credits');
    }
};
