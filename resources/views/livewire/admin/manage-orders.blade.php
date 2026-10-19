<main class="flex-1 flex flex-col min-w-0 overflow-y-auto">
    <!-- Top Bar & Header Halaman -->
    <header class="bg-white/80 backdrop-blur-md border-b border-[#161210]/10 px-6 sm:px-8 py-5 shrink-0">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <span class="text-xs font-bold uppercase tracking-wider text-[#C83B2B]">
                    Pelacakan & Layanan Pasar
                </span>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-[#161210] mt-0.5" style="font-family: 'Playfair Display', Georgia, serif;">
                    Manajemen Pesanan
                </h1>
                <p class="text-xs text-[#161210]/60 mt-1">
                    Pantau rincian pesanan bakso pelanggan dan perbarui status pengerjaan setelah konfirmasi melalui WhatsApp.
                </p>
            </div>
        </div>

        <!-- Filter Tab Status Pesanan -->
        <div class="flex items-center gap-2 mt-5 overflow-x-auto pb-1 text-xs">
            <button
                type="button"
                wire:click="$set('statusFilter', 'all')"
                class="px-3.5 py-1.5 rounded-lg font-semibold transition-colors cursor-pointer shrink-0 {{ $statusFilter === 'all' ? 'bg-[#161210] text-[#FAF6F0]' : 'bg-[#FAF6F0] text-[#161210]/70 hover:bg-white hover:text-[#161210] border border-[#161210]/10' }}"
            >
                Semua Pesanan ({{ $counts['all'] }})
            </button>
            <button
                type="button"
                wire:click="$set('statusFilter', 'pending')"
                class="px-3.5 py-1.5 rounded-lg font-semibold transition-colors cursor-pointer shrink-0 {{ $statusFilter === 'pending' ? 'bg-amber-600 text-white' : 'bg-amber-50 text-amber-800 hover:bg-amber-100 border border-amber-200' }}"
            >
                Menunggu ({{ $counts['pending'] }})
            </button>
            <button
                type="button"
                wire:click="$set('statusFilter', 'processing')"
                class="px-3.5 py-1.5 rounded-lg font-semibold transition-colors cursor-pointer shrink-0 {{ $statusFilter === 'processing' ? 'bg-blue-600 text-white' : 'bg-blue-50 text-blue-800 hover:bg-blue-100 border border-blue-200' }}"
            >
                Diproses ({{ $counts['processing'] }})
            </button>
            <button
                type="button"
                wire:click="$set('statusFilter', 'completed')"
                class="px-3.5 py-1.5 rounded-lg font-semibold transition-colors cursor-pointer shrink-0 {{ $statusFilter === 'completed' ? 'bg-emerald-600 text-white' : 'bg-emerald-50 text-emerald-800 hover:bg-emerald-100 border border-emerald-200' }}"
            >
                Selesai ({{ $counts['completed'] }})
            </button>
            <button
                type="button"
                wire:click="$set('statusFilter', 'cancelled')"
                class="px-3.5 py-1.5 rounded-lg font-semibold transition-colors cursor-pointer shrink-0 {{ $statusFilter === 'cancelled' ? 'bg-rose-600 text-white' : 'bg-rose-50 text-rose-800 hover:bg-rose-100 border border-rose-200' }}"
            >
                Dibatalkan ({{ $counts['cancelled'] }})
            </button>
        </div>
    </header>

    <!-- Konten Tabel Pesanan -->
    <div class="p-6 sm:p-8 space-y-6 flex-1">

        <!-- Flash Message Sukses -->
        @if (session()->has('success'))
            <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-2xl p-4 text-xs font-semibold flex items-center justify-between shadow-sm animate-fade-in">
                <div class="flex items-center gap-2.5">
                    <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span>{{ session('success') }}</span>
                </div>
                <button type="button" onclick="this.parentElement.remove()" class="text-emerald-500 hover:text-emerald-800 cursor-pointer">
                    &times;
                </button>
            </div>
        @endif

        <!-- Card Tabel Data -->
        <div class="bg-white rounded-3xl border border-[#161210]/10 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b border-[#161210]/10 bg-[#FAF6F0]/60 text-[11px] font-bold uppercase tracking-wider text-[#161210]/70">
                            <th scope="col" class="py-4 px-6">ID Pesanan</th>
                            <th scope="col" class="py-4 px-6">Waktu Pengambilan</th>
                            <th scope="col" class="py-4 px-6">Nama Pemesan</th>
                            <th scope="col" class="py-4 px-6">Mode Racikan</th>
                            <th scope="col" class="py-4 px-6 text-right">Total Estimasi</th>
                            <th scope="col" class="py-4 px-6 text-center">Status</th>
                            <th scope="col" class="py-4 px-6 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#161210]/5 text-xs font-medium text-[#161210]">
                        @forelse ($orders as $order)
                            <tr class="hover:bg-[#FAF6F0]/40 transition-colors">
                                <!-- Kolom 1: ID & Waktu Pemesanan -->
                                <td class="py-4 px-6">
                                    <span class="font-extrabold text-[#161210] font-mono text-xs">
                                        #{{ str_pad($order->id, 4, '0', STR_PAD_LEFT) }}
                                    </span>
                                    <span class="block text-[10px] text-[#161210]/50 mt-0.5">
                                        {{ $order->created_at ? $order->created_at->format('d/m/Y H:i') : '-' }}
                                    </span>
                                </td>

                                <!-- Kolom 2: Waktu Pengambilan -->
                                <td class="py-4 px-6">
                                    <div class="flex items-center gap-1.5 font-bold text-[#161210]">
                                        <svg class="w-3.5 h-3.5 text-[#C83B2B] shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        <span>
                                            {{ $order->pickup_at ? \Carbon\Carbon::parse($order->pickup_at)->format('d M Y, H:i') : '-' }}
                                        </span>
                                    </div>
                                    <span class="text-[10px] text-[#161210]/50 block pl-5">WIB (Pasar)</span>
                                </td>

                                <!-- Kolom 3: Nama Pemesan -->
                                <td class="py-4 px-6">
                                    <p class="font-bold text-sm text-[#161210]">
                                        {{ $order->customer_name }}
                                    </p>
                                    @if ($order->user)
                                        <span class="text-[10px] text-emerald-700 bg-emerald-50 px-1.5 py-0.5 rounded border border-emerald-200">
                                            Member Akun
                                        </span>
                                    @else
                                        <span class="text-[10px] text-[#161210]/50">
                                            Tamu WhatsApp
                                        </span>
                                    @endif
                                </td>

                                <!-- Kolom 4: Mode Racikan -->
                                <td class="py-4 px-6 space-y-1">
                                    <span class="inline-block px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider {{ $order->start_type === 'start_from_zero' ? 'bg-[#C83B2B]/10 text-[#C83B2B] border border-[#C83B2B]/20' : 'bg-amber-100 text-amber-800 border border-amber-200' }}">
                                        {{ $order->start_type === 'start_from_zero' ? 'Bahan Lengkap Toko' : 'Bawa Daging Sendiri' }}
                                    </span>
                                    <p class="text-[11px] text-[#161210]/70">
                                        {{ (int) $order->meat_weight }}g daging &bull; Racikan: <span class="capitalize font-semibold text-[#161210]">{{ $order->preference }}</span>
                                    </p>
                                </td>

                                <!-- Kolom 5: Total Estimasi -->
                                <td class="py-4 px-6 text-right font-extrabold text-[#C83B2B] text-sm whitespace-nowrap">
                                    {{ \Illuminate\Support\Number::currency($order->total_price, 'IDR', 'id', precision: 0) }}
                                    @if ((float) $order->grinding_fee > 0)
                                        <span class="block text-[10px] font-normal text-[#161210]/50">
                                            (Termasuk Giling: {{ \Illuminate\Support\Number::currency($order->grinding_fee, 'IDR', 'id', precision: 0) }})
                                        </span>
                                    @endif
                                </td>

                                <!-- Kolom 6: Status Pesanan (Interactive Dropdown Selector) -->
                                <td class="py-4 px-6 text-center">
                                    <div class="relative inline-block text-left">
                                        <select
                                            wire:change="updateStatus({{ $order->id }}, $event.target.value)"
                                            class="text-xs font-bold rounded-xl py-1.5 pl-3 pr-8 border cursor-pointer focus:outline-none transition-colors appearance-none {{ match($order->status) {
                                                'pending' => 'bg-amber-50 text-amber-800 border-amber-300 hover:bg-amber-100',
                                                'processing' => 'bg-blue-50 text-blue-800 border-blue-300 hover:bg-blue-100',
                                                'completed' => 'bg-emerald-50 text-emerald-800 border-emerald-300 hover:bg-emerald-100',
                                                'cancelled' => 'bg-rose-50 text-rose-800 border-rose-300 hover:bg-rose-100',
                                                default => 'bg-[#FAF6F0] text-[#161210] border-[#161210]/20'
                                            } }}"
                                        >
                                            <option value="pending" {{ $order->status === 'pending' ? 'selected' : '' }}>⏳ Menunggu</option>
                                            <option value="processing" {{ $order->status === 'processing' ? 'selected' : '' }}>⚙️ Diproses</option>
                                            <option value="completed" {{ $order->status === 'completed' ? 'selected' : '' }}>✅ Selesai</option>
                                            <option value="cancelled" {{ $order->status === 'cancelled' ? 'selected' : '' }}>❌ Dibatalkan</option>
                                        </select>
                                    </div>
                                </td>

                                <!-- Kolom 7: Aksi -->
                                <td class="py-4 px-6 text-right whitespace-nowrap">
                                    <button
                                        type="button"
                                        wire:click="viewOrder({{ $order->id }})"
                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-[#FAF6F0] hover:bg-[#161210] hover:text-white text-[#161210] font-semibold text-xs border border-[#161210]/10 transition-colors cursor-pointer"
                                    >
                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                        <span>Lihat Rincian</span>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="py-12 text-center text-xs text-[#161210]/50">
                                    Belum ada pesanan pada status ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Footer Tabel Informatif -->
            <div class="px-6 py-4 bg-[#FAF6F0]/40 border-t border-[#161210]/10 flex flex-col sm:flex-row items-center justify-between text-xs text-[#161210]/60 gap-2">
                <span>Menampilkan <strong>{{ $orders->count() }}</strong> pesanan.</span>
                <span class="italic text-[11px]">*Pembaruan status di sini membantu sinkronisasi pencatatan pesanan di kios pasar.</span>
            </div>
        </div>

    </div>

    <!-- MODAL RINCIAN PESANAN (DETAIL MODAL) -->
    @if ($showDetailModal && $selectedOrder)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 overflow-y-auto bg-[#161210]/80 backdrop-blur-sm animate-fade-in">
            <div
                class="bg-[#FAF6F0] rounded-2xl w-full max-w-2xl p-6 sm:p-8 shadow-2xl border border-[#161210]/10 relative z-10 max-h-[90vh] flex flex-col"
                @click.away="$wire.closeModal()"
            >
                <!-- Header Modal -->
                <div class="flex items-start justify-between pb-4 border-b border-[#161210]/10 shrink-0">
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="text-xs font-mono font-extrabold text-[#C83B2B] bg-[#C83B2B]/10 px-2 py-0.5 rounded border border-[#C83B2B]/20">
                                #{{ str_pad($selectedOrder->id, 4, '0', STR_PAD_LEFT) }}
                            </span>
                            <span class="text-[11px] font-bold uppercase tracking-wider px-2 py-0.5 rounded {{ match($selectedOrder->status) {
                                'pending' => 'bg-amber-100 text-amber-800',
                                'processing' => 'bg-blue-100 text-blue-800',
                                'completed' => 'bg-emerald-100 text-emerald-800',
                                'cancelled' => 'bg-rose-100 text-rose-800',
                                default => 'bg-gray-100 text-gray-800'
                            } }}">
                                {{ match($selectedOrder->status) {
                                    'pending' => 'Menunggu Konfirmasi',
                                    'processing' => 'Sedang Diproses',
                                    'completed' => 'Selesai',
                                    'cancelled' => 'Dibatalkan',
                                    default => $selectedOrder->status
                                } }}
                            </span>
                        </div>
                        <h2 class="text-xl sm:text-2xl font-bold text-[#161210] mt-1" style="font-family: 'Playfair Display', Georgia, serif;">
                            Pesanan: {{ $selectedOrder->customer_name }}
                        </h2>
                    </div>
                    <button
                        type="button"
                        wire:click="closeModal"
                        class="text-[#161210]/40 hover:text-[#161210] p-1.5 rounded-lg transition-colors cursor-pointer"
                    >
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <!-- Konten Body Scrollable -->
                <div class="mt-4 space-y-5 overflow-y-auto pr-1 flex-1">
                    
                    <!-- Kartu Informasi Pengambilan & Racikan -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs">
                        <div class="p-3.5 bg-white rounded-xl border border-[#161210]/10">
                            <span class="text-[10px] uppercase font-bold text-[#161210]/50 tracking-wider block">Waktu Pengambilan di Pasar</span>
                            <span class="font-extrabold text-[#161210] text-sm mt-0.5 block">
                                {{ $selectedOrder->pickup_at ? \Carbon\Carbon::parse($selectedOrder->pickup_at)->format('d M Y, H:i') : '-' }} WIB
                            </span>
                        </div>
                        <div class="p-3.5 bg-white rounded-xl border border-[#161210]/10">
                            <span class="text-[10px] uppercase font-bold text-[#161210]/50 tracking-wider block">Spesifikasi Racikan</span>
                            <span class="font-bold text-[#161210] mt-0.5 block">
                                {{ (int) $selectedOrder->meat_weight }}g Daging &bull; {{ ucfirst($selectedOrder->preference) }}
                            </span>
                            <span class="text-[10px] text-[#161210]/60 block mt-0.5">
                                Mode: {{ $selectedOrder->start_type === 'start_from_zero' ? 'Beli Semua Bahan di Pasar' : 'Bawa Daging Sendiri' }}
                            </span>
                        </div>
                    </div>

                    <!-- Snapshot Rincian Bahan yang Dipesan -->
                    <div>
                        <h3 class="text-xs font-bold uppercase tracking-wider text-[#161210] mb-2 flex items-center justify-between">
                            <span>Rincian Bahan Baku & Takaran</span>
                            <span class="text-[10px] text-[#161210]/50 normal-case font-normal">(Snapshot Harga Pesanan)</span>
                        </h3>
                        <div class="bg-white rounded-xl border border-[#161210]/10 overflow-hidden">
                            <table class="w-full text-left border-collapse text-xs">
                                <thead>
                                    <tr class="bg-[#FAF6F0]/70 border-b border-[#161210]/10 text-[10px] font-bold uppercase tracking-wider text-[#161210]/60">
                                        <th class="py-2.5 px-4">Nama Bahan</th>
                                        <th class="py-2.5 px-3 text-right">Takaran</th>
                                        <th class="py-2.5 px-3 text-right">Harga Satuan</th>
                                        <th class="py-2.5 px-4 text-right">Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-[#161210]/5">
                                    @forelse ($selectedOrder->orderItems as $item)
                                        <tr class="hover:bg-[#FAF6F0]/20">
                                            <td class="py-2.5 px-4 font-semibold text-[#161210]">
                                                {{ $item->material_name }}
                                            </td>
                                            <td class="py-2.5 px-3 text-right text-[#161210]/70 whitespace-nowrap">
                                                {{ (float) $item->quantity }} {{ $item->unit }}
                                            </td>
                                            <td class="py-2.5 px-3 text-right text-[#161210]/60 whitespace-nowrap">
                                                {{ \Illuminate\Support\Number::currency($item->unit_price, 'IDR', 'id', precision: 0) }}
                                            </td>
                                            <td class="py-2.5 px-4 text-right font-bold text-[#161210] whitespace-nowrap">
                                                {{ \Illuminate\Support\Number::currency($item->subtotal, 'IDR', 'id', precision: 0) }}
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="py-4 text-center text-xs text-[#161210]/50">
                                                Tidak ada rincian item bahan (Bawa daging sendiri tanpa tambahan bahan toko).
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Ringkasan Biaya Akhir -->
                    <div class="bg-white rounded-xl p-4 border border-[#161210]/10 space-y-2 text-xs">
                        <div class="flex justify-between text-[#161210]/70">
                            <span>Subtotal Bahan Baku Pasar:</span>
                            <span class="font-bold text-[#161210]">
                                {{ \Illuminate\Support\Number::currency($selectedOrder->orderItems->sum('subtotal'), 'IDR', 'id', precision: 0) }}
                            </span>
                        </div>
                        <div class="flex justify-between text-[#161210]/70">
                            <span>Biaya Jasa Giling Bakso:</span>
                            <span class="font-bold text-[#161210]">
                                {{ \Illuminate\Support\Number::currency($selectedOrder->grinding_fee, 'IDR', 'id', precision: 0) }}
                            </span>
                        </div>
                        <div class="pt-2 border-t border-[#161210]/10 flex justify-between items-center text-sm">
                            <span class="font-extrabold text-[#161210]">Total Tagihan (Grand Total):</span>
                            <span class="font-extrabold text-[#C83B2B] text-base" style="font-family: 'Playfair Display', Georgia, serif;">
                                {{ \Illuminate\Support\Number::currency($selectedOrder->total_price, 'IDR', 'id', precision: 0) }}
                            </span>
                        </div>
                    </div>

                    <!-- Pengubah Status Cepat di Dalam Modal -->
                    <div class="p-3.5 bg-[#FAF6F0] rounded-xl border border-[#161210]/10 flex flex-col sm:flex-row sm:items-center justify-between gap-3 text-xs">
                        <span class="font-bold text-[#161210]">Ubah Status Pesanan Ini:</span>
                        <div class="flex flex-wrap gap-1.5">
                            <button
                                type="button"
                                wire:click="updateStatus({{ $selectedOrder->id }}, 'pending')"
                                class="px-2.5 py-1 rounded-lg text-xs font-semibold cursor-pointer transition-colors {{ $selectedOrder->status === 'pending' ? 'bg-amber-600 text-white font-bold' : 'bg-white text-amber-800 border border-amber-300 hover:bg-amber-50' }}"
                            >
                                Menunggu
                            </button>
                            <button
                                type="button"
                                wire:click="updateStatus({{ $selectedOrder->id }}, 'processing')"
                                class="px-2.5 py-1 rounded-lg text-xs font-semibold cursor-pointer transition-colors {{ $selectedOrder->status === 'processing' ? 'bg-blue-600 text-white font-bold' : 'bg-white text-blue-800 border border-blue-300 hover:bg-blue-50' }}"
                            >
                                Diproses
                            </button>
                            <button
                                type="button"
                                wire:click="updateStatus({{ $selectedOrder->id }}, 'completed')"
                                class="px-2.5 py-1 rounded-lg text-xs font-semibold cursor-pointer transition-colors {{ $selectedOrder->status === 'completed' ? 'bg-emerald-600 text-white font-bold' : 'bg-white text-emerald-800 border border-emerald-300 hover:bg-emerald-50' }}"
                            >
                                Selesai
                            </button>
                            <button
                                type="button"
                                wire:click="updateStatus({{ $selectedOrder->id }}, 'cancelled')"
                                class="px-2.5 py-1 rounded-lg text-xs font-semibold cursor-pointer transition-colors {{ $selectedOrder->status === 'cancelled' ? 'bg-rose-600 text-white font-bold' : 'bg-white text-rose-800 border border-rose-300 hover:bg-rose-50' }}"
                            >
                                Dibatalkan
                            </button>
                        </div>
                    </div>

                </div>

                <!-- Footer Modal -->
                <div class="pt-4 mt-4 border-t border-[#161210]/10 flex justify-end shrink-0">
                    <button
                        type="button"
                        wire:click="closeModal"
                        class="px-5 py-2.5 rounded-xl bg-[#161210] hover:bg-[#161210]/90 text-white font-semibold text-xs transition-colors cursor-pointer"
                    >
                        Tutup Rincian
                    </button>
                </div>
            </div>
        </div>
    @endif
</main>
