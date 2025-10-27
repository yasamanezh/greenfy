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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_website_id')->nullable()->constrained()->onDelete('cascade');
            $table->morphs('transactionable');
            $table->string('payment_token')->nullable();
            $table->decimal('amount', 10, 2);
            $table->string('gateway');
            $table->integer('status')->default(TransactionStatus::PENDING->value);
            $table->json('gateway_response')->nullable();
            $table->boolean('wallet_used')->default(false);
            $table->decimal('wallet_amount', 10, 2)->default(0);
            $table->timestamps();

            $table->index('payment_token');
       
           
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
