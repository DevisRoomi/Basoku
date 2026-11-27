@props([
    'id' => 'pickupTime',
    'model' => 'pickupTime',
    'value' => '',
])

@php
    $initialDate = '';
    $initialHour = '07';
    $initialMinute = '00';

    if (is_string($value) && str_contains($value, 'T')) {
        [$d, $t] = explode('T', $value, 2);
        $initialDate = $d;
        $tParts = explode(':', $t);
        if (isset($tParts[0]) && is_numeric($tParts[0])) {
            $initialHour = str_pad((string) ((int) $tParts[0]), 2, '0', STR_PAD_LEFT);
        }
        if (isset($tParts[1]) && is_numeric($tParts[1])) {
            $initialMinute = str_pad((string) ((int) $tParts[1]), 2, '0', STR_PAD_LEFT);
        }
    }

    $minDate = now()->timezone('Asia/Jakarta')->format('Y-m-d');
@endphp

<div
    x-data="{
        datePart: @js($initialDate),
        hourPart: @js($initialHour),
        minutePart: @js($initialMinute),
        parseFromWire(val) {
            if (typeof val !== 'string' || !val.includes('T')) { return; }
            const [d, t] = val.split('T');
            if (d) { this.datePart = d; }
            if (t) {
                const parts = t.split(':');
                if (parts[0] !== undefined) { this.hourPart = String(parts[0]).padStart(2, '0'); }
                if (parts[1] !== undefined) { this.minutePart = String(parts[1]).padStart(2, '0'); }
            }
        },
        sync() {
            if (!this.datePart) { return; }
            $wire.set(@js($model), `${this.datePart}T${this.hourPart}:${this.minutePart}`, false);
        }
    }"
    x-init="
        parseFromWire($wire.get(@js($model)));
        $watch('$wire.{{ $model }}', (val) => parseFromWire(val));
    "
    class="grid grid-cols-1 sm:grid-cols-12 gap-2"
>
    <!-- Tanggal -->
    <div class="sm:col-span-6">
        <label for="{{ $id }}" class="sr-only">Tanggal pengambilan</label>
        <input
            type="date"
            id="{{ $id }}"
            min="{{ $minDate }}"
            x-model="datePart"
            @change="sync()"
            class="w-full rounded-2xl border-2 border-[#26221E]/15 bg-white px-3.5 py-2.5 text-sm font-mono font-semibold text-[#26221E] focus:outline-none focus:border-[#2D4235] focus:ring-2 focus:ring-[#2D4235]/15 transition-all duration-200"
        >
    </div>

    <!-- Jam & Menit Format 24 Jam (00.00 - 23.59 WIB) -->
    <div class="sm:col-span-6 flex items-center gap-1.5">
        <div class="flex-1 relative">
            <label for="{{ $id }}-hour" class="sr-only">Jam pengambilan (00 sampai 23 WIB)</label>
            <select
                id="{{ $id }}-hour"
                x-model="hourPart"
                @change="sync()"
                class="w-full rounded-2xl border-2 border-[#26221E]/15 bg-white pl-3 pr-7 py-2.5 text-sm font-mono font-bold text-[#26221E] focus:outline-none focus:border-[#2D4235] focus:ring-2 focus:ring-[#2D4235]/15 appearance-none cursor-pointer transition-all duration-200"
            >
                @for ($h = 0; $h <= 23; $h++)
                    @php $hh = str_pad((string) $h, 2, '0', STR_PAD_LEFT); @endphp
                    <option value="{{ $hh }}" @selected($initialHour === $hh)>{{ $hh }}</option>
                @endfor
            </select>
            <svg class="w-3.5 h-3.5 pointer-events-none absolute right-2.5 top-1/2 -translate-y-1/2 text-[#5A534A]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
            </svg>
        </div>

        <span class="font-mono font-extrabold text-[#26221E] select-none" aria-hidden="true">:</span>

        <div class="flex-1 relative">
            <label for="{{ $id }}-minute" class="sr-only">Menit pengambilan (00 sampai 59)</label>
            <select
                id="{{ $id }}-minute"
                x-model="minutePart"
                @change="sync()"
                class="w-full rounded-2xl border-2 border-[#26221E]/15 bg-white pl-3 pr-7 py-2.5 text-sm font-mono font-bold text-[#26221E] focus:outline-none focus:border-[#2D4235] focus:ring-2 focus:ring-[#2D4235]/15 appearance-none cursor-pointer transition-all duration-200"
            >
                @for ($m = 0; $m <= 59; $m++)
                    @php $mm = str_pad((string) $m, 2, '0', STR_PAD_LEFT); @endphp
                    <option value="{{ $mm }}" @selected($initialMinute === $mm)>{{ $mm }}</option>
                @endfor
            </select>
            <svg class="w-3.5 h-3.5 pointer-events-none absolute right-2.5 top-1/2 -translate-y-1/2 text-[#5A534A]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
            </svg>
        </div>

        <span class="px-2 py-2.5 rounded-xl bg-[#EAE3D5]/80 border border-[#26221E]/10 text-[11px] font-mono font-bold text-[#2D4235] shrink-0 select-none">
            WIB
        </span>
    </div>
</div>
