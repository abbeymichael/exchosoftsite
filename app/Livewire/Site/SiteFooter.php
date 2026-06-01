<?php

namespace App\Livewire\Site;

use App\Models\SiteSetting;
use Livewire\Component;

class SiteFooter extends Component
{
    public string $email    = 'contact@exchosoft.com';
    public string $linkedIn = '#';
    public string $twitter  = '#';
    public string $year     = '';

    public function mount(): void
    {
        $s = SiteSetting::getGroup('site');
        $this->email    = $s['contact_email']  ?? 'contact@exchosoft.com';
        $this->linkedIn = $s['social_linkedin'] ?? '#';
        $this->twitter  = $s['social_twitter']  ?? '#';
        $this->year     = date('Y');
    }

    public function render(): \Illuminate\Contracts\View\View
    {
        return view('livewire.site.site-footer');
    }
}
