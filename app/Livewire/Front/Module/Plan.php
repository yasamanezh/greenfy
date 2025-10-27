<?php

namespace App\Livewire\Front\Module;

use Livewire\Component;
use App\Models\Plan as planModel;

class Plan extends Component
{
    public function render()
    {
        $plans = planModel::get();
        return view('livewire.front.module.plan',['plans'=>$plans])->layout('layouts.front');
    }
}
