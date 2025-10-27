<?php

use App\Enums\ModuleCategory;
use App\Enums\PlanDuration;
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
        Schema::create('modules', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('version')->default('1.0.0');
            $table->string('author')->nullable();
            $table->integer('category')->default(ModuleCategory::OTHER->value);
            $table->string('icon')->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('is_premium')->default(false);
            $table->decimal('price', 10, 2)->default(0);
            $table->decimal('discount_price', 10, 2)->nullable();
            $table->decimal('discount_percentage', 5, 2)->nullable();
            $table->boolean('has_discount')->default(false);
            $table->integer('duration')->default(PlanDuration::MONTHLY->value);
            $table->integer('validity_days')->default(30);
            $table->json('config')->nullable();
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('modules');
    }
};
