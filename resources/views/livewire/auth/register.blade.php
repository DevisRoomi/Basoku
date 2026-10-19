<div class="min-h-screen w-full flex bg-[#F4EFE6] text-[#26221E]">

    <!-- Sisi Kiri: Visual Split Editorial Sinematik (Desktop: Ref 1 & Ref 2) -->
    <div class="hidden lg:flex lg:w-1/2 relative bg-[#2D4235] overflow-hidden flex-col justify-between p-12 lg:p-16 text-[#F4EFE6]">
        <!-- Video Latar Sinematik -->
        <video
            class="absolute inset-0 w-full h-full object-cover object-center opacity-30 pointer-events-none select-none"
            autoplay
            muted
            loop
            playsinline
            aria-hidden="true"
        >
            <source src="{{ asset('videos/basoku-hero.mp4') }}" type="video/mp4">
        </video>

        <!-- Dark Scrim Overlay -->
        <div class="absolute inset-0 bg-gradient-to-t from-[#2D4235] via-[#2D4235]/75 to-transparent"></div>

        <!-- Top Brand Mark -->
        <div class="relative z-10">
            <a href="{{ url('/') }}" class="inline-flex items-center gap-3.5 group focus:outline-none">
                <div class="w-10 h-10 rounded-full bg-[#FAF7F2] text-[#2D4235] flex items-center justify-center font-serif font-bold text-lg shadow-md transition-transform group-hover:scale-105 shrink-0">
                    B.
                </div>
                <div>
                    <span class="text-white text-2xl font-bold tracking-tight block leading-none" style="font-family: 'Playfair Display', Georgia, serif;">
                        BASOKU<span class="text-[#D96B43]">.</span>
                    </span>
                    <span class="text-[10px] uppercase font-mono tracking-widest text-[#D8E0D3] block mt-1">
                        Toko Bahan & Selepan Keluarga
                    </span>
                </div>
            </a>
        </div>

        <!-- Center Quote & Store Philosophy -->
        <div class="relative z-10 max-w-md my-auto py-12">
            <span class="w-10 h-[2px] bg-[#D96B43] block mb-6"></span>
            <blockquote class="text-2xl lg:text-3xl font-normal leading-relaxed text-[#F4EFE6]" style="font-family: 'Playfair Display', Georgia, serif;">
                &ldquo;Simpan racikan favorit keluarga atau usaha Anda dalam satu laboratorium resep yang teratur.&rdquo;
            </blockquote>
            <p class="text-xs uppercase font-mono tracking-widest text-[#D8E0D3] mt-6">
                Laboratorium Racikan Bakso Pribadi
            </p>
        </div>

        <!-- Bottom Store Details -->
        <div class="relative z-10 pt-6 border-t border-white/10 flex items-center justify-between text-xs text-[#D8E0D3]/70 font-mono">
            <span>TAK MENGIRA-NGIRA LAGI</span>
            <span>RESEP SELALU TERSIMPAN</span>
        </div>
    </div>

    <!-- Sisi Kanan: Kartu Form Register Bertekstur Oatmeal -->
    <div class="w-full lg:w-1/2 bg-[#F4EFE6] flex items-center justify-center p-6 sm:p-12 min-h-screen relative">
        <div class="w-full max-w-md">

            <!-- Back to Home Link -->
            <div class="mb-8 flex items-center justify-between">
                <a href="{{ url('/') }}" class="inline-flex items-center gap-2 text-xs font-semibold text-[#5A534A] hover:text-[#D96B43] transition-colors">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
                    </svg>
                    <span>Kembali ke Beranda</span>
                </a>
                <span class="text-xs font-mono font-bold text-[#D96B43] uppercase tracking-wider">
                    Registrasi Pelanggan
                </span>
            </div>

            <!-- Header Form -->
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-[#26221E] leading-tight" style="font-family: 'Playfair Display', Georgia, serif;">
                    Daftar Akun Baru
                </h1>
                <p class="text-xs text-[#5A534A] mt-2 leading-relaxed">
                    Simpan racikan favorit dan hitung estimasi belanja pasar Anda dengan mudah.
                </p>
            </div>

            <!-- Form Card -->
            <div class="bg-[#FAF7F2] rounded-[2.5rem] p-6 sm:p-8 shadow-sm border border-[#26221E]/10">
                <form wire:submit.prevent="register" class="space-y-4">
                    
                    <!-- Field: Nama Lengkap -->
                    <div>
                        <label for="name" class="block text-xs font-bold uppercase tracking-wider text-[#26221E] mb-1.5">
                            Nama Lengkap
                        </label>
                        <input
                            type="text"
                            id="name"
                            wire:model="name"
                            autocomplete="name"
                            autofocus
                            placeholder="Contoh: Budi Santoso"
                            class="w-full rounded-2xl border-2 border-[#26221E]/10 bg-white px-4 py-2.5 text-sm font-medium text-[#26221E] placeholder:text-[#26221E]/30 focus:outline-none focus:border-[#D96B43] focus:ring-2 focus:ring-[#D96B43]/15 transition-all duration-200"
                        >
                        @error('name')
                            <p class="text-[11px] text-[#8B231A] mt-1 font-medium flex items-center gap-1">
                                <svg class="w-3.5 h-3.5 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                </svg>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

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
                            placeholder="nama@email.com"
                            class="w-full rounded-2xl border-2 border-[#26221E]/10 bg-white px-4 py-2.5 text-sm font-medium text-[#26221E] placeholder:text-[#26221E]/30 focus:outline-none focus:border-[#D96B43] focus:ring-2 focus:ring-[#D96B43]/15 transition-all duration-200"
                        >
                        @error('email')
                            <p class="text-[11px] text-[#8B231A] mt-1 font-medium flex items-center gap-1">
                                <svg class="w-3.5 h-3.5 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                </svg>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <!-- Field: Password -->
                    <div>
                        <label for="password" class="block text-xs font-bold uppercase tracking-wider text-[#26221E] mb-1.5">
                            Kata Sandi
                        </label>
                        <input
                            type="password"
                            id="password"
                            wire:model="password"
                            autocomplete="new-password"
                            placeholder="Minimal 8 karakter"
                            class="w-full rounded-2xl border-2 border-[#26221E]/10 bg-white px-4 py-2.5 text-sm font-medium text-[#26221E] placeholder:text-[#26221E]/30 focus:outline-none focus:border-[#D96B43] focus:ring-2 focus:ring-[#D96B43]/15 transition-all duration-200"
                        >
                        @error('password')
                            <p class="text-[11px] text-[#8B231A] mt-1 font-medium flex items-center gap-1">
                                <svg class="w-3.5 h-3.5 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                </svg>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <!-- Field: Password Confirmation -->
                    <div>
                        <label for="password_confirmation" class="block text-xs font-bold uppercase tracking-wider text-[#26221E] mb-1.5">
                            Konfirmasi Kata Sandi
                        </label>
                        <input
                            type="password"
                            id="password_confirmation"
                            wire:model="password_confirmation"
                            autocomplete="new-password"
                            placeholder="Ketik ulang kata sandi"
                            class="w-full rounded-2xl border-2 border-[#26221E]/10 bg-white px-4 py-2.5 text-sm font-medium text-[#26221E] placeholder:text-[#26221E]/30 focus:outline-none focus:border-[#D96B43] focus:ring-2 focus:ring-[#D96B43]/15 transition-all duration-200"
                        >
                    </div>

                    <!-- Submit Button -->
                    <div class="pt-3">
                        <button
                            type="submit"
                            wire:loading.attr="disabled"
                            class="w-full py-3.5 px-6 rounded-full bg-[#D96B43] hover:bg-[#C25932] text-white font-bold text-sm shadow-md hover:shadow-lg transition-all duration-200 active:scale-[0.99] cursor-pointer flex items-center justify-center gap-2 disabled:opacity-60 disabled:cursor-not-allowed"
                        >
                            <span wire:loading.remove>Daftar Akun Baru</span>
                            <span wire:loading class="flex items-center gap-2">
                                <svg class="animate-spin h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                Mendaftarkan...
                            </span>
                        </button>
                    </div>

                </form>

                <div class="mt-6 pt-5 border-t border-[#26221E]/10 text-center">
                    <p class="text-xs text-[#5A534A]">
                        Sudah punya akun?
                        <a href="{{ route('login') }}" class="font-bold text-[#D96B43] hover:underline ml-1">
                            Masuk di sini
                        </a>
                    </p>
                </div>
            </div>

            <!-- Footer Note -->
            <p class="text-center text-xs text-[#5A534A] mt-8">
                &copy; {{ date('Y') }} BASOKU. Sistem Manajemen Racikan Pasar Tradisional.
            </p>

        </div>
    </div>

</div>
