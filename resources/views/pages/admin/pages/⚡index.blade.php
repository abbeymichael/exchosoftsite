<?php

use App\Models\Page;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Layout('layouts.admin')] #[Title('Pages — ExchoSoft')] class extends Component
{
    public string $search = '';

    public function getPagesProperty()
    {
        return Page::when($this->search, fn($q) => $q->where('title', 'like', '%'.$this->search.'%')
                ->orWhere('key', 'like', '%'.$this->search.'%'))
            ->orderBy('key')
            ->get();
    }

    public function toggleActive(int $id): void
    {
        $page = Page::findOrFail($id);
        $page->update(['is_active' => ! $page->is_active]);
    }
}; ?>

<div>
    <x-slot:heading>Pages</x-slot:heading>

    <div class="space-y-5">

        {{-- Toolbar --}}
        <div class="flex flex-col sm:flex-row sm:items-center gap-3">
            <div class="relative flex-1 max-w-xs">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                <input wire:model.live="search" type="text" placeholder="Search pages…" class="w-full rounded-xl border border-slate-200 pl-9 pr-3 py-2 text-sm focus:outline-none focus:border-cyan-400">
            </div>
        </div>

        {{-- Flash --}}
        @if(session('success'))
            <div class="rounded-xl bg-green-50 border border-green-100 px-4 py-3 text-sm text-green-700">{{ session('success') }}</div>
        @endif

        {{-- Table --}}
        <div class="overflow-hidden rounded-2xl border border-slate-100 bg-white shadow-sm">
            <table class="min-w-full text-sm">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-100">
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Page</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Key</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Banner</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">SEO</th>
                        <th class="px-5 py-3 text-center text-xs font-semibold uppercase tracking-wide text-slate-500">Status</th>
                        <th class="px-5 py-3 text-center text-xs font-semibold uppercase tracking-wide text-slate-500">Versions</th>
                        <th class="px-5 py-3 text-right text-xs font-semibold uppercase tracking-wide text-slate-500">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($this->pages as $page)
                    <tr class="hover:bg-slate-50/50 transition-colors">
                        <td class="px-5 py-3">
                            <p class="font-semibold text-slate-900">{{ $page->title }}</p>
                            @if($page->banner_heading)
                                <p class="text-xs text-slate-400 truncate max-w-xs">{{ $page->banner_heading }}</p>
                            @endif
                        </td>
                        <td class="px-5 py-3">
                            <span class="font-mono text-xs bg-slate-100 text-slate-600 rounded-lg px-2 py-1">{{ $page->key }}</span>
                        </td>
                        <td class="px-5 py-3 text-center">
                            @if($page->banner_image)
                                <span class="inline-flex items-center gap-1 text-xs text-green-600"><svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg> Set</span>
                            @else
                                <span class="text-xs text-slate-300">—</span>
                            @endif
                        </td>
                        <td class="px-5 py-3">
                            <div class="flex gap-1.5 flex-wrap">
                                @if($page->meta_title)   <span class="rounded-full bg-cyan-50 px-2 py-0.5 text-[10px] font-medium text-cyan-700">Meta</span> @endif
                                @if($page->og_image)     <span class="rounded-full bg-purple-50 px-2 py-0.5 text-[10px] font-medium text-purple-700">OG</span> @endif
                                @if($page->twitter_card) <span class="rounded-full bg-sky-50 px-2 py-0.5 text-[10px] font-medium text-sky-700">Twitter</span> @endif
                                @if($page->schema_markup)<span class="rounded-full bg-amber-50 px-2 py-0.5 text-[10px] font-medium text-amber-700">Schema</span> @endif
                                @if(!$page->meta_title && !$page->og_image && !$page->twitter_card && !$page->schema_markup)
                                    <span class="text-xs text-slate-300">None</span>
                                @endif
                            </div>
                        </td>
                        <td class="px-5 py-3 text-center">
                            <button wire:click="toggleActive({{ $page->id }})"
                                    class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-semibold transition-colors
                                           {{ $page->is_active ? 'bg-green-100 text-green-700 hover:bg-green-200' : 'bg-slate-100 text-slate-500 hover:bg-slate-200' }}">
                                {{ $page->is_active ? 'Active' : 'Inactive' }}
                            </button>
                        </td>
                        <td class="px-5 py-3 text-center">
                            <a href="{{ route('admin.pages.versions', $page->key) }}"
                               class="inline-flex items-center gap-1 text-xs text-slate-500 hover:text-slate-700 transition-colors">
                                <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                {{ $page->versions_count ?? $page->versions()->count() }}
                            </a>
                        </td>
                        <td class="px-5 py-3 text-right">
                            <div class="flex items-center justify-end gap-1">
                                <a href="{{ route('admin.pages.edit', $page->key) }}"
                                   class="rounded-lg p-1.5 text-slate-400 hover:bg-slate-100 hover:text-slate-700 transition-colors" title="Edit">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                </a>
                                <a href="{{ route('site.'.$page->key) }}" target="_blank"
                                   class="rounded-lg p-1.5 text-slate-400 hover:bg-slate-100 hover:text-slate-700 transition-colors" title="View live">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="px-5 py-12 text-center text-sm text-slate-400">No pages found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

    </div>
</div>
