<?php

namespace App\Livewire\Site;

use App\Models\Product;
use App\Models\CaseStudy;
use App\Models\SiteNotification;
use Livewire\Component;

class SiteNavigation extends Component
{
    public int $currentNotificationIndex = 0;
    public array $notifications = [];

    // Products mega-menu data (dynamic)
    public array $products = [];

    // Case studies data (dynamic)
    public array $caseStudies = [];

    public function mount(): void
    {
        // Load up to 3 active notifications ordered by priority (for carousel)
        $this->notifications = SiteNotification::current()
            ->orderByDesc('priority')
            ->limit(3)
            ->get(['id', 'title', 'message', 'button_label', 'button_url', 'is_dismissible'])
            ->map(fn($n) => [
                'id' => $n->id,
                'title' => $n->title,
                'message' => $n->message,
                'button_label' => $n->button_label,
                'button_url' => $n->button_url,
                'is_dismissible' => $n->is_dismissible,
                'dismissed' => session()->get('dismissed_notification_' . $n->id, false),
            ])
            ->toArray();

        // Load products for mega-menu (up to 8, published, ordered by sort_order)
        $this->products = Product::published()
            ->orderBy('sort_order')
            ->limit(8)
            ->get(['id', 'name', 'slug', 'tagline', 'logo', 'category'])
            ->map(fn($p) => [
                'name' => $p->name,
                'slug' => $p->slug,
                'tagline' => $p->tagline,
                'logo' => $p->logo ?? 'deployed_code',
                'category' => $p->category,
            ])
            ->toArray();

        // Load case studies for mega-menu (up to 3, published, featured first)
        $this->caseStudies = CaseStudy::published()
            ->orderByRaw('is_featured DESC')
            ->limit(3)
            ->get(['id', 'title', 'slug', 'client_name', 'client_industry', 'challenge', 'client_logo', 'tags'])
            ->map(fn($cs) => [
                'title' => $cs->title,
                'slug' => $cs->slug,
                'client_name' => $cs->client_name,
                'client_industry' => $cs->client_industry,
                'challenge' => $cs->challenge,
                'logo' => $cs->logo ?? 'deployed_code',
                'tags' => $cs->tags ?? [],
            ])
            ->toArray();
    }

    public function nextNotification(): void
    {
        if (count($this->notifications) === 0) {
            return;
        }

        $this->currentNotificationIndex = ($this->currentNotificationIndex + 1) % count($this->notifications);
    }

    public function previousNotification(): void
    {
        if (count($this->notifications) === 0) {
            return;
        }

        $this->currentNotificationIndex = ($this->currentNotificationIndex - 1 + count($this->notifications)) % count($this->notifications);
    }

    public function goToNotification(int $index): void
    {
        if ($index >= 0 && $index < count($this->notifications)) {
            $this->currentNotificationIndex = $index;
        }
    }

    public function dismissNotification(): void
    {
        if (count($this->notifications) === 0) {
            return;
        }

        $notification = $this->notifications[$this->currentNotificationIndex];

        if ($notification['is_dismissible']) {
            session()->put('dismissed_notification_' . $notification['id'], true);
            $this->notifications[$this->currentNotificationIndex]['dismissed'] = true;
        }

        // Auto-advance to next notification if available
        if (count($this->notifications) > 1) {
            $this->nextNotification();
        }
    }

    public function render(): \Illuminate\Contracts\View\View
    {
        return view('livewire.site.site-navigation');
    }
}
