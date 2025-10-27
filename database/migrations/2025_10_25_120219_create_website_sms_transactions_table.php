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
        Schema::create('website_sms_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_website_id')->constrained()->onDelete('cascade');
            $table->foreignId('sms_package_id')->constrained()->onDelete('cascade');
            $table->integer('sms_count');
            $table->decimal('amount', 10, 2);
            $table->string('payment_token')->nullable();
            $table->integer('status')->default(TransactionStatus::PENDING->value);
            $table->dateTime('expires_at');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('website_sms_transactions');
    }
};
