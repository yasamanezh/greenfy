<?php

namespace App\Livewire\Admin\Dashboard;

use App\Models\User;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('داشبورد')]
class Index extends Component
{
    /**
     * لیست وبسایت‌های کاربر جاری را بارگذاری می‌کند.
     */
    public function getWebsitesProperty()
    {
        /** @var User|null $user */
        $user = auth()->user();

        if (!$user) {
            return collect();
        }

        return $user->websites()
            ->with(['activeSubscription', 'smsCredits'])
            ->latest()
            ->get();
    }

    public function render(): View
    {
        return view('livewire.admin.dashboard.index', [
            'websites' => $this->websites,
        ]);
    }
}
