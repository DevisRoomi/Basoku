@php
    $hasUserConversation = count($chatHistory) > 1;
@endphp

<div
    x-data="{
        open: @entangle('isOpen').live,
        draft: '',
        showTopicsManual: false,
        isDesktop: window.innerWidth >= 640,
        expanded: false,
        resizing: false,
        panelW: 440,
        panelH: 520,
        startX: 0,
        startY: 0,
        startW: 440,
        startH: 520,
        maxSafeWidth() {
            return Math.max(360, Math.min(760, window.innerWidth - 48));
        },
        maxSafeHeight() {
            return Math.max(360, window.innerHeight - 216);
        },
        clampSize() {
            this.isDesktop = window.innerWidth >= 640;
            if (!this.isDesktop) { return; }
            this.panelW = Math.min(this.maxSafeWidth(), Math.max(360, this.panelW));
            this.panelH = Math.min(this.maxSafeHeight(), Math.max(340, this.panelH));
        },
        toggleExpand() {
            this.expanded = !this.expanded;
            if (this.expanded) {
                this.panelW = Math.min(600, this.maxSafeWidth());
                this.panelH = this.maxSafeHeight();
            } else {
                this.panelW = Math.min(440, this.maxSafeWidth());
                this.panelH = Math.min(520, this.maxSafeHeight());
            }
        },
        startResize(event) {
            if (!this.isDesktop) { return; }
            this.resizing = true;
            this.startX = event.clientX;
            this.startY = event.clientY;
            this.startW = this.panelW;
            this.startH = this.panelH;
            event.preventDefault();
        },
        onResizeMove(event) {
            if (!this.resizing) { return; }
            const nextW = this.startW + (this.startX - event.clientX);
            const nextH = this.startH + (this.startY - event.clientY);
            this.panelW = Math.min(this.maxSafeWidth(), Math.max(360, Math.round(nextW)));
            this.panelH = Math.min(this.maxSafeHeight(), Math.max(340, Math.round(nextH)));
            this.expanded = this.panelW >= 540 || this.panelH >= (this.maxSafeHeight() - 24);
        },
        stopResize() {
            this.resizing = false;
        },
        init() {
            this.clampSize();
        }
    }"
    @resize.window="clampSize()"
    @pointermove.window="onResizeMove($event)"
    @pointerup.window="stopResize()"
    @pointercancel.window="stopResize()"
    @keydown.escape.window="if (open) { open = false }"
    :class="open ? 'z-[55]' : 'z-40'"
    class="pointer-events-none fixed inset-0 overflow-hidden font-sans"
>
    <!-- 1. TOMBOL FLOATING WIDGET (TRIGGER) -->
    <div class="pointer-events-auto absolute bottom-5 right-4 sm:bottom-6 sm:right-6">
        <button
            type="button"
            id="tanyaBasokuBtn"
            @click="open = !open"
            :aria-expanded="open.toString()"
            class="group relative inline-flex items-center gap-2.5 px-3.5 py-3 sm:px-5 sm:py-3.5 rounded-full bg-[#8B231A] hover:bg-[#701A13] text-[#FAF7F2] shadow-2xl hover:shadow-[#8B231A]/40 transition-all duration-200 active:scale-95 cursor-pointer border-2 border-[#FAF7F2]/30 select-none"
            title="Buka Konsultasi Ahli Adonan Bakso AI"
            aria-label="Tanya Ahli Adonan Bakso BASOKU"
        >
            <!-- Ikon Robot/Konsultan dengan Sparkles -->
            <span class="relative flex items-center justify-center w-6 h-6 rounded-full bg-white/20 shrink-0">
                <svg class="w-4 h-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904L9 18.75l-.813-2.846a4.5 4.5 0 00-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 003.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 003.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 00-3.09 3.09zM18.259 8.715L18 9.75l-.259-1.035a3.375 3.375 0 00-2.455-2.456L14.25 6l1.036-.259a3.375 3.375 0 002.455-2.456L18 2.25l.259 1.035a3.375 3.375 0 002.456 2.456L21.75 6l-1.035.259a3.375 3.375 0 00-2.456 2.456zM16.894 20.567L16.5 21.75l-.394-1.183a2.25 2.25 0 00-1.423-1.423L13.5 18.75l1.183-.394a2.25 2.25 0 001.423-1.423l.394-1.183.394 1.183a2.25 2.25 0 001.423 1.423l1.183.394-1.183.394a2.25 2.25 0 00-1.423 1.423z" />
                </svg>
                <span class="absolute -top-1 -right-1 w-2.5 h-2.5 rounded-full bg-emerald-400 border border-[#8B231A] animate-pulse"></span>
            </span>

            <div class="text-left hidden sm:block">
                <span class="text-[10px] uppercase font-bold tracking-wider block text-white/80 leading-none">
                    Konsultan AI
                </span>
                <span class="text-xs font-extrabold tracking-tight block text-white mt-0.5">
                    Tanya BASOKU
                </span>
            </div>

            <!-- Arrow Toggle Indicator -->
            <svg
                class="w-4 h-4 text-white/80 transition-transform duration-200"
                :class="open ? 'rotate-180' : ''"
                fill="none"
                viewBox="0 0 24 24"
                stroke="currentColor"
                aria-hidden="true"
            >
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7" />
            </svg>
        </button>
    </div>

    <!-- 2. JENDELA LACI CHAT INTERAKTIF (Dinamis & Resizable di Desktop) -->
    <div
        x-show="open"
        role="dialog"
        aria-label="Konsultan Ahli Adonan Bakso BASOKU"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 translate-y-4 scale-95"
        x-transition:enter-end="opacity-100 translate-y-0 scale-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 translate-y-0 scale-100"
        x-transition:leave-end="opacity-0 translate-y-4 scale-95"
        :style="isDesktop ? `width: ${panelW}px; height: ${panelH}px; box-shadow: 0 25px 50px -12px rgba(38, 34, 30, 0.35);` : 'box-shadow: 0 25px 50px -12px rgba(38, 34, 30, 0.35);'"
        :class="resizing ? 'select-none' : 'transition-[width,height] duration-150 ease-out'"
        class="pointer-events-auto absolute bottom-20 sm:bottom-24 right-4 sm:right-6 w-[calc(100vw-2rem)] max-h-[calc(100dvh-11.75rem)] sm:max-h-[calc(100dvh-13.5rem)] h-[70dvh] bg-[#FAF7F2] rounded-3xl border border-[#26221E]/15 shadow-2xl flex flex-col overflow-hidden"
        style="display: none;"
    >
        <!-- Drag Handle Sudut Kiri-Atas (Khusus Desktop agar ukuran kotak chat bisa ditarik bebas) -->
        <button
            type="button"
            @pointerdown="startResize($event)"
            class="hidden sm:flex items-center justify-center absolute top-0 left-0 z-20 w-5 h-5 cursor-nwse-resize text-white/45 hover:text-white/95 transition-colors rounded-tl-3xl"
            title="Tarik untuk atur lebar & tinggi kotak chat"
            aria-label="Tarik sudut untuk mengatur ukuran kotak chat"
        >
            <svg class="w-3 h-3" viewBox="0 0 12 12" fill="none" stroke="currentColor" stroke-width="1.6" aria-hidden="true">
                <path stroke-linecap="round" d="M2 7L7 2M2 10L10 2" />
            </svg>
        </button>

        <!-- Header Chat -->
        <div class="bg-[#2D4235] text-[#FAF7F2] px-4 py-3.5 sm:px-5 flex items-center justify-between shrink-0 border-b border-white/10 select-none">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-2xl bg-[#8B231A] text-white flex items-center justify-center font-bold text-sm shadow-inner shrink-0" aria-hidden="true">
                    🥩
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <h3 class="text-base font-bold text-white tracking-tight leading-none" style="font-family: 'Playfair Display', Georgia, serif;">
                            Tanya BASOKU
                        </h3>
                        <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded text-[9px] font-bold uppercase tracking-wider bg-emerald-500/20 text-emerald-200 border border-emerald-400/30">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                            Online
                        </span>
                    </div>
                    <p class="text-[11px] text-[#FAF7F2]/80 mt-1 leading-none font-sans">
                        Konsultan Ahli Adonan &amp; Selepan Pasar AI
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-1">
                <!-- Tombol Perbesar / Perkecil (Desktop) -->
                <button
                    type="button"
                    @click="toggleExpand()"
                    class="hidden sm:inline-flex p-2 rounded-xl text-white/75 hover:text-white hover:bg-white/10 transition-colors cursor-pointer"
                    :title="expanded ? 'Kembalikan ukuran normal' : 'Perbesar kotak chat'"
                    :aria-label="expanded ? 'Kembalikan ukuran normal kotak chat' : 'Perbesar kotak chat'"
                >
                    <svg x-show="!expanded" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 9V3.75m0 0H9m-5.25 0L9.75 9.75M20.25 15v5.25m0 0H15m5.25 0l-6-6" />
                    </svg>
                    <svg x-show="expanded" style="display: none;" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 9V4.5M9 9H4.5M9 9L3.75 3.75M15 15v4.5m0-4.5h4.5m-4.5 0l5.25 5.25" />
                    </svg>
                </button>

                <!-- Tombol Reset Obrolan -->
                <button
                    type="button"
                    wire:click="clearChat"
                    @click="draft = ''; showTopicsManual = false;"
                    class="p-2 rounded-xl text-white/75 hover:text-white hover:bg-white/10 transition-colors cursor-pointer"
                    title="Bersihkan Percakapan"
                    aria-label="Bersihkan Percakapan"
                >
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                </button>

                <!-- Tombol Tutup Laci -->
                <button
                    type="button"
                    @click="open = false"
                    class="p-2 rounded-xl text-white/75 hover:text-white hover:bg-white/10 transition-colors cursor-pointer"
                    title="Tutup Widget"
                    aria-label="Tutup Widget"
                >
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>

        <!-- Wadah Pesan Chat (Auto-Scroll pada balasan baru) -->
        <div
            id="basokuChatContainer"
            x-init="$watch('$wire.chatHistory', () => { $nextTick(() => { $el.scrollTop = $el.scrollHeight; }); })"
            x-effect="if (open) { $nextTick(() => { $el.scrollTop = $el.scrollHeight; }); }"
            class="flex-1 min-h-0 overflow-y-auto p-4 sm:p-5 space-y-4 bg-[#F4EFE6]/50 scroll-smooth"
            aria-live="polite"
        >
            @foreach ($chatHistory as $idx => $chat)
                @php
                    $role = is_array($chat) ? ($chat['role'] ?? 'assistant') : 'assistant';
                    $rawMessage = is_array($chat) && is_string($chat['message'] ?? null) ? $chat['message'] : '';
                    $escapedHtml = e($rawMessage);
                    $formattedHtml = preg_replace('/^#{1,3}\s+(.+)$/m', '<span class="block font-bold text-[#2D4235] mt-2 mb-0.5">$1</span>', $escapedHtml) ?? $escapedHtml;
                    $formattedHtml = preg_replace('/^\s*[-*_]{3,}\s*$/m', '<hr class="my-2 border-[#26221E]/15">', $formattedHtml) ?? $formattedHtml;
                    $formattedHtml = preg_replace('/^\s*[\*\-]\s+/m', '&bull; ', $formattedHtml) ?? $formattedHtml;
                    $formattedHtml = preg_replace('/\*\*(.+?)\*\*/s', '<strong class="font-bold text-[#26221E]">$1</strong>', $formattedHtml) ?? $formattedHtml;
                    $formattedHtml = preg_replace('/(?<!\*)\*([^*\n]+)\*(?!\*)/', '<em class="italic">$1</em>', $formattedHtml) ?? $formattedHtml;
                    $formattedHtml = preg_replace('/`([^`]+)`/', '<code class="px-1 py-0.5 rounded bg-[#F4EFE6] text-[11px] font-mono">$1</code>', $formattedHtml) ?? $formattedHtml;
                    // Whitelist ketat khusus tautan WhatsApp resmi (XSS-safe karena sesudah e())
                    $formattedHtml = preg_replace(
                        '/\[([^\]]+)\]\((https:\/\/wa\.me\/[0-9]{8,20}(?:\?text=[A-Za-z0-9%._~+\-]+)?)\)/',
                        '<a href="$2" target="_blank" rel="noopener noreferrer" class="underline decoration-[#2D4235]/60 font-bold text-[#2D4235] hover:text-[#8B231A]">$1</a>',
                        $formattedHtml
                    ) ?? $formattedHtml;
                    $formattedHtml = preg_replace(
                        '/(?<!href=")(https:\/\/wa\.me\/[0-9]{8,20}(?:\?text=[A-Za-z0-9%._~+\-]+)?)/',
                        '<a href="$1" target="_blank" rel="noopener noreferrer" class="underline decoration-[#2D4235]/60 font-bold text-[#2D4235] hover:text-[#8B231A] break-all">$1</a>',
                        $formattedHtml
                    ) ?? $formattedHtml;
                    $formattedHtml = nl2br($formattedHtml);
                @endphp
                @if ($role === 'user')
                    <!-- Pesan Pelanggan -->
                    <div wire:key="chat-msg-{{ $idx }}-user" class="flex flex-col items-end">
                        <div class="max-w-[85%] bg-[#8B231A] text-[#FAF7F2] px-4 py-3 rounded-2xl rounded-tr-xs text-xs sm:text-[13px] leading-relaxed shadow-sm font-sans">
                            {{ $rawMessage }}
                        </div>
                        <span class="text-[10px] text-[#5A534A]/80 mt-1 font-mono pr-1">
                            {{ is_array($chat) ? ($chat['time'] ?? '') : '' }}
                        </span>
                    </div>
                @else
                    <!-- Jawaban Asisten AI -->
                    <div wire:key="chat-msg-{{ $idx }}-assistant" class="flex items-start gap-2.5">
                        <div class="w-6 h-6 rounded-full bg-[#2D4235] text-white flex items-center justify-center text-[10px] shrink-0 mt-0.5 shadow-sm" aria-hidden="true">
                            🥣
                        </div>
                        <div class="flex-1 max-w-[90%]">
                            <div class="bg-white border border-[#26221E]/10 text-[#26221E] px-4 py-3 rounded-2xl rounded-tl-xs text-xs sm:text-[13px] leading-relaxed shadow-sm font-sans">
                                {!! $formattedHtml !!}
                            </div>
                            <span class="text-[10px] text-[#5A534A]/80 mt-1 font-mono pl-1 block">
                                Ahli BASOKU &bull; {{ is_array($chat) ? ($chat['time'] ?? '') : '' }}
                            </span>
                        </div>
                    </div>
                @endif
            @endforeach

            <!-- Indikator Ketik (Sedang Menunggu Respon) -->
            <div wire:loading wire:target="sendMessage, askQuickPrompt, askQuickPromptByIndex" class="flex items-start gap-2.5">
                <div class="w-6 h-6 rounded-full bg-[#2D4235] text-white flex items-center justify-center text-[10px] shrink-0 mt-0.5 animate-pulse" aria-hidden="true">
                    ✨
                </div>
                <div class="bg-white border border-[#26221E]/10 px-4 py-2.5 rounded-2xl rounded-tl-xs shadow-sm flex items-center gap-2">
                    <span class="text-xs text-[#5A534A] font-medium font-sans">Ahli BASOKU sedang menganalisis adonan...</span>
                    <div class="flex items-center gap-1" aria-hidden="true">
                        <span class="w-1.5 h-1.5 rounded-full bg-[#8B231A] animate-bounce"></span>
                        <span class="w-1.5 h-1.5 rounded-full bg-[#8B231A] animate-bounce [animation-delay:0.2s]"></span>
                        <span class="w-1.5 h-1.5 rounded-full bg-[#8B231A] animate-bounce [animation-delay:0.4s]"></span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pertanyaan Cepat (Auto-hide saat user mulai mengetik atau percakapan sudah berjalan; dapat dibuka manual lewat tombol Topik) -->
        <div
            x-show="({{ $hasUserConversation ? 'false' : 'true' }} && draft.trim().length === 0) || showTopicsManual"
            x-transition:enter="transition ease-out duration-150"
            x-transition:enter-start="opacity-0 translate-y-1"
            x-transition:enter-end="opacity-100 translate-y-0"
            x-transition:leave="transition ease-in duration-100"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="px-3 py-2.5 bg-[#FAF7F2] border-t border-[#26221E]/10 shrink-0 select-none"
        >
            <div class="flex items-center justify-between mb-1.5">
                <span class="text-[10px] font-bold uppercase tracking-wider text-[#5A534A]">
                    Topik Populer:
                </span>
                <button
                    x-show="showTopicsManual"
                    type="button"
                    @click="showTopicsManual = false"
                    class="text-[10px] font-semibold text-[#5A534A] hover:text-[#8B231A] cursor-pointer"
                >
                    Sembunyikan
                </button>
            </div>
            <div class="flex flex-wrap gap-1.5 text-[11px]">
                @foreach ($quickPrompts as $idx => $prompt)
                    <button
                        wire:key="quick-prompt-{{ $idx }}"
                        type="button"
                        wire:click="askQuickPromptByIndex({{ $idx }})"
                        @click="showTopicsManual = false; draft = '';"
                        wire:loading.attr="disabled"
                        wire:target="sendMessage, askQuickPrompt, askQuickPromptByIndex"
                        class="px-2.5 py-1.5 rounded-xl bg-white hover:bg-[#8B231A]/10 text-[#26221E] hover:text-[#8B231A] border border-[#26221E]/15 text-left transition-colors cursor-pointer disabled:opacity-50"
                    >
                        {{ $prompt }}
                    </button>
                @endforeach
            </div>
        </div>

        <!-- Form Input Pesan -->
        <div class="p-3 bg-white border-t border-[#26221E]/10 shrink-0">
            <form
                wire:submit="sendMessage"
                @submit="draft = ''; showTopicsManual = false;"
                class="flex items-center gap-1.5 sm:gap-2"
            >
                <!-- Tombol Ringkas Munculkan Topik Populer (tampil saat panel topik sedang tersembunyi) -->
                <button
                    type="button"
                    @click="showTopicsManual = !showTopicsManual"
                    :aria-pressed="showTopicsManual.toString()"
                    title="Tampilkan atau sembunyikan Topik Populer"
                    aria-label="Tampilkan atau sembunyikan Topik Populer"
                    class="p-2.5 rounded-xl border transition-colors cursor-pointer shrink-0"
                    :class="showTopicsManual ? 'bg-[#2D4235] text-white border-[#2D4235]' : 'bg-[#FAF7F2] text-[#5A534A] hover:text-[#2D4235] border-[#26221E]/15'"
                >
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 18v-5.25m0 0a6.01 6.01 0 001.5-.189m-1.5.189a6.01 6.01 0 01-1.5-.189m3.75 7.478a12.06 12.06 0 01-4.5 0m3.75 2.383a14.406 14.406 0 01-3 0M14.25 18v-.192c0-.983.658-1.823 1.508-2.316a7.5 7.5 0 10-7.517 0c.85.493 1.509 1.333 1.509 2.316V18" />
                    </svg>
                </button>

                <label for="ai-consultant-question" class="sr-only">Tulis pertanyaan konsultasi adonan bakso</label>
                <input
                    id="ai-consultant-question"
                    name="question"
                    type="text"
                    wire:model="question"
                    @input="draft = $event.target.value; if (draft.trim().length > 0) { showTopicsManual = false; }"
                    placeholder="Tanyakan takaran resep atau tips selepan..."
                    class="flex-1 min-w-0 px-3.5 py-2.5 rounded-xl bg-[#FAF7F2] border border-[#26221E]/15 text-[#26221E] text-xs focus:outline-none focus:border-[#8B231A] focus:ring-1 focus:ring-[#8B231A] transition-all font-sans"
                    wire:loading.attr="disabled"
                    wire:target="sendMessage, askQuickPrompt, askQuickPromptByIndex"
                    maxlength="500"
                />

                <button
                    type="submit"
                    wire:loading.attr="disabled"
                    wire:target="sendMessage, askQuickPrompt, askQuickPromptByIndex"
                    aria-label="Kirim pesan konsultasi"
                    class="px-4 py-2.5 rounded-xl bg-[#8B231A] hover:bg-[#701A13] text-[#FAF7F2] font-bold text-xs shadow-md transition-all active:scale-95 cursor-pointer disabled:opacity-50 shrink-0 flex items-center justify-center gap-1"
                >
                    <span>Kirim</span>
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 12L3.269 3.126A59.768 59.768 0 0121.485 12 59.77 59.77 0 013.27 20.876L5.999 12zm0 0h7.5" />
                    </svg>
                </button>
            </form>
            @error('question')
                <span class="text-[#8B231A] text-[10px] mt-1 block font-semibold">{{ $message }}</span>
            @enderror
        </div>
    </div>
</div>
