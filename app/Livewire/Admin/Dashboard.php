<?php

namespace App\Livewire\Admin;

use App\Models\Order;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.admin')]
#[Title('Dasbor Analitik Pengelola — BASOKU')]
class Dashboard extends Component
{
    /**
     * Pastikan pengguna memiliki peran admin pada setiap lifecycle request.
     */
    public function boot(): void
    {
        if (! Auth::check() || Auth::user()->role !== 'admin') {
            abort(403, 'Akses khusus administrator BASOKU.');
        }
    }

    /**
     * Pastikan pengguna memiliki peran admin.
     */
    public function mount(): mixed
    {
        if (! Auth::check() || Auth::user()->role !== 'admin') {
            return $this->redirect('/masuk', navigate: true);
        }

        return null;
    }

    /**
     * Logout sesi admin dan arahkan kembali ke gerbang masuk.
     */
    public function logout(): mixed
    {
        Auth::logout();

        session()->invalidate();
        session()->regenerateToken();

        return $this->redirect('/masuk', navigate: true);
    }

    /**
     * Render dasbor analitik menggunakan agregasi SQL terukur (tanpa full-table scan Order::all).
     */
    public function render(): View
    {
        // 1. Agregasi Status & Omzet langsung via SQL (1 query ringan)
        $statusRows = Order::query()
            ->selectRaw('LOWER(TRIM(status)) as status_key, COUNT(*) as total_count, COALESCE(SUM(total_price), 0) as total_sum')
            ->groupBy('status_key')
            ->get()
            ->keyBy('status_key');

        $sumStatusCount = fn (array $keys): int => (int) collect($keys)->sum(fn (string $k) => (int) ($statusRows[$k]->total_count ?? 0));
        $sumStatusRevenue = fn (array $keys): float => (float) collect($keys)->sum(fn (string $k) => (float) ($statusRows[$k]->total_sum ?? 0.0));

        $totalOrders = (int) $statusRows->sum('total_count');
        $processingCount = $sumStatusCount(Order::aliasesFor('processing'));
        $completedCount = $sumStatusCount(Order::aliasesFor('completed'));
        $pendingCount = $sumStatusCount(Order::aliasesFor('pending'));
        $cancelledCount = $sumStatusCount(Order::aliasesFor('cancelled'));

        $validStatuses = array_merge(Order::aliasesFor('processing'), Order::aliasesFor('completed'));
        $validOrdersCount = $processingCount + $completedCount;
        $validRevenue = $sumStatusRevenue($validStatuses);
        $pendingRevenue = $sumStatusRevenue(Order::aliasesFor('pending'));

        // 2. Agregasi Preferensi Racikan langsung via SQL (1 query ringan)
        $prefRows = Order::query()
            ->selectRaw('LOWER(TRIM(preference)) as pref_key, COUNT(*) as total_count')
            ->groupBy('pref_key')
            ->pluck('total_count', 'pref_key');

        $enakCount = (int) ($prefRows['enak'] ?? 0);
        $seimbangCount = (int) ($prefRows['seimbang'] ?? 0);
        $ekonomisCount = (int) ($prefRows['ekonomis'] ?? 0);
        $uratCount = (int) ($prefRows['urat'] ?? 0);
        $kustomCount = (int) ($prefRows['kustom'] ?? 0);
        $katalogCount = (int) (($prefRows['belanja_katalog'] ?? 0) + ($prefRows['bahan_saja'] ?? 0));
        $totalPreferences = $enakCount + $seimbangCount + $ekonomisCount + $uratCount + $kustomCount + $katalogCount;

        $preferencePercentages = [
            'enak' => $totalPreferences > 0 ? round(($enakCount / $totalPreferences) * 100, 1) : 0.0,
            'seimbang' => $totalPreferences > 0 ? round(($seimbangCount / $totalPreferences) * 100, 1) : 0.0,
            'ekonomis' => $totalPreferences > 0 ? round(($ekonomisCount / $totalPreferences) * 100, 1) : 0.0,
            'urat' => $totalPreferences > 0 ? round(($uratCount / $totalPreferences) * 100, 1) : 0.0,
            'kustom' => $totalPreferences > 0 ? round(($kustomCount / $totalPreferences) * 100, 1) : 0.0,
            'katalog' => $totalPreferences > 0 ? round(($katalogCount / $totalPreferences) * 100, 1) : 0.0,
        ];

        $preferenceCategories = [
            ['key' => 'enak', 'label' => 'Enak', 'chart_label' => 'Racikan Enak', 'count' => $enakCount, 'pct' => $preferencePercentages['enak'], 'color' => '#8B231A', 'hover' => '#701A13', 'always_show' => true],
            ['key' => 'seimbang', 'label' => 'Seimbang', 'chart_label' => 'Racikan Seimbang', 'count' => $seimbangCount, 'pct' => $preferencePercentages['seimbang'], 'color' => '#2D4235', 'hover' => '#1E2D24', 'always_show' => true],
            ['key' => 'ekonomis', 'label' => 'Ekonomis', 'chart_label' => 'Racikan Ekonomis', 'count' => $ekonomisCount, 'pct' => $preferencePercentages['ekonomis'], 'color' => '#B84E26', 'hover' => '#9A3F1D', 'always_show' => true],
            ['key' => 'kustom', 'label' => 'Kustom', 'chart_label' => 'Rasio Kustom', 'count' => $kustomCount, 'pct' => $preferencePercentages['kustom'], 'color' => '#4338CA', 'hover' => '#3730A3', 'always_show' => $kustomCount > 0],
            ['key' => 'katalog', 'label' => 'Bahan Saja', 'chart_label' => 'Bahan Saja', 'count' => $katalogCount, 'pct' => $preferencePercentages['katalog'], 'color' => '#047857', 'hover' => '#065F46', 'always_show' => $katalogCount > 0],
            ['key' => 'urat', 'label' => 'Urat', 'chart_label' => 'Bakso Urat', 'count' => $uratCount, 'pct' => $preferencePercentages['urat'], 'color' => '#78350F', 'hover' => '#5A260C', 'always_show' => $uratCount > 0],
        ];

        // Tentukan preferensi terpopuler secara dinamis mencakup Kustom, Bahan Saja, Urat, Enak, Seimbang, Ekonomis
        $topPreference = [
            'label' => 'Belum Ada',
            'count' => 0,
            'percentage' => 0.0,
            'color' => '#B84E26',
        ];
        foreach ($preferenceCategories as $cat) {
            if ($cat['count'] > $topPreference['count']) {
                $topPreference = [
                    'label' => $cat['label'],
                    'count' => $cat['count'],
                    'percentage' => $cat['pct'],
                    'color' => $cat['color'],
                ];
            }
        }

        // 3. Pesanan Terbaru (tanpa eager-loading orderItems yang tidak dipakai di tabel beranda)
        $recentOrders = Order::query()
            ->latest()
            ->take(8)
            ->get();

        // 4. Tren Bulanan (6 Bulan Terakhir — hanya query 6 bulan terakhir)
        $sixMonthsStart = now()->subMonths(5)->startOfMonth();
        $recentSixMonthOrders = Order::query()
            ->where('created_at', '>=', $sixMonthsStart)
            ->get(['status', 'total_price', 'created_at']);

        $monthlyTrend = [];
        $indonesianMonths = [
            1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr', 5 => 'Mei', 6 => 'Jun',
            7 => 'Jul', 8 => 'Agu', 9 => 'Sep', 10 => 'Okt', 11 => 'Nov', 12 => 'Des',
        ];

        for ($i = 5; $i >= 0; $i--) {
            $point = now()->subMonths($i);
            $yearMonth = $point->format('Y-m');
            $month = (int) $point->format('n');
            $monthLabel = ($indonesianMonths[$month] ?? $point->format('M')).' '.$point->format('y');

            $monthOrders = $recentSixMonthOrders->filter(
                fn (Order $order) => $order->created_at && $order->created_at->format('Y-m') === $yearMonth
            );

            $orderVolume = $monthOrders->count();
            $monthValidRevenue = (float) $monthOrders
                ->filter(fn (Order $o) => in_array(strtolower(trim((string) $o->status)), $validStatuses, true))
                ->sum('total_price');

            $monthlyTrend[] = [
                'label' => $monthLabel,
                'orders' => $orderVolume,
                'revenue' => $monthValidRevenue,
            ];
        }

        // 5. Struktur Data Chart.js
        $visiblePreferenceCategories = array_values(array_filter($preferenceCategories, fn (array $c) => $c['always_show']));

        $preferenceChart = [
            'labels' => array_column($visiblePreferenceCategories, 'chart_label'),
            'data' => array_column($visiblePreferenceCategories, 'count'),
            'colors' => array_column($visiblePreferenceCategories, 'color'),
            'hoverColors' => array_column($visiblePreferenceCategories, 'hover'),
        ];

        $statusChart = [
            'labels' => ['Sedang Diproses', 'Selesai', 'Menunggu', 'Dibatalkan'],
            'data' => [$processingCount, $completedCount, $pendingCount, $cancelledCount],
            'colors' => ['#2D4235', '#166534', '#D97706', '#991B1B'],
            'monthlyLabels' => array_column($monthlyTrend, 'label'),
            'monthlyOrders' => array_column($monthlyTrend, 'orders'),
            'monthlyRevenue' => array_column($monthlyTrend, 'revenue'),
        ];

        return view('livewire.admin.dashboard', [
            'totalOrders' => $totalOrders,
            'validRevenue' => $validRevenue,
            'validOrdersCount' => $validOrdersCount,
            'processingCount' => $processingCount,
            'completedCount' => $completedCount,
            'pendingCount' => $pendingCount,
            'pendingRevenue' => $pendingRevenue,
            'cancelledCount' => $cancelledCount,
            'enakCount' => $enakCount,
            'seimbangCount' => $seimbangCount,
            'ekonomisCount' => $ekonomisCount,
            'uratCount' => $uratCount,
            'kustomCount' => $kustomCount,
            'katalogCount' => $katalogCount,
            'totalPreferences' => $totalPreferences,
            'preferencePercentages' => $preferencePercentages,
            'topPreference' => $topPreference,
            'preferenceLegendItems' => $visiblePreferenceCategories,
            'recentOrders' => $recentOrders,
            'monthlyTrend' => $monthlyTrend,
            'preferenceChart' => $preferenceChart,
            'statusChart' => $statusChart,
        ]);
    }
}
