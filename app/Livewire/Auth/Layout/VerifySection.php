<?php

namespace App\Livewire\Auth\Layout;

use App\Actions\Auth\SendOtpAction;
use App\Actions\Auth\VerifyOtpAction;
use Illuminate\Support\Arr;
use Illuminate\Validation\ValidationException;
use Livewire\Component;

class VerifySection extends Component
{
    public string $phone;
    public array $digits = ['', '', '', '', ''];
    public bool $canResend = false;
    public int $secondsLeft = 120;
    protected ?int $timerId = null;

    protected $listeners = [
        'code-sent' => 'restartTimer',
        'decrement-timer' => 'decrementTimer'
    ];

    public function mount(string $phone, array $digits = []): void
    {
        $this->phone = $phone;
        foreach (range(0, 4) as $index) {
            $this->digits[$index] = $digits[$index] ?? '';
        }
        $this->restartTimer();
    }

    public function updatedDigits(): void
    {
        $this->dispatch('otp-digits-updated', digits: $this->digits);
    }

    public function submit(VerifyOtpAction $verifyOtpAction): void
    {
        $this->resetErrorBag();
        $code = implode('', $this->digits);

        try {
            $result = $verifyOtpAction($this->phone, $code);

            if ($result['completed']) {
                $this->dispatch('register-completed');
                return;
            }

            $this->dispatch('register-step-changed', step: $result['nextStep'], phone: $this->phone);
            $this->dispatch('register-data-sync', key: 'user-info', value: [
                'name' => $result['user']->name,
                'email' => $result['user']->email,
            ]);
            $this->dispatch('register-message', type: 'success', message: 'کد تایید شد، لطفاً ادامه دهید');
        } catch (ValidationException $e) {
            $this->addError('code', Arr::first($e->errors()['code'] ?? []) ?? 'کد تایید نامعتبر است');
            $this->dispatch('register-message', type: 'danger', message: 'کد تایید نامعتبر است');
        } catch (\Throwable $e) {
            $this->addError('code', 'خطای غیرمنتظره‌ای رخ داد.');
            $this->dispatch('register-message', type: 'danger', message: 'خطای غیرمنتظره‌ای رخ داد');
        }
    }

    public function resend(SendOtpAction $sendOtpAction): void
    {
        $this->resetErrorBag();
        try {
            $sendOtpAction($this->phone, true);
            $this->restartTimer();
            $this->dispatch('register-message', type: 'success', message: 'کد جدید ارسال شد');
        } catch (ValidationException $e) {
            $this->addError('code', Arr::first($e->errors()['phone'] ?? []) ?? 'ارسال مجدد امکان‌پذیر نیست');
            $this->dispatch('register-message', type: 'danger', message: 'ارسال مجدد امکان‌پذیر نیست');
        }
    }

    public function editPhone(): void
    {
        $this->resetErrorBag();
        $this->resetValidation();
        $this->digits = ['', '', '', '', ''];
        $this->dispatch('register-message', type: '', message: '');
        $this->dispatch('password-edit-phone');
    }

    public function restartTimer(): void
    {
        $this->canResend = false;
        $this->secondsLeft = 120;

        $this->dispatch('clear-timer');
        $this->dispatch('start-timer', seconds: $this->secondsLeft);
    }

    public function decrementTimer(): void
    {
        if ($this->secondsLeft > 0) {
            $this->secondsLeft--;
            if ($this->secondsLeft <= 0) {
                $this->canResend = true;
            }
        }
    }

    public function updateTimer(int $seconds): void
    {
        $this->secondsLeft = $seconds;
        if ($seconds <= 0) {
            $this->canResend = true;
        }
    }

    public function render()
    {
        return view('livewire.auth.layout.verify-section');
    }
}
