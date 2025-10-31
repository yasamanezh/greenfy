<?php

namespace App\Livewire\Auth\Layout;

use App\Actions\Website\SaveWebsiteInfoAction;
use Illuminate\Validation\ValidationException;
use Livewire\Component;

class WebsiteInfoSection extends Component
{
    public string $phone;
    public string $websiteName = '';
    public string $subdomain = '';
    public ?string $description = '';

    public function mount(string $phone, array $data = []): void
    {
        $this->phone = $phone;
        foreach (['websiteName', 'subdomain', 'description'] as $field) {
            if (isset($data[$field])) {
                $this->{$field} = $data[$field];
            }
        }
    }

    public function submit(SaveWebsiteInfoAction $action): void
    {
        $this->resetErrorBag();

        try {
            $action($this->phone, [
                'websiteName' => $this->websiteName,
                'subdomain' => $this->subdomain,
                'description' => $this->description,
            ]);
            $this->dispatch('register-completed');
        } catch (ValidationException $e) {
            foreach ($e->errors() as $field => $messages) {
                $this->addError($field, $messages[0]);
            }
        }
    }

    public function render()
    {
        return view('livewire.auth.layout.website-info-section');
    }
}
