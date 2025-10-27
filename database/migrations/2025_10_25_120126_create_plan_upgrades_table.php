<?php

use App\Enums\TransactionStatus;
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
        Schema::create('plan_upgrades', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_website_id')->constrained()->onDelete('cascade');
            $table->foreignId('from_website_subscription_id')->constrained('website_subscriptions')->onDelete('cascade');
            $table->foreignId('to_plan_id')->constrained('plans')->onDelete('cascade');
            $table->foreignId('to_plan_price_id')->constrained('plan_prices')->onDelete('cascade');
            $table->decimal('upgrade_price', 10, 2);
            $table->decimal('paid_amount', 10, 2)->nullable();
            $table->integer('status')->default(TransactionStatus::PENDING->value);
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('plan_upgrades');
    }
};
