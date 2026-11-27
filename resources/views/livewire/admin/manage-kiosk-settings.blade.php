<main class="flex-1 flex flex-col min-w-0 overflow-y-auto bg-[#FAF6F0]">
    <!-- Top Bar -->
    <header class="bg-white/80 backdrop-blur-md border-b border-[#161210]/10 px-6 sm:px-8 py-5 shrink-0">
        <div class="max-w-5xl mx-auto">
            <span class="text-xs font-bold uppercase tracking-wider text-[#C83B2B]">
                Operasional Los Pasar
            </span>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-[#161210] mt-0.5" style="font-family: 'Playfair Display', Georgia, serif;">
                Pengaturan Profil &amp; Tarif Kios
            </h1>
            <p class="text-xs text-[#161210]/60 mt-1">
                Ubah nomor WhatsApp resmi, tarif jasa selepan, dan jam operasional tanpa perlu menyentuh file <code>.env</code> server.
            </p>
        </div>
    </header>

    <div class="p-6 sm:p-8 flex-1">
        <div class="max-w-5xl mx-auto grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
            <!-- Form Pengaturan -->
            <section class="lg:col-span-7 bg-white rounded-3xl border border-[#161210]/10 p-6 sm:p-8 shadow-sm space-y-5">
                @if ($savedNotice)
                    <div role="status" class="rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-900 px-4 py-3 text-xs font-semibold">
                        {{ $savedNotice }}
                    </div>
                @endif

                <form wire:submit.prevent="save" class="space-y-5">
                    <div>
                        <label for="kiosk-wa-number" class="block text-xs font-bold uppercase tracking-wider text-[#161210] mb-1.5">
                            Nomor WhatsApp Resmi Kios <span class="text-[#C83B2B]">*</span>
                        </label>
                        <input
                            id="kiosk-wa-number"
                            type="tel"
                            wire:model="wa_number"
                            placeholder="6285122797969 atau 0851-2279-7969"
                            class="w-full rounded-xl border-2 border-[#161210]/10 bg-[#FAF6F0]/40 px-4 py-3 text-sm font-mono font-semibold text-[#161210] focus:outline-none focus:border-[#C83B2B]"
                        >
                        <p class="text-[11px] text-[#5A534A] mt-1">
                            Otomatis dikonversi ke format internasional <code>62...</code> untuk tombol checkout WhatsApp &amp; struk thermal.
                        </p>
                        @error('wa_number')
                            <p class="text-[11px] text-[#C83B2B] mt-1 font-semibold">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="kiosk-grinding-fee" class="block text-xs font-bold uppercase tracking-wider text-[#161210] mb-1.5">
                            Tarif Jasa Selepan per 1 Kg Daging (Rp) <span class="text-[#C83B2B]">*</span>
                        </label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-xs font-bold text-[#161210]/50">Rp</span>
                            <input
                                id="kiosk-grinding-fee"
                                type="text"
                                inputmode="numeric"
                                wire:model="grinding_fee_per_kg"
                                placeholder="15000"
                                class="w-full rounded-xl border-2 border-[#161210]/10 bg-[#FAF6F0]/40 pl-11 pr-14 py-3 text-sm font-mono font-bold text-[#161210] focus:outline-none focus:border-[#C83B2B]"
                            >
                            <span class="absolute inset-y-0 right-0 pr-4 flex items-center text-xs font-mono text-[#5A534A]">/ kg</span>
                        </div>
                        <p class="text-[11px] text-[#5A534A] mt-1">
                            Langsung memperbarui tarif di Kalkulator Racikan, banner Jasa Selepan Katalog, dan Konsultan AI.
                        </p>
                        @error('grinding_fee_per_kg')
                            <p class="text-[11px] text-[#C83B2B] mt-1 font-semibold">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="kiosk-open-hour" class="block text-xs font-bold uppercase tracking-wider text-[#161210] mb-1.5">
                                Jam Buka (WIB) <span class="text-[#C83B2B]">*</span>
                            </label>
                            <select
                                id="kiosk-open-hour"
                                wire:model="open_hour"
                                class="w-full rounded-xl border-2 border-[#161210]/10 bg-[#FAF6F0]/40 px-3.5 py-3 text-sm font-mono font-bold text-[#161210] focus:outline-none focus:border-[#C83B2B] cursor-pointer"
                            >
                                @for ($h = 0; $h <= 22; $h++)
                                    <option value="{{ $h }}">{{ sprintf('%02d.00 WIB', $h) }}</option>
                                @endfor
                            </select>
                            @error('open_hour')
                                <p class="text-[11px] text-[#C83B2B] mt-1 font-semibold">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="kiosk-close-hour" class="block text-xs font-bold uppercase tracking-wider text-[#161210] mb-1.5">
                                Jam Tutup (WIB) <span class="text-[#C83B2B]">*</span>
                            </label>
                            <select
                                id="kiosk-close-hour"
                                wire:model="close_hour"
                                class="w-full rounded-xl border-2 border-[#161210]/10 bg-[#FAF6F0]/40 px-3.5 py-3 text-sm font-mono font-bold text-[#161210] focus:outline-none focus:border-[#C83B2B] cursor-pointer"
                            >
                                @for ($h = 1; $h <= 23; $h++)
                                    <option value="{{ $h }}">{{ sprintf('%02d.00 WIB', $h) }}</option>
                                @endfor
                            </select>
                            @error('close_hour')
                                <p class="text-[11px] text-[#C83B2B] mt-1 font-semibold">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="pt-2 flex justify-end">
                        <button
                            type="submit"
                            wire:loading.attr="disabled"
                            wire:target="save"
                            class="px-6 py-3 rounded-xl bg-[#C83B2B] hover:bg-[#A92F21] text-white font-bold text-xs shadow-md shadow-[#C83B2B]/20 transition-all cursor-pointer disabled:opacity-60"
                        >
                            <span wire:loading.remove wire:target="save">Simpan Pengaturan Kios</span>
                            <span wire:loading wire:target="save">Menyimpan...</span>
                        </button>
                    </div>
                </form>
            </section>

            <!-- Pratinjau Aktif Publik -->
            <aside class="lg:col-span-5 bg-[#2D4235] text-[#F4EFE6] rounded-3xl p-6 sm:p-7 shadow-md space-y-5">
                <div>
                    <span class="text-[10.5px] font-mono uppercase tracking-widest text-[#D8E0D3]">Pratinjau Langsung</span>
                    <h2 class="text-xl font-bold text-white mt-1" style="font-family: 'Playfair Display', Georgia, serif;">
                        Tampilan di Sisi Pelanggan
                    </h2>
                </div>

                @php
                    $previewOpen = max(0, min(22, (int) $open_hour));
                    $previewClose = max(1, min(23, (int) $close_hour));
                    $previewFee = is_numeric(str_replace(['.', ','], '', (string) $grinding_fee_per_kg))
                        ? (float) str_replace(['.', ','], '', (string) $grinding_fee_per_kg)
                        : 15000.0;
                @endphp

                <div class="space-y-3 text-xs">
                    <div class="p-3.5 rounded-2xl bg-white/10 border border-white/10">
                        <span class="text-[10px] font-mono uppercase text-[#D8E0D3]/80 block">Jam Operasional &amp; Validasi Pickup</span>
                        <span class="text-sm font-bold text-white block mt-1">
                            Setiap Hari &bull; {{ sprintf('%02d.00 – %02d.00 WIB', $previewOpen, $previewClose) }}
                        </span>
                    </div>

                    <div class="p-3.5 rounded-2xl bg-white/10 border border-white/10">
                        <span class="text-[10px] font-mono uppercase text-[#D8E0D3]/80 block">Tarif Jasa Selepan</span>
                        <span class="text-sm font-mono font-bold text-white block mt-1">
                            Rp {{ number_format($previewFee, 0, ',', '.') }} / kg daging
                        </span>
                    </div>

                    <div class="p-3.5 rounded-2xl bg-white/10 border border-white/10">
                        <span class="text-[10px] font-mono uppercase text-[#D8E0D3]/80 block">Tujuan Checkout WhatsApp</span>
                        <span class="text-sm font-mono font-bold text-emerald-200 block mt-1 break-all">
                            https://wa.me/{{ \App\Support\CheckoutHelper::canonicalPhoneDigits((string) $wa_number) ?: '6285122797969' }}
                        </span>
                    </div>
                </div>
            </aside>
        </div>
    </div>
</main>
