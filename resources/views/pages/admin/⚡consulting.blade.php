<?php

use App\Models\ConsultingInquiry;
use App\Models\User;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;

new #[Layout('layouts.admin')] #[Title('Consulting Inquiries — ExchoSoft')] class extends Component
{
    use WithPagination;

    public string $search = '';
    public string $filterStatus = '';
    public string $filterType = '';
    public ?int $viewId = null;

    // Edit note/status inline
    public bool $showNote = false;
    public ?int $noteId = null;
    public string $adminNotes = '';
    public string $newStatus = '';

    public function viewInquiry(int $id): void { $this->viewId = $id; }
    public function closeView(): void { $this->viewId = null; }

    public function openNote(int $id): void
    {
        $inq = ConsultingInquiry::findOrFail($id);
        $this->noteId = $id;
        $this->adminNotes = $inq->admin_notes ?? '';
        $this->newStatus = $inq->status;
        $this->showNote = true;
    }

    public function saveNote(): void
    {
        ConsultingInquiry::findOrFail($this->noteId)->update([
            'admin_notes'   => $this->adminNotes,
            'status'        => $this->newStatus,
            'responded_at'  => now(),
        ]);
        $this->showNote = false;
        session()->flash('success', 'Inquiry updated.');
    }

    // ────────────────────────────────────────────────────────────────────────
    #[Computed]
    public function inquiries()
    {
        return ConsultingInquiry::with(['customerUser', 'assignedAdmin'])
            ->when($this->search, fn($q) => $q->where('name', 'like', '%'.$this->search.'%')
                ->orWhere('email', 'like', '%'.$this->search.'%')
                ->orWhere('subject', 'like', '%'.$this->search.'%'))
            ->when($this->filterStatus, fn($q) => $q->where('status', $this->filterStatus))
            ->when($this->filterType, fn($q) => $q->where('inquiry_type', $this->filterType))
            ->latest()
            ->paginate(15);
    }

    #[Computed]
    public function viewInquiry()
    {
        return $this->viewId ? ConsultingInquiry::with(['customerUser'])->find($this->viewId) : null;
    }

    #[Computed]
    public function stats()
    {
        return [
            'total'    => ConsultingInquiry::count(),
            'new'      => ConsultingInquiry::where('status', 'new')->count(),
            'reviewing'=> ConsultingInquiry::where('status', 'reviewing')->count(),
            'accepted' => ConsultingInquiry::where('status', 'accepted')->count(),
        ];
    }
}; ?>

<div>
    <x-slot:heading>Consulting & Gigs</x-slot:heading>

    <div class="space-y-5">

        @if(session('success'))
            <div class="rounded-xl bg-green-50 border border-green-200 px-4 py-3 text-sm text-green-700">{{ session('success') }}</div>
        @endif

        {{-- Stats --}}
        <div class="grid grid-cols-2 gap-4 lg:grid-cols-4">
            <div class="rounded-2xl bg-white p-5 shadow-sm ring-1 ring-slate-100">
                <p class="text-2xl font-bold text-slate-900">{{ $this->stats['total'] }}</p>
                <p class="text-sm text-slate-500">Total Inquiries</p>
            </div>
            <div class="rounded-2xl bg-white p-5 shadow-sm ring-1 ring-slate-100">
                <p class="text-2xl font-bold text-cyan-600">{{ $this->stats['new'] }}</p>
                <p class="text-sm text-slate-500">New</p>
            </div>
            <div class="rounded-2xl bg-white p-5 shadow-sm ring-1 ring-slate-100">
                <p class="text-2xl font-bold text-blue-600">{{ $this->stats['reviewing'] }}</p>
                <p class="text-sm text-slate-500">Reviewing</p>
            </div>
            <div class="rounded-2xl bg-white p-5 shadow-sm ring-1 ring-slate-100">
                <p class="text-2xl font-bold text-green-600">{{ $this->stats['accepted'] }}</p>
                <p class="text-sm text-slate-500">Accepted</p>
            </div>
        </div>

        {{-- Filters --}}
        <div class="flex flex-wrap items-center gap-2">
            <div class="relative">
                <svg class="absolute left-3 top-2.5 h-4 w-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                <input wire:model.live.debounce.300ms="search" type="text" placeholder="Name, email, subject..." class="pl-9 pr-4 py-2 rounded-xl border border-slate-200 text-sm focus:outline-none focus:border-cyan-400 w-52">
            </div>
            <select wire:model.live="filterType" class="rounded-xl border border-slate-200 text-sm px-3 py-2 focus:outline-none focus:border-cyan-400">
                <option value="">All Types</option>
                <option value="consulting">Consulting</option>
                <option value="gig">Gig</option>
                <option value="contract">Contract</option>
                <option value="partnership">Partnership</option>
            </select>
            <select wire:model.live="filterStatus" class="rounded-xl border border-slate-200 text-sm px-3 py-2 focus:outline-none focus:border-cyan-400">
                <option value="">All Status</option>
                <option value="new">New</option>
                <option value="reviewing">Reviewing</option>
                <option value="quoted">Quoted</option>
                <option value="accepted">Accepted</option>
                <option value="declined">Declined</option>
                <option value="completed">Completed</option>
            </select>
        </div>

        {{-- Table --}}
        <div class="rounded-2xl bg-white shadow-sm ring-1 ring-slate-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-100">
                            <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Ref / Contact</th>
                            <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Subject</th>
                            <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Type</th>
                            <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Budget</th>
                            <th class="px-5 py-3 text-center text-xs font-semibold uppercase tracking-wide text-slate-500">Status</th>
                            <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Date</th>
                            <th class="px-5 py-3 text-right text-xs font-semibold uppercase tracking-wide text-slate-500">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($this->inquiries as $inq)
                        @php
                            $colors = ['new'=>'cyan','reviewing'=>'blue','quoted'=>'violet','accepted'=>'green','declined'=>'red','completed'=>'emerald'];
                            $c = $colors[$inq->status] ?? 'slate';
                        @endphp
                        <tr class="hover:bg-slate-50/50 transition-colors">
                            <td class="px-5 py-3">
                                <p class="font-mono text-xs font-semibold text-slate-500">{{ $inq->reference }}</p>
                                <p class="font-medium text-slate-900">{{ $inq->name }}</p>
                                <p class="text-xs text-slate-400">{{ $inq->email }}</p>
                                @if($inq->company)<p class="text-xs text-slate-400">{{ $inq->company }}</p>@endif
                            </td>
                            <td class="px-5 py-3">
                                <p class="font-medium text-slate-900 line-clamp-2">{{ $inq->subject }}</p>
                            </td>
                            <td class="px-5 py-3">
                                <span class="inline-flex items-center rounded-full bg-slate-100 px-2.5 py-0.5 text-xs font-medium text-slate-700 capitalize">{{ $inq->inquiry_type }}</span>
                            </td>
                            <td class="px-5 py-3 text-xs text-slate-600">{{ $inq->budget_range ?? '—' }}</td>
                            <td class="px-5 py-3 text-center">
                                <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-semibold bg-{{ $c }}-100 text-{{ $c }}-700 capitalize">{{ $inq->status }}</span>
                            </td>
                            <td class="px-5 py-3 text-xs text-slate-500">{{ $inq->created_at->format('d M Y') }}</td>
                            <td class="px-5 py-3 text-right">
                                <div class="flex items-center justify-end gap-1">
                                    <button wire:click="viewInquiry({{ $inq->id }})" class="rounded-lg p-1.5 text-slate-400 hover:bg-slate-100 hover:text-slate-700 transition-colors">
                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    </button>
                                    <button wire:click="openNote({{ $inq->id }})" class="rounded-lg p-1.5 text-slate-400 hover:bg-slate-100 hover:text-cyan-600 transition-colors">
                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="7" class="px-5 py-12 text-center text-sm text-slate-400">No inquiries yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($inquiries->hasPages())<div class="border-t border-slate-100 px-5 py-4">{{ $inquiries->links() }}</div>@endif
        </div>
    </div>

    {{-- View Slide-over --}}
    @if($this->viewInquiry)
    <div class="fixed inset-0 z-50 flex">
        <div class="fixed inset-0 bg-slate-900/50" wire:click="closeView"></div>
        <div class="relative ml-auto w-full max-w-lg bg-white shadow-2xl flex flex-col h-full overflow-y-auto">
            <div class="flex items-center justify-between border-b border-slate-100 px-6 py-4 sticky top-0 bg-white z-10">
                <div><h2 class="text-base font-semibold text-slate-900">Inquiry Detail</h2><p class="text-xs font-mono text-slate-400">{{ $this->viewInquiry->reference }}</p></div>
                <button wire:click="closeView" class="rounded-lg p-1.5 text-slate-400 hover:bg-slate-100">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <div class="px-6 py-5 space-y-4">
                <div class="rounded-xl bg-slate-50 p-4">
                    <p class="text-xs font-semibold uppercase text-slate-500 mb-2">Contact</p>
                    <p class="font-semibold text-slate-900">{{ $this->viewInquiry->name }}</p>
                    <p class="text-sm text-slate-500">{{ $this->viewInquiry->email }}</p>
                    @if($this->viewInquiry->phone)<p class="text-sm text-slate-500">{{ $this->viewInquiry->phone }}</p>@endif
                    @if($this->viewInquiry->company)<p class="text-sm text-slate-500">{{ $this->viewInquiry->company }}</p>@endif
                </div>
                <div class="grid grid-cols-2 gap-3 text-sm">
                    <div><p class="text-xs text-slate-500">Type</p><p class="font-medium text-slate-900 capitalize">{{ $this->viewInquiry->inquiry_type }}</p></div>
                    <div><p class="text-xs text-slate-500">Status</p><p class="font-medium text-slate-900 capitalize">{{ $this->viewInquiry->status }}</p></div>
                    <div><p class="text-xs text-slate-500">Budget</p><p class="font-medium text-slate-900">{{ $this->viewInquiry->budget_range ?? '—' }}</p></div>
                    <div><p class="text-xs text-slate-500">Timeline</p><p class="font-medium text-slate-900">{{ $this->viewInquiry->timeline ?? '—' }}</p></div>
                </div>
                <div><p class="text-xs font-semibold uppercase text-slate-500 mb-1">Subject</p><p class="font-semibold text-slate-900">{{ $this->viewInquiry->subject }}</p></div>
                <div><p class="text-xs font-semibold uppercase text-slate-500 mb-1">Description</p><p class="text-sm text-slate-700 bg-slate-50 rounded-xl p-3">{{ $this->viewInquiry->description }}</p></div>
                @if($this->viewInquiry->how_heard)
                <div><p class="text-xs text-slate-500">How they heard about us:</p><p class="text-sm text-slate-700">{{ $this->viewInquiry->how_heard }}</p></div>
                @endif
                @if($this->viewInquiry->admin_notes)
                <div><p class="text-xs font-semibold uppercase text-slate-500 mb-1">Admin Notes</p><p class="text-sm text-slate-700 bg-amber-50 rounded-xl p-3 border border-amber-100">{{ $this->viewInquiry->admin_notes }}</p></div>
                @endif
            </div>
        </div>
    </div>
    @endif

    {{-- Update Note/Status Modal --}}
    @if($showNote)
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="fixed inset-0 bg-slate-900/50" wire:click="$set('showNote', false)"></div>
        <div class="relative w-full max-w-md rounded-2xl bg-white shadow-2xl p-6">
            <h3 class="text-base font-semibold text-slate-900 mb-4">Update Inquiry</h3>
            <div class="space-y-3">
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Status</label>
                    <select wire:model="newStatus" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:outline-none focus:border-cyan-400">
                        <option value="new">New</option>
                        <option value="reviewing">Reviewing</option>
                        <option value="quoted">Quoted</option>
                        <option value="accepted">Accepted</option>
                        <option value="declined">Declined</option>
                        <option value="completed">Completed</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Admin Notes</label>
                    <textarea wire:model="adminNotes" rows="4" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:outline-none focus:border-cyan-400 resize-none"></textarea>
                </div>
            </div>
            <div class="flex gap-3 mt-4">
                <button wire:click="saveNote" class="flex-1 rounded-xl bg-cyan-600 py-2.5 text-sm font-semibold text-white hover:bg-cyan-700 transition-colors">Save</button>
                <button wire:click="$set('showNote', false)" class="flex-1 rounded-xl bg-slate-100 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-200 transition-colors">Cancel</button>
            </div>
        </div>
    </div>
    @endif
</div>
