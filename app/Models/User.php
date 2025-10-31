<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'phone',
        'password',
        'is_admin',
        'registration_step',
        'sms_verification_code',
        'sms_verification_expires_at',
        'banned_until',
        'sms_attempts',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'phone_verified_at' => 'datetime',
        'sms_verification_expires_at' => 'datetime',
        'banned_until' => 'datetime',
    ];

    // Relationships
    public function websites()
    {
        return $this->hasMany(UserWebsite::class);
    }

    public function wallet()
    {
        return $this->hasOne(Wallet::class);
    }

    // Authentication Methods
    public function isBanned(): bool
    {
        return $this->banned_until && $this->banned_until->isFuture();
    }

    public function canSendSms(): bool
    {
        // اگر بن شده، نمی‌تواند پیامک بفرستد
        if ($this->isBanned()) {
            return false;
        }

        // اگر در یک ساعت گذشته 5 پیامک فرستاده، بن می‌شود
        if ($this->sms_attempts >= 5) {
            $this->banned_until = now()->addHours(2);
            $this->save();
            return false;
        }

        return true;
    }

    public function incrementSmsAttempts()
    {
        $this->sms_attempts++;
        $this->save();
    }

    public function resetSmsAttempts()
    {
        $this->sms_attempts = 0;
        $this->save();
    }

    public function generateVerificationCode()
    {
        $this->sms_verification_code = rand(10000, 99999);
        $this->sms_verification_expires_at = now()->addMinutes(2);
        $this->save();
    }

    public function verifyCode($code): bool
    {
        if (!$this->sms_verification_expires_at || $this->sms_verification_expires_at->isPast()) {
            return false;
        }

        return $this->sms_verification_code === $code;
    }

    public function getCurrentStep(): string
    {
        return $this->registration_step ?? 'phone';
    }

    public function setStep(string $step)
    {
        $this->registration_step = $step;
        $this->save();
    }

    public function markPhoneAsVerified()
    {
        $this->phone_verified_at = now();
        $this->sms_verification_code = null;
        $this->sms_verification_expires_at = null;
        $this->save();
    }
}
