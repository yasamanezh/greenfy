<?php

use App\Enums\SubscriptionStatus;
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
        Schema::create('website_modules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_website_id')->constrained()->onDelete('cascade');
            $table->foreignId('module_id')->constrained()->onDelete('cascade');
            $table->dateTime('start_date');
            $table->dateTime('end_date');
            $table->integer('status')->default(SubscriptionStatus::ACTIVE->value);
            $table->json('config')->nullable();
            $table->timestamps();

            $table->unique(['user_website_id', 'module_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('website_modules');
    }
};
