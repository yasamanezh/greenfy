<?php

namespace App\Livewire\Auth;

use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.auth')]
class Register extends Component
{
    public string $currentStep = 'auth';
    public string $phone = '';
    public array $userInfo = [
        'name' => '',
        'email' => '',
    ];
    public array $websiteInfo = [
        'websiteName' => '',
        'subdomain' => '',
        'description' => '',
    ];

    public ?string $message = null;
    public ?string $messageType = null;

    protected $listeners = [
        'register-step-changed' => 'handleStepChange',
        'register-message' => 'handleMessage',
        'register-phone-updated' => 'handlePhoneUpdate',
        'register-data-sync' => 'handleDataSync',
        'register-completed' => 'redirectToDashboard',
        'password-switch-to-otp' => 'handleSwitchToOtp',
        'password-edit-phone' => 'handleEditPhone',
    ];

    public function handleSwitchToOtp(): void
    {
        session(['login_mode' => 'otp']);
        $this->currentStep = 'verify';
    }

    public function mount(?string $step = null): void
    {
        
        $allowedSteps = ['auth', 'verify', 'password-login', 'user-info', 'website-info'];
        $requestedStep = $step && in_array($step, $allowedSteps, true) ? $step :'auth';
        $this->currentStep = $requestedStep;
    }

    public function handleStepChange(string $step, ?string $phone = null): void
    {
        $this->currentStep = $step;
        if ($phone) {
            $this->phone = $phone;
            session(['registration_phone' => $phone]);
        }
    }

    public function handleMessage(string $type, string $message): void
    {
        $this->messageType = $type;
        $this->message = $message;
    }

    public function handlePhoneUpdate(string $phone): void
    {
        $this->phone = $phone;
    }



    public function handleDataSync(string $key, array $value): void
    {
        if ($key === 'user-info') {
            $this->userInfo = array_merge($this->userInfo, $value);
        } elseif ($key === 'website-info') {
            $this->websiteInfo = array_merge($this->websiteInfo, $value);
        }
    }

    public function handleEditPhone(): void
    {
        session()->forget(['registration_phone', 'login_mode']);
        $this->currentStep = 'auth';
        $this->phone = '';
        $this->userInfo = ['name' => '', 'email' => ''];
        $this->websiteInfo = ['websiteName' => '', 'subdomain' => '', 'description' => ''];
    }

    public function redirectToDashboard(): void
    {

        $this->redirect(route('dashboard'), navigate: false);
    }

    public function render()
    {
        return view('livewire.auth.register');
    }
}
