# BASOKU - Deployment Tracker & Pre-Deployment Log

Dokumen ini mencatat seluruh rangkaian tahapan, audit, optimasi, pembersihan, dan verifikasi sebelum aplikasi BASOKU dideploy ke production.

---

## 1. Status Lingkungan Saat Ini (Baseline)

- **Tanggal & Waktu Inisialisasi:** 24 November 2026
- **Framework & Runtime:** Laravel 13.35.0 (PHP 8.5.11 / PHP 8.4 compatible)
- **Database:** FlyEnv MySQL (Port 3306)
- **Database Status:** Database `basoku` aktif, seluruh migrasi (13 migrasi termasuk `alter_start_type_in_orders_and_saved_recipes`) berhasil dijalankan.
- **Web Server:** Aktif via Laravel Herd di `http://basoku.test` (PHP 8.4.26 + Nginx 1.25.2).
- **Test Suite Baseline:** 88 tests passed, 364 assertions (100% passing).
- **Frontend Assets:** Vite build aktif (`public/build/manifest.json`).

---

## 2. Roadmap Tahapan Pre-Deployment

| No | Tahapan | Status | Ringkasan Tindakan |
|---|---|---|---|
| 1 | **Menjalankan Web & Baseline Setup** | ✅ Selesai | Laravel Herd (`http://basoku.test`) & FlyEnv MySQL aktif, migrasi lengkap, 88 test pass. |
| 2 | **Pembersihan Kode Sampah & Debugging** | ✅ Selesai | Hapus 10 file screenshot sisa (2.75 MB), verifikasi zero dead code/debug statement, sinkronisasi `.env.example`. |
| 3 | **Hardening Keamanan & Otorisasi** | ✅ Selesai | Defense-in-depth `boot()` di seluruh admin Livewire, RateLimiter brute-force guard (Login, Register, Checkout), Force HTTPS production, API key header Gemini. |
| 4 | **Optimasi Performa & Caching** | ✅ Selesai | Reduksi query drastis (Dashboard: 26 query -> 2 query, Orders: 5 query -> 1 query), kompilasi Vite build aset produksi, validasi caching Laravel (`route`, `config`, `view`). |
| 5 | **Audit Responsivitas Lintas Layar** | ✅ Selesai | Cek & perbaiki layout mobile (375px), tablet (768px), desktop: touch targets 44px+ di navbar, mitigasi CLS gambar (`width`/`height`), layout parameter kalkulator responsif, theme-color & manifest PWA. |
| 6 | **Peningkatan Kenyamanan & UX** | ✅ Selesai | Feedback loading state pada tombol aksi Livewire (`wire:loading`, disabled state, spinner), kelengkapan label aksesibilitas (`aria-label`) pada kontrol interaktif, pencegahan multi-klik/double-submit. |
| 7 | **Verifikasi Akhir, Audit Lighthouse & Checklist Deploy** | ✅ Selesai | Audit Google Lighthouse (Accessibility: 100, Best Practices: 100, SEO: 100, Agentic Browsing: 100%, Performance: 93-97), Full test suite check (88/88 passed), dan checklist produksi siap jalan. |

---

## 3. Log Perubahan (Change Log)

### [Tahap 1] Inisialisasi Lingkungan & Web Server
- **Waktu:** 24 November 2026
- **Aksi:**
  - Menghubungkan project `basoku` ke Laravel Herd via directory junction di `C:\Users\Lenovo\.config\herd\config\valet\Sites\basoku`.
  - Mengintegrasikan dengan FlyEnv MySQL di port 3306.
  - Menjalankan migrasi pending `2026_11_04_233607_alter_start_type_in_orders_and_saved_recipes` pada database `basoku`.
  - Memverifikasi response HTTP `http://basoku.test` (Status 200 OK via Nginx + PHP 8.4).
  - Menjalankan test suite baseline (88 tests passed, 364 assertions).
  - Membuat dan memperbarui file pelacak `DEPLOYMENT_TRACKER.md`.

### [Tahap 2] Pembersihan Kode Sampah & Debugging
- **Waktu:** 24 November 2026
- **Aksi:**
  - Menghapus 10 file tangkapan layar lokal sisa audit/testing di root directory (`screenshot_dial_card_centered.png`, `screenshot_dial_focused.png`, `screenshot_dial_in_action.png`, `screenshot_dial_refined.png`, `screenshot_dial_timbangan.png`, `screenshot_hero_clean.png`, `screenshot_hero_mobile.png`, `screenshot_hero_refined_motion.png`, `screenshot_scroll_motion.png`, `screenshot_test_kalkulator_modal.png`) membebaskan ruang ~2.75 MB.
  - Memindai codebase (`app/`, `resources/`, `routes/`): terverifikasi bebas dari `dd()`, `dump()`, `var_dump()`, dan `console.log()` sisa debug.
  - Memperbarui [`.env.example`](file:///c:/Devis/Workspace/Projects/BASOKU/basoku_code/.env.example) agar mencakup seluruh parameter produksi yang dibutuhkan (`GEMINI_BASE_URL`, `GEMINI_VERIFY_SSL`, `KIOSK_WA_NUMBER`, `KIOSK_GRINDING_FEE_PER_KG`, `KIOSK_OPEN_HOUR`, `KIOSK_CLOSE_HOUR`).
  - Menjalankan ulang test suite: 88 passed, 364 assertions (100% lulus).

### [Tahap 3] Hardening Keamanan & Otorisasi
- **Waktu:** 24 November 2026
- **Aksi:**
  - Menambahkan proteksi berlapis (*defense-in-depth*) lifecycle hook `boot()` pada 4 komponen Livewire Admin ([`Dashboard.php`](file:///c:/Devis/Workspace/Projects/BASOKU/basoku_code/app/Livewire/Admin/Dashboard.php), [`ManageMaterials.php`](file:///c:/Devis/Workspace/Projects/BASOKU/basoku_code/app/Livewire/Admin/ManageMaterials.php), [`ManageOrders.php`](file:///c:/Devis/Workspace/Projects/BASOKU/basoku_code/app/Livewire/Admin/ManageOrders.php), [`ManageRecipes.php`](file:///c:/Devis/Workspace/Projects/BASOKU/basoku_code/app/Livewire/Admin/ManageRecipes.php)) untuk menjamin seluruh mutasi data Livewire tertolak jika bukan admin.
  - Mengimplementasikan `RateLimiter` anti-bruteforce pada gerbang masuk ([`Login.php`](file:///c:/Devis/Workspace/Projects/BASOKU/basoku_code/app/Livewire/Auth/Login.php): max 5 percobaan/menit), pendaftaran ([`Register.php`](file:///c:/Devis/Workspace/Projects/BASOKU/basoku_code/app/Livewire/Auth/Register.php): max 5 pendaftaran/5 menit), dan checkout ([`RecipeCalculator.php`](file:///c:/Devis/Workspace/Projects/BASOKU/basoku_code/app/Livewire/RecipeCalculator.php): max 10 order/menit).
  - Mengonfigurasi `URL::forceScheme('https')` pada [`AppServiceProvider.php`](file:///c:/Devis/Workspace/Projects/BASOKU/basoku_code/app/Providers/AppServiceProvider.php) saat berada di environment production.
  - Mengamankan komunikasi Gemini API pada [`GeminiConsultantService.php`](file:///c:/Devis/Workspace/Projects/BASOKU/basoku_code/app/Services/GeminiConsultantService.php) dengan menyertakan header resmi `x-goog-api-key`.
  - Merapikan seluruh sintaks kode PHP sesuai standar PSR-12 menggunakan Laravel Pint (`vendor/bin/pint --dirty --format agent`).
  - Verifikasi otomatis: Seluruh 88 test lulus tanpa ada regresi (100% green).

### [Tahap 4] Optimasi Performa & Caching
- **Waktu:** 24 November 2026
- **Aksi:**
  - Optimasi query analitik pada [`Dashboard.php`](file:///c:/Devis/Workspace/Projects/BASOKU/basoku_code/app/Livewire/Admin/Dashboard.php): mereduksi dari **26 query SQL** menjadi hanya **2 query**, memproses agregasi omzet valid, sebaran preferensi, dan tren 6 bulan secara in-memory.
  - Optimasi tab status pesanan pada [`ManageOrders.php`](file:///c:/Devis/Workspace/Projects/BASOKU/basoku_code/app/Livewire/Admin/ManageOrders.php): mereduksi dari 5 query `count()` terpisah menjadi 1 query `selectRaw` teragregasi.
  - Menjalankan build aset frontend produksi (`npm run build`) via Vite, menghasilkan bundel CSS & JS terkompresi dengan cache-busting hash.
  - Memvalidasi seluruh perintah caching Laravel (`route:cache`, `config:cache`, `view:cache`): terbukti berhasil tanpa konflik serialisasi.
  - Verifikasi otomatis: 88 tests passed, 364 assertions (100% lulus).

### [Tahap 7] Verifikasi Akhir, Audit Google Lighthouse & Checklist Deploy
- **Waktu:** 24 November 2026
- **Aksi:**
  - **Sertifikasi TLS/HTTPS Lokal:** Mengaktifkan sertifikat TLS lokal melalui Laravel Herd (`herd secure basoku`) pada domain `https://basoku.test`.
  - **Optimasi Kontras Aksesibilitas (WCAG AA):** Memperbaiki rasio kontras tombol aksi utama (`#BC4C22` / `#B84E26` terhadap teks putih menghasilkan rasio 5.1:1 > ambang minimum 4.5:1), memperbaiki kontras teks status verifikasi GPS (`#A93E18`), serta penyesuaian kontras teks hak cipta footer (`#3B362F` dengan rasio 7:1).
  - **Hierarki Heading Semantik:** Memperbaiki urutan heading descending pada [`hero.blade.php`](file:///c:/Devis/Workspace/Projects/BASOKU/basoku_code/resources/views/components/sections/hero.blade.php) (mengubah `<h4>` subfitur menjadi `<h2>` di bawah `<h1>`) dan [`recipe-calculator.blade.php`](file:///c:/Devis/Workspace/Projects/BASOKU/basoku_code/resources/views/livewire/recipe-calculator.blade.php) (mengubah `<h3>` ringkasan takaran menjadi `<h2>`).
  - **Aksesibilitas Form & AI Navigation:** Melengkapi elemen range input slider timbangan analog dengan `id="meatWeightRange"` dan `aria-label="Atur berat daging dalam gram"`, serta menyelaraskan teks terlihat dan nama aksesibel pada link brand navbar (`aria-label="BASOKU - Beranda"`).
  - **Audit Google Lighthouse Resmi:**
    - 🟢 **Accessibility:** **100 / 100** (Target tercapai, melampaui baseline 97)
    - 🟢 **Best Practices:** **100 / 100** (Target tercapai, 100)
    - 🟢 **SEO:** **100 / 100** (Target tercapai, 100)
    - 🟢 **Agentic Browsing:** **100% (4/4 Lulus)** (Target tercapai, melampaui baseline 2/4)
    - 🟢 **Performance:** **93 - 97 / 100** (Waktu respon cepat, First Contentful Paint 0.6s, LCP 0.9s)
  - **Formatting & Standarisasi Kode:** Menjalankan Laravel Pint (`vendor/bin/pint --dirty --format agent`) — terverifikasi bersih dan konsisten.
  - **Verifikasi Test Suite Otomatis:** Menjalankan seluruh test suite unit & fitur (`php artisan test --without-tty`): **88 passed, 364 assertions (100% lulus)**.

### [Audit Mendalam — Fase 1] Perbaikan Bug Kritis, Keamanan & Logika Inti
- **Waktu:** 25 November 2026
- **Aksi:**
  - **Timezone Aplikasi (`Asia/Jakarta` / WIB):** Mengubah [`config/app.php`](file:///c:/Devis/Workspace/Projects/BASOKU/basoku_code/config/app.php) dari hardcoded `UTC` menjadi `env('APP_TIMEZONE', 'Asia/Jakarta')` dan menyelaraskan [`.env.example`](file:///c:/Devis/Workspace/Projects/BASOKU/basoku_code/.env.example) agar validasi jadwal pickup, timestamp chat AI, dan ekspor CSV admin 100% sesuai waktu WIB.
  - **Penutupan Celah IDOR & Restore Penuh Resep Tersimpan:** Memperbaiki [`RecipeCalculator.php`](file:///c:/Devis/Workspace/Projects/BASOKU/basoku_code/app/Livewire/RecipeCalculator.php) (`mount()`) agar `?recipe_id=` hanya dapat diakses pemiliknya (`SavedRecipe::where('user_id', Auth::id())->find(...)`), menghapus fallback tidak valid (`'weight'`, `'standar'`), serta memulihkan seluruh parameter racikan (`meatType`, `flourType`, `customFlourRatio`, `useGrinding`, `extraItems`, dan jumlah pack bahan saja) saat resep dibuka ulang.
  - **Hardening Otorisasi Admin Livewire:** Mendaftarkan `EnsureAdmin` ke `Livewire::addPersistentMiddleware()` pada [`AppServiceProvider.php`](file:///c:/Devis/Workspace/Projects/BASOKU/basoku_code/app/Providers/AppServiceProvider.php) serta mengubah hook `boot()` pada 4 komponen Admin (`Dashboard`, `ManageMaterials`, `ManageOrders`, `ManageRecipes`) menjadi `abort(403)` agar request mutasi langsung dari non-admin terhenti total sebelum eksekusi aksi.
  - **Perbaikan Bug Berat Daging `0` & Kalkulasi Kalkulator:** Menambahkan `updatingStartType()` dan clamp properti publik `$meatWeight` agar perpindahan mode `ingredients_only` $\leftrightarrow$ `start_from_zero`/`own_meat` selalu memulihkan berat minimal `500g` dan `useGrinding = true`; mengganti hardcoded `15.0` di mode anggaran dengan `config('services.kiosk.grinding_fee_per_kg')`; memperhitungkan `extraItems` di mode anggaran; dan mengonsolidasikan lookup bahan inti dari 4+ query SQL menjadi 1 query ringan.
  - **Validasi Ketersediaan Stok & Rate Limiter Katalog:** Memeriksa `is_available = true` untuk bahan inti di [`RecipeCalculator.php`](file:///c:/Devis/Workspace/Projects/BASOKU/basoku_code/app/Livewire/RecipeCalculator.php) (menampilkan banner peringatan stok kosong & menonaktifkan checkout) serta menambahkan `RateLimiter` (10 req/menit) + filter `is_available = true` pada [`MaterialCatalog.php`](file:///c:/Devis/Workspace/Projects/BASOKU/basoku_code/app/Livewire/MaterialCatalog.php).
  - **Perbaikan Bug Tampilan Blade & Harga Dinamis:** Memperbaiki kebocoran sintaks Blade mentah `@{{ number_format(...) }}` di bagian bahan pelengkap [`recipe-calculator.blade.php`](file:///c:/Devis/Workspace/Projects/BASOKU/basoku_code/resources/views/livewire/recipe-calculator.blade.php), mengganti seluruh label harga daging/tepung/bumbu hardcoded menjadi data dinamis langsung dari database, dan menambahkan `wire:key` pada loop Livewire.
  - **Penyatuan Jam Operasional & Prefix Nomor Pesanan:** Menyeragamkan jam buka kios ke `config('services.kiosk.open_hour', 4)` (`04.00–12.00 WIB`) di seluruh Navbar, Footer, Modal Kalkulator, Modal Katalog, dan validasi backend; serta menyatukan format nomor pesanan WhatsApp dari `#ORD-` menjadi `#BSK-` agar identik dengan Admin & Riwayat Pesanan.
  - **Perbaikan Ekspor CSV Admin (`flour_weight`):** Mengubah [`ManageOrders.php`](file:///c:/Devis/Workspace/Projects/BASOKU/basoku_code/app/Livewire/Admin/ManageOrders.php) agar menghitung berat tepung riil dari relasi `orderItems.material` alih-alih kolom mati `$order->flour_weight` yang sebelumnya selalu `0`.
  - **Verifikasi & Regression Tests:** Menambah 6 test baru (IDOR, mode switching, stok habis, raw Blade leak, unavailable catalog checkout, non-admin Livewire `403`, isi CSV riil) serta memperbaiki test flaky di `MaterialCatalogTest`. Seluruh **94 tests passed (397 assertions)**, Laravel Pint lulus, dan `npm run build` sukses.

### [Audit Mendalam — Fase 2 & Fase 3] Fungsionalitas Fitur End-to-End, AI Multi-Turn & Konsistensi UI/UX
- **Waktu:** 25 November 2026
- **Aksi Fase 2 (Fungsionalitas Fitur & AI Consultant):**
  - **AI Consultant Multi-Turn Memory & Rate Limiter:** Memperbaiki [`GeminiConsultantService.php`](file:///c:/Devis/Workspace/Projects/BASOKU/basoku_code/app/Services/GeminiConsultantService.php) agar meneruskan riwayat percakapan (dibatasi 10 pesan terakhir, dimulai dari role `user`, tanpa duplikasi) ke payload Gemini `contents`, serta menambahkan `RateLimiter` (maks. 10 pertanyaan / 5 menit per IP) pada [`AiConsultant.php`](file:///c:/Devis/Workspace/Projects/BASOKU/basoku_code/app/Livewire/AiConsultant.php).
  - **Halaman Publik Lacak Pesanan Aman (`/lacak-pesanan`):** Membangun komponen [`TrackOrder.php`](file:///c:/Devis/Workspace/Projects/BASOKU/basoku_code/app/Livewire/TrackOrder.php) + [`track-order.blade.php`](file:///c:/Devis/Workspace/Projects/BASOKU/basoku_code/resources/views/livewire/track-order.blade.php) untuk pembeli tamu dengan verifikasi ganda wajib (Nomor Pesanan `#BSK-XXXXX` + Nomor WhatsApp ternormalisasi), rate limiter 15 percobaan/menit, progress stepper 3 tahap, dan auto-refresh 15 detik.
  - **Preservasi Draft Racikan Tamu saat Login:** [`RecipeCalculator.php`](file:///c:/Devis/Workspace/Projects/BASOKU/basoku_code/app/Livewire/RecipeCalculator.php) & [`Login.php`](file:///c:/Devis/Workspace/Projects/BASOKU/basoku_code/app/Livewire/Auth/Login.php) kini menyimpan draf racikan tamu ke session saat klik *"Simpan Racikan"*, mengarahkan kembali ke `/kalkulator` setelah login, dan langsung membuka modal simpan tanpa kehilangan racikan.
  - **Tombol WA Notifikasi Status Admin & Perbaikan Matematika Struk:** Menambahkan `getCustomerWaUrl()` pada [`ManageOrders.php`](file:///c:/Devis/Workspace/Projects/BASOKU/basoku_code/app/Livewire/Admin/ManageOrders.php) (menormalisasi `08...` $\rightarrow$ `628...` dan menyusun pesan WA otomatis sesuai status `pending`/`processing`/`completed`/`cancelled`) serta memperbaiki bug tampilan double-counting Jasa Selepan pada modal detail & struk thermal [`manage-orders.blade.php`](file:///c:/Devis/Workspace/Projects/BASOKU/basoku_code/resources/views/livewire/admin/manage-orders.blade.php).
- **Aksi Fase 3 (Konsistensi UI/UX & Ergonomi Layar HP):**
  - **Penyatuan Desain `/pesanan-saya`:** Memindahkan [`MyOrders.php`](file:///c:/Devis/Workspace/Projects/BASOKU/basoku_code/app/Livewire/Customer/MyOrders.php) dari layout lama `layouts.app` ke `components.layouts.app`, mereduksi 4 query count status menjadi 1 query `selectRaw`, serta menyelaraskan [`my-orders.blade.php`](file:///c:/Devis/Workspace/Projects/BASOKU/basoku_code/resources/views/livewire/customer/my-orders.blade.php) dengan `<x-navbar />`, `<x-footer />`, palet Deep Sage `#2D4235` + Oatmeal `#FAF7F2`, dan widget AI Consultant.
  - **Input Angka Manual di Timbangan Analog:** Menambahkan kolom `<input type="number">` + tombol taktil `-100g` / `+100g` di samping slider pada [`recipe-calculator.blade.php`](file:///c:/Devis/Workspace/Projects/BASOKU/basoku_code/resources/views/livewire/recipe-calculator.blade.php) serta catatan transparansi ketika `totalMarketCost > budget` di mode anggaran.
  - **Single `<h1>` di Beranda & Sinkronisasi Copywriting:** Menambahkan properti `$embedded` pada `RecipeCalculator` agar beranda ([`welcome.blade.php`](file:///c:/Devis/Workspace/Projects/BASOKU/basoku_code/resources/views/welcome.blade.php)) tepat memiliki 1 tag `<h1>`, menyelaraskan kartu rasio beranda dengan formula asli (`Enak 10:1 / 10%`, `Seimbang 5:1 / 20%`, `Ekonomis 5:2 / 40%`), memperbaiki highlight aktif navbar untuk `/pesanan-saya` & `/lacak-pesanan`, serta memperjelas judul/CTA halaman [`login.blade.php`](file:///c:/Devis/Workspace/Projects/BASOKU/basoku_code/resources/views/livewire/auth/login.blade.php).
  - **Verifikasi Otomatis:** Menambah 7 test fitur baru (`TrackOrderTest`, multi-turn & rate limit AI, preservasi draft login, normalisasi WA admin, single `<h1>` beranda). Seluruh **101 tests passed (446 assertions)**, Laravel Pint lulus, dan `npm run build` sukses dalam 2,44 detik.

### [Audit Mendalam — Fase 4 & Fase 5] Pembersihan Aset/Kode Mati, Pagination Admin, PWA & Final Sign-Off
- **Waktu:** 26 November 2026
- **Aksi Fase 4 (Pembersihan Total & Skalabilitas):**
  - **Hapus ~9,4 MB Aset Mati:** Menghapus 11 file model 3D `.glb` tidak terpakai di `public/models/` (~4,2 MB) dan video mentah `public/videos/basoku-hero.mp4` (5,22 MB), menyisakan aset teroptimasi (`basoku-hero-opt.mp4` & `basoku-hero-poster.webp`).
  - **Hapus View & Kode Legacy Mati:** Menghapus layout duplikat `resources/views/layouts/app.blade.php` & `auth.blade.php`, 4 section Blade tidak terpakai (`cta-banner`, `how-it-works`, `ingredients-preview`, `introduction`), method mati `calculateFlourPackaging()` di [`RecipeCalculator.php`](file:///c:/Devis/Workspace/Projects/BASOKU/basoku_code/app/Livewire/RecipeCalculator.php), serta blok Blade mati `$item['flour_packaging']` — sekaligus meringankan bundle CSS produksi dari 104,35 kB menjadi **92,79 kB** (gzip 16,09 kB).
  - **Pagination Panel Pesanan Admin:** Menambahkan `WithPagination` (`paginate(15)` + reset halaman saat ganti filter status) pada [`ManageOrders.php`](file:///c:/Devis/Workspace/Projects/BASOKU/basoku_code/app/Livewire/Admin/ManageOrders.php) & [`manage-orders.blade.php`](file:///c:/Devis/Workspace/Projects/BASOKU/basoku_code/resources/views/livewire/admin/manage-orders.blade.php), sambil mempertahankan ekspor CSV tetap memuat seluruh pesanan yang cocok.
  - **Aktivasi PWA Service Worker Aman:** Memperbarui [`public/sw.js`](file:///c:/Devis/Workspace/Projects/BASOKU/basoku_code/public/sw.js) ke `basoku-shell-v2` dengan pengecualian tegas untuk seluruh endpoint dinamis/autentikasi (`/livewire`, `/admin`, `/masuk`, `/daftar`, `/keluar`, `/racikan-saya`, `/pesanan-saya`, `/lacak-pesanan`), lalu mendaftarkannya pada layout utama [`components/layouts/app.blade.php`](file:///c:/Devis/Workspace/Projects/BASOKU/basoku_code/resources/views/components/layouts/app.blade.php).
  - **Higiene Git:** Menambahkan `lighthouse*.json` ke [`.gitignore`](file:///c:/Devis/Workspace/Projects/BASOKU/basoku_code/.gitignore).
- **Aksi Fase 5 (Verifikasi Final & Google Lighthouse Ulang):**
  - **Laravel Pint:** `passed`
  - **PHPUnit Feature & Unit Suite:** **102 passed / 102 tests (447 assertions)** — 100% lulus termasuk test pagination admin baru.
  - **Vite Production Build:** Sukses (`1.88s`, CSS 92,79 kB / gzip 16,09 kB) & validasi `php artisan optimize` lulus penuh.
  - **Audit Google Lighthouse Final (`https://basoku.test`):**
    - **Desktop:** Performance **92**, Accessibility **100**, Best Practices **100**, SEO **100**, Agentic Browsing **100% (4/4)**
    - **Mobile (4G Throttling):** Performance **85** *(naik dari 70)*, Accessibility **100** *(naik dari 88)*, Best Practices **100** *(naik dari 78)*, SEO **100**, Agentic Browsing **100% (4/4)** *(naik dari 50%)*

---

## 4. Checklist Eksekusi Deploy ke Server Produksi (Pre-Deployment Execution Checklist)

Berikut adalah panduan langkah demi langkah saat melakukan deployment final di server produksi (VPS / Docker / Laravel Cloud / Shared Hosting):

1. **Konfigurasi Lingkungan (`.env`):**
   ```bash
   APP_NAME=BASOKU
   APP_ENV=production
   APP_DEBUG=false
   APP_URL=https://nama-domain-anda.com
   FORCE_HTTPS=true
   ```
2. **Install Dependensi Produksi Tanpa Paket Dev:**
   ```bash
   composer install --no-dev --optimize-autoloader
   ```
3. **Migrasi Database:**
   ```bash
   php artisan migrate --force
   ```
4. **Kompilasi Aset Frontend (Vite):**
   ```bash
   npm ci
   npm run build
   ```
5. **Aktifkan Caching Produksi Laravel:**
   ```bash
   php artisan config:cache
   php artisan route:cache
   php artisan view:cache
   php artisan event:cache
   ```
6. **Pastikan Permission Direktori Storage:**
   ```bash
   chmod -R 775 storage bootstrap/cache
   ```
7. **Verifikasi Status:**
   Pastikan aplikasi dapat diakses melalui HTTPS dan seluruh aset Vite terlayani dengan respons HTTP 200.






