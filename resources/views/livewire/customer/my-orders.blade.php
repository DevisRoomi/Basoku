<div class="min-h-screen bg-[#FFFDF9] py-10 sm:py-16" wire:poll.15s>
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">

        <!-- Header Navigasi Akun / Tab Menu -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-8 border-b border-[#161210]/10">
            <div>
                <div class="flex items-center gap-3">
                    <span class="px-2.5 py-1 rounded-full text-[11px] font-bold tracking-wider uppercase bg-[#C83B2B]/10 text-[#C83B2B] border border-[#C83B2B]/20">
                        Area Pelanggan
                    </span>
                    <span class="text-xs text-[#161210]/60 flex items-center gap-1.5">
                        <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                        Live Update Antrean (15 dtk)
                    </span>
                </div>
                <h1 class="text-2xl sm:text-3xl font-serif font-black text-[#161210] tracking-tight mt-1.5">
                    Pesanan & Antrean Saya
                </h1>
                <p class="text-sm text-[#161210]/70 mt-1">
                    Pantau status penggilingan daging dan kesiapan pesanan Anda di kios BASOKU secara real-time.
                </p>
            </div>

            <!-- Tab Switcher: Resep vs Pesanan -->
            <div class="inline-flex p-1 bg-[#161210]/5 rounded-xl border border-[#161210]/10 shrink-0 self-start sm:self-auto">
                <a
                    href="{{ route('customer.recipes') }}"
                    class="px-4 py-2 rounded-lg text-xs font-semibold text-[#161210]/70 hover:text-[#161210] hover:bg-white/60 transition-all"
                >
                    Laboratorium Resep
                </a>
                <a
                    href="{{ route('customer.orders') }}"
                    class="px-4 py-2 rounded-lg text-xs font-bold text-[#161210] bg-white shadow-sm border border-[#161210]/10 transition-all"
                >
                    Riwayat Pesanan
                </a>
            </div>
        </div>

        <!-- Filter Status & Pencarian -->
        <div class="mt-8 flex flex-col md:flex-row md:items-center justify-between gap-4">
            <!-- Filter Status Tabs -->
            <div class="flex items-center gap-2 overflow-x-auto pb-2 md:pb-0 scrollbar-none">
                <button
                    wire:click="filterByStatus('all')"
                    class="px-3.5 py-1.5 rounded-full text-xs font-bold transition-all shrink-0 {{ $statusFilter === 'all' ? 'bg-[#161210] text-white shadow-sm' : 'bg-white border border-[#161210]/15 text-[#161210]/70 hover:bg-[#161210]/5' }}"
                >
                    Semua ({{ $counts['all'] }})
                </button>
                <button
                    wire:click="filterByStatus('pending')"
                    class="px-3.5 py-1.5 rounded-full text-xs font-bold transition-all shrink-0 {{ $statusFilter === 'pending' ? 'bg-amber-600 text-white shadow-sm' : 'bg-white border border-amber-200 text-amber-800 hover:bg-amber-50' }}"
                >
                    Menunggu ({{ $counts['pending'] }})
                </button>
                <button
                    wire:click="filterByStatus('processing')"
                    class="px-3.5 py-1.5 rounded-full text-xs font-bold transition-all shrink-0 {{ $statusFilter === 'processing' ? 'bg-blue-600 text-white shadow-sm' : 'bg-white border border-blue-200 text-blue-800 hover:bg-blue-50' }}"
                >
                    Diproses ({{ $counts['processing'] }})
                </button>
                <button
                    wire:click="filterByStatus('completed')"
                    class="px-3.5 py-1.5 rounded-full text-xs font-bold transition-all shrink-0 {{ $statusFilter === 'completed' ? 'bg-emerald-600 text-white shadow-sm' : 'bg-white border border-emerald-200 text-emerald-800 hover:bg-emerald-50' }}"
                >
                    Selesai ({{ $counts['completed'] }})
                </button>
            </div>

            <!-- Tombol Buat Pesanan Baru -->
            <a
                href="{{ route('calculator') }}"
                class="inline-flex items-center justify-center gap-2 px-5 py-2 rounded-full bg-[#C83B2B] hover:bg-[#A82B1D] text-white text-xs font-bold shadow-sm transition-all active:scale-[0.98] shrink-0"
            >
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                </svg>
                <span>Pesan Racikan Baru</span>
            </a>
        </div>

        <!-- Daftar Kartu Pesanan -->
        <div class="mt-8 space-y-4">
            @forelse ($orders as $order)
                @php
                    $isPending = in_array(strtolower($order->status), ['pending', 'menunggu']);
                    $isProcessing = in_array(strtolower($order->status), ['processing', 'diproses']);
                    $isCompleted = in_array(strtolower($order->status), ['completed', 'selesai']);
                    $isCancelled = in_array(strtolower($order->status), ['cancelled', 'batal']);
                @endphp

                <div class="bg-white rounded-2xl border border-[#161210]/10 shadow-sm overflow-hidden transition-all hover:border-[#161210]/25">
                    <!-- Header Kartu -->
                    <div class="p-5 sm:p-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-[#161210]/5 bg-[#FFFDF9]/60">
                        <div class="flex items-center gap-3">
                            <span class="font-mono text-xs font-bold px-2.5 py-1 rounded bg-[#161210]/5 text-[#161210] border border-[#161210]/10">
                                #BSK-{{ str_pad($order->id, 5, '0', STR_PAD_LEFT) }}
                            </span>
                            <span class="text-xs text-[#161210]/50 font-medium">
                                {{ $order->created_at->translatedFormat('d M Y, H:i') }} WIB
                            </span>
                        </div>

                        <!-- Status Badge -->
                        <div>
                            @if ($isPending)
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-amber-50 text-amber-800 border border-amber-200">
                                    <span class="w-2 h-2 rounded-full bg-amber-500 animate-pulse"></span>
                                    Menunggu Konfirmasi Kios
                                </span>
                            @elseif ($isProcessing)
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-blue-50 text-blue-800 border border-blue-200">
                                    <span class="w-2 h-2 rounded-full bg-blue-500 animate-pulse"></span>
                                    Sedang Digiling / Diproses
                                </span>
                            @elseif ($isCompleted)
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-800 border border-emerald-200">
                                    <svg class="w-3.5 h-3.5 text-emerald-600" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                    </svg>
                                    Selesai &bull; Siap Diambil
                                </span>
                            @elseif ($isCancelled)
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-rose-50 text-rose-800 border border-rose-200">
                                    Pesanan Dibatalkan
                                </span>
                            @endif
                        </div>
                    </div>

                    <!-- Isi Ringkasan Pesanan -->
                    <div class="p-5 sm:p-6 grid grid-cols-1 md:grid-cols-4 gap-5 items-center">
                        <!-- Info Mode & Racikan -->
                        <div class="space-y-1">
                            <span class="text-[10px] uppercase font-bold tracking-wider text-[#161210]/50 block">Mode & Racikan</span>
                            <div class="font-bold text-sm text-[#161210]">
                                {{ match($order->start_type) {
                                    'start_from_zero' => 'Bahan Lengkap Toko',
                                    'ingredients_only' => 'Beli Bahan Saja (Katalog)',
                                    default => 'Bawa Daging Sendiri'
                                } }}
                            </div>
                            <div class="text-xs text-[#161210]/70">
                                @if ($order->start_type === 'ingredients_only')
                                    Retail Pasar Langsung
                                @else
                                    {{ (int) $order->meat_weight }}g daging &bull; <span class="capitalize font-medium">{{ str_replace('_', ' ', $order->preference) }}</span>
                                @endif
                            </div>
                        </div>

                        <!-- Jadwal Pickup -->
                        <div class="space-y-1">
                            <span class="text-[10px] uppercase font-bold tracking-wider text-[#161210]/50 block">Jadwal Ambil (Pickup)</span>
                            <div class="font-bold text-sm text-[#161210] flex items-center gap-1.5">
                                <svg class="w-4 h-4 text-[#C83B2B]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                {{ $order->pickup_at ? $order->pickup_at->translatedFormat('d M, H:i').' WIB' : '-' }}
                            </div>
                            <span class="text-[11px] text-[#161210]/60 block">Kios Pasar (05.00–12.00)</span>
                        </div>

                        <!-- Total Tagihan -->
                        <div class="space-y-1">
                            <span class="text-[10px] uppercase font-bold tracking-wider text-[#161210]/50 block">Total Pembayaran</span>
                            <div class="text-base font-black font-sans text-[#161210] tabular-nums">
                                Rp {{ number_format($order->total_price, 0, ',', '.') }}
                            </div>
                            <span class="text-[10px] text-[#161210]/60 block">Bayar tunai di kios</span>
                        </div>

                        <!-- Tombol Aksi & Tanya WA -->
                        <div class="flex items-center md:justify-end gap-2.5 pt-3 md:pt-0 border-t md:border-t-0 border-[#161210]/5">
                            <button
                                wire:click="selectOrder({{ $order->id }})"
                                class="px-4 py-2 rounded-xl text-xs font-bold border border-[#161210]/15 hover:bg-[#161210]/5 text-[#161210] transition-all"
                            >
                                {{ $selectedOrderId === $order->id ? 'Tutup Rincian' : 'Lihat Rincian' }}
                            </button>

                            <a
                                href="https://wa.me/{{ config('services.kiosk.wa_number', '6285122797969') }}?text={{ urlencode('Halo Kios BASOKU, saya mau konfirmasi pesanan #BSK-'.str_pad($order->id, 5, '0', STR_PAD_LEFT).' atas nama '.$order->customer_name) }}"
                                target="_blank"
                                class="inline-flex items-center justify-center p-2 rounded-xl bg-emerald-50 text-emerald-700 border border-emerald-200 hover:bg-emerald-100 transition-all"
                                title="Chat Kios via WhatsApp"
                            >
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981z"/>
                                </svg>
                            </a>
                        </div>
                    </div>

                    <!-- Accordion Rincian Item Bahan -->
                    @if ($selectedOrderId === $order->id)
                        <div class="px-5 sm:px-6 pb-6 pt-2 bg-[#FFFDF9] border-t border-[#161210]/10 space-y-4">
                            <div class="flex items-center justify-between">
                                <span class="text-xs font-bold uppercase tracking-wider text-[#161210]/60">Daftar Bahan & Komponen Racikan:</span>
                                @if ((float) $order->grinding_fee > 0)
                                    <span class="text-xs font-semibold text-[#161210]">
                                        Jasa Selepan: <span class="font-sans font-bold">Rp {{ number_format($order->grinding_fee, 0, ',', '.') }}</span>
                                    </span>
                                @endif
                            </div>

                            <div class="divide-y divide-[#161210]/5 border border-[#161210]/10 rounded-xl overflow-hidden bg-white">
                                @forelse ($order->orderItems as $item)
                                    <div class="p-3.5 flex items-center justify-between text-xs">
                                        <div class="space-y-0.5">
                                            <span class="font-bold text-[#161210]">{{ $item->material_name }}</span>
                                            <span class="text-[#161210]/60 block text-[11px]">
                                                {{ (float) $item->quantity }} {{ $item->unit }} @ Rp {{ number_format($item->unit_price, 0, ',', '.') }}
                                            </span>
                                        </div>
                                        <span class="font-mono font-bold text-[#161210] tabular-nums">
                                            Rp {{ number_format($item->subtotal, 0, ',', '.') }}
                                        </span>
                                    </div>
                                @empty
                                    <div class="p-4 text-center text-xs text-[#161210]/50 italic">
                                        Rincian bahan digiling langsung di kios.
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    @endif
                </div>
            @empty
                <!-- State Kosong -->
                <div class="py-16 px-6 text-center bg-white rounded-3xl border border-dashed border-[#161210]/20">
                    <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-[#161210]/5 flex items-center justify-center text-[#161210]/40">
                        <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                        </svg>
                    </div>
                    <h3 class="font-serif text-lg font-bold text-[#161210]">Belum Ada Riwayat Pesanan</h3>
                    <p class="text-xs text-[#161210]/60 max-w-sm mx-auto mt-1 mb-6">
                        Anda belum memiliki pesanan aktif di kios BASOKU. Buat racikan bakso atau pilih bahan di katalog untuk memesan.
                    </p>
                    <a
                        href="{{ route('calculator') }}"
                        class="inline-flex items-center gap-2 px-6 py-3 rounded-full bg-[#C83B2B] hover:bg-[#A82B1D] text-white text-xs font-bold shadow-md transition-all active:scale-[0.98]"
                    >
                        <span>Mulai Buat Pesanan Baru</span>
                    </a>
                </div>
            @endforelse

            <div class="mt-6">
                {{ $orders->links() }}
            </div>
        </div>

    </div>
</div>
