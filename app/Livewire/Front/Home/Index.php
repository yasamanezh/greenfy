<?php

namespace App\Livewire\Front\Home;

use App\Models\UserWebsite;
use Livewire\Component;

class Index extends Component
{
    public function render()
    {
        $website = UserWebsite::with([
            'user:id,name,email,phone',
            'activeSubscription' => function($query) {
                $query->with(['plan:id,name,slug', 'planPrice:id,duration,price']);
            },
            'smsCredits'
        ])
        ->first();

        dd($website);
        return view('livewire.front.home.index')->layout('layouts.front');
    }
}
