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
        Schema::table('users', function (Blueprint $table) {
            $table->string('registration_step')->default('phone')->after('phone');
            $table->string('sms_verification_code')->nullable()->after('registration_step');
            $table->timestamp('sms_verification_expires_at')->nullable()->after('sms_verification_code');
            $table->timestamp('banned_until')->nullable()->after('sms_verification_expires_at');
            $table->integer('sms_attempts')->default(0)->after('banned_until');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['registration_step', 'sms_verification_code', 'sms_verification_expires_at', 'banned_until', 'sms_attempts']);
        });
    }
};
