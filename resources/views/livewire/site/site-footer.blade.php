{{--
  site-footer.blade.php  — Livewire 4 component
  Full v5 footer: dot-matrix bg, brand col, 4-col grid, social icons, bottom bar
--}}
<footer class="relative bg-primary pt-20 pb-10 px-4 md:px-16 text-white overflow-hidden">
    <div class="absolute inset-0 dot-matrix opacity-20 pointer-events-none"></div>
    <div class="absolute inset-0 bg-gradient-to-b from-primary/60 via-primary/80 to-primary/95 pointer-events-none">
    </div>
    <div
        class="absolute top-0 left-1/2 -translate-x-1/2 w-[800px] h-[300px] bg-secondary/5 blur-[120px] rounded-full pointer-events-none">
    </div>
    <div class="relative z-10 max-w-7xl mx-auto">
        <div class="grid grid-cols-2 sm:grid-cols-2 lg:grid-cols-5 gap-x-8 gap-y-10 mb-16">
            <div class="col-span-2 lg:col-span-1">
                <div class="flex items-center gap-2 mb-6"><span
                        class="material-symbols-outlined text-secondary-container text-3xl"
                        style="font-variation-settings:'FILL' 1;">hub</span><span
                        class="font-syne text-xl font-bold">Exchosoft Consult</span></div>
                <p class="text-on-primary-container text-sm leading-relaxed mb-6">Built From Here.<br />Industrial
                    Reliability meets Cutting-Edge Innovation.</p>
                <div class="flex gap-4">
                    <a class="w-8 h-8 rounded-full bg-white/5 flex items-center justify-center hover:bg-secondary-container hover:text-primary transition-all"
                        href="#"><span class="material-symbols-outlined text-sm">public</span></a>
                    <a class="w-8 h-8 rounded-full bg-white/5 flex items-center justify-center hover:bg-secondary-container hover:text-primary transition-all"
                        href="#"><span class="material-symbols-outlined text-sm">alternate_email</span></a>
                </div>
            </div>
            <div>
                <h6 class="font-syne font-bold text-secondary-fixed text-xs uppercase tracking-widest mb-6">Solutions
                </h6>
                <ul class="space-y-4 text-sm text-on-primary-container">
                    <li><a class="hover:text-white transition-colors" href="#">WashOps</a></li>
                    <li><a class="hover:text-white transition-colors" href="#">ChurchOps</a></li>
                    <li><a class="hover:text-white transition-colors" href="#">ClinicOps</a></li>
                    <li><a class="hover:text-white transition-colors" href="#">LabOps</a></li>
                </ul>
            </div>
            <div>
                <h6 class="font-syne font-bold text-secondary-fixed text-xs uppercase tracking-widest mb-6">Expertise
                </h6>
                <ul class="space-y-4 text-sm text-on-primary-container">
                    <li><a class="hover:text-white transition-colors" href="#">Custom Development</a></li>
                    <li><a class="hover:text-white transition-colors" href="#">Consulting</a></li>
                    <li><a class="hover:text-white transition-colors" href="#">Architecture</a></li>
                    <li><a class="hover:text-white transition-colors" href="#">Digital Transformation</a></li>
                </ul>
            </div>
            <div>
                <h6 class="font-syne font-bold text-secondary-fixed text-xs uppercase tracking-widest mb-6">Resources
                </h6>
                <ul class="space-y-4 text-sm text-on-primary-container">
                    <li><a class="hover:text-white transition-colors" href="#">About Us</a></li>
                    <li><a class="hover:text-white transition-colors" href="#">Insights</a></li>
                    <li><a class="hover:text-white transition-colors" href="#">Case Studies</a></li>
                    <li><a class="hover:text-white transition-colors" href="#">Global Support</a></li>
                </ul>
            </div>
            <div class="col-span-2 lg:col-span-1">
                <h6 class="font-syne font-bold text-secondary-fixed text-xs uppercase tracking-widest mb-6">Talk to Us
                </h6>
                <p class="text-xs text-on-primary-container mb-4">Subscribe to our industrial insights.</p>
                <div class="flex bg-white/5 rounded-full p-1 border border-white/10">
                    <input
                        class="bg-transparent border-none text-xs flex-grow px-4 focus:ring-0 text-white placeholder-white/30"
                        placeholder="Email" type="email" />
                    <button
                        class="bg-secondary-container text-primary px-4 py-2 rounded-full text-[10px] font-bold uppercase">Join</button>
                </div>
            </div>
        </div>
        <div
            class="pt-8 border-t border-white/5 flex flex-col md:flex-row justify-between items-center gap-4 text-[11px] text-on-primary-container">
            <p>© 2024 Exchosoft Consult. All rights reserved.</p>
            <div class="flex flex-wrap justify-center gap-6">
                <a class="hover:text-white transition-colors" href="#">Privacy Policy</a>
                <a class="hover:text-white transition-colors" href="#">Terms of Service</a>
                <a class="hover:text-white transition-colors" href="#">Security Architecture</a>
                <a class="hover:text-white transition-colors" href="#">Cookie Policy</a>
            </div>
        </div>
    </div>
</footer>
