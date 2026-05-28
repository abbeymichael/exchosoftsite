<?php

use App\Models\DemoBooking;
use App\Models\ShopProduct;
use App\Models\User;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;

new #[Layout('layouts.admin')] #[Title('Demo Bookings — ExchoSoft')] class extends Component
{
    use WithPagination;

    public string $search = '';
    public string $filterStatus = '';
    public ?int $viewId = null;

    // Confirm form
    public bool $showConfirm = false;
    public ?int $confirmId = null;
    public string $confirmedDate = '';
    public string $confirmedTime = '';
    public string $meetingLink = '';
    public string $adminNotes = '';

    public function viewBooking(int $id): void { $this->viewId = $id; }
    public function closeView(): void { $this->viewId = null; }

    public function openConfirm(int $id): void
    {
        $this->confirmId = $id;
        $booking = DemoBooking::findOrFail($id);
        $this->confirmedDate = $booking->preferred_date->format('Y-m-d');
        $this->confirmedTime = $booking->preferred_time ?? '';
        $this->meetingLink = $booking->meeting_link ?? '';
        $this->adminNotes = $booking->admin_notes ?? '';
        $this->showConfirm = true;
    }

    public function confirmBooking(): void
    {
        $this->validate([
            'confirmedDate' => 'required|date',
            'confirmedTime' => 'required|string',
        ]);

        DemoBooking::findOrFail($this->confirmId)->update([
            'status'         => 'confirmed',
            'confirmed_at'   => now(),
            'confirmed_date' => $this->confirmedDate,
            'confirmed_time' => $this->confirmedTime,
            'meeting_link'   => $this->meetingLink,
            'admin_notes'    => $this->adminNotes,
        ]);

        $this->showConfirm = false;
        $this->confirmId = null;
        session()->flash('success', 'Demo booking confirmed!');
    }

    public function updateStatus(int $id, string $status): void
    {
        DemoBooking::findOrFail($id)->update(['status' => $status]);
        session()->flash('success', 'Status updated.');
    }

    // ────────────────────────────────────────────────────────────────────────
    #[Computed]
    public function bookings()
    {
        return DemoBooking::with(['shopProduct', 'customerUser', 'assignedAdmin'])
            ->when($this->search, fn($q) => $q->where('name', 'like', '%'.$this->search.'%')
                ->orWhere('email', 'like', '%'.$this->search.'%')
                ->orWhere('reference', 'like', '%'.$this->search.'%'))
            ->when($this->filterStatus, fn($q) => $q->where('status', $this->filterStatus))
            ->latest()
            ->paginate(15);
    }

    #[Computed]
    public function viewBooking()
    {
        return $this->viewId ? DemoBooking::with(['shopProduct', 'customerUser'])->find($this->viewId) : null;
    }

    #[Computed]
    public function stats()
    {
        return [
            'total'    => DemoBooking::count(),
            'pending'  => DemoBooking::where('status', 'pending')->count(),
            'upcoming' => DemoBooking::upcoming()->count(),
            'completed'=> DemoBooking::where('status', 'completed')->count(),
        ];
    }
}; ?>

<div>
    <x-slot:heading>Demo Bookings</x-slot:heading>

    <div class="space-y-5">

        @if(session('success'))
            <div class="rounded-xl bg-green-50 border border-green-200 px-4 py-3 text-sm text-green-700">{{ session('success') }}</div>
        @endif

        {{-- Stats --}}
        <div class="grid grid-cols-2 gap-4 lg:grid-cols-4">
            <div class="rounded-2xl bg-white p-5 shadow-sm ring-1 ring-slate-100">
                <p class="text-2xl font-bold text-slate-900">{{ $this->stats['total'] }}</p>
                <p class="text-sm text-slate-500">Total Bookings</p>
            </div>
            <div class="rounded-2xl bg-white p-5 shadow-sm ring-1 ring-slate-100">
                <p class="text-2xl font-bold text-amber-600">{{ $this->stats['pending'] }}</p>
                <p class="text-sm text-slate-500">Pending Review</p>
            </div>
            <div class="rounded-2xl bg-white p-5 shadow-sm ring-1 ring-slate-100">
                <p class="text-2xl font-bold text-cyan-600">{{ $this->stats['upcoming'] }}</p>
                <p class="text-sm text-slate-500">Upcoming Demos</p>
            </div>
            <div class="rounded-2xl bg-white p-5 shadow-sm ring-1 ring-slate-100">
                <p class="text-2xl font-bold text-green-600">{{ $this->stats['completed'] }}</p>
                <p class="text-sm text-slate-500">Completed</p>
            </div>
        </div>

        {{-- Filters --}}
        <div class="flex flex-wrap items-center gap-2">
            <div class="relative">
                <svg class="absolute left-3 top-2.5 h-4 w-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                <input wire:model.live.debounce.300ms="search" type="text" placeholder="Name, email or ref..." class="pl-9 pr-4 py-2 rounded-xl border border-slate-200 text-sm focus:outline-none focus:border-cyan-400 w-52">
            </div>
            <select wire:model.live="filterStatus" class="rounded-xl border border-slate-200 text-sm px-3 py-2 focus:outline-none focus:border-cyan-400">
                <option value="">All Status</option>
                <option value="pending">Pending</option>
                <option value="confirmed">Confirmed</option>
                <option value="rescheduled">Rescheduled</option>
                <option value="completed">Completed</option>
                <option value="cancelled">Cancelled</option>
                <option value="no_show">No Show</option>
            </select>
        </div>

        {{-- Table --}}
        <div class="rounded-2xl bg-white shadow-sm ring-1 ring-slate-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-100">
                            <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Ref / Contact</th>
                            <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Product</th>
                            <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Preferred Date</th>
                            <th class="px-5 py-3 text-center text-xs font-semibold uppercase tracking-wide text-slate-500">Type</th>
                            <th class="px-5 py-3 text-center text-xs font-semibold uppercase tracking-wide text-slate-500">Status</th>
                            <th class="px-5 py-3 text-right text-xs font-semibold uppercase tracking-wide text-slate-500">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($this->bookings as $booking)
                        @php
                            $colors = ['pending'=>'amber','confirmed'=>'green','rescheduled'=>'blue','completed'=>'emerald','cancelled'=>'red','no_show'=>'slate'];
                            $c = $colors[$booking->status] ?? 'slate';
                        @endphp
                        <tr class="hover:bg-slate-50/50 transition-colors">
                            <td class="px-5 py-3">
                                <p class="font-mono text-xs font-semibold text-slate-500">{{ $booking->reference }}</p>
                                <p class="font-medium text-slate-900">{{ $booking->name }}</p>
                                <p class="text-xs text-slate-400">{{ $booking->email }}</p>
                                @if($booking->company) <p class="text-xs text-slate-400">{{ $booking->company }}</p> @endif
                            </td>
                            <td class="px-5 py-3">
                                <p class="text-slate-700">{{ $booking->product_name ?? $booking->shopProduct?->name ?? '—' }}</p>
                            </td>
                            <td class="px-5 py-3">
                                <p class="font-medium text-slate-900">{{ $booking->preferred_date->format('d M Y') }}</p>
                                @if($booking->preferred_time) <p class="text-xs text-slate-400">{{ $booking->preferred_time }}</p> @endif
                            </td>
                            <td class="px-5 py-3 text-center">
                                <span class="inline-flex items-center rounded-full bg-slate-100 px-2.5 py-0.5 text-xs font-medium text-slate-700 capitalize">{{ $booking->demo_type }}</span>
                            </td>
                            <td class="px-5 py-3 text-center">
                                <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-semibold bg-{{ $c }}-100 text-{{ $c }}-700 capitalize">{{ $booking->status }}</span>
                            </td>
                            <td class="px-5 py-3 text-right">
                                <div class="flex items-center justify-end gap-1">
                                    @if($booking->status === 'pending')
                                    <button wire:click="openConfirm({{ $booking->id }})" class="rounded-lg px-2 py-1 text-xs font-medium text-green-600 bg-green-50 hover:bg-green-100 transition-colors">Confirm</button>
                                    @endif
                                    <button wire:click="viewBooking({{ $booking->id }})" class="rounded-lg p-1.5 text-slate-400 hover:bg-slate-100 hover:text-slate-700 transition-colors">
                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    </button>
                                    <div x-data="{ open: false }" class="relative">
                                        <button @click="open = !open" class="rounded-lg p-1.5 text-slate-400 hover:bg-slate-100 transition-colors">
                                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"/></svg>
                                        </button>
                                        <div x-show="open" @click.away="open=false" class="absolute right-0 mt-1 w-36 rounded-xl bg-white shadow-lg ring-1 ring-slate-100 z-20 py-1">
                                            <button wire:click="updateStatus({{ $booking->id }}, 'completed')" @click="open=false" class="w-full text-left px-3 py-1.5 text-xs text-slate-600 hover:bg-slate-50">Mark Completed</button>
                                            <button wire:click="updateStatus({{ $booking->id }}, 'no_show')" @click="open=false" class="w-full text-left px-3 py-1.5 text-xs text-slate-600 hover:bg-slate-50">Mark No Show</button>
                                            <button wire:click="updateStatus({{ $booking->id }}, 'cancelled')" @click="open=false" class="w-full text-left px-3 py-1.5 text-xs text-red-600 hover:bg-red-50">Cancel</button>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-5 py-12 text-center text-sm text-slate-400">No demo bookings yet.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($bookings->hasPages())
                <div class="border-t border-slate-100 px-5 py-4">{{ $bookings->links() }}</div>
            @endif
        </div>
    </div>

    {{-- View Slide-over --}}
    @if($this->viewBooking)
    <div class="fixed inset-0 z-50 flex">
        <div class="fixed inset-0 bg-slate-900/50" wire:click="closeView"></div>
        <div class="relative ml-auto w-full max-w-lg bg-white shadow-2xl flex flex-col h-full overflow-y-auto">
            <div class="flex items-center justify-between border-b border-slate-100 px-6 py-4 sticky top-0 bg-white z-10">
                <div><h2 class="text-base font-semibold text-slate-900">Booking Detail</h2><p class="text-xs font-mono text-slate-400">{{ $this->viewBooking->reference }}</p></div>
                <button wire:click="closeView" class="rounded-lg p-1.5 text-slate-400 hover:bg-slate-100">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <div class="px-6 py-5 space-y-4">
                <div class="rounded-xl bg-slate-50 p-4 space-y-1">
                    <p class="text-xs font-semibold uppercase text-slate-500 mb-2">Contact</p>
                    <p class="font-semibold text-slate-900">{{ $this->viewBooking->name }}</p>
                    <p class="text-sm text-slate-500">{{ $this->viewBooking->email }}</p>
                    @if($this->viewBooking->phone)<p class="text-sm text-slate-500">{{ $this->viewBooking->phone }}</p>@endif
                    @if($this->viewBooking->company)<p class="text-sm text-slate-500">{{ $this->viewBooking->company }}</p>@endif
                    @if($this->viewBooking->job_title)<p class="text-xs text-slate-400">{{ $this->viewBooking->job_title }}</p>@endif
                </div>
                <div class="grid grid-cols-2 gap-3 text-sm">
                    <div><p class="text-xs text-slate-500">Product</p><p class="font-medium text-slate-900">{{ $this->viewBooking->product_name ?? $this->viewBooking->shopProduct?->name ?? '—' }}</p></div>
                    <div><p class="text-xs text-slate-500">Demo Type</p><p class="font-medium text-slate-900 capitalize">{{ $this->viewBooking->demo_type }}</p></div>
                    <div><p class="text-xs text-slate-500">Preferred Date</p><p class="font-medium text-slate-900">{{ $this->viewBooking->preferred_date->format('d M Y') }}</p></div>
                    <div><p class="text-xs text-slate-500">Preferred Time</p><p class="font-medium text-slate-900">{{ $this->viewBooking->preferred_time ?? '—' }}</p></div>
                    <div><p class="text-xs text-slate-500">Attendees</p><p class="font-medium text-slate-900">{{ $this->viewBooking->attendees }}</p></div>
                    <div><p class="text-xs text-slate-500">Status</p><p class="font-medium text-slate-900 capitalize">{{ $this->viewBooking->status }}</p></div>
                </div>
                @if($this->viewBooking->requirements)
                <div><p class="text-xs font-semibold uppercase text-slate-500 mb-1">Requirements</p><p class="text-sm text-slate-700 bg-slate-50 rounded-xl p-3">{{ $this->viewBooking->requirements }}</p></div>
                @endif
                @if($this->viewBooking->message)
                <div><p class="text-xs font-semibold uppercase text-slate-500 mb-1">Message</p><p class="text-sm text-slate-700 bg-slate-50 rounded-xl p-3">{{ $this->viewBooking->message }}</p></div>
                @endif
                @if($this->viewBooking->confirmed_date)
                <div class="rounded-xl bg-green-50 border border-green-200 p-4">
                    <p class="text-xs font-semibold uppercase text-green-600 mb-2">Confirmed Details</p>
                    <p class="text-sm text-slate-700">Date: {{ $this->viewBooking->confirmed_date->format('d M Y') }} at {{ $this->viewBooking->confirmed_time }}</p>
                    @if($this->viewBooking->meeting_link)<a href="{{ $this->viewBooking->meeting_link }}" target="_blank" class="text-sm text-cyan-600 hover:underline">{{ $this->viewBooking->meeting_link }}</a>@endif
                </div>
                @endif
                @if($this->viewBooking->admin_notes)
                <div><p class="text-xs font-semibold uppercase text-slate-500 mb-1">Admin Notes</p><p class="text-sm text-slate-700 bg-slate-50 rounded-xl p-3">{{ $this->viewBooking->admin_notes }}</p></div>
                @endif
            </div>
        </div>
    </div>
    @endif

    {{-- Confirm Modal --}}
    @if($showConfirm)
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="fixed inset-0 bg-slate-900/50" wire:click="$set('showConfirm', false)"></div>
        <div class="relative w-full max-w-md rounded-2xl bg-white shadow-2xl p-6">
            <h3 class="text-base font-semibold text-slate-900 mb-4">Confirm Demo Booking</h3>
            <div class="space-y-3">
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Confirmed Date *</label>
                    <input wire:model="confirmedDate" type="date" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:outline-none focus:border-cyan-400">
                    @error('confirmedDate') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Confirmed Time *</label>
                    <input wire:model="confirmedTime" type="text" placeholder="e.g. 10:00 AM" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:outline-none focus:border-cyan-400">
                    @error('confirmedTime') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Meeting Link</label>
                    <input wire:model="meetingLink" type="url" placeholder="https://meet.google.com/..." class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:outline-none focus:border-cyan-400">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Admin Notes</label>
                    <textarea wire:model="adminNotes" rows="2" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:outline-none focus:border-cyan-400 resize-none"></textarea>
                </div>
            </div>
            <div class="flex gap-3 mt-4">
                <button wire:click="confirmBooking" class="flex-1 rounded-xl bg-green-600 py-2.5 text-sm font-semibold text-white hover:bg-green-700 transition-colors">Confirm Demo</button>
                <button wire:click="$set('showConfirm', false)" class="flex-1 rounded-xl bg-slate-100 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-200 transition-colors">Cancel</button>
            </div>
        </div>
    </div>
    @endif
</div>
