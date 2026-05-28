<?php

use App\Models\ConsultingInquiry;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Layout('layouts.site')] #[Title('Consulting & Services — Exchosoft Consult')] class extends Component {
    public string $name = '';
    public string $email = '';
    public string $phone = '';
    public string $company = '';
    public string $inquiry_type = 'consulting';
    public string $subject = '';
    public string $description = '';
    public string $budget_range = '';
    public string $timeline = '';
    public string $how_heard = '';
    public bool $submitted = false;

    public function mount(): void
    {
        if (auth()->check()) {
            $this->name = auth()->user()->name;
            $this->email = auth()->user()->email;
        }
    }

    public function submit(): void
    {
        $this->validate([
            'name' => 'required|string|max:200',
            'email' => 'required|email',
            'subject' => 'required|string|max:300',
            'description' => 'required|string|min:30',
        ]);

        ConsultingInquiry::create([
            'customer_user_id' => auth()->id(),
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'company' => $this->company,
            'inquiry_type' => $this->inquiry_type,
            'subject' => $this->subject,
            'description' => $this->description,
            'budget_range' => $this->budget_range,
            'timeline' => $this->timeline,
            'how_heard' => $this->how_heard,
        ]);

        $this->submitted = true;
    }

}; ?>

<div>
    <style>
        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
        }

        .radar-beam {
            background: conic-gradient(from 0deg at 50% 50%, rgba(76, 217, 253, 0.4) 0deg, transparent 90deg);
            animation: rotate 6s linear infinite;
        }

        @keyframes rotate {
            from {
                transform: rotate(0deg);
            }

            to {
                transform: rotate(360deg);
            }
        }

        .grid-pattern {
            background-image: radial-gradient(circle, #b1ecff 1px, transparent 1px);
            background-size: 32px 32px;
            opacity: 0.05;
        }

        .glass-card {
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(177, 236, 255, 0.3);
        }
    </style>

        <section class="relative bg-primary overflow-hidden min-h-[600px] flex items-center">
            <div class="absolute inset-0 grid-pattern opacity-10"></div>
            <!-- Radar Animation -->
            <div class="absolute right-0 top-1/2 -translate-y-1/2 w-[600px] h-[600px] hidden lg:block opacity-40">
                <div class="absolute inset-0 border border-secondary-container/20 rounded-full"></div>
                <div class="absolute inset-[100px] border border-secondary-container/20 rounded-full"></div>
                <div class="absolute inset-[200px] border border-secondary-container/20 rounded-full"></div>
                <div class="radar-beam absolute inset-0 rounded-full"></div>
                <!-- Radar Icons -->
                <div class="absolute top-[15%] left-[50%] -translate-x-1/2 flex flex-col items-center">
                    <span class="material-symbols-outlined text-secondary-container text-4xl mb-2"
                        data-icon="insights">insights</span>
                    <span class="text-secondary-fixed text-[10px] uppercase tracking-widest font-bold">Strategy</span>
                </div>
                <div class="absolute bottom-[15%] left-[50%] -translate-x-1/2 flex flex-col items-center">
                    <span class="material-symbols-outlined text-secondary-container text-4xl mb-2"
                        data-icon="code">code</span>
                    <span class="text-secondary-fixed text-[10px] uppercase tracking-widest font-bold">Dev</span>
                </div>
                <div class="absolute top-[50%] left-[15%] -translate-y-1/2 flex flex-col items-center">
                    <span class="material-symbols-outlined text-secondary-container text-4xl mb-2"
                        data-icon="analytics">analytics</span>
                    <span class="text-secondary-fixed text-[10px] uppercase tracking-widest font-bold">Analysis</span>
                </div>
                <div class="absolute top-[50%] right-[15%] -translate-y-1/2 flex flex-col items-center">
                    <span class="material-symbols-outlined text-secondary-container text-4xl mb-2"
                        data-icon="cloud_done">cloud_done</span>
                    <span class="text-secondary-fixed text-[10px] uppercase tracking-widest font-bold">Cloud</span>
                </div>
            </div>
            <div class="relative z-10 px-margin-mobile md:px-margin-desktop max-w-container-max mx-auto w-full">
                <div class="flex items-center space-x-2 text-secondary-fixed font-label-md mb-8">
                    <span>Home</span>
                    <span class="material-symbols-outlined text-sm">chevron_right</span>
                    <span class="text-on-primary">Consulting &amp; Services</span>
                </div>
                <div class="max-w-3xl">
                    <h1
                        class="font-display-lg-mobile md:font-display-lg text-display-lg-mobile md:text-display-lg text-on-primary mb-6 leading-tight">
                        Technology Consulting <br /><span class="text-secondary-container">Built on Real
                            Experience</span>
                    </h1>
                    <p class="font-body-lg text-body-lg text-on-primary/80 mb-10 leading-relaxed">
                        Strategic guidance and custom development for businesses that need technology that actually
                        works —
                        not theory, not templates, not compromises.
                    </p>
                    <div class="flex flex-wrap gap-4">
                        <button
                            class="bg-secondary-container text-on-secondary-fixed px-8 py-4 font-label-md font-bold hover:opacity-90 transition-all flex items-center gap-2">
                            Initialize Project <span class="material-symbols-outlined">arrow_forward</span>
                        </button>
                    </div>
                </div>
            </div>
        </section>
        <!-- Advisory & Development Services Section -->
        <section class="py-24 px-margin-mobile md:px-margin-desktop max-w-container-max mx-auto">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-gutter mb-20">
                <div class="lg:col-span-5">
                    <h2 class="font-headline-xl text-headline-xl text-primary mb-6">What We Offer: Advisory &amp;
                        Development Services</h2>
                </div>
                <div class="lg:col-span-7">
                    <p class="font-body-lg text-body-lg text-on-surface-variant max-w-2xl">
                        We offer technology consulting grounded in real-world experience across Africa, the Caribbean,
                        and
                        the diaspora. Whether you need strategic guidance, a bespoke system, or a technical partner who
                        understands your market — we're built for that.
                    </p>
                </div>
            </div>
            <div
                class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-px bg-outline-variant/30 border border-outline-variant/30">
                <!-- 01 -->
                <div class="bg-background p-12 group hover:bg-surface-container transition-colors duration-500">
                    <div class="text-secondary-container font-headline-md mb-8">01.</div>
                    <h3
                        class="font-headline-md text-headline-md mb-4 text-primary group-hover:text-secondary transition-colors">
                        Custom Software Development</h3>
                    <p class="text-on-surface-variant font-body-md">Built from scratch for your specific operations. We
                        bypass generic templates to create architectural solutions that solve your unique pain points.
                    </p>
                </div>
                <!-- 02 -->
                <div class="bg-background p-12 group hover:bg-surface-container transition-colors duration-500">
                    <div class="text-secondary-container font-headline-md mb-8">02.</div>
                    <h3
                        class="font-headline-md text-headline-md mb-4 text-primary group-hover:text-secondary transition-colors">
                        Technology Strategy &amp; Consulting</h3>
                    <p class="text-on-surface-variant font-body-md">Objective guidance on investments and system
                        choices. We
                        help you navigate the landscape to choose tools that scale with your growth.</p>
                </div>
                <!-- 03 -->
                <div class="bg-background p-12 group hover:bg-surface-container transition-colors duration-500">
                    <div class="text-secondary-container font-headline-md mb-8">03.</div>
                    <h3
                        class="font-headline-md text-headline-md mb-4 text-primary group-hover:text-secondary transition-colors">
                        System Architecture &amp; Design</h3>
                    <p class="text-on-surface-variant font-body-md">Offline-first, cloud-connected, LAN-collaborative
                        designs. We architect for resilience in every infrastructure environment.</p>
                </div>
                <!-- 04 -->
                <div class="bg-background p-12 group hover:bg-surface-container transition-colors duration-500">
                    <div class="text-secondary-container font-headline-md mb-8">04.</div>
                    <h3
                        class="font-headline-md text-headline-md mb-4 text-primary group-hover:text-secondary transition-colors">
                        Business Process Analysis</h3>
                    <p class="text-on-surface-variant font-body-md">Map workflows and identify inefficiencies. We look
                        at
                        the humans behind the screens to ensure tech supports the actual process.</p>
                </div>
                <!-- 05 -->
                <div class="bg-background p-12 group hover:bg-surface-container transition-colors duration-500">
                    <div class="text-secondary-container font-headline-md mb-8">05.</div>
                    <h3
                        class="font-headline-md text-headline-md mb-4 text-primary group-hover:text-secondary transition-colors">
                        Digital Transformation</h3>
                    <p class="text-on-surface-variant font-body-md">Operational modernization respecting your context.
                        We
                        transition your legacy processes into high-performance digital ecosystems.</p>
                </div>
                <!-- 06 -->
                <div class="bg-background p-12 group hover:bg-surface-container transition-colors duration-500">
                    <div class="text-secondary-container font-headline-md mb-8">06.</div>
                    <h3
                        class="font-headline-md text-headline-md mb-4 text-primary group-hover:text-secondary transition-colors">
                        Ongoing Support &amp; Partnership</h3>
                    <p class="text-on-surface-variant font-body-md">Evolution-focused partnership. We don't just ship
                        and
                        disappear; we maintain, optimize, and iterate as your business matures.</p>
                </div>
            </div>
        </section>
        <!-- Our Expertise Section -->
        <section class="py-24 bg-surface-container-low">
            <div class="px-margin-mobile md:px-margin-desktop max-w-container-max mx-auto">
                <div class="mb-16 flex flex-col md:flex-row md:items-end justify-between gap-6">
                    <div>
                        <span class="text-secondary font-label-md tracking-widest uppercase mb-4 block">Proven Vertical
                            Success</span>
                        <h2 class="font-headline-xl text-headline-xl text-primary">Our Expertise: Areas of Deep
                            Experience
                        </h2>
                    </div>
                    <div class="h-px bg-outline-variant flex-grow mx-8 hidden lg:block"></div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-gutter">
                    <!-- Healthcare -->
                    <div class="glass-card p-8 rounded-xl hover:-translate-y-2 transition-transform duration-300">
                        <div class="w-12 h-12 rounded-lg bg-primary-container flex items-center justify-center mb-6">
                            <span class="material-symbols-outlined text-secondary-container"
                                data-icon="local_hospital">local_hospital</span>
                        </div>
                        <h4 class="font-headline-md text-headline-md text-primary mb-3">Healthcare Systems</h4>
                        <p class="text-on-surface-variant font-body-md">Hospital management, pharmacy ops, and secure,
                            HIPAA-compliant data storage designed for clinical efficiency.</p>
                    </div>
                    <!-- Faith Organizations -->
                    <div class="glass-card p-8 rounded-xl hover:-translate-y-2 transition-transform duration-300">
                        <div class="w-12 h-12 rounded-lg bg-primary-container flex items-center justify-center mb-6">
                            <span class="material-symbols-outlined text-secondary-container"
                                data-icon="church">church</span>
                        </div>
                        <h4 class="font-headline-md text-headline-md text-primary mb-3">Faith Organizations</h4>
                        <p class="text-on-surface-variant font-body-md">Comprehensive membership tracking to
                            multi-branch
                            financial reporting and donation management.</p>
                    </div>
                    <!-- Service Businesses -->
                    <div class="glass-card p-8 rounded-xl hover:-translate-y-2 transition-transform duration-300">
                        <div class="w-12 h-12 rounded-lg bg-primary-container flex items-center justify-center mb-6">
                            <span class="material-symbols-outlined text-secondary-container"
                                data-icon="local_laundry_service">local_laundry_service</span>
                        </div>
                        <h4 class="font-headline-md text-headline-md text-primary mb-3">Service Businesses</h4>
                        <p class="text-on-surface-variant font-body-md">Custom POS systems, inventory tracking,
                            Kanban-based
                            workflow boards, and operational analytics.</p>
                    </div>
                    <!-- Financial Services -->
                    <div class="glass-card p-8 rounded-xl hover:-translate-y-2 transition-transform duration-300">
                        <div class="w-12 h-12 rounded-lg bg-primary-container flex items-center justify-center mb-6">
                            <span class="material-symbols-outlined text-secondary-container"
                                data-icon="account_balance">account_balance</span>
                        </div>
                        <h4 class="font-headline-md text-headline-md text-primary mb-3">Financial Services</h4>
                        <p class="text-on-surface-variant font-body-md">Secure, auditable systems for micro-finance
                            institutions, credit unions, and diaspora remittance partners.</p>
                    </div>
                    <!-- Heritage & Culture -->
                    <div class="glass-card p-8 rounded-xl hover:-translate-y-2 transition-transform duration-300">
                        <div class="w-12 h-12 rounded-lg bg-primary-container flex items-center justify-center mb-6">
                            <span class="material-symbols-outlined text-secondary-container"
                                data-icon="public">public</span>
                        </div>
                        <h4 class="font-headline-md text-headline-md text-primary mb-3">Heritage &amp; Culture</h4>
                        <p class="text-on-surface-variant font-body-md">Digital platforms for diaspora engagement,
                            cultural
                            preservation, and cross-border community collaboration.</p>
                    </div>
                    <!-- Mobile-First -->
                    <div class="glass-card p-8 rounded-xl hover:-translate-y-2 transition-transform duration-300">
                        <div class="w-12 h-12 rounded-lg bg-primary-container flex items-center justify-center mb-6">
                            <span class="material-symbols-outlined text-secondary-container"
                                data-icon="smartphone">smartphone</span>
                        </div>
                        <h4 class="font-headline-md text-headline-md text-primary mb-3">Mobile-First Products</h4>
                        <p class="text-on-surface-variant font-body-md">Applications optimized for variable
                            connectivity,
                            high-latency environments, and intuitive mobile interaction.</p>
                    </div>
                </div>
            </div>
        </section>
        <!-- Send an Inquiry Form Section -->
        <section class="py-24 px-margin-mobile md:px-margin-desktop max-w-container-max mx-auto">
            <div class="bg-primary p-8 md:p-16 rounded-2xl relative overflow-hidden">
                <div class="absolute inset-0 grid-pattern opacity-5"></div>
                <div class="relative z-10 grid grid-cols-1 lg:grid-cols-2 gap-16">
                    <div>
                        <h2 class="font-headline-xl text-headline-xl text-on-primary mb-6">Send an Inquiry</h2>
                        <p class="font-body-lg text-body-lg text-on-primary/70 mb-12">
                            Tell us about your project or requirements and we'll get back to you within 24–48 hours.
                            Let's
                            build something that actually works.
                        </p>
                        <div class="space-y-8">
                            <div class="flex items-start gap-4">
                                <div class="bg-secondary-container/20 p-3 rounded">
                                    <span
                                        class="material-symbols-outlined text-secondary-container">mark_as_unread</span>
                                </div>
                                <div>
                                    <div class="text-on-primary font-bold">Email Dispatch</div>
                                    <div class="text-on-primary/50 font-label-md">consult@exchosoft.com</div>
                                </div>
                            </div>
                            <div class="flex items-start gap-4">
                                <div class="bg-secondary-container/20 p-3 rounded">
                                    <span class="material-symbols-outlined text-secondary-container">schedule</span>
                                </div>
                                <div>
                                    <div class="text-on-primary font-bold">Response Protocol</div>
                                    <div class="text-on-primary/50 font-label-md">24-48 Business Hours</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <form class="space-y-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="space-y-2">
                                <label class="text-on-primary/70 font-label-md">Full Name*</label>
                                <input
                                    class="w-full bg-on-primary/5 border border-on-primary/20 text-on-primary p-3 rounded focus:border-secondary-container outline-none transition-colors"
                                    placeholder="John Doe" type="text" />
                            </div>
                            <div class="space-y-2">
                                <label class="text-on-primary/70 font-label-md">Email*</label>
                                <input
                                    class="w-full bg-on-primary/5 border border-on-primary/20 text-on-primary p-3 rounded focus:border-secondary-container outline-none transition-colors"
                                    placeholder="john@company.com" type="email" />
                            </div>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="space-y-2">
                                <label class="text-on-primary/70 font-label-md">Phone</label>
                                <input
                                    class="w-full bg-on-primary/5 border border-on-primary/20 text-on-primary p-3 rounded focus:border-secondary-container outline-none transition-colors"
                                    placeholder="+1 000 000 0000" type="tel" />
                            </div>
                            <div class="space-y-2">
                                <label class="text-on-primary/70 font-label-md">Company</label>
                                <input
                                    class="w-full bg-on-primary/5 border border-on-primary/20 text-on-primary p-3 rounded focus:border-secondary-container outline-none transition-colors"
                                    placeholder="Organization Name" type="text" />
                            </div>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="space-y-2">
                                <label class="text-on-primary/70 font-label-md">Inquiry Type*</label>
                                <select
                                    class="w-full bg-on-primary/10 border border-on-primary/20 text-on-primary p-3 rounded focus:border-secondary-container outline-none appearance-none">
                                    <option class="bg-primary">Custom Software</option>
                                    <option class="bg-primary">Strategy &amp; Advisory</option>
                                    <option class="bg-primary">System Audit</option>
                                    <option class="bg-primary">Digital Transformation</option>
                                </select>
                            </div>
                            <div class="space-y-2">
                                <label class="text-on-primary/70 font-label-md">Budget Range</label>
                                <select
                                    class="w-full bg-on-primary/10 border border-on-primary/20 text-on-primary p-3 rounded focus:border-secondary-container outline-none">
                                    <option class="bg-primary">Under $10k</option>
                                    <option class="bg-primary">$10k - $50k</option>
                                    <option class="bg-primary">$50k - $100k</option>
                                    <option class="bg-primary">$100k+</option>
                                </select>
                            </div>
                        </div>
                        <div class="space-y-2">
                            <label class="text-on-primary/70 font-label-md">Subject*</label>
                            <input
                                class="w-full bg-on-primary/5 border border-on-primary/20 text-on-primary p-3 rounded focus:border-secondary-container outline-none transition-colors"
                                placeholder="Brief subject of inquiry" type="text" />
                        </div>
                        <div class="space-y-2">
                            <label class="text-on-primary/70 font-label-md">Describe Your Project*</label>
                            <textarea
                                class="w-full bg-on-primary/5 border border-on-primary/20 text-on-primary p-3 rounded focus:border-secondary-container outline-none transition-colors"
                                placeholder="Tell us about your requirements..." rows="4"></textarea>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="space-y-2">
                                <label class="text-on-primary/70 font-label-md">Timeline</label>
                                <input
                                    class="w-full bg-on-primary/5 border border-on-primary/20 text-on-primary p-3 rounded focus:border-secondary-container outline-none transition-colors"
                                    placeholder="e.g. 3 Months" type="text" />
                            </div>
                            <div class="space-y-2">
                                <label class="text-on-primary/70 font-label-md">How did you hear about us?</label>
                                <input
                                    class="w-full bg-on-primary/5 border border-on-primary/20 text-on-primary p-3 rounded focus:border-secondary-container outline-none transition-colors"
                                    placeholder="Search, Referral, Social..." type="text" />
                            </div>
                        </div>
                        <button
                            class="w-full bg-secondary-container text-on-secondary-fixed py-4 font-label-md font-bold hover:opacity-90 transition-all flex items-center justify-center gap-3 group"
                            type="submit">
                            Send Transmission
                            <span
                                class="material-symbols-outlined group-hover:translate-x-1 transition-transform">send</span>
                        </button>
                    </form>
                </div>
            </div>
        </section>



</div>
