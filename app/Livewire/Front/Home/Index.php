<?php

namespace App\Livewire\Front\Home;

use App\Models\UserWebsite;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Index extends Component
{
    public function render()
    {
       
        return view('livewire.front.home.index')->layout('layouts.front');
    }
}
