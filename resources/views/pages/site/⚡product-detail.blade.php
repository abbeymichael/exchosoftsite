<?php
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use App\Models\Product;

new #[Layout('layouts.site')] #[Title('Deploy — Exchosoft Consult')] class extends Component {
    public Product $product;
    public int $activeGallery = 0;

    public function mount(string $slug): void
    {
        $this->product = Product::where('slug', $slug)
            ->where('is_published', true)
            ->with(['caseStudies', 'whitepapers'])
            ->firstOrFail();
    }

    public function setGallery(int $i): void
    {
        $this->activeGallery = $i;
    }

    public function render()
    {
        return $this->view()->title($this->product->name . ' - Software by Exchosoft Consult');
    }
};
?>

<div
    class="font-syne bg-slate-50 dark:bg-slate-950 text-slate-900 dark:text-slate-100 antialiased selection:bg-[#00b8db]/30 selection:text-[#00b8db]">

    {{-- ── 1. CONVERSION-FOCUSED HERO ENGINE ── --}}

    <header class="relative bg-slate-900 text-white pt-28 pb-20 px-6 md:px-16 overflow-hidden border-b border-slate-800">
        <div
            class="absolute inset-0 bg-[linear-gradient(to_right,#1e293b_1px,transparent_1px),linear-gradient(to_bottom,#1e293b_1px,transparent_1px)] bg-[size:3rem_3rem] opacity-30 pointer-events-none">
        </div>
        <div class="absolute -top-40 -right-40 w-96 h-96 bg-[#00b8db]/10 rounded-full blur-3xl pointer-events-none">
        </div>

        <div class="max-w-7xl mx-auto grid grid-cols-1 lg:grid-cols-12 gap-12 items-center relative z-10">

            <div class="lg:col-span-7 space-y-6">
                <div
                    class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-[#00b8db]/10 border border-[#00b8db]/20 text-[#00b8db] text-xs font-semibold tracking-wider uppercase font-mono">
                    <span class="w-2 h-2 rounded-full bg-[#00b8db] animate-ping"></span>
                    {{ $this->product->platform ?? 'Local Network Node' }} Architecture
                </div>

                <h1 class="text-4xl md:text-5xl lg:text-6xl font-extrabold tracking-tight text-white leading-none">
                    {{ $this->product->name }}
                </h1>

                <p class="text-lg md:text-xl text-slate-300 font-normal leading-relaxed max-w-2xl">
                    {{ $this->product->tagline ?? 'Resilient local operational infrastructure engineered to secure your workflow from network down-times.' }}
                </p>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-4 text-sm text-slate-400">
                    <div class="flex items-center gap-2.5">
                        <span class="material-symbols-outlined text-[#00b8db]">cloud_off</span>
                        <span>100% Operational without Internet</span>
                    </div>
                    <div class="flex items-center gap-2.5">
                        <span class="material-symbols-outlined text-[#00b8db]">bolt</span>
                        <span>Zero Web-Loading Delays</span>
                    </div>
                </div>

                <div class="flex flex-wrap gap-4 pt-4">
                    <a href="#topology-licensing-engine"
                        class="inline-flex items-center gap-2 bg-[#00b8db] hover:bg-[#009cb8] text-white font-semibold text-sm px-8 py-4 rounded-xl shadow-lg shadow-[#00b8db]/10 transition-all transform hover:-translate-y-0.5 no-underline">
                        <span class="material-symbols-outlined text-sm">lan</span> Select Architecture Topology
                    </a>
                    @if ($this->product->demo_url)
                        <a href="{{ $this->product->demo_url }}" target="_blank"
                            class="inline-flex items-center gap-2 border border-slate-700 bg-slate-800/40 text-slate-300 font-medium text-sm px-6 py-4 rounded-xl hover:border-[#00b8db]/40 hover:text-white transition-all no-underline">
                            <span class="material-symbols-outlined text-sm">visibility</span> Launch Interactive Sandbox
                        </a>
                    @endif
                </div>
            </div>

            <div class="lg:col-span-5" x-data="{ active: @entangle('activeGallery') }">
                @php
                    $gallery = collect($this->product->gallery ?? []);
                    if ($this->product->cover_image) {
                        $gallery->prepend($this->product->cover_image);
                    }
                    $gallery = $gallery->unique()->values();
                @endphp

                <div class="bg-slate-950 border border-slate-800 rounded-2xl p-2 shadow-2xl relative">
                    <div class="relative rounded-xl overflow-hidden aspect-[4/3] bg-slate-900">
                        @if ($gallery->count())
                            @foreach ($gallery as $i => $img)
                                <img src="{{ asset(Storage::url($img)) }}" alt="Software layout configuration monitor"
                                    class="absolute inset-0 w-full h-full object-cover transition-opacity duration-300"
                                    :style="active === {{ $i }} ? 'opacity:1' : 'opacity:0'">
                            @endforeach
                        @else
                            <div
                                class="absolute inset-0 flex flex-col items-center justify-center text-slate-700 space-y-2">
                                <span class="material-symbols-outlined text-5xl">settings_remote</span>
                                <span class="text-xs font-mono">System Interface Active</span>
                            </div>
                        @endif
                    </div>

                    @if ($gallery->count() > 1)
                        <div class="flex gap-2 mt-2 px-1 overflow-x-auto no-scrollbar">
                            @foreach ($gallery as $i => $img)
                                <img src="{{ asset(Storage::url($img)) }}" alt="Sub-frame track node"
                                    class="w-14 h-10 rounded-md object-cover cursor-pointer border-2 transition-all shrink-0"
                                    :class="active === {{ $i }} ? 'border-[#00b8db] opacity-100' :
                                        'border-transparent opacity-40'"
                                    @click="active = {{ $i }}; $wire.setGallery({{ $i }})">
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

        </div>
    </header>

    {{-- ── 2. SPLIT LAYOUT ENGINE ── --}}
    <main class="max-w-7xl mx-auto px-6 md:px-16 py-16 grid grid-cols-1 lg:grid-cols-12 gap-12 items-start">

        <div class="lg:col-span-8 space-y-12">

            <section class="space-y-6">
                <h2 class="text-2xl md:text-3xl font-extrabold tracking-tight text-slate-900 dark:text-white">
                    Engineered for Local Infrastructure Challenges
                </h2>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <div
                        class="p-5 bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 rounded-xl space-y-2">
                        <span class="material-symbols-outlined text-[#00b8db]">signal_cellular_nodata</span>
                        <h4 class="font-bold text-slate-900 dark:text-white text-md">No Internet, No Problem</h4>
                        <p class="text-sm text-slate-500 dark:text-slate-400 leading-relaxed">Runs entirely inside your
                            premises. Transactions register immediately even if global fiber lines fail entirely.</p>
                    </div>

                    <div
                        class="p-5 bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 rounded-xl space-y-2">
                        <span class="material-symbols-outlined text-[#00b8db]">speed</span>
                        <h4 class="font-bold text-slate-900 dark:text-white text-md">Sub-Millisecond Response</h4>
                        <p class="text-sm text-slate-500 dark:text-slate-400 leading-relaxed">No spinning loaders or
                            browser page hangs. Instant localized operations keep customer queues moving.</p>
                    </div>
                </div>
            </section>

            @if ($this->product->full_description)
                <section class="pt-8 border-t border-slate-200/60 dark:border-slate-800/60">
                    <div
                        class="prose prose-slate max-w-none dark:prose-invert
            prose-headings:font-bold prose-headings:tracking-tight
            prose-headings:text-slate-900 dark:prose-headings:text-white
            prose-headings:mt-5 prose-headings:mb-1.5
            prose-h1:text-lg prose-h2:text-base prose-h3:text-sm
            prose-h4:text-xs prose-h4:uppercase prose-h4:tracking-wider
            prose-p:text-sm prose-p:leading-relaxed
            prose-p:text-slate-600 dark:prose-p:text-slate-400
            prose-p:mt-0 prose-p:mb-3
            prose-li:text-sm prose-li:text-slate-600
            dark:prose-li:text-slate-400 prose-li:my-0.5
            prose-ul:mt-1.5 prose-ul:mb-3 prose-ul:pl-5
            prose-ol:mt-1.5 prose-ol:mb-3 prose-ol:pl-5
            prose-hr:my-4 prose-hr:border-[#00b8db]/20
            prose-strong:text-slate-900 dark:prose-strong:text-white
            prose-img:rounded-xl prose-img:border prose-img:border-slate-200
            dark:prose-img:border-slate-800 prose-img:shadow-md prose-img:my-4
            [&_ul]:list-none [&_ul>li]:relative [&_ul>li]:pl-4
            [&_ul>li]:before:content-[''] [&_ul>li]:before:absolute
            [&_ul>li]:before:left-0 [&_ul>li]:before:top-[0.45em]
            [&_ul>li]:before:w-1.5 [&_ul>li]:before:h-1.5
            [&_ul>li]:before:rounded-full [&_ul>li]:before:bg-[#00b8db]
            [&_ol]:list-none [&_ol]:counter-reset-[item]
            [&_ol>li]:relative [&_ol>li]:pl-6
            [&_ol>li]:before:content-[counter(item)]
            [&_ol>li]:before:counter-increment-[item]
            [&_ol>li]:before:absolute [&_ol>li]:before:left-0
            [&_ol>li]:before:top-0 [&_ol>li]:before:font-bold
            [&_ol>li]:before:font-mono [&_ol>li]:before:text-[#00b8db]
            [&_ol>li]:before:text-xs">
                        {!! \Illuminate\Support\Str::markdown($this->product->full_description) !!}
                    </div>
                </section>
            @endif

            @php $cases = $this->product->caseStudies()->latest()->take(2)->get(); @endphp
            @if ($cases->count())
                <section class="pt-8 border-t border-slate-200/60 dark:border-slate-800/60 space-y-6">
                    <h3 class="text-xl font-bold tracking-tight text-slate-900 dark:text-white">Active Operational
                        Verifications</h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        @foreach ($cases as $case)
                            <a href="{{ route('site.case-studies.show', $case->slug) }}" wire:navigate
                                class="group p-5 bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 rounded-xl no-underline flex flex-col justify-between hover:border-[#00b8db]/60 transition-all">
                                <div>
                                    <span
                                        class="text-xs font-mono text-[#00b8db] font-semibold uppercase block mb-1">Active
                                        Architecture</span>
                                    <h4
                                        class="font-bold text-slate-900 dark:text-white group-hover:text-[#00b8db] transition-colors line-clamp-1 mb-2">
                                        {{ $case->title }}</h4>
                                    <p
                                        class="text-xs text-slate-500 dark:text-slate-400 line-clamp-2 leading-relaxed mb-4">
                                        {{ $case->summary ?? $case->excerpt }}</p>
                                </div>
                                <span class="text-xs font-semibold text-[#00b8db] flex items-center gap-1">
                                    Review Local Metrics <span
                                        class="material-symbols-outlined text-sm">arrow_forward</span>
                                </span>
                            </a>
                        @endforeach
                    </div>
                </section>
            @endif

            {{-- ── DECOUPLED PRICING AND TOPOLOGY ARCHITECTURE MATRIX ── --}}
            <section id="topology-licensing-engine" class="pt-12 border-t border-slate-200/60 dark:border-slate-800/60"
                x-data="{
                    selectedMode: 'standalone',
                    selectedCycle: null,
                    init() {
                        const cycles = this.availableCycles;
                        this.selectedCycle = cycles.includes('yearly') ? 'yearly' : (cycles[0] ?? 'yearly');
                    },
                    plans: {{ json_encode(
                        $this->product->activePlans()->ordered()->get()->map(
                            fn($p) => [
                                'id' => $p->id,
                                'name' => $p->name,
                                'description' => $p->description,
                                'price' => number_format($p->effective_price, 2),
                                'raw_price' => (float) $p->price,
                                'is_on_sale' => (bool) $p->is_on_sale,
                                'formatted_old_price' => number_format($p->price, 0),
                                'currency' => $p->currency ?? 'GHS',
                                'mode' => $p->form_factor ?? 'standalone',
                                'cycle' => $p->is_lifetime
                                    ? 'lifetime'
                                    : ($p->duration_days <= 31
                                        ? 'monthly'
                                        : ($p->duration_days <= 93
                                            ? 'quarterly'
                                            : 'yearly')),
                                'max_nodes' => $p->max_activations ?? 1,
                                'offline_ttl' => $p->offline_ttl_hours ?? 'Infinite',
                                'billing_label' => $p->billing_label,
                            ],
                        ),
                    ) }},
                    get availableCycles() {
                        const order = ['monthly', 'quarterly', 'yearly', 'lifetime'];
                        const cycles = [...new Set(this.plans.filter(p => p.mode === this.selectedMode).map(p => p.cycle))];
                        return order.filter(c => cycles.includes(c));
                    },
                    get currentPlan() {
                        return this.plans.find(p => p.mode === this.selectedMode && p.cycle === this.selectedCycle) || null;
                    },
                    selectMode(mode) {
                        this.selectedMode = mode;
                        const cycles = this.availableCycles;
                        if (!cycles.includes(this.selectedCycle)) {
                            this.selectedCycle = cycles.includes('yearly') ? 'yearly' : (cycles[0] ?? 'yearly');
                        }
                    }
                }">

                <div class="space-y-6">
                    <div class="space-y-2">
                        <span
                            class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-mono font-bold uppercase bg-[#00b8db]/10 text-[#00b8db] tracking-wider">
                            <span class="material-symbols-outlined text-sm">lan</span> Deployment Architecture
                        </span>
                        <h2 class="text-2xl md:text-3xl font-extrabold tracking-tight text-slate-900 dark:text-white">
                            Choose Your Infrastructure Configuration
                        </h2>
                        <p class="text-sm text-slate-500 dark:text-slate-400 max-w-3xl leading-relaxed">
                            Our architecture executes localized runtime deployments directly on bare-metal systems
                            within your local area network layout. Define how cluster entities organize operational
                            logic to secure physical boundaries from runtime cloud maintenance overheads.
                        </p>
                    </div>

                    {{-- TOPOLOGY FORMS COMPONENT WRAPPER --}}
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">

                        <div @click="selectMode('standalone')"
                            :class="selectedMode === 'standalone' ?
                                'border-[#00b8db] bg-[#00b8db]/5 dark:bg-[#00b8db]/5 ring-1 ring-[#00b8db]/20' :
                                'border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900/40 hover:border-slate-300 dark:hover:border-slate-700'"
                            class="p-5 rounded-xl border cursor-pointer transition-all flex flex-col justify-between relative overflow-hidden select-none">
                            <div>
                                <div class="w-10 h-10 rounded-lg flex items-center justify-center mb-4 transition-all"
                                    :class="selectedMode === 'standalone' ? 'bg-[#00b8db]/20 text-[#00b8db]' :
                                        'bg-slate-100 dark:bg-slate-800 text-slate-400 dark:text-slate-500'">
                                    <span class="material-symbols-outlined text-lg">desktop_windows</span>
                                </div>
                                <h3 class="font-bold text-sm text-slate-900 dark:text-white">Standalone Desktop App</h3>
                                <p class="text-xs text-slate-500 dark:text-slate-400 mt-2 leading-relaxed">
                                    Executes transactions contained entirely inside a singular decoupled hardware
                                    computer framework. Utilizes integrated isolated databases requiring zero client
                                    network nodes.
                                </p>
                            </div>
                            <div
                                class="mt-6 pt-3 border-t border-slate-100 dark:border-slate-800/60 font-mono text-[10px] text-slate-400 uppercase tracking-wider flex items-center gap-1.5">
                                <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span> Isolated Workspace
                                Environment
                            </div>
                        </div>

                        <div @click="selectMode('lan_orchestrated')"
                            :class="selectedMode === 'lan_orchestrated' ?
                                'border-[#00b8db] bg-[#00b8db]/5 dark:bg-[#00b8db]/5 ring-1 ring-[#00b8db]/20' :
                                'border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900/40 hover:border-slate-300 dark:hover:border-slate-700'"
                            class="p-5 rounded-xl border cursor-pointer transition-all flex flex-col justify-between relative overflow-hidden select-none">
                            <div
                                class="absolute top-0 right-0 bg-[#00b8db] text-slate-950 font-mono font-black text-[8px] tracking-widest px-2.5 py-0.5 uppercase rounded-bl shadow-sm">
                                Includes CoreOps</div>
                            <div>
                                <div class="w-10 h-10 rounded-lg flex items-center justify-center mb-4 transition-all"
                                    :class="selectedMode === 'lan_orchestrated' ? 'bg-[#00b8db]/20 text-[#00b8db]' :
                                        'bg-slate-100 dark:bg-slate-800 text-slate-400 dark:text-slate-500'">
                                    <span class="material-symbols-outlined text-lg">hub</span>
                                </div>
                                <h3 class="font-bold text-sm text-slate-900 dark:text-white">LAN Multi-Workstation
                                    Cluster</h3>
                                <p class="text-xs text-slate-500 dark:text-slate-400 mt-2 leading-relaxed">
                                    Bundles an internal CoreOps background orchestration license parameter. Provisions a
                                    targeted workstation node on your LAN as a primary master host engine to sync
                                    transactions locally across active nodes.
                                </p>
                            </div>
                            <div class="mt-6 pt-3 border-t border-slate-100 dark:border-slate-800/60 font-mono text-[10px] uppercase tracking-wider flex items-center gap-1.5"
                                :class="selectedMode === 'lan_orchestrated' ? 'text-[#00b8db]' : 'text-slate-400'">
                                <span class="w-1.5 h-1.5 rounded-full bg-[#00b8db]"></span> Zero Cloud Subscription
                                Dependency
                            </div>
                        </div>

                        <div @click="selectMode('hybrid_cloud')"
                            :class="selectedMode === 'hybrid_cloud' ?
                                'border-[#00b8db] bg-[#00b8db]/5 dark:bg-[#00b8db]/5 ring-1 ring-[#00b8db]/20' :
                                'border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900/40 hover:border-slate-300 dark:hover:border-slate-700'"
                            class="p-5 rounded-xl border cursor-pointer transition-all flex flex-col justify-between relative overflow-hidden select-none">
                            <div>
                                <div class="w-10 h-10 rounded-lg flex items-center justify-center mb-4 transition-all"
                                    :class="selectedMode === 'hybrid_cloud' ? 'bg-[#00b8db]/20 text-[#00b8db]' :
                                        'bg-slate-100 dark:bg-slate-800 text-slate-400 dark:text-slate-500'">
                                    <span class="material-symbols-outlined text-lg">cloud_sync</span>
                                </div>
                                <h3 class="font-bold text-sm text-slate-900 dark:text-white">Hybrid Local + Private
                                    Cloud</h3>
                                <p class="text-xs text-slate-500 dark:text-slate-400 mt-2 leading-relaxed">
                                    Fuses high-velocity local availability parameters with asynchronous encrypted
                                    synchronization relays linking your on-premise infrastructure layout up to an
                                    isolated dedicated private cloud framework.
                                </p>
                            </div>
                            <div
                                class="mt-6 pt-3 border-t border-slate-100 dark:border-slate-800/60 font-mono text-[10px] text-slate-400 uppercase tracking-wider flex items-center gap-1.5">
                                <span class="w-1.5 h-1.5 rounded-full bg-purple-500"></span> Cross-Facility Secure
                                Synced Node
                            </div>
                        </div>

                    </div>

                    {{-- PARAMETER FREQUENCY HORIZONTAL SWITCH SEGMENT --}}
                    {{-- Only cycles that exist for the selected deployment mode are rendered --}}
                    <div class="flex justify-center pt-2" x-show="availableCycles.length > 1">
                        <div
                            class="inline-flex bg-white dark:bg-slate-900 p-1 rounded-xl border border-slate-200 dark:border-slate-800 font-mono text-[11px] select-none shadow-sm">
                            <template x-for="cycle in availableCycles" :key="cycle">
                                <button type="button" @click="selectedCycle = cycle"
                                    :class="selectedCycle === cycle ? 'bg-[#00b8db]/15 text-[#00b8db] font-bold' :
                                        'text-slate-500 dark:text-slate-400 hover:text-[#00b8db]'"
                                    class="px-4 py-2 rounded-lg border-0 transition-all cursor-pointer bg-transparent capitalize"
                                    x-text="cycle === 'lifetime' ? 'Lifetime ⚡' : cycle.charAt(0).toUpperCase() + cycle.slice(1)"></button>
                            </template>
                        </div>
                    </div>

                    {{-- SELECTED SPECIFICATIONS TARGET MANIFEST CARD --}}
                    <div
                        class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-6 relative overflow-hidden shadow-sm">

                        <div x-show="currentPlan" x-transition:enter="transition ease-out duration-150"
                            x-transition:enter-start="opacity-0 scale-98"
                            class="grid grid-cols-1 md:grid-cols-12 gap-6 items-center">

                            {{-- Specific Data Array Elements --}}
                            <div class="md:col-span-7 space-y-4">
                                <div>
                                    <span
                                        class="text-[10px] font-mono font-bold tracking-widest text-slate-400 uppercase block">Topology
                                        Configuration Tier</span>
                                    <h4 class="text-lg font-extrabold text-slate-900 dark:text-white mt-0.5"
                                        x-text="currentPlan ? currentPlan.name : ''"></h4>
                                    <p class="text-xs text-slate-400 dark:text-slate-500 mt-1"
                                        x-text="currentPlan ? currentPlan.description : ''"
                                        x-show="currentPlan && currentPlan.description"></p>
                                </div>

                                <div
                                    class="grid grid-cols-1 sm:grid-cols-2 gap-x-4 gap-y-2.5 pt-1 font-mono text-[11px]">
                                    <div class="flex items-start gap-2 text-slate-600 dark:text-slate-400">
                                        <span
                                            class="material-symbols-outlined text-[#00b8db] text-sm mt-0.5">verified_user</span>
                                        <div>
                                            <span class="text-slate-900 dark:text-white font-bold block">Fulfillment
                                                Layer:</span>
                                            <span
                                                x-text="selectedMode === 'standalone' ? 'Isolated Workspace Binary' : (selectedMode === 'lan_orchestrated' ? 'Master CoreOps Core Node + Client Packs' : 'Hybrid Streaming Edge Pack')"></span>
                                        </div>
                                    </div>
                                    <div class="flex items-start gap-2 text-slate-600 dark:text-slate-400">
                                        <span
                                            class="material-symbols-outlined text-[#00b8db] text-sm mt-0.5">devices</span>
                                        <div>
                                            <span class="text-slate-900 dark:text-white font-bold block">Capacity
                                                Scope:</span>
                                            <span
                                                x-text="selectedMode === 'standalone' ? '1 Isolated Workstation Machine' : 'Up to ' + (currentPlan ? currentPlan.max_nodes : 1) + ' Workstations Concurrent'"></span>
                                        </div>
                                    </div>
                                    <div class="flex items-start gap-2 text-slate-600 dark:text-slate-400">
                                        <span
                                            class="material-symbols-outlined text-[#00b8db] text-sm mt-0.5">wifi_off</span>
                                        <div>
                                            <span class="text-slate-900 dark:text-white font-bold block">Offline
                                                Tolerance TTL:</span>
                                            <span
                                                x-text="currentPlan ? (currentPlan.offline_ttl === 'Infinite' ? 'Infinite Autonomous local availability' : currentPlan.offline_ttl + ' Hours safe validation state') : ''"></span>
                                        </div>
                                    </div>
                                    <div class="flex items-start gap-2 text-slate-600 dark:text-slate-400">
                                        <span
                                            class="material-symbols-outlined text-[#00b8db] text-sm mt-0.5">dns</span>
                                        <div>
                                            <span class="text-slate-900 dark:text-white font-bold block">Database
                                                Sub-System:</span>
                                            <span
                                                x-text="selectedMode === 'standalone' ? 'Embedded SQLite Core Engine' : 'Centralized Local PostgreSQL Ledger Server'"></span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Financial Interactivity Targets --}}
                            <div
                                class="md:col-span-5 text-center md:text-right md:border-l border-slate-100 dark:border-slate-800 md:pl-6 space-y-4">
                                <div>
                                    <div class="text-slate-400 text-xs font-mono">Licensing Deployment Rate</div>
                                    <div class="my-1 flex items-baseline justify-center md:justify-end gap-1.5">
                                        <span class="text-slate-400 font-mono text-xs font-semibold"
                                            x-text="currentPlan ? currentPlan.currency : 'GHS'"></span>
                                        <span
                                            class="text-3xl md:text-4xl font-black font-mono tracking-tight text-[#00b8db]"
                                            x-text="currentPlan ? currentPlan.price : '0.00'"></span>
                                    </div>
                                    <div class="flex items-center justify-center md:justify-end gap-1.5 text-[11px] font-mono"
                                        x-show="currentPlan && currentPlan.is_on_sale">
                                        <span class="text-slate-400 dark:text-slate-500 line-through"
                                            x-text="currentPlan ? currentPlan.currency + ' ' + currentPlan.formatted_old_price : ''"></span>
                                        <span
                                            class="text-emerald-500 font-bold uppercase tracking-wider text-[9px] px-1.5 py-0.5 rounded bg-emerald-500/10">Save</span>
                                    </div>
                                    <div class="text-slate-400 dark:text-slate-500 text-[10px] font-mono uppercase tracking-widest mt-1.5"
                                        x-text="currentPlan ? currentPlan.billing_label : ''"></div>
                                </div>

                                <button type="button"
                                    @click="window.location.href='/checkout?plan=' + currentPlan.id"
                                    class="w-full inline-flex items-center justify-center gap-2 px-5 py-3.5 bg-[#00b8db] hover:bg-[#009cb8] text-white text-xs font-bold font-sans uppercase tracking-wider rounded-xl transition-all shadow-md shadow-[#00b8db]/10 cursor-pointer border-0">
                                    <span class="material-symbols-outlined text-base">shopping_cart</span> Provision
                                    Software Key
                                </button>
                            </div>

                        </div>

                        {{-- Fallback Segment Triggered on Missing Combination Metrics --}}
                        <div x-show="!currentPlan" x-transition class="text-center py-4 space-y-2.5">
                            <span class="material-symbols-outlined text-amber-500 text-2xl">schema</span>
                            <h4 class="text-sm font-bold text-slate-900 dark:text-white">Custom Cross-Facility
                                Blueprint Required</h4>
                            <p class="text-xs text-slate-500 dark:text-slate-400 max-w-md mx-auto leading-relaxed">
                                This designated framework mode is allocated explicitly via custom operations
                                infrastructure scopes. Contact our architectural routing channel to map this deployment
                                profile for your enterprise nodes.
                            </p>
                            <div class="pt-1">
                                <a href="{{ route('site.contact', ['intent' => 'custom_topology', 'product_context' => $this->product->slug]) }}"
                                    class="inline-flex text-xs font-bold text-[#00b8db] hover:underline no-underline items-center gap-1">
                                    Route to Architecture Operations Desk <span
                                        class="material-symbols-outlined text-xs">arrow_forward</span>
                                </a>
                            </div>
                        </div>

                    </div>
                </div>
            </section>
            {{-- ── END OF DECOUPLED PRICING MATRIX ── --}}

        </div>

        <aside class="lg:col-span-4 sticky top-24 space-y-6">

            <div
                class="bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 rounded-2xl p-5 space-y-4 shadow-sm">
                <h5 class="text-xs font-bold text-slate-400 uppercase tracking-widest">Network Topology Parameters</h5>
                <dl class="text-xs space-y-3 font-mono">
                    <div
                        class="flex justify-between items-center pb-2.5 border-b border-slate-100 dark:border-slate-800/60">
                        <dt class="text-slate-400 font-sans">Core Base Engine</dt>
                        <dd class="text-slate-900 dark:text-slate-200 font-bold">
                            v{{ $this->product->current_version ?? '1.0' }}</dd>
                    </div>
                    <div
                        class="flex justify-between items-center pb-2.5 border-b border-slate-100 dark:border-slate-800/60">
                        <dt class="text-slate-400 font-sans">Cryptographic Layer</dt>
                        <dd class="text-slate-900 dark:text-slate-200 font-bold">AES-256 Ledger</dd>
                    </div>
                    <div class="flex justify-between items-center">
                        <dt class="text-slate-400 font-sans">Asset Ownership Scope</dt>
                        <dd class="text-slate-900 dark:text-slate-200 font-bold">Sovereign Store</dd>
                    </div>
                </dl>
            </div>

            <div
                class="p-5 bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 rounded-2xl space-y-3 shadow-sm">
                <h4 class="text-xs font-bold flex items-center gap-1.5 text-slate-900 dark:text-white">
                    <span class="material-symbols-outlined text-[#00b8db] text-lg">schema</span> Need a Custom
                    Deployment?
                </h4>
                <p class="text-[11px] text-slate-500 dark:text-slate-400 leading-relaxed">
                    If your infrastructure requires a non-standard topology, custom node counts, or a bespoke licensing
                    arrangement, our architecture team will map it out for you.
                </p>
                <div class="pt-1">
                    <a href="{{ route('site.contact', ['intent' => 'custom_topology', 'product_context' => $this->product->slug]) }}"
                        class="inline-flex items-center gap-1 text-xs font-bold text-[#00b8db] hover:text-[#009cb8] no-underline">
                        Route to Architecture Operations Desk <span
                            class="material-symbols-outlined text-xs">arrow_forward</span>
                    </a>
                </div>
            </div>

            <div
                class="p-5 bg-gradient-to-br from-slate-900 to-slate-950 text-white rounded-2xl space-y-3 relative overflow-hidden border border-slate-800 shadow-sm">
                <div
                    class="absolute -right-8 -bottom-8 w-24 h-24 bg-[#00b8db]/10 rounded-full blur-xl pointer-events-none">
                </div>
                <h4 class="text-sm font-bold flex items-center gap-1.5">
                    <span class="material-symbols-outlined text-[#00b8db] text-lg">corporate_fare</span> Need
                    Enterprise Integration?
                </h4>
                <p class="text-[11px] text-slate-400 leading-relaxed">
                    Looking to integrate custom hardware nodes, complex cluster relays, or arrange specific
                    service-level agreements for enterprise networks in West African centers? Our team handles direct
                    infrastructure configurations.
                </p>
                <div class="pt-1">
                    <a href="{{ route('site.contact', ['intent' => 'enterprise_consult', 'product_context' => $this->product->slug]) }}"
                        wire:navigate
                        class="inline-flex items-center gap-1 text-xs font-bold text-[#00b8db] hover:text-[#009cb8] no-underline">
                        Initiate Operations Route <span class="material-symbols-outlined text-xs">arrow_forward</span>
                    </a>
                </div>
            </div>

        </aside>
    </main>

</div>
