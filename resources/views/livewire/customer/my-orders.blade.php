<div class="min-h-screen flex flex-col justify-between text-[#26221E] bg-motif-kawung" wire:poll.15s>
    <!-- Navbar Publik -->
    <x-navbar />

    <!-- Editorial Deep Sage Header Banner (Seragam dengan Laboratorium Resep) -->
    <div class="relative bg-[#2D4235] text-[#F4EFE6] pt-12 sm:pt-16 pb-16 px-4 sm:px-6 lg:px-12 border-b border-[#2D4235]/30 overflow-hidden">
        <div class="max-w-7xl mx-auto relative z-10 flex flex-col md:flex-row md:items-end justify-between gap-6">
            <div class="max-w-2xl">
                <div class="flex flex-wrap items-center gap-3 mb-3">
                    <span class="w-8 h-[2px] bg-[#D96B43]"></span>
                    <span class="text-xs font-mono font-bold tracking-[0.2em] uppercase text-[#D8E0D3]">
                        Area Pelanggan
                    </span>
                    <span class="text-xs text-[#D8E0D3]/80 flex items-center gap-1.5">
                        <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                        Live Update Antrean (15 dtk)
                    </span>
                </div>
                <h1 class="text-3xl sm:text-5xl font-bold text-white tracking-tight leading-tight" style="font-family: 'Playfair Display', Georgia, serif;">
                    Pesanan & Antrean Saya
                </h1>
                <p class="text-sm text-[#D8E0D3]/85 mt-3 leading-relaxed font-normal">
                    Pantau status penggilingan daging dan kesiapan pesanan Anda di kios BASOKU secara real-time.
                </p>

                <!-- Tab Switcher: Laboratorium Resep, Riwayat Pesanan & Pengaturan Akun -->
                <div class="mt-4 inline-flex flex-wrap p-1 bg-white/10 rounded-xl border border-white/15 gap-0.5">
                    <a
                        href="{{ route('customer.recipes') }}"
                        class="px-3.5 py-1.5 rounded-lg text-xs font-semibold text-white/75 hover:text-white hover:bg-white/10 transition-all"
                    >
                        Laboratorium Resep
                    </a>
                    <a
                        href="{{ route('customer.orders') }}"
                        class="px-3.5 py-1.5 rounded-lg text-xs font-bold text-white bg-white/20 transition-all"
                    >
                        Riwayat Pesanan
                    </a>
                    <a
                        href="{{ route('account.settings') }}"
                        class="px-3.5 py-1.5 rounded-lg text-xs font-semibold text-white/75 hover:text-white hover:bg-white/10 transition-all"
                    >
                        Pengaturan Akun
                    </a>
                </div>
            </div>

            <!-- Tombol Buat Pesanan Baru -->
            <a
                href="{{ route('calculator') }}"
                class="inline-flex items-center justify-center gap-2.5 px-6 py-3.5 rounded-full bg-[#B84E26] hover:bg-[#9E3F1C] text-white font-semibold text-xs shadow-md transition-all duration-200 active:scale-[0.99] shrink-0"
            >
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                </svg>
                <span>Pesan Racikan Baru</span>
            </a>
        </div>
    </div>

    <!-- Area Konten Utama -->
    <main class="flex-1 py-10 sm:py-12 px-4 sm:px-6 lg:px-12 max-w-7xl mx-auto w-full">
        @if (session()->has('order_notice'))
            <div role="status" class="mb-6 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-900 px-4 py-3 text-xs font-semibold">
                {{ session('order_notice') }}
            </div>
        @endif

        @if (session()->has('order_error'))
            <div role="alert" class="mb-6 rounded-2xl bg-rose-50 border border-rose-200 text-rose-900 px-4 py-3 text-xs font-semibold">
                {{ session('order_error') }}
            </div>
        @endif

        <!-- Filter Status Tabs -->
        <div class="flex items-center gap-2 overflow-x-auto pb-2 scrollbar-none">
            <button
                type="button"
                wire:click="filterByStatus('all')"
                class="px-4 py-2 rounded-full text-xs font-bold transition-all shrink-0 cursor-pointer {{ $statusFilter === 'all' ? 'bg-[#2D4235] text-white shadow-sm' : 'bg-[#FAF7F2] border border-[#26221E]/15 text-[#26221E]/80 hover:bg-white' }}"
            >
                Semua ({{ $counts['all'] }})
            </button>
            <button
                type="button"
                wire:click="filterByStatus('pending')"
                class="px-4 py-2 rounded-full text-xs font-bold transition-all shrink-0 cursor-pointer {{ $statusFilter === 'pending' ? 'bg-amber-700 text-white shadow-sm' : 'bg-[#FAF7F2] border border-amber-300 text-amber-900 hover:bg-amber-50' }}"
            >
                Menunggu ({{ $counts['pending'] }})
            </button>
            <button
                type="button"
                wire:click="filterByStatus('processing')"
                class="px-4 py-2 rounded-full text-xs font-bold transition-all shrink-0 cursor-pointer {{ $statusFilter === 'processing' ? 'bg-blue-700 text-white shadow-sm' : 'bg-[#FAF7F2] border border-blue-300 text-blue-900 hover:bg-blue-50' }}"
            >
                Diproses ({{ $counts['processing'] }})
            </button>
            <button
                type="button"
                wire:click="filterByStatus('completed')"
                class="px-4 py-2 rounded-full text-xs font-bold transition-all shrink-0 cursor-pointer {{ $statusFilter === 'completed' ? 'bg-emerald-700 text-white shadow-sm' : 'bg-[#FAF7F2] border border-emerald-300 text-emerald-900 hover:bg-emerald-50' }}"
            >
                Selesai ({{ $counts['completed'] }})
            </button>
            <button
                type="button"
                wire:click="filterByStatus('cancelled')"
                class="px-4 py-2 rounded-full text-xs font-bold transition-all shrink-0 cursor-pointer {{ $statusFilter === 'cancelled' ? 'bg-rose-700 text-white shadow-sm' : 'bg-[#FAF7F2] border border-rose-300 text-rose-900 hover:bg-rose-50' }}"
            >
                Dibatalkan ({{ $counts['cancelled'] }})
            </button>
        </div>

        <!-- Daftar Kartu Pesanan -->
        <div class="mt-6 space-y-4">
            @forelse ($orders as $order)
                @php
                    $isPending = in_array(strtolower((string) $order->status), ['pending', 'menunggu'], true);
                    $isProcessing = in_array(strtolower((string) $order->status), ['processing', 'diproses'], true);
                    $isCompleted = in_array(strtolower((string) $order->status), ['completed', 'selesai'], true);
                    $isCancelled = in_array(strtolower((string) $order->status), ['cancelled', 'batal', 'dibatalkan'], true);
                    $orderCode = '#BSK-'.str_pad((string) $order->id, 5, '0', STR_PAD_LEFT);
                @endphp

                <div wire:key="customer-order-{{ $order->id }}" class="bg-[#FAF7F2] rounded-3xl border border-[#26221E]/10 shadow-xs overflow-hidden transition-all hover:shadow-md">
                    <!-- Header Kartu -->
                    <div class="p-5 sm:p-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-[#26221E]/10 bg-white/70">
                        <div class="flex flex-wrap items-center gap-3">
                            <span class="font-mono text-xs font-bold px-3 py-1 rounded-lg bg-[#2D4235] text-[#F4EFE6]">
                                {{ $orderCode }}
                            </span>
                            <span class="text-xs text-[#5A534A] font-medium">
                                {{ $order->created_at ? $order->created_at->translatedFormat('d M Y, H:i').' WIB' : '-' }}
                            </span>
                        </div>

                        <!-- Status Badge -->
                        <div>
                            @if ($isPending)
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-amber-50 text-amber-900 border border-amber-200">
                                    <span class="w-2 h-2 rounded-full bg-amber-500 animate-pulse"></span>
                                    Menunggu Konfirmasi Kios
                                </span>
                            @elseif ($isProcessing)
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-blue-50 text-blue-900 border border-blue-200">
                                    <span class="w-2 h-2 rounded-full bg-blue-500 animate-pulse"></span>
                                    Sedang Digiling / Diproses
                                </span>
                            @elseif ($isCompleted)
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-900 border border-emerald-200">
                                    <svg class="w-3.5 h-3.5 text-emerald-700" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                    </svg>
                                    Selesai &bull; Siap Diambil
                                </span>
                            @elseif ($isCancelled)
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-rose-50 text-rose-900 border border-rose-200">
                                    Pesanan Dibatalkan
                                </span>
                            @endif
                        </div>
                    </div>

                    <!-- Isi Ringkasan Pesanan -->
                    <div class="p-5 sm:p-6 grid grid-cols-1 md:grid-cols-4 gap-5 items-center">
                        <!-- Info Mode & Racikan -->
                        <div class="space-y-1">
                            <span class="text-[10px] uppercase font-mono font-bold tracking-wider text-[#5A534A] block">Mode &amp; Racikan</span>
                            <div class="font-bold text-sm text-[#26221E]">
                                {{ match($order->start_type) {
                                    'start_from_zero' => 'Bahan Lengkap Toko',
                                    'ingredients_only' => 'Beli Bahan Saja (Katalog)',
                                    default => 'Bawa Daging Sendiri'
                                } }}
                            </div>
                            <div class="text-xs text-[#5A534A]">
                                @if ($order->start_type === 'ingredients_only')
                                    Retail Pasar Langsung
                                @else
                                    {{ (int) $order->meat_weight }}g daging &bull; <span class="capitalize font-medium text-[#26221E]">{{ str_replace('_', ' ', $order->preference) }}</span>
                                @endif
                            </div>
                        </div>

                        <!-- Jadwal Pickup -->
                        <div class="space-y-1">
                            <span class="text-[10px] uppercase font-mono font-bold tracking-wider text-[#5A534A] block">Jadwal Ambil (Pickup)</span>
                            <div class="font-bold text-sm text-[#26221E] flex items-center gap-1.5">
                                <svg class="w-4 h-4 text-[#B84E26]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                {{ $order->pickup_at ? $order->pickup_at->translatedFormat('d M, H:i').' WIB' : '-' }}
                            </div>
                            <span class="text-[11px] text-[#5A534A] block">
                                Kios Pasar ({{ sprintf('%02d.00–%02d.00', config('services.kiosk.open_hour', 4), config('services.kiosk.close_hour', 12)) }})
                            </span>
                        </div>

                        <!-- Total Tagihan -->
                        <div class="space-y-1">
                            <span class="text-[10px] uppercase font-mono font-bold tracking-wider text-[#5A534A] block">Total Pembayaran</span>
                            <div class="text-lg font-bold text-[#B84E26] tabular-nums" style="font-family: 'Playfair Display', Georgia, serif;">
                                Rp {{ number_format($order->total_price, 0, ',', '.') }}
                            </div>
                            <span class="text-[10px] text-[#5A534A] block">Bayar tunai di kios</span>
                        </div>

                        <!-- Tombol Aksi & Tanya WA -->
                        <div class="flex flex-wrap items-center md:justify-end gap-2 pt-3 md:pt-0 border-t md:border-t-0 border-[#26221E]/10">
                            <button
                                type="button"
                                wire:click="selectOrder({{ $order->id }})"
                                class="px-4 py-2 rounded-full text-xs font-bold border border-[#26221E]/20 bg-white hover:bg-[#2D4235] hover:text-white text-[#26221E] transition-all cursor-pointer"
                            >
                                {{ $selectedOrderId === $order->id ? 'Tutup Rincian' : 'Lihat Rincian' }}
                            </button>

                            @if ($isPending)
                                <button
                                    type="button"
                                    wire:click="cancelOrder({{ $order->id }})"
                                    wire:confirm="Yakin ingin membatalkan pesanan {{ $orderCode }}? Pesanan yang dibatalkan tidak akan diproses oleh kios."
                                    class="px-3.5 py-2 rounded-full text-xs font-bold border border-rose-300 bg-rose-50 hover:bg-rose-700 hover:text-white text-rose-800 transition-all cursor-pointer"
                                >
                                    Batalkan
                                </button>
                            @endif

                            <a
                                href="https://wa.me/{{ config('services.kiosk.wa_number', '6285122797969') }}?text={{ urlencode('Halo Kios BASOKU, saya mau konfirmasi pesanan '.$orderCode.' atas nama '.$order->customer_name) }}"
                                target="_blank"
                                rel="noopener noreferrer"
                                aria-label="Chat Kios via WhatsApp untuk pesanan {{ $orderCode }}"
                                class="inline-flex items-center justify-center min-w-[38px] min-h-[38px] p-2 rounded-full bg-emerald-50 text-emerald-800 border border-emerald-200 hover:bg-emerald-100 transition-all"
                                title="Chat Kios via WhatsApp"
                            >
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981z"/>
                                </svg>
                            </a>
                        </div>
                    </div>

                    <!-- Accordion Rincian Item Bahan -->
                    @if ($selectedOrderId === $order->id)
                        <div class="px-5 sm:px-6 pb-6 pt-4 bg-white/80 border-t border-[#26221E]/10 space-y-4">
                            <div class="flex flex-wrap items-center justify-between gap-2">
                                <span class="text-xs font-mono font-bold uppercase tracking-wider text-[#5A534A]">Daftar Bahan &amp; Komponen Racikan:</span>
                                @if ((float) $order->grinding_fee > 0)
                                    <span class="text-xs font-semibold text-[#26221E]">
                                        Jasa Selepan: <span class="font-mono font-bold">Rp {{ number_format($order->grinding_fee, 0, ',', '.') }}</span>
                                    </span>
                                @endif
                            </div>

                            @php
                                $visibleOrderItems = (float) $order->grinding_fee > 0
                                    ? $order->orderItems->reject(fn ($item) => str_contains(strtolower((string) $item->material_name), 'selepan'))
                                    : $order->orderItems;
                            @endphp
                            <div class="divide-y divide-[#26221E]/10 border border-[#26221E]/10 rounded-2xl overflow-hidden bg-white">
                                @forelse ($visibleOrderItems as $item)
                                    @php
                                        $isOwnMeatItem = $order->isOwnMeat() && (float) $item->subtotal <= 0;
                                    @endphp
                                    <div wire:key="order-item-{{ $item->id }}" class="p-3.5 flex items-center justify-between text-xs">
                                        <div class="space-y-0.5">
                                            <span class="font-bold text-[#26221E]">{{ $item->material_name }}</span>
                                            <span class="text-[#5A534A] block text-[11px] font-mono">
                                                @if ($isOwnMeatItem)
                                                    {{ (float) $item->quantity }} {{ $item->unit }} &bull; Daging milik pelanggan
                                                @else
                                                    {{ (float) $item->quantity }} {{ $item->unit }} &times; Rp {{ number_format($item->unit_price, 0, ',', '.') }}
                                                @endif
                                            </span>
                                        </div>
                                        <span class="font-mono font-bold tabular-nums {{ $isOwnMeatItem ? 'text-emerald-700' : 'text-[#26221E]' }}">
                                            {{ $isOwnMeatItem ? 'Bawa Sendiri' : 'Rp '.number_format($item->subtotal, 0, ',', '.') }}
                                        </span>
                                    </div>
                                @empty
                                    <div class="p-4 text-center text-xs text-[#5A534A] italic">
                                        Rincian bahan digiling langsung di kios.
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    @endif
                </div>
            @empty
                <!-- State Kosong (Seragam dengan Laboratorium Resep) -->
                <div class="bg-[#FAF7F2] rounded-3xl p-10 sm:p-16 border border-[#26221E]/10 text-center max-w-lg mx-auto shadow-sm">
                    <div class="w-16 h-16 rounded-full bg-[#2D4235] text-[#F4EFE6] font-serif font-bold text-2xl mx-auto flex items-center justify-center mb-4">
                        B.
                    </div>
                    <h2 class="text-2xl font-bold text-[#26221E]" style="font-family: 'Playfair Display', Georgia, serif;">
                        Belum Ada Riwayat Pesanan
                    </h2>
                    <p class="text-xs text-[#5A534A] mt-2 leading-relaxed">
                        Anda belum memiliki pesanan aktif di kios BASOKU. Buat racikan bakso atau pilih bahan di katalog untuk memesan.
                    </p>
                    <a
                        href="{{ route('calculator') }}"
                        class="mt-6 inline-flex items-center gap-2 px-6 py-3 rounded-full bg-[#B84E26] hover:bg-[#9E3F1C] text-white font-bold text-xs shadow-md transition-all active:scale-[0.98]"
                    >
                        <span>Mulai Buat Pesanan Baru</span>
                    </a>
                </div>
            @endforelse

            <div class="mt-6">
                {{ $orders->links() }}
            </div>
        </div>
    </main>

    <!-- Footer Publik -->
    <x-footer />
</div>
