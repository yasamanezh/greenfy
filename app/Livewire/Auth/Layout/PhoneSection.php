<?php

namespace App\Livewire\Auth\Layout;

use App\Actions\Auth\SendOtpAction;
use Illuminate\Validation\ValidationException;
use Livewire\Component;

class PhoneSection extends Component
{
    public string $phone = '';

    public function mount(string $phone = ''): void
    {
        $this->phone = $phone;
    }

    public function submit(SendOtpAction $sendOtpAction): void
    {

        $this->validate([
            'phone' => 'required|string|max:11|min:11',
        ]);
        $this->resetErrorBag();

        try {
            $result = $sendOtpAction($this->phone);

            session(['registration_phone' => $this->phone, 'login_mode' => $result['mode']]);

            $this->dispatch('register-phone-updated', phone: $this->phone);

            if ($result['mode'] === 'password') {
                $this->dispatch('register-step-changed', step: 'password-login', phone: $this->phone);
                $this->dispatch('register-message', type: 'info', message: 'برای این شماره کلمه عبور ثبت شده است. لطفاً کلمه عبور را وارد کنید.');
            } else {
                $this->dispatch('register-step-changed', step: 'verify', phone: $this->phone);
                $this->dispatch('register-message', type: 'success', message: 'کد تایید به شماره شما ارسال شد');
                $this->dispatch('code-sent');
            }
        } catch (ValidationException $e) {
            $errors = $e->errors();
            $message = $errors['phone'][0] ?? 'خطایی رخ داده است، لطفاً دوباره تلاش کنید';
            $this->addError('phone', $message);
            $this->dispatch('register-message', type: 'danger', message: $message);
        } catch (\Throwable $e) {
            $this->addError('phone', 'خطای غیرمنتظره‌ای رخ داد.');
            $this->dispatch('register-message', type: 'danger', message: 'خطای غیرمنتظره‌ای رخ داد.');
        }
    }

    public function render()
    {
        return view('livewire.auth.layout.phone-section', [
            'forgotPasswordUrl' => route('password.request'),
        ]);
    }
}
