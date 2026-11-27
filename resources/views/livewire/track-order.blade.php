<div class="min-h-screen flex flex-col justify-between text-[#26221E] bg-motif-kawung" @if ($order) wire:poll.15s @endif>
    <x-navbar />

    <!-- Editorial Deep Sage Header -->
    <div class="relative bg-[#2D4235] text-[#F4EFE6] pt-12 sm:pt-16 pb-14 px-4 sm:px-6 lg:px-12 border-b border-[#2D4235]/30">
        <div class="max-w-4xl mx-auto">
            <div class="flex items-center gap-2 mb-3">
                <span class="w-8 h-[2px] bg-[#D96B43]"></span>
                <span class="text-xs font-mono font-bold tracking-[0.2em] uppercase text-[#D8E0D3]">
                    Pelacakan Mandiri Tanpa Login
                </span>
            </div>
            <h1 class="text-3xl sm:text-5xl font-bold text-white tracking-tight leading-tight" style="font-family: 'Playfair Display', Georgia, serif;">
                Lacak Status Pesanan
            </h1>
            <p class="text-sm text-[#D8E0D3]/85 mt-3 leading-relaxed max-w-2xl">
                Masukkan nomor pesanan (<span class="font-mono text-white">#BSK-XXXXX</span>) dan nomor WhatsApp yang digunakan saat checkout untuk memantau progres penggilingan adonan Anda secara real-time.
            </p>
        </div>
    </div>

    <main class="flex-1 py-10 sm:py-14 px-4 sm:px-6 lg:px-12 max-w-4xl mx-auto w-full space-y-8">
        <!-- Form Pencarian Terverifikasi -->
        <div class="bg-[#FAF7F2] rounded-3xl border border-[#26221E]/10 p-6 sm:p-8 shadow-sm">
            <form wire:submit.prevent="searchOrder" class="grid grid-cols-1 md:grid-cols-12 gap-4 items-end">
                <div class="md:col-span-4">
                    <label for="track-order-code" class="block text-xs font-bold uppercase tracking-wider text-[#26221E] mb-1.5">
                        Nomor Pesanan
                    </label>
                    <input
                        id="track-order-code"
                        type="text"
                        wire:model="orderCode"
                        placeholder="#BSK-00001"
                        class="w-full rounded-2xl border-2 border-[#26221E]/15 bg-white px-4 py-3 text-sm font-mono font-semibold text-[#26221E] focus:outline-none focus:border-[#2D4235]"
                    >
                    @error('orderCode')
                        <p class="text-[11px] text-[#8B231A] mt-1 font-semibold">{{ $message }}</p>
                    @enderror
                </div>

                <div class="md:col-span-5">
                    <label for="track-phone-number" class="block text-xs font-bold uppercase tracking-wider text-[#26221E] mb-1.5">
                        Nomor WhatsApp Pemesan
                    </label>
                    <input
                        id="track-phone-number"
                        type="tel"
                        wire:model="phoneNumber"
                        placeholder="081234567890"
                        class="w-full rounded-2xl border-2 border-[#26221E]/15 bg-white px-4 py-3 text-sm font-mono font-semibold text-[#26221E] focus:outline-none focus:border-[#2D4235]"
                    >
                    @error('phoneNumber')
                        <p class="text-[11px] text-[#8B231A] mt-1 font-semibold">{{ $message }}</p>
                    @enderror
                </div>

                <div class="md:col-span-3">
                    <button
                        type="submit"
                        wire:loading.attr="disabled"
                        wire:target="searchOrder"
                        class="w-full py-3.5 px-5 rounded-2xl bg-[#B84E26] hover:bg-[#9E3F1C] text-white font-bold text-sm shadow-sm transition-all cursor-pointer disabled:opacity-60"
                    >
                        <span wire:loading.remove wire:target="searchOrder">Cek Status</span>
                        <span wire:loading wire:target="searchOrder">Mencari...</span>
                    </button>
                </div>
            </form>

            @if ($lookupError)
                <div role="alert" class="mt-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-900 p-4 text-xs font-semibold">
                    {{ $lookupError }}
                </div>
            @endif

            @if ($actionNotice)
                <div role="status" class="mt-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-900 p-4 text-xs font-semibold">
                    {{ $actionNotice }}
                </div>
            @endif
        </div>

        <!-- Hasil Pelacakan Pesanan -->
        @if ($order)
            @php
                $status = strtolower((string) $order->status);
                $isPending = in_array($status, ['pending', 'menunggu'], true);
                $isProcessing = in_array($status, ['processing', 'diproses'], true);
                $isCompleted = in_array($status, ['completed', 'selesai'], true);
                $isCancelled = in_array($status, ['cancelled', 'batal', 'dibatalkan'], true);
                $orderCodeLabel = '#BSK-'.str_pad((string) $order->id, 5, '0', STR_PAD_LEFT);
            @endphp

            <div class="bg-[#FAF7F2] rounded-3xl border border-[#26221E]/10 shadow-md overflow-hidden">
                <!-- Header Status -->
                <div class="p-6 sm:p-8 bg-[#2D4235] text-[#F4EFE6] flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div>
                        <div class="flex items-center gap-2.5">
                            <span class="font-mono text-xs font-bold px-2.5 py-1 rounded-lg bg-white/15 text-white">
                                {{ $orderCodeLabel }}
                            </span>
                            <span class="text-xs text-[#D8E0D3] flex items-center gap-1.5">
                                <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                                Auto-refresh 15 detik
                            </span>
                        </div>
                        <h2 class="text-2xl font-bold text-white mt-2" style="font-family: 'Playfair Display', Georgia, serif;">
                            Pesanan atas nama {{ $order->customer_name }}
                        </h2>
                        <p class="text-xs text-[#D8E0D3]/80 mt-1">
                            Jadwal Ambil: {{ $order->pickup_at ? $order->pickup_at->format('d M Y, H:i').' WIB' : '-' }}
                        </p>
                    </div>

                    <div class="flex items-center gap-2">
                        @if ($isPending)
                            <span class="px-3.5 py-1.5 rounded-full text-xs font-bold bg-amber-100 text-amber-900">
                                Menunggu Konfirmasi Kios
                            </span>
                        @elseif ($isProcessing)
                            <span class="px-3.5 py-1.5 rounded-full text-xs font-bold bg-blue-100 text-blue-900">
                                Sedang Digiling / Diproses
                            </span>
                        @elseif ($isCompleted)
                            <span class="px-3.5 py-1.5 rounded-full text-xs font-bold bg-emerald-100 text-emerald-900">
                                Selesai &bull; Siap Diambil
                            </span>
                        @elseif ($isCancelled)
                            <span class="px-3.5 py-1.5 rounded-full text-xs font-bold bg-rose-100 text-rose-900">
                                Pesanan Dibatalkan
                            </span>
                        @endif
                    </div>
                </div>

                <!-- Step Progress Bar -->
                @if (! $isCancelled)
                    <div class="px-5 sm:px-8 py-5 sm:py-6 bg-white border-b border-[#26221E]/10">
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-2.5 sm:gap-3 text-center">
                            <div class="p-3 rounded-2xl border {{ ($isPending || $isProcessing || $isCompleted) ? 'border-[#2D4235] bg-[#E8ECE4]/60 text-[#2D4235]' : 'border-[#26221E]/10 text-[#5A534A]' }}">
                                <span class="block text-[10px] font-mono font-bold uppercase">Tahap 1</span>
                                <span class="block text-xs font-bold mt-0.5">Konfirmasi Kios</span>
                            </div>
                            <div class="p-3 rounded-2xl border {{ ($isProcessing || $isCompleted) ? 'border-[#2D4235] bg-[#E8ECE4]/60 text-[#2D4235]' : 'border-[#26221E]/10 text-[#5A534A]' }}">
                                <span class="block text-[10px] font-mono font-bold uppercase">Tahap 2</span>
                                <span class="block text-xs font-bold mt-0.5">Proses Selepan</span>
                            </div>
                            <div class="p-3 rounded-2xl border {{ $isCompleted ? 'border-emerald-600 bg-emerald-50 text-emerald-900' : 'border-[#26221E]/10 text-[#5A534A]' }}">
                                <span class="block text-[10px] font-mono font-bold uppercase">Tahap 3</span>
                                <span class="block text-xs font-bold mt-0.5">Siap Diambil</span>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Rincian Item & Total -->
                <div class="p-6 sm:p-8 space-y-5">
                    <div class="bg-white rounded-2xl border border-[#26221E]/10 divide-y divide-[#26221E]/10 overflow-hidden">
                        @foreach ($order->orderItems as $item)
                            @php
                                $isOwnMeatTrack = $order->isOwnMeat() && (float) $item->subtotal <= 0;
                            @endphp
                            <div wire:key="track-item-{{ $item->id }}" class="px-4 py-3 flex items-center justify-between text-xs">
                                <div>
                                    <span class="font-bold text-[#26221E] block">{{ $item->material_name }}</span>
                                    <span class="text-[#5A534A] font-mono text-[11px]">{{ (float) $item->quantity }} {{ $item->unit }}</span>
                                </div>
                                <span class="font-mono font-bold tabular-nums {{ $isOwnMeatTrack ? 'text-emerald-700' : 'text-[#26221E]' }}">
                                    {{ $isOwnMeatTrack ? 'Bawa Sendiri' : 'Rp '.number_format($item->subtotal, 0, ',', '.') }}
                                </span>
                            </div>
                        @endforeach
                    </div>

                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pt-3 border-t border-[#26221E]/10">
                        <div>
                            <span class="text-[10px] uppercase font-mono text-[#5A534A] block">Total Estimasi Pembayaran di Kios</span>
                            <span class="text-2xl font-bold text-[#26221E] tabular-nums" style="font-family: 'Playfair Display', Georgia, serif;">
                                Rp {{ number_format($order->total_price, 0, ',', '.') }}
                            </span>
                        </div>
                        <div class="flex flex-wrap gap-2.5">
                            <a
                                href="https://wa.me/{{ config('services.kiosk.wa_number', '6285122797969') }}?text={{ urlencode('Halo Kios BASOKU, saya ingin menanyakan progres pesanan '.$orderCodeLabel.' atas nama '.$order->customer_name) }}"
                                target="_blank"
                                rel="noopener noreferrer"
                                class="inline-flex items-center gap-2 px-5 py-2.5 rounded-full bg-[#2D4235] hover:bg-[#1E2D24] text-white text-xs font-bold transition-colors"
                            >
                                Hubungi Kios via WhatsApp
                            </a>
                            @if ($isPending)
                                <button
                                    type="button"
                                    wire:click="cancelTrackedOrder"
                                    wire:confirm="Yakin ingin membatalkan pesanan {{ $orderCodeLabel }}? Pesanan yang dibatalkan tidak akan diproses oleh kios."
                                    class="px-4 py-2.5 rounded-full border border-rose-300 bg-rose-50 hover:bg-rose-700 hover:text-white text-xs font-bold text-rose-800 transition-colors cursor-pointer"
                                >
                                    Batalkan Pesanan
                                </button>
                            @endif
                            <button
                                type="button"
                                wire:click="resetSearch"
                                class="px-4 py-2.5 rounded-full border border-[#26221E]/20 bg-white hover:bg-[#EAE3D5] text-xs font-semibold text-[#26221E] cursor-pointer"
                            >
                                Cari Pesanan Lain
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </main>

    <x-footer />
</div>
