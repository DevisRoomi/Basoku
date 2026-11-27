<?php

namespace App\Livewire\Admin;

use App\Models\KioskSetting;
use App\Support\CheckoutHelper;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.admin')]
#[Title('Pengaturan Kios — BASOKU Admin')]
class ManageKioskSettings extends Component
{
    public string $wa_number = '';

    public string $grinding_fee_per_kg = '15000';

    public int|string $open_hour = 4;

    public int|string $close_hour = 12;

    public ?string $savedNotice = null;

    public function boot(): void
    {
        if (! Auth::check() || Auth::user()->role !== 'admin') {
            abort(403, 'Akses khusus administrator BASOKU.');
        }
    }

    public function mount(): void
    {
        $current = KioskSetting::currentValues();
        $this->wa_number = $current['wa_number'];
        $this->grinding_fee_per_kg = (string) (int) round($current['grinding_fee_per_kg']);
        $this->open_hour = $current['open_hour'];
        $this->close_hour = $current['close_hour'];
    }

    /**
     * Simpan pengaturan operasional kios ke database dan langsung terapkan ke seluruh aplikasi.
     */
    public function save(): void
    {
        $this->savedNotice = null;

        // Normalisasi nomor WA ke format internasional 62... agar tautan https://wa.me/... selalu valid
        $this->wa_number = CheckoutHelper::canonicalPhoneDigits($this->wa_number);

        // Normalisasi format ribuan Indonesia pada tarif selepan (mis. "15.000" atau "20.000" -> "20000")
        $cleanedFee = str_replace(' ', '', trim($this->grinding_fee_per_kg));
        if (str_contains($cleanedFee, '.') && str_contains($cleanedFee, ',')) {
            $cleanedFee = str_replace(['.', ','], ['', '.'], $cleanedFee);
        } elseif (preg_match('/^-?\d{1,3}(\.\d{3})+$/', $cleanedFee)) {
            $cleanedFee = str_replace('.', '', $cleanedFee);
        } else {
            $cleanedFee = str_replace(',', '.', $cleanedFee);
        }
        $this->grinding_fee_per_kg = $cleanedFee;

        $validated = $this->validate([
            'wa_number' => ['required', 'string', 'regex:/^62[0-9]{8,14}$/'],
            'grinding_fee_per_kg' => ['required', 'numeric', 'min:0', 'max:500000'],
            'open_hour' => ['required', 'integer', 'min:0', 'max:22'],
            'close_hour' => ['required', 'integer', 'min:1', 'max:23', 'gt:open_hour'],
        ], [
            'wa_number.required' => 'Nomor WhatsApp resmi kios wajib diisi.',
            'wa_number.regex' => 'Nomor WhatsApp kios harus berupa nomor Indonesia valid (mis. 085122797969 atau 6285122797969).',
            'grinding_fee_per_kg.required' => 'Tarif jasa selepan per kg wajib diisi.',
            'grinding_fee_per_kg.numeric' => 'Tarif jasa selepan harus berupa angka.',
            'grinding_fee_per_kg.min' => 'Tarif jasa selepan tidak boleh negatif.',
            'open_hour.required' => 'Jam buka kios wajib diisi.',
            'close_hour.required' => 'Jam tutup kios wajib diisi.',
            'close_hour.gt' => 'Jam tutup kios harus lebih besar dari jam buka.',
        ]);

        KioskSetting::saveValues([
            'wa_number' => $validated['wa_number'],
            'grinding_fee_per_kg' => (float) $validated['grinding_fee_per_kg'],
            'open_hour' => (int) $validated['open_hour'],
            'close_hour' => (int) $validated['close_hour'],
        ]);

        $this->savedNotice = 'Pengaturan operasional kios berhasil disimpan dan langsung aktif di seluruh halaman publik.';
    }

    public function render(): View
    {
        return view('livewire.admin.manage-kiosk-settings');
    }
}
