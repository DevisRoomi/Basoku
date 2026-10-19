<div class="font-sans">
    <!-- 1. TOMBOL FLOATING WIDGET (TRIGGER) -->
    <div class="fixed bottom-6 right-6 z-50">
        <button
            type="button"
            wire:click="toggleChat"
            class="group relative inline-flex items-center gap-2.5 px-4 py-3 sm:px-5 sm:py-3.5 rounded-full bg-[#8B231A] hover:bg-[#701A13] text-[#FAF7F2] shadow-2xl hover:shadow-[#8B231A]/40 transition-all duration-200 active:scale-95 cursor-pointer border-2 border-[#FAF7F2]/30"
            title="Buka Konsultasi Ahli Adonan Bakso AI"
            aria-label="Tanya Ahli Adonan Bakso BASOKU"
        >
            <!-- Ikon Robot/Konsultan dengan Sparkles -->
            <span class="relative flex items-center justify-center w-6 h-6 rounded-full bg-white/20 shrink-0">
                <svg class="w-4 h-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904L9 18.75l-.813-2.846a4.5 4.5 0 00-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 003.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 003.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 00-3.09 3.09zM18.259 8.715L18 9.75l-.259-1.035a3.375 3.375 0 00-2.455-2.456L14.25 6l1.036-.259a3.375 3.375 0 002.455-2.456L18 2.25l.259 1.035a3.375 3.375 0 002.456 2.456L21.75 6l-1.035.259a3.375 3.375 0 00-2.456 2.456zM16.894 20.567L16.5 21.75l-.394-1.183a2.25 2.25 0 00-1.423-1.423L13.5 18.75l1.183-.394a2.25 2.25 0 001.423-1.423l.394-1.183.394 1.183a2.25 2.25 0 001.423 1.423l1.183.394-1.183.394a2.25 2.25 0 00-1.423 1.423z" />
                </svg>
                <span class="absolute -top-1 -right-1 w-2.5 h-2.5 rounded-full bg-emerald-400 border border-[#8B231A] animate-pulse"></span>
            </span>

            <div class="text-left hidden xs:block">
                <span class="text-[10px] uppercase font-bold tracking-wider block text-white/70 leading-none">
                    Konsultan AI
                </span>
                <span class="text-xs font-extrabold tracking-tight block text-white mt-0.5">
                    Tanya BASOKU
                </span>
            </div>

            <!-- Arrow Toggle Indicator -->
            <svg class="w-4 h-4 text-white/70 transition-transform duration-200 {{ $isOpen ? 'rotate-180' : '' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7" />
            </svg>
        </button>
    </div>

    <!-- 2. JENDELA LACI CHAT INTERAKTIF -->
    @if ($isOpen)
        <div 
            class="fixed bottom-24 right-4 sm:right-6 z-50 w-[calc(100vw-2rem)] sm:w-[420px] max-h-[640px] h-[82vh] sm:h-[580px] bg-[#FAF7F2] rounded-3xl border border-[#26221E]/15 shadow-2xl flex flex-col overflow-hidden animate-fade-in"
            style="box-shadow: 0 25px 50px -12px rgba(38, 34, 30, 0.35);"
        >
            <!-- Header Chat -->
            <div class="bg-[#2D4235] text-[#FAF7F2] px-5 py-4 flex items-center justify-between shrink-0 border-b border-white/10">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-2xl bg-[#8B231A] text-white flex items-center justify-center font-bold text-sm shadow-inner shrink-0">
                        🥩
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <h3 class="text-base font-bold text-white tracking-tight leading-none" style="font-family: 'Playfair Display', Georgia, serif;">
                                Tanya BASOKU
                            </h3>
                            <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded text-[9px] font-bold uppercase tracking-wider bg-emerald-500/20 text-emerald-300 border border-emerald-400/30">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                                Online
                            </span>
                        </div>
                        <p class="text-[11px] text-[#FAF7F2]/70 mt-1 leading-none font-sans">
                            Konsultan Ahli Adonan & Selepan Pasar AI
                        </p>
                    </div>
                </div>

                <div class="flex items-center gap-1">
                    <!-- Tombol Reset Obrolan -->
                    <button
                        type="button"
                        wire:click="clearChat"
                        class="p-2 rounded-xl text-white/60 hover:text-white hover:bg-white/10 transition-colors cursor-pointer"
                        title="Bersihkan Percakapan"
                    >
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                    </button>

                    <!-- Tombol Tutup Laci -->
                    <button
                        type="button"
                        wire:click="toggleChat"
                        class="p-2 rounded-xl text-white/60 hover:text-white hover:bg-white/10 transition-colors cursor-pointer"
                        title="Tutup Widget"
                    >
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Wadah Pesan Chat -->
            <div 
                id="basokuChatContainer"
                class="flex-1 overflow-y-auto p-4 sm:p-5 space-y-4 bg-[#F4EFE6]/50 scroll-smooth"
            >
                @foreach ($chatHistory as $chat)
                    @if ($chat['role'] === 'user')
                        <!-- Pesan Pelanggan -->
                        <div class="flex flex-col items-end">
                            <div class="max-w-[85%] bg-[#8B231A] text-[#FAF7F2] px-4 py-3 rounded-2xl rounded-tr-xs text-xs sm:text-[13px] leading-relaxed shadow-sm font-sans">
                                {{ $chat['message'] }}
                            </div>
                            <span class="text-[10px] text-[#5A534A]/70 mt-1 font-mono pr-1">
                                {{ $chat['time'] ?? '' }}
                            </span>
                        </div>
                    @else
                        <!-- Jawaban Asisten AI -->
                        <div class="flex items-start gap-2.5">
                            <div class="w-6 h-6 rounded-full bg-[#2D4235] text-white flex items-center justify-center text-[10px] shrink-0 mt-0.5 shadow-sm">
                                🥣
                            </div>
                            <div class="flex-1 max-w-[90%]">
                                <div class="bg-white border border-[#26221E]/10 text-[#26221E] px-4 py-3 rounded-2xl rounded-tl-xs text-xs sm:text-[13px] leading-relaxed shadow-sm font-sans">
                                    {!! nl2br(e($chat['message'])) !!}
                                </div>
                                <span class="text-[10px] text-[#5A534A]/70 mt-1 font-mono pl-1 block">
                                    Ahli BASOKU • {{ $chat['time'] ?? '' }}
                                </span>
                            </div>
                        </div>
                    @endif
                @endforeach

                <!-- Indikator Ketik (Sedang Menunggu Respon) -->
                <div wire:loading wire:target="sendMessage, askQuickPrompt" class="flex items-start gap-2.5">
                    <div class="w-6 h-6 rounded-full bg-[#2D4235] text-white flex items-center justify-center text-[10px] shrink-0 mt-0.5 animate-pulse">
                        ✨
                    </div>
                    <div class="bg-white border border-[#26221E]/10 px-4 py-2.5 rounded-2xl rounded-tl-xs shadow-sm flex items-center gap-2">
                        <span class="text-xs text-[#5A534A] font-medium font-sans">Ahli BASOKU sedang menganalisis adonan...</span>
                        <div class="flex items-center gap-1">
                            <span class="w-1.5 h-1.5 rounded-full bg-[#8B231A] animate-bounce"></span>
                            <span class="w-1.5 h-1.5 rounded-full bg-[#8B231A] animate-bounce [animation-delay:0.2s]"></span>
                            <span class="w-1.5 h-1.5 rounded-full bg-[#8B231A] animate-bounce [animation-delay:0.4s]"></span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Pertanyaan Cepat (Quick Prompts) -->
            <div class="p-3 bg-[#FAF7F2] border-t border-[#26221E]/10 shrink-0">
                <span class="text-[10px] font-bold uppercase tracking-wider text-[#5A534A] block mb-1.5">
                    Topik Populer:
                </span>
                <div class="flex items-center gap-1.5 overflow-x-auto pb-1 text-[11px] no-scrollbar">
                    @foreach ($quickPrompts as $prompt)
                        <button
                            type="button"
                            wire:click="askQuickPrompt('{{ addslashes($prompt) }}')"
                            wire:loading.attr="disabled"
                            class="px-2.5 py-1.5 rounded-xl bg-white hover:bg-[#8B231A]/10 text-[#26221E] hover:text-[#8B231A] border border-[#26221E]/15 whitespace-nowrap transition-colors cursor-pointer shrink-0 disabled:opacity-50"
                        >
                            {{ $prompt }}
                        </button>
                    @endforeach
                </div>
            </div>

            <!-- Form Input Pesan -->
            <div class="p-3 bg-white border-t border-[#26221E]/10 shrink-0">
                <form wire:submit="sendMessage" class="flex items-center gap-2">
                    <input
                        type="text"
                        wire:model="question"
                        placeholder="Tanyakan takaran resep atau tips selepan..."
                        class="flex-1 px-4 py-2.5 rounded-xl bg-[#FAF7F2] border border-[#26221E]/15 text-[#26221E] text-xs focus:outline-none focus:border-[#8B231A] focus:ring-1 focus:ring-[#8B231A] transition-all font-sans"
                        wire:loading.attr="disabled"
                        wire:target="sendMessage, askQuickPrompt"
                        maxlength="500"
                    />

                    <button
                        type="submit"
                        wire:loading.attr="disabled"
                        wire:target="sendMessage, askQuickPrompt"
                        class="px-4 py-2.5 rounded-xl bg-[#8B231A] hover:bg-[#701A13] text-[#FAF7F2] font-bold text-xs shadow-md transition-all active:scale-95 cursor-pointer disabled:opacity-50 shrink-0 flex items-center justify-center gap-1"
                    >
                        <span>Kirim</span>
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 12L3.269 3.126A59.768 59.768 0 0121.485 12 59.77 59.77 0 013.27 20.876L5.999 12zm0 0h7.5" />
                        </svg>
                    </button>
                </form>
                @error('question')
                    <span class="text-red-600 text-[10px] mt-1 block font-medium">{{ $message }}</span>
                @enderror
            </div>
        </div>
    @endif
</div>
