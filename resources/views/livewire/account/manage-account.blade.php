<div class="{{ $isAdmin ? 'min-h-full bg-[#FAF6F0] text-[#161210]' : 'min-h-screen flex flex-col justify-between text-[#26221E] bg-motif-kawung' }}">
    @if (! $isAdmin)
        <x-navbar />

        <!-- Header Area Pelanggan -->
        <div class="relative bg-[#2D4235] text-[#F4EFE6] pt-12 sm:pt-16 pb-16 px-4 sm:px-6 lg:px-12 border-b border-[#2D4235]/30 overflow-hidden">
            <div class="max-w-7xl mx-auto relative z-10 flex flex-col md:flex-row md:items-end justify-between gap-6">
                <div class="max-w-2xl">
                    <div class="flex items-center gap-2 mb-3">
                        <span class="w-8 h-[2px] bg-[#D96B43]"></span>
                        <span class="text-xs font-mono font-bold tracking-[0.2em] uppercase text-[#D8E0D3]">
                            Keamanan &amp; Profil Akun
                        </span>
                    </div>
                    <h1 class="text-3xl sm:text-5xl font-bold text-white tracking-tight leading-tight" style="font-family: 'Playfair Display', Georgia, serif;">
                        Pengaturan Akun
                    </h1>
                    <p class="text-sm text-[#D8E0D3]/85 mt-3 leading-relaxed">
                        Kelola identitas akun dan perbarui kata sandi Anda secara berkala untuk menjaga keamanan riwayat racikan &amp; pesanan.
                    </p>

                    <div class="mt-4 inline-flex flex-wrap p-1 bg-white/10 rounded-xl border border-white/15 gap-0.5">
                        <a
                            href="{{ route('customer.recipes') }}"
                            class="px-3.5 py-1.5 rounded-lg text-xs font-semibold text-white/75 hover:text-white hover:bg-white/10 transition-all"
                        >
                            Laboratorium Resep
                        </a>
                        <a
                            href="{{ route('customer.orders') }}"
                            class="px-3.5 py-1.5 rounded-lg text-xs font-semibold text-white/75 hover:text-white hover:bg-white/10 transition-all"
                        >
                            Riwayat Pesanan
                        </a>
                        <a
                            href="{{ route('account.settings') }}"
                            class="px-3.5 py-1.5 rounded-lg text-xs font-bold text-white bg-white/20 transition-all"
                        >
                            Pengaturan Akun
                        </a>
                    </div>
                </div>
            </div>
        </div>
    @else
        <!-- Header Panel Admin -->
        <div class="px-6 sm:px-10 pt-8 pb-6 border-b border-[#161210]/10 bg-white/60">
            <div class="max-w-5xl mx-auto">
                <span class="text-[11px] font-mono font-bold uppercase tracking-widest text-[#C83B2B]">
                    Panel Admin &bull; Keamanan Akun
                </span>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-[#161210] mt-1" style="font-family: 'Playfair Display', Georgia, serif;">
                    Pengaturan Akun &amp; Ganti Kata Sandi
                </h1>
                <p class="text-xs text-[#5A534A] mt-1">
                    Perbarui profil pengelola kios dan ubah kata sandi admin agar akses panel tetap terlindungi.
                </p>
            </div>
        </div>
    @endif

    <!-- Konten Utama -->
    <main class="{{ $isAdmin ? 'px-6 sm:px-10 py-8 max-w-5xl mx-auto w-full' : 'flex-1 py-10 sm:py-12 px-4 sm:px-6 lg:px-12 max-w-5xl mx-auto w-full' }}">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Kartu 1: Informasi Profil -->
            <section class="bg-[#FAF7F2] rounded-3xl border border-[#26221E]/10 p-6 sm:p-8 shadow-xs space-y-5">
                <div>
                    <span class="text-[10.5px] font-mono font-bold uppercase tracking-widest text-[#B84E26]">Identitas</span>
                    <h2 class="text-xl font-bold text-[#26221E] mt-0.5" style="font-family: 'Playfair Display', Georgia, serif;">
                        Informasi Profil
                    </h2>
                    <p class="text-xs text-[#5A534A] mt-1">
                        Nama dan email ini digunakan saat masuk serta pada catatan akun Anda.
                    </p>
                </div>

                @if ($profileNotice)
                    <div role="status" class="rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-900 px-4 py-3 text-xs font-semibold">
                        {{ $profileNotice }}
                    </div>
                @endif

                <form wire:submit.prevent="updateProfile" class="space-y-4">
                    <div>
                        <label for="account-name" class="block text-xs font-bold uppercase tracking-wider text-[#26221E] mb-1.5">
                            Nama Lengkap
                        </label>
                        <input
                            id="account-name"
                            type="text"
                            wire:model="name"
                            autocomplete="name"
                            class="w-full rounded-2xl border-2 border-[#26221E]/15 bg-white px-4 py-3 text-sm font-medium text-[#26221E] focus:outline-none focus:border-[#2D4235]"
                        >
                        @error('name')
                            <p class="text-[11px] text-[#8B231A] mt-1 font-semibold">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="account-email" class="block text-xs font-bold uppercase tracking-wider text-[#26221E] mb-1.5">
                            Alamat Email
                        </label>
                        <input
                            id="account-email"
                            type="email"
                            wire:model="email"
                            autocomplete="email"
                            class="w-full rounded-2xl border-2 border-[#26221E]/15 bg-white px-4 py-3 text-sm font-medium text-[#26221E] focus:outline-none focus:border-[#2D4235]"
                        >
                        @error('email')
                            <p class="text-[11px] text-[#8B231A] mt-1 font-semibold">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="pt-1">
                        <button
                            type="submit"
                            wire:loading.attr="disabled"
                            wire:target="updateProfile"
                            class="px-6 py-3 rounded-full bg-[#2D4235] hover:bg-[#1F3025] text-white font-bold text-xs shadow-sm transition-all cursor-pointer disabled:opacity-60"
                        >
                            <span wire:loading.remove wire:target="updateProfile">Simpan Perubahan Profil</span>
                            <span wire:loading wire:target="updateProfile">Menyimpan...</span>
                        </button>
                    </div>
                </form>
            </section>

            <!-- Kartu 2: Ubah Kata Sandi -->
            <section class="bg-[#FAF7F2] rounded-3xl border border-[#26221E]/10 p-6 sm:p-8 shadow-xs space-y-5">
                <div>
                    <span class="text-[10.5px] font-mono font-bold uppercase tracking-widest text-[#B84E26]">Keamanan</span>
                    <h2 class="text-xl font-bold text-[#26221E] mt-0.5" style="font-family: 'Playfair Display', Georgia, serif;">
                        Ubah Kata Sandi
                    </h2>
                    <p class="text-xs text-[#5A534A] mt-1">
                        Gunakan kata sandi kuat minimal 8 karakter yang unik dan berbeda dari sandi lama.
                    </p>
                </div>

                @if ($passwordNotice)
                    <div role="status" class="rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-900 px-4 py-3 text-xs font-semibold">
                        {{ $passwordNotice }}
                    </div>
                @endif

                <form wire:submit.prevent="updatePassword" class="space-y-4">
                    <div>
                        <label for="account-current-password" class="block text-xs font-bold uppercase tracking-wider text-[#26221E] mb-1.5">
                            Kata Sandi Saat Ini
                        </label>
                        <input
                            id="account-current-password"
                            type="password"
                            wire:model="current_password"
                            autocomplete="current-password"
                            placeholder="••••••••"
                            class="w-full rounded-2xl border-2 border-[#26221E]/15 bg-white px-4 py-3 text-sm font-medium text-[#26221E] focus:outline-none focus:border-[#2D4235]"
                        >
                        @error('current_password')
                            <p class="text-[11px] text-[#8B231A] mt-1 font-semibold">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="account-new-password" class="block text-xs font-bold uppercase tracking-wider text-[#26221E] mb-1.5">
                            Kata Sandi Baru
                        </label>
                        <input
                            id="account-new-password"
                            type="password"
                            wire:model="password"
                            autocomplete="new-password"
                            placeholder="Minimal 8 karakter"
                            class="w-full rounded-2xl border-2 border-[#26221E]/15 bg-white px-4 py-3 text-sm font-medium text-[#26221E] focus:outline-none focus:border-[#2D4235]"
                        >
                        @error('password')
                            <p class="text-[11px] text-[#8B231A] mt-1 font-semibold">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="account-password-confirmation" class="block text-xs font-bold uppercase tracking-wider text-[#26221E] mb-1.5">
                            Konfirmasi Kata Sandi Baru
                        </label>
                        <input
                            id="account-password-confirmation"
                            type="password"
                            wire:model="password_confirmation"
                            autocomplete="new-password"
                            placeholder="Ulangi kata sandi baru"
                            class="w-full rounded-2xl border-2 border-[#26221E]/15 bg-white px-4 py-3 text-sm font-medium text-[#26221E] focus:outline-none focus:border-[#2D4235]"
                        >
                    </div>

                    <div class="pt-1">
                        <button
                            type="submit"
                            wire:loading.attr="disabled"
                            wire:target="updatePassword"
                            class="px-6 py-3 rounded-full bg-[#B84E26] hover:bg-[#9E3F1C] text-white font-bold text-xs shadow-sm transition-all cursor-pointer disabled:opacity-60"
                        >
                            <span wire:loading.remove wire:target="updatePassword">Perbarui Kata Sandi</span>
                            <span wire:loading wire:target="updatePassword">Memperbarui...</span>
                        </button>
                    </div>
                </form>
            </section>
        </div>
    </main>

    @if (! $isAdmin)
        <x-footer />
    @endif
</div>
