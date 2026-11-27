<div>
    <!-- Back to Login Link -->
    <div class="mb-8 flex items-center justify-between">
        <a href="{{ route('login') }}" wire:navigate class="inline-flex items-center gap-2 text-xs font-semibold text-[#5A534A] hover:text-[#D96B43] transition-colors">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
            </svg>
            <span>Kembali ke Halaman Masuk</span>
        </a>
        <span class="text-xs font-mono font-bold text-[#B84E26] uppercase tracking-wider">
            Kata Sandi Baru
        </span>
    </div>

    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-[#26221E] leading-tight" style="font-family: 'Playfair Display', Georgia, serif;">
            Atur Ulang Kata Sandi
        </h1>
        <p class="text-xs text-[#5A534A] mt-2 leading-relaxed">
            Buat kata sandi baru minimal 8 karakter untuk mengamankan akun BASOKU Anda.
        </p>
    </div>

    <!-- Form Card -->
    <div class="bg-[#FAF7F2] rounded-3xl p-6 sm:p-8 shadow-sm border border-[#26221E]/10">
        <form wire:submit.prevent="resetPassword" class="space-y-5">
            <div>
                <label for="reset-email" class="block text-xs font-bold uppercase tracking-wider text-[#26221E] mb-1.5">
                    Alamat Email
                </label>
                <input
                    type="email"
                    id="reset-email"
                    wire:model="email"
                    autocomplete="email"
                    placeholder="nama@email.com"
                    class="w-full rounded-2xl border-2 border-[#26221E]/10 bg-white px-4 py-3 text-sm font-medium text-[#26221E] focus:outline-none focus:border-[#D96B43] focus:ring-2 focus:ring-[#D96B43]/15 transition-all duration-200"
                >
                @error('email')
                    <p class="text-[11px] text-[#8B231A] mt-1.5 font-medium">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="reset-password" class="block text-xs font-bold uppercase tracking-wider text-[#26221E] mb-1.5">
                    Kata Sandi Baru
                </label>
                <input
                    type="password"
                    id="reset-password"
                    wire:model="password"
                    autocomplete="new-password"
                    placeholder="Minimal 8 karakter"
                    class="w-full rounded-2xl border-2 border-[#26221E]/10 bg-white px-4 py-3 text-sm font-medium text-[#26221E] focus:outline-none focus:border-[#D96B43] focus:ring-2 focus:ring-[#D96B43]/15 transition-all duration-200"
                >
                @error('password')
                    <p class="text-[11px] text-[#8B231A] mt-1.5 font-medium">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="reset-password-confirmation" class="block text-xs font-bold uppercase tracking-wider text-[#26221E] mb-1.5">
                    Konfirmasi Kata Sandi Baru
                </label>
                <input
                    type="password"
                    id="reset-password-confirmation"
                    wire:model="password_confirmation"
                    autocomplete="new-password"
                    placeholder="Ulangi kata sandi baru"
                    class="w-full rounded-2xl border-2 border-[#26221E]/10 bg-white px-4 py-3 text-sm font-medium text-[#26221E] focus:outline-none focus:border-[#D96B43] focus:ring-2 focus:ring-[#D96B43]/15 transition-all duration-200"
                >
            </div>

            <div class="pt-1">
                <button
                    type="submit"
                    wire:loading.attr="disabled"
                    wire:target="resetPassword"
                    class="w-full py-3.5 px-6 rounded-full bg-[#B84E26] hover:bg-[#9E3F1C] text-white font-bold text-sm shadow-md transition-all duration-200 active:scale-[0.99] cursor-pointer flex items-center justify-center gap-2 disabled:opacity-60 disabled:cursor-not-allowed"
                >
                    <span wire:loading.remove wire:target="resetPassword">Simpan Kata Sandi Baru</span>
                    <span wire:loading wire:target="resetPassword">Menyimpan...</span>
                </button>
            </div>
        </form>
    </div>

    <p class="text-center text-xs text-[#5A534A] mt-8">
        &copy; {{ date('Y') }} BASOKU. Sistem Manajemen Racikan Pasar Tradisional.
    </p>
</div>
