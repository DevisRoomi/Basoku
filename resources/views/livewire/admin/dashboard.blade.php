<main class="flex-1 flex flex-col min-w-0 overflow-y-auto bg-[#F4EFE6]">
    <!-- Top Bar -->
    <header class="bg-[#FAF7F2]/90 backdrop-blur-md border-b border-[#26221E]/10 px-6 sm:px-8 py-4 flex items-center justify-between shrink-0 sticky top-0 z-20">
        <div>
            <span class="text-[11px] font-bold uppercase tracking-wider text-[#D96B43]">
                Panel Pengelola Pasar
            </span>
            <h1 class="text-lg sm:text-2xl font-bold text-[#26221E]" style="font-family: 'Playfair Display', Georgia, serif;">
                Selamat Datang di Dasbor Admin BASOKU
            </h1>
        </div>
        <div class="flex items-center gap-3">
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-[#2D4235]/10 text-[#2D4235] border border-[#2D4235]/20">
                <span class="w-2 h-2 rounded-full bg-[#2D4235] animate-pulse"></span>
                Sesi Aktif
            </span>
            <a
                href="{{ route('admin.orders') }}"
                wire:navigate
                class="hidden sm:inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-xl text-xs font-semibold bg-[#8B231A] text-[#FAF7F2] hover:bg-[#701A13] transition-colors shadow-sm"
            >
                <span>Kelola Pesanan</span>
                &rarr;
            </a>
        </div>
    </header>

    <!-- Wadah Konten Dinamis -->
    <div class="p-6 sm:p-8 space-y-8 flex-1">
        <!-- Banner Sambutan Editorial -->
        <div class="bg-[#FAF7F2] rounded-3xl p-6 sm:p-8 border border-[#26221E]/10 shadow-sm relative overflow-hidden">
            <div class="absolute -right-12 -bottom-12 w-56 h-56 bg-[#D96B43]/10 rounded-full blur-3xl pointer-events-none"></div>
            <div class="absolute right-12 top-0 w-32 h-32 bg-[#2D4235]/5 rounded-full blur-2xl pointer-events-none"></div>
            
            <div class="max-w-3xl relative z-10">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-[11px] font-bold uppercase tracking-widest text-[#8B231A] bg-[#8B231A]/10 border border-[#8B231A]/20 mb-3">
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                    </svg>
                    Pusat Analitik & Intelijen Pasar
                </div>
                <h2 class="text-2xl sm:text-3xl font-extrabold text-[#26221E] tracking-tight" style="font-family: 'Playfair Display', Georgia, serif;">
                    Ringkasan Kinerja Bisnis & Resep Bakso
                </h2>
                <p class="text-sm text-[#5A534A] mt-2 leading-relaxed font-sans">
                    Pantau realisasi pendapatan kotor yang sah secara ketat dari pesanan berstatus <span class="font-semibold text-[#2D4235]">Diproses</span> dan <span class="font-semibold text-[#2D4235]">Selesai</span>, serta telaah kecenderungan preferensi adonan pasar (Enak, Seimbang, atau Ekonomis) untuk efisiensi pengadaan bahan baku.
                </p>
            </div>
        </div>

        <!-- 4 Kartu Metrik Utama -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
            <!-- Kartu 1: Omzet Valid (Strict Revenue) -->
            <div class="bg-[#FAF7F2] rounded-3xl p-6 border border-[#26221E]/10 shadow-sm relative overflow-hidden group hover:border-[#8B231A]/30 transition-all">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold text-[#5A534A] uppercase tracking-wider">Omzet Valid Riil</span>
                    <span class="w-9 h-9 rounded-xl bg-[#8B231A]/10 text-[#8B231A] flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </span>
                </div>
                <div class="mt-4">
                    <p class="text-2xl sm:text-3xl font-extrabold text-[#8B231A] font-sans tabular-nums tracking-tight">
                        Rp {{ number_format($validRevenue, 0, ',', '.') }}
                    </p>
                    <div class="flex items-center gap-1.5 mt-2 text-xs font-medium text-[#2D4235]">
                        <span class="inline-block w-2 h-2 rounded-full bg-[#2D4235]"></span>
                        <span>{{ $validOrdersCount }} pesanan (Diproses & Selesai)</span>
                    </div>
                    <p class="text-[11px] text-[#5A534A]/80 mt-1">
                        Menunggu konfirmasi: Rp {{ number_format($pendingRevenue, 0, ',', '.') }}
                    </p>
                </div>
            </div>

            <!-- Kartu 2: Total Pesanan Masuk -->
            <div class="bg-[#FAF7F2] rounded-3xl p-6 border border-[#26221E]/10 shadow-sm relative overflow-hidden group hover:border-[#2D4235]/30 transition-all">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold text-[#5A534A] uppercase tracking-wider">Total Pesanan</span>
                    <span class="w-9 h-9 rounded-xl bg-[#2D4235]/10 text-[#2D4235] flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                        </svg>
                    </span>
                </div>
                <div class="mt-4">
                    <p class="text-2xl sm:text-3xl font-extrabold text-[#26221E] font-sans tabular-nums tracking-tight">
                        {{ number_format($totalOrders) }}
                    </p>
                    <div class="flex items-center gap-2 mt-2 text-xs text-[#5A534A]">
                        <span class="text-[#D97706] font-semibold">{{ $pendingCount }} Menunggu</span>
                        <span>•</span>
                        <span class="text-[#2D4235] font-semibold">{{ $processingCount }} Diproses</span>
                    </div>
                    <p class="text-[11px] text-[#5A534A]/80 mt-1">
                        {{ $completedCount }} Selesai, {{ $cancelledCount }} Dibatalkan
                    </p>
                </div>
            </div>

            <!-- Kartu 3: Preferensi Terfavorit -->
            <div class="bg-[#FAF7F2] rounded-3xl p-6 border border-[#26221E]/10 shadow-sm relative overflow-hidden group hover:border-[#D96B43]/30 transition-all">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold text-[#5A534A] uppercase tracking-wider">Resep Terpopuler</span>
                    <span class="w-9 h-9 rounded-xl bg-[#D96B43]/10 text-[#D96B43] flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" />
                        </svg>
                    </span>
                </div>
                <div class="mt-4">
                    @php
                        $topPref = 'Seimbang';
                        $maxCount = $seimbangCount;
                        if ($enakCount > $maxCount) {
                            $topPref = 'Enak';
                            $maxCount = $enakCount;
                        }
                        if ($ekonomisCount > $maxCount) {
                            $topPref = 'Ekonomis';
                            $maxCount = $ekonomisCount;
                        }
                    @endphp
                    <p class="text-2xl sm:text-3xl font-extrabold text-[#D96B43] font-sans tracking-tight">
                        {{ $totalPreferences > 0 ? $topPref : 'Belum Ada' }}
                    </p>
                    <div class="flex items-center gap-1.5 mt-2 text-xs font-medium text-[#5A534A]">
                        <span>Dominasi: {{ $preferencePercentages[strtolower($topPref)] ?? 0 }}% dari racikan</span>
                    </div>
                    <p class="text-[11px] text-[#5A534A]/80 mt-1">
                        {{ $enakCount }} Enak • {{ $seimbangCount }} Seimbang • {{ $ekonomisCount }} Ekonomis
                    </p>
                </div>
            </div>

            <!-- Kartu 4: Konversi & Realisasi -->
            <div class="bg-[#FAF7F2] rounded-3xl p-6 border border-[#26221E]/10 shadow-sm relative overflow-hidden group hover:border-[#2D4235]/30 transition-all">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold text-[#5A534A] uppercase tracking-wider">Tingkat Realisasi</span>
                    <span class="w-9 h-9 rounded-xl bg-emerald-600/10 text-emerald-700 flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </span>
                </div>
                <div class="mt-4">
                    @php
                        $realizationRate = $totalOrders > 0 ? round(($validOrdersCount / $totalOrders) * 100, 1) : 0;
                    @endphp
                    <p class="text-2xl sm:text-3xl font-extrabold text-[#2D4235] font-sans tabular-nums tracking-tight">
                        {{ $realizationRate }}%
                    </p>
                    <div class="flex items-center gap-1.5 mt-2 text-xs font-medium text-[#5A534A]">
                        <span>{{ $validOrdersCount }} dari {{ $totalOrders }} order diproses/selesai</span>
                    </div>
                    <p class="text-[11px] text-[#5A534A]/80 mt-1">
                        Rata-rata: Rp {{ $validOrdersCount > 0 ? number_format($validRevenue / $validOrdersCount, 0, ',', '.') : '0' }} /order
                    </p>
                </div>
            </div>
        </div>

        <!-- Bagian 2: Dua Grafik Interaktif Chart.js -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
            <!-- Grafik 1: Sebaran Preferensi Resep (Doughnut / Polar) -->
            <div class="lg:col-span-5 bg-[#FAF7F2] rounded-3xl p-6 sm:p-7 border border-[#26221E]/10 shadow-sm flex flex-col justify-between">
                <div>
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <span class="text-[10px] font-bold uppercase tracking-wider text-[#D96B43]">
                                Distribusi Resep
                            </span>
                            <h3 class="text-lg sm:text-xl font-bold text-[#26221E]" style="font-family: 'Playfair Display', Georgia, serif;">
                                Karakter Adonan Pilihan
                            </h3>
                            <p class="text-xs text-[#5A534A] mt-1">
                                Perbandingan pilihan racikan Enak, Seimbang, dan Ekonomis dari pelanggan.
                            </p>
                        </div>
                        <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-[#D96B43]/10 text-[#D96B43] border border-[#D96B43]/20 shrink-0">
                            {{ $totalPreferences }} Racikan
                        </span>
                    </div>

                    <!-- Wadah Canvas Chart 1 -->
                    <div wire:ignore class="relative h-64 sm:h-72 mt-6 flex items-center justify-center">
                        <canvas id="recipePreferenceChart" class="max-h-full"></canvas>
                    </div>
                </div>

                <!-- Legenda & Rincian Proporsi -->
                <div class="grid grid-cols-3 gap-2 pt-6 mt-4 border-t border-[#26221E]/10 text-center">
                    <div class="p-2.5 rounded-2xl bg-white/60 border border-[#26221E]/5">
                        <div class="flex items-center justify-center gap-1.5 mb-1">
                            <span class="w-2.5 h-2.5 rounded-full bg-[#8B231A]"></span>
                            <span class="text-[11px] font-bold text-[#26221E]">Enak</span>
                        </div>
                        <p class="text-sm font-extrabold text-[#8B231A] font-sans tabular-nums">{{ $enakCount }}</p>
                        <p class="text-[10px] text-[#5A534A]">{{ $preferencePercentages['enak'] }}%</p>
                    </div>
                    <div class="p-2.5 rounded-2xl bg-white/60 border border-[#26221E]/5">
                        <div class="flex items-center justify-center gap-1.5 mb-1">
                            <span class="w-2.5 h-2.5 rounded-full bg-[#2D4235]"></span>
                            <span class="text-[11px] font-bold text-[#26221E]">Seimbang</span>
                        </div>
                        <p class="text-sm font-extrabold text-[#2D4235] font-sans tabular-nums">{{ $seimbangCount }}</p>
                        <p class="text-[10px] text-[#5A534A]">{{ $preferencePercentages['seimbang'] }}%</p>
                    </div>
                    <div class="p-2.5 rounded-2xl bg-white/60 border border-[#26221E]/5">
                        <div class="flex items-center justify-center gap-1.5 mb-1">
                            <span class="w-2.5 h-2.5 rounded-full bg-[#D96B43]"></span>
                            <span class="text-[11px] font-bold text-[#26221E]">Ekonomis</span>
                        </div>
                        <p class="text-sm font-extrabold text-[#D96B43] font-sans tabular-nums">{{ $ekonomisCount }}</p>
                        <p class="text-[10px] text-[#5A534A]">{{ $preferencePercentages['ekonomis'] }}%</p>
                    </div>
                </div>
            </div>

            <!-- Grafik 2: Tren Status & Distribusi Pesanan (Bar / Line) -->
            <div class="lg:col-span-7 bg-[#FAF7F2] rounded-3xl p-6 sm:p-7 border border-[#26221E]/10 shadow-sm flex flex-col justify-between">
                <div>
                    <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-3">
                        <div>
                            <span class="text-[10px] font-bold uppercase tracking-wider text-[#2D4235]">
                                Analisis Operasional
                            </span>
                            <h3 class="text-lg sm:text-xl font-bold text-[#26221E]" style="font-family: 'Playfair Display', Georgia, serif;">
                                Tren Volume & Distribusi Status Pesanan
                            </h3>
                            <p class="text-xs text-[#5A534A] mt-1">
                                Perkembangan pesanan masuk bulanan dan status pemrosesan di meja penggilingan.
                            </p>
                        </div>
                        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-[11px] font-semibold bg-[#2D4235]/10 text-[#2D4235] border border-[#2D4235]/20 self-start sm:self-auto shrink-0">
                            <span class="w-2 h-2 rounded-full bg-[#2D4235]"></span>
                            Status Pengerjaan
                        </div>
                    </div>

                    <!-- Wadah Canvas Chart 2 -->
                    <div wire:ignore class="relative h-64 sm:h-72 mt-6">
                        <canvas id="orderStatusChart" class="w-full h-full"></canvas>
                    </div>
                </div>

                <!-- Ringkasan Status Pills -->
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-2 pt-6 mt-4 border-t border-[#26221E]/10">
                    <div class="px-3 py-2 rounded-2xl bg-white/60 border border-[#26221E]/5 flex items-center justify-between">
                        <div>
                            <p class="text-[10px] text-[#5A534A] uppercase font-bold">Diproses</p>
                            <p class="text-sm font-extrabold text-[#2D4235] font-sans tabular-nums">{{ $processingCount }}</p>
                        </div>
                        <span class="w-2.5 h-2.5 rounded-full bg-[#2D4235]"></span>
                    </div>
                    <div class="px-3 py-2 rounded-2xl bg-white/60 border border-[#26221E]/5 flex items-center justify-between">
                        <div>
                            <p class="text-[10px] text-[#5A534A] uppercase font-bold">Selesai</p>
                            <p class="text-sm font-extrabold text-emerald-700 font-sans tabular-nums">{{ $completedCount }}</p>
                        </div>
                        <span class="w-2.5 h-2.5 rounded-full bg-emerald-600"></span>
                    </div>
                    <div class="px-3 py-2 rounded-2xl bg-white/60 border border-[#26221E]/5 flex items-center justify-between">
                        <div>
                            <p class="text-[10px] text-[#5A534A] uppercase font-bold">Menunggu</p>
                            <p class="text-sm font-extrabold text-amber-700 font-sans tabular-nums">{{ $pendingCount }}</p>
                        </div>
                        <span class="w-2.5 h-2.5 rounded-full bg-amber-500"></span>
                    </div>
                    <div class="px-3 py-2 rounded-2xl bg-white/60 border border-[#26221E]/5 flex items-center justify-between">
                        <div>
                            <p class="text-[10px] text-[#5A534A] uppercase font-bold">Dibatalkan</p>
                            <p class="text-sm font-extrabold text-rose-700 font-sans tabular-nums">{{ $cancelledCount }}</p>
                        </div>
                        <span class="w-2.5 h-2.5 rounded-full bg-rose-600"></span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Bagian 3: Tabel Pesanan Terkini -->
        <div class="bg-[#FAF7F2] rounded-3xl border border-[#26221E]/10 shadow-sm overflow-hidden">
            <div class="p-6 sm:p-7 border-b border-[#26221E]/10 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div>
                    <span class="text-[10px] font-bold uppercase tracking-wider text-[#8B231A]">
                        Log Transaksi Terkini
                    </span>
                    <h3 class="text-lg sm:text-xl font-bold text-[#26221E] mt-0.5" style="font-family: 'Playfair Display', Georgia, serif;">
                        Pesanan Masuk Terbaru
                    </h3>
                    <p class="text-xs text-[#5A534A] mt-1">
                        Daftar transaksi teranyar dari kalkulator dan WhatsApp pelanggan.
                    </p>
                </div>
                <a
                    href="{{ route('admin.orders') }}"
                    wire:navigate
                    class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl text-xs font-semibold text-[#8B231A] hover:text-[#FAF7F2] hover:bg-[#8B231A] border border-[#8B231A]/20 transition-all self-start sm:self-auto cursor-pointer"
                >
                    <span>Lihat Semua Pesanan</span>
                    &rarr;
                </a>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b border-[#26221E]/10 bg-[#F4EFE6]/60 text-[11px] font-bold uppercase tracking-wider text-[#5A534A]">
                            <th scope="col" class="py-3.5 px-6">ID & Waktu</th>
                            <th scope="col" class="py-3.5 px-6">Nama Pemesan</th>
                            <th scope="col" class="py-3.5 px-6">Karakter Resep</th>
                            <th scope="col" class="py-3.5 px-6">Daging & Berat</th>
                            <th scope="col" class="py-3.5 px-6 text-right">Total Nilai</th>
                            <th scope="col" class="py-3.5 px-6 text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#26221E]/5 text-xs font-medium text-[#26221E]">
                        @forelse ($recentOrders as $order)
                            <tr class="hover:bg-white/60 transition-colors">
                                <!-- ID & Waktu -->
                                <td class="py-4 px-6 whitespace-nowrap">
                                    <span class="font-extrabold font-mono text-[#26221E]">
                                        #{{ str_pad((string)$order->id, 4, '0', STR_PAD_LEFT) }}
                                    </span>
                                    <span class="block text-[10px] text-[#5A534A] mt-0.5">
                                        {{ $order->created_at ? $order->created_at->format('d M Y, H:i') : '-' }}
                                    </span>
                                </td>

                                <!-- Nama Pemesan -->
                                <td class="py-4 px-6 font-bold text-[#26221E]">
                                    {{ $order->customer_name }}
                                </td>

                                <!-- Karakter Resep -->
                                <td class="py-4 px-6 whitespace-nowrap">
                                    @php
                                        $prefLower = strtolower($order->preference);
                                    @endphp
                                    @if ($prefLower === 'enak')
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-bold bg-[#8B231A]/10 text-[#8B231A] border border-[#8B231A]/20">
                                            Enak
                                        </span>
                                    @elseif ($prefLower === 'seimbang')
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-bold bg-[#2D4235]/10 text-[#2D4235] border border-[#2D4235]/20">
                                            Seimbang
                                        </span>
                                    @elseif ($prefLower === 'ekonomis')
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-bold bg-[#D96B43]/10 text-[#D96B43] border border-[#D96B43]/20">
                                            Ekonomis
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-bold bg-[#26221E]/10 text-[#26221E]">
                                            {{ ucfirst($order->preference) }}
                                        </span>
                                    @endif
                                </td>

                                <!-- Daging & Berat -->
                                <td class="py-4 px-6 whitespace-nowrap text-[#5A534A]">
                                    <span class="font-semibold text-[#26221E]">{{ number_format($order->meat_weight) }} gram</span>
                                    <span class="block text-[10px]">
                                        {{ $order->start_type === 'start_from_zero' ? 'Beli Pasar' : 'Daging Sendiri' }}
                                    </span>
                                </td>

                                <!-- Total Nilai -->
                                <td class="py-4 px-6 text-right whitespace-nowrap font-sans font-bold tabular-nums text-[#26221E]">
                                    Rp {{ number_format($order->total_price, 0, ',', '.') }}
                                </td>

                                <!-- Status -->
                                <td class="py-4 px-6 text-center whitespace-nowrap">
                                    @php
                                        $statusClass = match(strtolower($order->status)) {
                                            'completed', 'selesai' => 'bg-emerald-50 text-emerald-800 border-emerald-200',
                                            'processing', 'diproses' => 'bg-blue-50 text-blue-800 border-blue-200',
                                            'pending', 'menunggu' => 'bg-amber-50 text-amber-800 border-amber-200',
                                            'cancelled', 'batal' => 'bg-rose-50 text-rose-800 border-rose-200',
                                            default => 'bg-stone-50 text-stone-800 border-stone-200',
                                        };
                                        $statusLabel = match(strtolower($order->status)) {
                                            'completed', 'selesai' => 'Selesai',
                                            'processing', 'diproses' => 'Diproses',
                                            'pending', 'menunggu' => 'Menunggu',
                                            'cancelled', 'batal' => 'Dibatalkan',
                                            default => ucfirst($order->status),
                                        };
                                    @endphp
                                    <span class="inline-block px-2.5 py-1 rounded-full text-[11px] font-bold border {{ $statusClass }}">
                                        {{ $statusLabel }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-12 text-center text-[#5A534A]">
                                    <svg class="w-10 h-10 mx-auto text-[#5A534A]/40 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                    </svg>
                                    <p class="font-bold text-sm text-[#26221E]">Belum Ada Pesanan Masuk</p>
                                    <p class="text-xs text-[#5A534A]/70 mt-1">Transaksi racikan kalkulator dari pelanggan akan tercantum di sini.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Pintasan Cepat ke Modul Lain -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
            <!-- Modul Harga Pasar -->
            <a
                href="{{ route('admin.materials') }}"
                wire:navigate
                class="bg-[#FAF7F2] rounded-3xl p-6 border border-[#26221E]/10 shadow-sm hover:border-[#D96B43]/50 transition-all flex items-center justify-between group cursor-pointer"
            >
                <div class="flex items-center gap-4">
                    <span class="w-12 h-12 rounded-2xl bg-[#D96B43]/10 text-[#D96B43] flex items-center justify-center shrink-0 group-hover:scale-105 transition-transform">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </span>
                    <div>
                        <h4 class="text-base font-bold text-[#26221E] group-hover:text-[#D96B43] transition-colors" style="font-family: 'Playfair Display', Georgia, serif;">
                            Katalog Harga Bahan & Selepan
                        </h4>
                        <p class="text-xs text-[#5A534A] mt-0.5">
                            Perbarui harga daging sapi, ayam, tepung aren, dan ongkos selepan pasar.
                        </p>
                    </div>
                </div>
                <span class="text-[#D96B43] text-lg font-bold group-hover:translate-x-1 transition-transform">
                    &rarr;
                </span>
            </a>

            <!-- Modul Pesanan WhatsApp -->
            <a
                href="{{ route('admin.orders') }}"
                wire:navigate
                class="bg-[#FAF7F2] rounded-3xl p-6 border border-[#26221E]/10 shadow-sm hover:border-[#2D4235]/50 transition-all flex items-center justify-between group cursor-pointer"
            >
                <div class="flex items-center gap-4">
                    <span class="w-12 h-12 rounded-2xl bg-[#2D4235]/10 text-[#2D4235] flex items-center justify-center shrink-0 group-hover:scale-105 transition-transform">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                    </span>
                    <div>
                        <h4 class="text-base font-bold text-[#26221E] group-hover:text-[#2D4235] transition-colors" style="font-family: 'Playfair Display', Georgia, serif;">
                            Manajemen Pesanan Pelanggan
                        </h4>
                        <p class="text-xs text-[#5A534A] mt-0.5">
                            Ubah status pengerjaan, lihat rincian racikan, dan hubungi pelanggan.
                        </p>
                    </div>
                </div>
                <span class="text-[#2D4235] text-lg font-bold group-hover:translate-x-1 transition-transform">
                    &rarr;
                </span>
            </a>
        </div>
    </div>

    <!-- Script Inisialisasi Chart.js -->
    <script>
        (function() {
            const chartData = {
                preference: @json($preferenceChart),
                status: @json($statusChart)
            };

            function initBasokuCharts() {
                if (typeof Chart === 'undefined') {
                    console.warn('Chart.js belum dimuat. Mencoba kembali dalam 100ms...');
                    setTimeout(initBasokuCharts, 100);
                    return;
                }

                // 1. Chart Preferensi Resep (Doughnut Chart)
                const prefCanvas = document.getElementById('recipePreferenceChart');
                if (prefCanvas) {
                    if (window.basokuRecipeChartInstance) {
                        window.basokuRecipeChartInstance.destroy();
                    }

                    const prefCtx = prefCanvas.getContext('2d');
                    const hasPrefData = chartData.preference.data.some(v => v > 0);

                    window.basokuRecipeChartInstance = new Chart(prefCtx, {
                        type: 'doughnut',
                        data: {
                            labels: chartData.preference.labels,
                            datasets: [{
                                data: hasPrefData ? chartData.preference.data : [1, 1, 1],
                                backgroundColor: hasPrefData ? chartData.preference.colors : ['#E8E3DA', '#DDD7CD', '#D2CCC1'],
                                hoverBackgroundColor: hasPrefData ? chartData.preference.hoverColors : ['#D2CCC1', '#C6BFB3', '#B8B0A2'],
                                borderWidth: 3,
                                borderColor: '#FAF7F2',
                                hoverOffset: 6
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    display: false
                                },
                                tooltip: {
                                    backgroundColor: '#26221E',
                                    titleColor: '#FAF7F2',
                                    bodyColor: '#FAF7F2',
                                    padding: 12,
                                    cornerRadius: 12,
                                    boxPadding: 6,
                                    callbacks: {
                                        label: function(context) {
                                            if (!hasPrefData) return 'Belum ada data racikan';
                                            const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                            const val = context.raw;
                                            const pct = total > 0 ? Math.round((val / total) * 100) : 0;
                                            return `${context.label}: ${val} pesanan (${pct}%)`;
                                        }
                                    }
                                }
                            },
                            cutout: '68%'
                        }
                    });
                }

                // 2. Chart Status & Tren Pesanan (Bar Chart)
                const statusCanvas = document.getElementById('orderStatusChart');
                if (statusCanvas) {
                    if (window.basokuStatusChartInstance) {
                        window.basokuStatusChartInstance.destroy();
                    }

                    const statusCtx = statusCanvas.getContext('2d');
                    window.basokuStatusChartInstance = new Chart(statusCtx, {
                        type: 'bar',
                        data: {
                            labels: chartData.status.labels,
                            datasets: [{
                                label: 'Jumlah Pesanan',
                                data: chartData.status.data,
                                backgroundColor: [
                                    'rgba(45, 66, 53, 0.85)',   // Diproses: Deep Sage
                                    'rgba(22, 101, 52, 0.85)',  // Selesai: Emerald
                                    'rgba(217, 119, 6, 0.85)',  // Menunggu: Amber
                                    'rgba(153, 27, 27, 0.85)'   // Dibatalkan: Maroon/Rose
                                ],
                                borderColor: [
                                    '#2D4235',
                                    '#166534',
                                    '#D97706',
                                    '#991B1B'
                                ],
                                borderWidth: 1.5,
                                borderRadius: 12,
                                borderSkipped: false,
                                maxBarThickness: 44
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    display: false
                                },
                                tooltip: {
                                    backgroundColor: '#26221E',
                                    titleColor: '#FAF7F2',
                                    bodyColor: '#FAF7F2',
                                    padding: 12,
                                    cornerRadius: 12,
                                    callbacks: {
                                        label: function(context) {
                                            return `Total: ${context.raw} pesanan`;
                                        }
                                    }
                                }
                            },
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    ticks: {
                                        precision: 0,
                                        color: '#5A534A',
                                        font: {
                                            family: "'Instrument Sans', sans-serif",
                                            size: 11
                                        }
                                    },
                                    grid: {
                                        color: 'rgba(38, 34, 30, 0.06)'
                                    }
                                },
                                x: {
                                    ticks: {
                                        color: '#26221E',
                                        font: {
                                            family: "'Instrument Sans', sans-serif",
                                            size: 11,
                                            weight: '600'
                                        }
                                    },
                                    grid: {
                                        display: false
                                    }
                                }
                            }
                        }
                    });
                }
            }

            // Inisialisasi saat pertama kali load dan saat Livewire navigate
            if (document.readyState === 'complete' || document.readyState === 'interactive') {
                initBasokuCharts();
            } else {
                document.addEventListener('DOMContentLoaded', initBasokuCharts);
            }

            document.addEventListener('livewire:navigated', initBasokuCharts);
        })();
    </script>
</main>
