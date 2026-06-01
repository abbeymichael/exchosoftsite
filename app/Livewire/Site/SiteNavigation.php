<?php

namespace App\Livewire\Site;

use App\Models\Product;
use App\Models\SiteSetting;
use Livewire\Component;

class SiteNavigation extends Component
{
    public bool $notificationVisible = true;
    public string $notificationText  = '';
    public string $notificationLink  = '#';
    public string $notificationLabel = 'Read Now';

    // Products mega-menu data (dynamic)
    public array $products = [];

    public function mount(): void
    {
        // Load notification bar from settings or fallback
        $s = SiteSetting::getGroup('site');
        $this->notificationVisible = (bool) ($s['nav_notification_visible'] ?? true);
        $this->notificationText    = $s['nav_notification_text']  ?? 'New ↗ Case Study: Healthcare Digital Transformation in Ghana';
        $this->notificationLink    = $s['nav_notification_link']  ?? '#';
        $this->notificationLabel   = $s['nav_notification_label'] ?? 'Read Now';

        // Dynamic products for mega-menu — published, up to 8
        $this->products = Product::published()
            ->orderBy('sort_order')
            ->limit(8)
            ->get(['name', 'slug', 'tagline', 'category', 'icon'])
            ->toArray();
    }

    public function dismissNotification(): void
    {
        $this->notificationVisible = false;
    }

    public function render(): \Illuminate\Contracts\View\View
    {
        return view('livewire.site.site-navigation');
    }
}
