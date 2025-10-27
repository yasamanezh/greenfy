<?php

use App\Enums\SmsStatus;
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
        Schema::create('sms_logs', function (Blueprint $table) {
            $table->id();
               $table->foreignId('user_website_id')->constrained()->onDelete('cascade');
            $table->foreignId('sms_provider_id')->constrained()->onDelete('cascade');
            $table->string('to');
            $table->text('message');
            $table->string('message_id')->nullable();
            $table->integer('status')->default(SmsStatus::SENT->value);
            $table->json('provider_response')->nullable();
            $table->decimal('cost', 8, 4)->nullable();
            $table->integer('sms_count')->default(1);
            $table->timestamps();
            
            $table->index('to');
            $table->index('message_id');
            $table->index('created_at');
       
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sms_logs');
    }
};
