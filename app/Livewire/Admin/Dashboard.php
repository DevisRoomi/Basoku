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
    public string $currentTab = 'beranda';

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

    public function setTab(string $tab): void
    {
        $this->currentTab = $tab;
    }

    /**
     * Render dasbor analitik beserta metrik pesanan dan preferensi racikan.
     */
    public function render(): View
    {
        // 1. Total seluruh pesanan masuk
        $totalOrders = Order::count();

        // 2. Omzet Valid: HANYA dari status 'diproses' atau 'selesai' (termasuk alias 'processing' & 'completed')
        // Pesanan 'menunggu', 'pending', 'batal', 'cancelled' secara ketat DIABAIKAN.
        $validStatuses = ['diproses', 'selesai', 'processing', 'completed'];
        $validRevenue = (float) Order::whereIn('status', $validStatuses)->sum('total_price');
        $validOrdersCount = Order::whereIn('status', $validStatuses)->count();

        // 3. Status breakdown untuk visibilitas operasional
        $processingCount = Order::whereIn('status', ['processing', 'diproses'])->count();
        $completedCount = Order::whereIn('status', ['completed', 'selesai'])->count();
        $pendingCount = Order::whereIn('status', ['pending', 'menunggu'])->count();
        $cancelledCount = Order::whereIn('status', ['cancelled', 'batal'])->count();

        $pendingRevenue = (float) Order::whereIn('status', ['pending', 'menunggu'])->sum('total_price');

        // 4. Sebaran Preferensi Racikan Resep: Enak, Seimbang, Ekonomis
        $enakCount = Order::whereIn('preference', ['enak', 'Enak', 'ENAK'])->count();
        $seimbangCount = Order::whereIn('preference', ['seimbang', 'Seimbang', 'SEIMBANG'])->count();
        $ekonomisCount = Order::whereIn('preference', ['ekonomis', 'Ekonomis', 'EKONOMIS'])->count();
        $totalPreferences = $enakCount + $seimbangCount + $ekonomisCount;

        $preferencePercentages = [
            'enak' => $totalPreferences > 0 ? round(($enakCount / $totalPreferences) * 100, 1) : 0.0,
            'seimbang' => $totalPreferences > 0 ? round(($seimbangCount / $totalPreferences) * 100, 1) : 0.0,
            'ekonomis' => $totalPreferences > 0 ? round(($ekonomisCount / $totalPreferences) * 100, 1) : 0.0,
        ];

        // 5. Pesanan Terbaru
        $recentOrders = Order::with('orderItems')
            ->latest()
            ->take(8)
            ->get();

        // 6. Tren Bulanan (6 Bulan Terakhir)
        $monthlyTrend = [];
        $indonesianMonths = [
            1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr', 5 => 'Mei', 6 => 'Jun',
            7 => 'Jul', 8 => 'Agu', 9 => 'Sep', 10 => 'Okt', 11 => 'Nov', 12 => 'Des',
        ];

        for ($i = 5; $i >= 0; $i--) {
            $point = now()->subMonths($i);
            $year = (int) $point->format('Y');
            $month = (int) $point->format('n');
            $monthLabel = ($indonesianMonths[$month] ?? $point->format('M')).' '.$point->format('y');

            $orderVolume = Order::whereYear('created_at', $year)
                ->whereMonth('created_at', $month)
                ->count();

            $monthValidRevenue = (float) Order::whereYear('created_at', $year)
                ->whereMonth('created_at', $month)
                ->whereIn('status', $validStatuses)
                ->sum('total_price');

            $monthlyTrend[] = [
                'label' => $monthLabel,
                'orders' => $orderVolume,
                'revenue' => $monthValidRevenue,
            ];
        }

        // 7. Struktur Data Chart.js
        $preferenceChart = [
            'labels' => ['Racikan Enak', 'Racikan Seimbang', 'Racikan Ekonomis'],
            'data' => [$enakCount, $seimbangCount, $ekonomisCount],
            'colors' => ['#8B231A', '#2D4235', '#D96B43'],
            'hoverColors' => ['#701A13', '#1E2D24', '#BF5732'],
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
            'totalPreferences' => $totalPreferences,
            'preferencePercentages' => $preferencePercentages,
            'recentOrders' => $recentOrders,
            'monthlyTrend' => $monthlyTrend,
            'preferenceChart' => $preferenceChart,
            'statusChart' => $statusChart,
        ]);
    }
}
