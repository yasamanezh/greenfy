<?php

namespace App\Livewire\Auth\Layout;

use App\Actions\Auth\SaveUserInfoAction;
use Illuminate\Validation\ValidationException;
use Livewire\Component;

class UserInfoSection extends Component
{
    public array $data = [
        'name' => '',
        'email' => '',
        'password' => '',
        'password_confirmation' => '',
    ];

    public string $phone;

    public function mount(string $phone, array $data = []): void
    {
        $this->phone = $phone;
        $this->data = array_merge($this->data, $data);
    }

    public function submit(SaveUserInfoAction $action): void
    {
        $this->resetErrorBag();

        try {
            $payload = array_merge($this->data, ['phone' => $this->phone]);
            $action($this->phone, $payload);
            $this->dispatch('register-data-sync', key: 'user-info', value: $this->data);
            $this->dispatch('register-message', type: '', message: '');
            $this->dispatch('register-step-changed', step: 'website-info');
        } catch (ValidationException $e) {
            foreach ($e->errors() as $field => $messages) {
                $this->addError($field, $messages[0]);
            }
        }
    }

    public function render()
    {
        return view('livewire.auth.layout.user-info-section');
    }
}
