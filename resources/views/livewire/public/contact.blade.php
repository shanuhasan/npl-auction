<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <div class="mb-10 text-center md:text-left">
        <h1 class="text-4xl md:text-5xl font-black text-white uppercase tracking-widest mb-4">Contact Us</h1>
        <p class="text-gray-400 text-lg">Have any questions or inquiries? Feel free to reach out to us and we'll get back to you as soon as possible.</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-12">
        <!-- Contact Information -->
        <div class="col-span-1 space-y-8">
            @php
                $email = setting('contact_email');
                $phone = setting('contact_phone');
            @endphp

            <div class="bg-[#141B2D] rounded-2xl p-8 border border-white/5 shadow-xl relative overflow-hidden group hover:border-[#FFC800]/30 transition-all duration-300">
                <div class="absolute top-0 right-0 w-32 h-32 bg-[#FFC800]/5 rounded-full blur-2xl transform translate-x-1/2 -translate-y-1/2 group-hover:bg-[#FFC800]/10 transition-all"></div>
                <h3 class="text-2xl font-bold text-white mb-6 uppercase tracking-wider relative z-10">Get In Touch</h3>
                
                <div class="space-y-6 relative z-10">
                    @if($email)
                    <div class="flex items-start gap-4">
                        <div class="w-12 h-12 rounded-full bg-[#0B0F19] flex items-center justify-center border border-white/10 text-[#FFC800]">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" /></svg>
                        </div>
                        <div>
                            <h4 class="text-sm text-gray-500 uppercase font-bold tracking-wider mb-1">Email Us</h4>
                            <a href="mailto:{{ $email }}" class="text-white hover:text-[#FFC800] transition-colors text-lg">{{ $email }}</a>
                        </div>
                    </div>
                    @endif
                    
                    @if($phone)
                    <div class="flex items-start gap-4">
                        <div class="w-12 h-12 rounded-full bg-[#0B0F19] flex items-center justify-center border border-white/10 text-[#FFC800]">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" /></svg>
                        </div>
                        <div>
                            <h4 class="text-sm text-gray-500 uppercase font-bold tracking-wider mb-1">Call Us</h4>
                            <a href="tel:{{ $phone }}" class="text-white hover:text-[#FFC800] transition-colors text-lg">{{ $phone }}</a>
                        </div>
                    </div>
                    @endif
                </div>

                <!-- Social Links -->
                @php
                    $facebook = setting('facebook');
                    $instagram = setting('instagram');
                    $twitter = setting('twitter');
                    $youtube = setting('youtube');
                @endphp

                @if($facebook || $instagram || $twitter || $youtube)
                <div class="mt-8 pt-8 border-t border-white/10 relative z-10">
                    <h4 class="text-sm text-gray-400 uppercase font-bold tracking-wider mb-4">Follow Us</h4>
                    <div class="flex items-center gap-3">
                        @if($facebook)
                        <a href="{{ $facebook }}" target="_blank" class="w-10 h-10 rounded-full bg-[#0B0F19] border border-white/10 flex items-center justify-center text-gray-400 hover:text-[#FFC800] hover:border-[#FFC800] hover:-translate-y-1 transition-all">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path fill-rule="evenodd" d="M22 12c0-5.523-4.477-10-10-10S2 6.477 2 12c0 4.991 3.657 9.128 8.438 9.878v-6.987h-2.54V12h2.54V9.797c0-2.506 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.243 0-1.63.771-1.63 1.562V12h2.773l-.443 2.89h-2.33v6.988C18.343 21.128 22 16.991 22 12z" clip-rule="evenodd" /></svg>
                        </a>
                        @endif
                        @if($instagram)
                        <a href="{{ $instagram }}" target="_blank" class="w-10 h-10 rounded-full bg-[#0B0F19] border border-white/10 flex items-center justify-center text-gray-400 hover:text-[#FFC800] hover:border-[#FFC800] hover:-translate-y-1 transition-all">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path fill-rule="evenodd" d="M12.315 2c2.43 0 2.784.013 3.808.06 1.064.049 1.791.218 2.427.465a4.902 4.902 0 011.772 1.153 4.902 4.902 0 011.153 1.772c.247.636.416 1.363.465 2.427.048 1.067.06 1.407.06 4.123v.08c0 2.643-.012 2.987-.06 4.043-.049 1.064-.218 1.791-.465 2.427a4.902 4.902 0 01-1.153 1.772 4.902 4.902 0 01-1.772 1.153c-.636.247-1.363.416-2.427.465-1.067.048-1.407.06-4.123.06h-.08c-2.643 0-2.987-.012-4.043-.06-1.064-.049-1.791-.218-2.427-.465a4.902 4.902 0 01-1.772-1.153 4.902 4.902 0 01-1.153-1.772c-.247-.636-.416-1.363-.465-2.427-.047-1.024-.06-1.379-.06-3.808v-.63c0-2.43.013-2.784.06-3.808.049-1.064.218-1.791.465-2.427a4.902 4.902 0 011.153-1.772A4.902 4.902 0 015.45 2.525c.636-.247 1.363-.416 2.427-.465C8.901 2.013 9.256 2 11.685 2h.63zm-.081 1.802h-.468c-2.456 0-2.784.011-3.807.058-.975.045-1.504.207-1.857.344-.467.182-.8.398-1.15.748-.35.35-.566.683-.748 1.15-.137.353-.3.882-.344 1.857-.047 1.023-.058 1.351-.058 3.807v.468c0 2.456.011 2.784.058 3.807.045.975.207 1.504.344 1.857.182.466.399.8.748 1.15.35.35.683.566 1.15.748.353.137.882.3 1.857.344 1.054.048 1.37.058 4.041.058h.08c2.597 0 2.917-.01 3.96-.058.976-.045 1.505-.207 1.858-.344.466-.182.8-.398 1.15-.748.35-.35.566-.683.748-1.15.137-.353.3-.882.344-1.857.048-1.055.058-1.37.058-4.041v-.08c0-2.597-.01-2.917-.058-3.96-.045-.976-.207-1.505-.344-1.858a3.097 3.097 0 00-.748-1.15 3.098 3.098 0 00-1.15-.748c-.353-.137-.882-.3-1.857-.344-1.023-.047-1.351-.058-3.807-.058zM12 6.865a5.135 5.135 0 110 10.27 5.135 5.135 0 010-10.27zm0 1.802a3.333 3.333 0 100 6.666 3.333 3.333 0 000-6.666zm5.338-3.205a1.2 1.2 0 110 2.4 1.2 1.2 0 010-2.4z" clip-rule="evenodd" /></svg>
                        </a>
                        @endif
                        @if($twitter)
                        <a href="{{ $twitter }}" target="_blank" class="w-10 h-10 rounded-full bg-[#0B0F19] border border-white/10 flex items-center justify-center text-gray-400 hover:text-[#FFC800] hover:border-[#FFC800] hover:-translate-y-1 transition-all">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path d="M8.29 20.251c7.547 0 11.675-6.253 11.675-11.675 0-.178 0-.355-.012-.53A8.348 8.348 0 0022 5.92a8.19 8.19 0 01-2.357.646 4.118 4.118 0 001.804-2.27 8.224 8.224 0 01-2.605.996 4.107 4.107 0 00-6.993 3.743 11.65 11.65 0 01-8.457-4.287 4.106 4.106 0 001.27 5.477A4.072 4.072 0 012.8 9.713v.052a4.105 4.105 0 003.292 4.022 4.095 4.095 0 01-1.853.07 4.108 4.108 0 003.834 2.85A8.233 8.233 0 012 18.407a11.616 11.616 0 006.29 1.84" /></svg>
                        </a>
                        @endif
                        @if($youtube)
                        <a href="{{ $youtube }}" target="_blank" class="w-10 h-10 rounded-full bg-[#0B0F19] border border-white/10 flex items-center justify-center text-gray-400 hover:text-[#FFC800] hover:border-[#FFC800] hover:-translate-y-1 transition-all">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path fill-rule="evenodd" d="M19.812 5.418c.861.23 1.538.907 1.768 1.768C21.998 8.746 22 12 22 12s0 3.255-.418 4.814a2.504 2.504 0 0 1-1.768 1.768c-1.56.419-7.814.419-7.814.419s-6.255 0-7.814-.419a2.505 2.505 0 0 1-1.768-1.768C2 15.255 2 12 2 12s0-3.255.417-4.814a2.507 2.507 0 0 1 1.768-1.768C5.744 5 11.998 5 11.998 5s6.255 0 7.814.418ZM15.194 12 10 15V9l5.194 3Z" clip-rule="evenodd"/></svg>
                        </a>
                        @endif
                    </div>
                </div>
                @endif

            </div>
        </div>

        <!-- Contact Form -->
        <div class="col-span-1 md:col-span-2">
            <div class="bg-[#141B2D] rounded-2xl p-8 border border-white/5 shadow-xl relative overflow-hidden">
                <h3 class="text-2xl font-bold text-white mb-6 uppercase tracking-wider border-b border-white/10 pb-4">Send us a message</h3>
                
                @if (session()->has('success'))
                    <div class="bg-green-500/20 border-l-4 border-green-500 p-4 mb-8 rounded">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-green-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm text-green-300 font-medium">{{ session('success') }}</p>
                            </div>
                        </div>
                    </div>
                @endif

                <form wire:submit.prevent="submit" class="space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="name" class="block text-sm font-bold text-gray-400 uppercase tracking-wider mb-2">Your Name *</label>
                            <input type="text" id="name" wire:model.defer="name" class="w-full bg-[#0B0F19] border border-white/10 rounded-lg p-4 text-white focus:outline-none focus:border-[#FFC800] focus:ring-1 focus:ring-[#FFC800] transition-colors placeholder-gray-600" placeholder="Enter your full name">
                            @error('name') <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span> @enderror
                        </div>
                        
                        <div>
                            <label for="email" class="block text-sm font-bold text-gray-400 uppercase tracking-wider mb-2">Email Address *</label>
                            <input type="email" id="email" wire:model.defer="email" class="w-full bg-[#0B0F19] border border-white/10 rounded-lg p-4 text-white focus:outline-none focus:border-[#FFC800] focus:ring-1 focus:ring-[#FFC800] transition-colors placeholder-gray-600" placeholder="you@example.com">
                            @error('email') <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    
                    <div>
                        <label for="phone" class="block text-sm font-bold text-gray-400 uppercase tracking-wider mb-2">Phone Number</label>
                        <input type="text" id="phone" wire:model.defer="phone" class="w-full bg-[#0B0F19] border border-white/10 rounded-lg p-4 text-white focus:outline-none focus:border-[#FFC800] focus:ring-1 focus:ring-[#FFC800] transition-colors placeholder-gray-600" placeholder="Optional">
                        @error('phone') <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label for="message" class="block text-sm font-bold text-gray-400 uppercase tracking-wider mb-2">Your Message *</label>
                        <textarea id="message" wire:model.defer="message" rows="5" class="w-full bg-[#0B0F19] border border-white/10 rounded-lg p-4 text-white focus:outline-none focus:border-[#FFC800] focus:ring-1 focus:ring-[#FFC800] transition-colors placeholder-gray-600 resize-none" placeholder="How can we help you?"></textarea>
                        @error('message') <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div class="pt-2 flex justify-end">
                        <button type="submit" class="bg-gradient-to-r from-[#FFC800] to-[#D4A000] hover:from-[#FFE040] hover:to-[#FFC800] text-[#0B0F19] font-bold uppercase tracking-widest py-4 px-10 rounded-lg shadow-lg hover:shadow-[0_0_20px_rgba(255,200,0,0.4)] transition-all transform hover:-translate-y-1 flex items-center">
                            <span wire:loading.remove wire:target="submit">Send Message</span>
                            <span wire:loading wire:target="submit">Sending...</span>
                            <svg wire:loading.remove wire:target="submit" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 ml-2" viewBox="0 0 20 20" fill="currentColor">
                                <path d="M10.894 2.553a1 1 0 00-1.788 0l-7 14a1 1 0 001.169 1.409l5-1.429A1 1 0 009 15.571V11a1 1 0 112 0v4.571a1 1 0 00.725.962l5 1.428a1 1 0 001.17-1.408l-7-14z" />
                            </svg>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
