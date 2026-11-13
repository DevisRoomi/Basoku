<div>
    <!-- Back to Home Link -->
    <div class="mb-8 flex items-center justify-between">
        <a href="{{ url('/') }}" wire:navigate class="inline-flex items-center gap-2 text-xs font-semibold text-[#5A534A] hover:text-[#D96B43] transition-colors">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
            </svg>
            <span>Kembali ke Beranda</span>
        </a>
        <span class="text-xs font-mono font-bold text-[#D96B43] uppercase tracking-wider">
            Pintu Masuk Admin
        </span>
    </div>

    <!-- Header Form -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-[#26221E] leading-tight" style="font-family: 'Playfair Display', Georgia, serif;">
            Masuk ke Akun Anda
        </h1>
        <p class="text-xs text-[#5A534A] mt-2 leading-relaxed">
            Akses laboratorium resep racikan, riwayat antrean pesanan, atau panel pengelola kios.
        </p>
    </div>

    <!-- Form Card -->
    <div class="bg-[#FAF7F2] rounded-3xl p-6 sm:p-8 shadow-sm border border-[#26221E]/10">
        <form wire:submit.prevent="authenticate" class="space-y-5">
            
            <!-- Field: Email -->
            <div>
                <label for="email" class="block text-xs font-bold uppercase tracking-wider text-[#26221E] mb-1.5">
                    Alamat Email
                </label>
                <input
                    type="email"
                    id="email"
                    wire:model="email"
                    autocomplete="email"
                    autofocus
                    placeholder="admin@basoku.com"
                    class="w-full rounded-2xl border-2 border-[#26221E]/10 bg-white px-4 py-3 text-sm font-medium text-[#26221E] placeholder:text-[#26221E]/30 focus:outline-none focus:border-[#D96B43] focus:ring-2 focus:ring-[#D96B43]/15 transition-all duration-200"
                >
                @error('email')
                    <p class="text-[11px] text-[#8B231A] mt-1.5 font-medium flex items-center gap-1">
                        <svg class="w-3.5 h-3.5 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                        </svg>
                        {{ $message }}
                    </p>
                @enderror
            </div>

            <!-- Field: Password -->
            <div>
                <div class="flex items-center justify-between mb-1.5">
                    <label for="password" class="block text-xs font-bold uppercase tracking-wider text-[#26221E]">
                        Kata Sandi
                    </label>
                </div>
                <input
                    type="password"
                    id="password"
                    wire:model="password"
                    autocomplete="current-password"
                    placeholder="••••••••"
                    class="w-full rounded-2xl border-2 border-[#26221E]/10 bg-white px-4 py-3 text-sm font-medium text-[#26221E] placeholder:text-[#26221E]/30 focus:outline-none focus:border-[#D96B43] focus:ring-2 focus:ring-[#D96B43]/15 transition-all duration-200"
                >
                @error('password')
                    <p class="text-[11px] text-[#8B231A] mt-1.5 font-medium flex items-center gap-1">
                        <svg class="w-3.5 h-3.5 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                        </svg>
                        {{ $message }}
                    </p>
                @enderror
            </div>

            <!-- Remember Me -->
            <div class="flex items-center justify-between text-xs pt-1">
                <label class="flex items-center gap-2 cursor-pointer text-[#5A534A] hover:text-[#26221E]">
                    <input
                        type="checkbox"
                        wire:model="remember"
                        class="rounded border-[#26221E]/20 text-[#D96B43] focus:ring-[#D96B43]/30"
                    >
                    <span>Ingat saya di perangkat ini</span>
                </label>
            </div>

            <!-- Submit Button -->
            <div class="pt-2">
                <button
                    type="submit"
                    wire:loading.attr="disabled"
                    class="w-full py-3.5 px-6 rounded-full bg-[#D96B43] hover:bg-[#C25932] text-white font-bold text-sm shadow-md hover:shadow-lg transition-all duration-200 active:scale-[0.99] cursor-pointer flex items-center justify-center gap-2 disabled:opacity-60 disabled:cursor-not-allowed"
                >
                    <span wire:loading.remove>Masuk ke Dasbor</span>
                    <span wire:loading class="flex items-center gap-2">
                        <svg class="animate-spin h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Memeriksa Akses...
                    </span>
                </button>
            </div>

            <!-- Tautan Registrasi Akun Baru -->
            <div class="pt-2 text-center text-xs text-[#5A534A] border-t border-[#26221E]/10">
                Belum punya akun pelanggan?
                <a href="{{ route('register') }}" wire:navigate class="font-bold text-[#D96B43] hover:underline ml-1">
                    Daftar di sini
                </a>
            </div>

        </form>
    </div>

    <!-- Footer Note -->
    <p class="text-center text-xs text-[#5A534A] mt-8">
        &copy; {{ date('Y') }} BASOKU. Sistem Manajemen Racikan Pasar Tradisional.
    </p>
</div>
