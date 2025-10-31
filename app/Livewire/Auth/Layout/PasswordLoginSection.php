<?php

namespace App\Livewire\Auth\Layout;

use App\Actions\Auth\ProcessPasswordLoginAction;
use App\Actions\Auth\SendOtpAction;
use Illuminate\Validation\ValidationException;
use Livewire\Component;

class PasswordLoginSection extends Component
{
    public string $phone;
    public string $password = '';

    public function mount(string $phone): void
    {
        $this->phone = $phone;
    }

    public function submit(ProcessPasswordLoginAction $action): void
    {
       
        $this->resetErrorBag();
        try {
            $action($this->phone, $this->password);
            $this->dispatch('register-completed');
        } catch (ValidationException $e) {
            $this->addError('password', $e->getMessage());
            $this->dispatch('register-message', type: 'danger', message: $e->getMessage());
        }
    }

    public function switch(SendOtpAction $sendOtpAction): void
    {
        $this->resetErrorBag();
        try {
            $sendOtpAction($this->phone, true);
            session(['login_mode' => 'otp', 'registration_phone' => $this->phone]);
            $this->dispatch('register-phone-updated', phone: $this->phone);
            $this->dispatch('register-step-changed', step: 'verify', phone: $this->phone);
            $this->dispatch('register-message', type: 'success', message: 'کد جدید ارسال شد');
            $this->dispatch('code-sent');
        } catch (ValidationException $e) {
            $message = $e->errors()['phone'][0] ?? 'ارسال کد امکان‌پذیر نیست';
            $this->dispatch('register-message', type: 'danger', message: $message);
        } catch (\Throwable $e) {
            $this->dispatch('register-message', type: 'danger', message: 'خطای غیرمنتظره‌ای رخ داد');
        }
    }

    public function render()
    {
        return view('livewire.auth.layout.password-login-section');
    }
}
