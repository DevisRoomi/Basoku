<?php

namespace App\Services;

use App\Models\RecipeFormula;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GeminiConsultantService
{
    /**
     * Kirim pertanyaan konsultasi adonan bakso ke Google Gemini API,
     * dengan proteksi Graceful Degradation (Anti-Crash) ke fallback lokal cerdas.
     *
     * @param  array<string, mixed>  $context
     */
    public function ask(string $question, array $context = []): string
    {
        $apiKey = config('services.gemini.key');

        // Jika API Key belum disetel di .env, langsung berikan panduan lokal cerdas
        if (empty($apiKey)) {
            Log::info('Gemini API Key tidak disetel. Menggunakan panduan cerdas lokal BASOKU.');

            return $this->getLocalFallback($question);
        }

        $baseUrl = rtrim(config('services.gemini.base_url', 'https://generativelanguage.googleapis.com/v1beta'), '/');
        $model = config('services.gemini.model', 'gemini-1.5-flash');
        $endpoint = "{$baseUrl}/models/{$model}:generateContent?key={$apiKey}";

        $contents = $this->buildConversationContents($question, $context['history'] ?? []);

        $payload = [
            'systemInstruction' => [
                'parts' => [
                    ['text' => $this->getSystemPrompt()],
                ],
            ],
            'contents' => $contents,
            'generationConfig' => [
                'temperature' => 0.7,
                'maxOutputTokens' => 800,
            ],
        ];

        try {
            $httpClient = Http::timeout(30)
                ->withHeaders(['x-goog-api-key' => $apiKey])
                ->withOptions(['force_ip_resolve' => 'v4']);
            if (! config('services.gemini.verify_ssl', true)) {
                $httpClient = $httpClient->withoutVerifying();
            }

            $response = $httpClient->post($endpoint, $payload);

            if ($response->successful()) {
                $data = $response->json();
                $reply = $data['candidates'][0]['content']['parts'][0]['text'] ?? null;

                if (! empty($reply)) {
                    return trim($reply);
                }
            }

            Log::warning('Gemini API merespon non-200 atau kosong.', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return $this->getLocalFallback($question);
        } catch (\Throwable $e) {
            Log::error('Gemini API Error: '.$e->getMessage(), [
                'exception' => get_class($e),
            ]);

            return $this->getLocalFallback($question);
        }
    }

    /**
     * Susun array multi-turn contents sesuai kontrak Gemini API (dimulai dengan role 'user', dibatasi 10 pesan terakhir).
     *
     * @param  array<int, array{role?: string, message?: string}>  $history
     * @return array<int, array{role: string, parts: array<int, array{text: string}>}>
     */
    protected function buildConversationContents(string $question, array $history): array
    {
        $contents = [];
        $recentHistory = array_slice($history, -10);

        foreach ($recentHistory as $entry) {
            if (! is_array($entry)) {
                continue;
            }

            $text = trim((string) ($entry['message'] ?? ''));
            if ($text === '') {
                continue;
            }

            $role = ($entry['role'] ?? '') === 'assistant' ? 'model' : 'user';

            // Gemini API mewajibkan percakapan dimulai dengan role 'user' (lewati pesan sambutan awal asisten)
            if (empty($contents) && $role === 'model') {
                continue;
            }

            $contents[] = [
                'role' => $role,
                'parts' => [
                    ['text' => $text],
                ],
            ];
        }

        // Pastikan pertanyaan terbaru selalu berada di akhir tanpa duplikasi
        $lastEntry = end($contents);
        $alreadyAppended = is_array($lastEntry)
            && ($lastEntry['role'] ?? '') === 'user'
            && ($lastEntry['parts'][0]['text'] ?? '') === trim($question);

        if (! $alreadyAppended) {
            $contents[] = [
                'role' => 'user',
                'parts' => [
                    ['text' => trim($question)],
                ],
            ];
        }

        return $contents;
    }

    /**
     * System Prompt persona konsultan adonan pasar BASOKU dilengkapi profil kios.
     */
    public function getSystemPrompt(): string
    {
        $presets = RecipeFormula::orderedPresets();
        $ratioSummary = sprintf(
            'Enak (%s — %s), Seimbang (%s — %s), Ekonomis (%s — %s), serta mode Kustom Bebas 10%%–450%% (hingga cilok/bakso aci)',
            $presets['enak']['flour_pct'],
            $presets['enak']['per_kg_example'],
            $presets['seimbang']['flour_pct'],
            $presets['seimbang']['per_kg_example'],
            $presets['ekonomis']['flour_pct'],
            $presets['ekonomis']['per_kg_example'],
        );

        return <<<PROMPT
Anda adalah "Tanya BASOKU" — Asisten Konsultan Ahli Adonan & Selepan Pasar dari kios BASOKU.

Profil & Informasi Resmi Kios BASOKU:
- Nama Kios: BASOKU (Spesialis Bahan & Selepan Bakso)
- Lokasi Kios: Los Selepan Pasar Klakah, Kecamatan Klakah, Kabupaten Lumajang, Jawa Timur
- Koordinat GPS: 7°59'31.5"S 113°14'58.9"E (-7.992083, 113.249694)
- Patokan Lokasi: Area Los Selepan Pasar Klakah, dekat Puskesmas Klakah & Jl. Gn. Ringgit
- Jam Buka Operasional: Setiap hari (Senin s/d Minggu), buka subuh pukul 04.00 - 12.00 WIB
- Layanan Utama: Belanja bahan baku segar (daging sapi gandik/paha, urat sengkel/brisket, tepung tapioka, aneka bumbu) dan jasa giling/selepan adonan bakso berkualitas tinggi.

Persona & Pengetahuan Ahli:
1. Ramah, hangat, bersahabat, santun khas pedagang pasar nusantara yang penuh kearifan lokal ("Halo sedulur bakso!").
2. Sangat menguasai ilmu adonan bakso:
   - Kimia protein daging dingin: peran protein miosin yang harus diekstrak garam pada suhu dingin (< 15°C) menggunakan es batu (15%-20% dari bobot daging).
   - Rasio tepung tapioka/aren aktif di toko BASOKU saat ini: {$ratioSummary}.
   - Pemilihan bagian daging: Gandik/Paha Belakang (Topside) untuk bakso halus kenyal, Sengkel/Urat Brisket untuk bakso urat.
   - Urutan penggilingan di mesin selepan: Daging + es + garam di awal; tepung dan bumbu penyedap di akhir adonan.
   - Estimasi butir & porsi: 1 kg daging sapi menghasilkan sekitar 70-80 butir ukuran sedang (~18-20g) atau 14-16 porsi mangkok (bertambah seiring rasio tepung).
3. Berikan saran yang praktis, higienis, dan realistis untuk diaplikasikan di dapur rumahan maupun pengusaha bakso keliling/kios.
4. Jika ditanya seputar alamat, lokasi, rute, jam buka, atau kontak kios BASOKU, jelaskan secara ramah dengan informasi lokasi Pasar Klakah di atas.
5. Format jawaban dengan paragraf ringkas atau poin-poin agar nyaman dibaca di layar ponsel.
PROMPT;
    }

    /**
     * Fallback jawaban cerdas lokal berbasis basis pengetahuan kios selepan BASOKU
     * saat API offline, timeout, kuota habis, atau key belum disetel.
     */
    public function getLocalFallback(string $question): string
    {
        $q = strtolower($question);

        // 0. Deteksi Pertanyaan di Luar Topik (seperti Cuaca)
        if (
            str_contains($q, 'cuaca') ||
            str_contains($q, 'hujan') ||
            str_contains($q, 'prakiraan') ||
            str_contains($q, 'suhu udara')
        ) {
            return "🌤️ **Waduh, saya konsultan adonan bakso jadi belum bisa mengecek cuaca langsung sedulur.**\n\nTapi kalau mau belanja daging segar atau giling adonan, Los Selepan Pasar Klakah buka setiap hari subuh **04.00 – 12.00 WIB**!";
        }

        // 1. Topik Harga & Ketersediaan Es Batu
        if ((str_contains($q, 'harga') && str_contains($q, 'es')) || str_contains($q, 'harga es') || str_contains($q, 'beli es')) {
            return "🧊 **Harga & Ketersediaan Es Batu di Kios BASOKU:**\n\n"
                ."Di kios BASOKU Los Selepan Pasar Klakah, kami selalu menyediakan es batu kristal higienis untuk kebutuhan adonan bakso sedulur:\n"
                ."• **Es Batu Kemasan/Plastik:** Tersedia mulai **Rp2.000 – Rp5.000** per kantong.\n"
                ."• **Untuk Penggilingan:** Takaran es batu (15% – 20% dari bobot daging) sudah kami siapkan agar adonan bakso tetap dingin di bawah 15°C dan kenyal alami.\n\n"
                .'Monggo langsung merapat ke kios subuh **04.00 – 12.00 WIB**!';
        }

        // 2. Topik Alamat, Lokasi, Patokan, Jam Buka, & Profil Kios Pasar Klakah
        if (
            str_contains($q, 'alamat') ||
            str_contains($q, 'lokasi') ||
            str_contains($q, 'tempat') ||
            str_contains($q, 'dimana') ||
            str_contains($q, 'di mana') ||
            (str_contains($q, 'kios') && ! str_contains($q, 'harga')) ||
            (str_contains($q, 'pasar') && ! str_contains($q, 'giling') && ! str_contains($q, 'selepan') && ! str_contains($q, 'harga'))
        ) {
            return "📍 **Lokasi & Profil Kios BASOKU:**\n\n"
                ."• **Nama Kios:** BASOKU (Spesialis Bahan & Selepan Bakso)\n"
                ."• **Alamat:** Los Selepan Pasar Klakah, Kecamatan Klakah, Kabupaten Lumajang, Jawa Timur.\n"
                ."• **Patokan Lokasi:** Di dalam area Los Selepan Pasar Klakah, dekat Puskesmas Klakah & akses Jl. Gn. Ringgit.\n"
                ."• **Koordinat GPS:** `7°59'31.5\"S 113°14'58.9\"E` (-7.992083, 113.249694).\n"
                ."• **Jam Buka:** Setiap hari subuh **04.00 – 12.00 WIB**.\n"
                ."• **Layanan:** Belanja bahan baku segar (daging sapi gandik/paha pilihan, urat sengkel/brisket, tepung tapioka berkualitas, bumbu lengkap) dan jasa giling/selepan adonan bakso higienis.\n\n"
                .'Monggo pinarak ke kios subuh-subuh untuk mendapatkan daging segar terbaik dan adonan bakso kenyal alami!';
        }

        // 3. Topik Es Batu & Suhu Dingin (Protein Miosin)
        if (str_contains($q, 'es batu') || str_contains($q, 'suhu') || str_contains($q, 'dingin') || str_contains($q, 'miosin') || preg_match('/\bes\b/i', $q)) {
            return "🧊 **Takaran Es Batu & Rahasia Protein Miosin:**\n\n"
                ."Saat menggiling adonan di mesin selepan, gunakan es batu sebanyak **15% – 20% dari bobot daging segar** (sekitar 150g – 200g es per 1 kg daging).\n\n"
                ."• **Mengapa wajib pakai es?** Gesekan pisau mesin selepan menimbulkan panas tinggi. Suhu adonan wajib dijaga **di bawah 15°C** agar protein miosin daging tidak rusak (terdenaturasi).\n"
                ."• **Hasilnya:** Protein miosin yang terjaga dingin akan mengikat air dan lemak secara sempurna, menghasilkan tekstur bakso yang kenyal alami ('kemripik') tanpa perlu obat pengenyal kimia berbahaya!";
        }

        // 3. Topik Estimasi Porsi & Jumlah Butir dari 1 kg
        if (str_contains($q, 'porsi') || str_contains($q, 'butir') || str_contains($q, 'hasil') || str_contains($q, '1 kg')) {
            return "🥣 **Estimasi Butir & Porsi dari 1 Kg Daging Sapi:**\n\n"
                ."Dengan formula racikan standar pasar (1 kg daging + 200g tepung tapioka + 200g es batu + 50g bumbu), total bobot adonan basah adalah sekitar **1,45 kg**.\n\n"
                ."• **Bakso Ukuran Sedang (Diameter ± 3 cm, bobot ~18–20g):** Menghasilkan **70 – 80 butir bakso**.\n"
                ."• **Penyajian Mangkok:** Jika per mangkok berisi 5 butir bakso, maka 1 kg daging cukup untuk **14 – 16 porsi mangkok bakso** kenyang.\n"
                .'• *Tips:* Jika menggunakan formula ekonomis (tepung 400g), hasil butir bisa bertambah hingga 95 – 110 butir!';
        }

        // 4. Topik Bagian Daging & Bakso Urat
        if (str_contains($q, 'urat') || str_contains($q, 'sengkel') || str_contains($q, 'gandik') || str_contains($q, 'bagian') || str_contains($q, 'paha')) {
            return "🥩 **Rekomendasi Bagian Daging Terbaik:**\n\n"
                ."• **Bakso Halus Kenyal:** Gunakan daging sapi bagian **Paha Belakang (Gandik / Topside)** atau **Pendas**. Bagian ini memiliki serat panjang, padat miosin, dan minim lemak sehingga daya ikat airnya paling prima.\n"
                ."• **Bakso Urat Istimewa:** Kombinasikan 70% daging gandik dengan 30% urat Sengkel atau Sandung Lamur (*brisket*). Minta operator selepan untuk mencincang kasar uratnya di tahap akhir penggilingan agar tekstur 'krenyes' uratnya terasa mantap!";
        }

        // 5. Topik Rasio Tepung Tapioka / Sagu (100% Sinkron dengan Formula Admin DB)
        if (str_contains($q, 'tepung') || str_contains($q, 'tapioka') || str_contains($q, 'sagu') || str_contains($q, 'rasio')) {
            $presets = RecipeFormula::orderedPresets();

            return "⚖️ **Kaidah Rasio Tepung Tapioka BASOKU (Acuan per 1 kg Daging):**\n\n"
                ."Tepung tapioka berfungsi sebagai pengikat adonan. Sesuaikan persentasenya dengan target selera Anda:\n\n"
                ."1. **{$presets['enak']['name']} ({$presets['enak']['flour_pct']}):** {$presets['enak']['per_kg_example']} ({$presets['enak']['explicit_ratio']}). {$presets['enak']['description']}\n"
                ."2. **{$presets['seimbang']['name']} ({$presets['seimbang']['flour_pct']}):** {$presets['seimbang']['per_kg_example']} ({$presets['seimbang']['explicit_ratio']}). {$presets['seimbang']['description']}\n"
                ."3. **{$presets['ekonomis']['name']} ({$presets['ekonomis']['flour_pct']}):** {$presets['ekonomis']['per_kg_example']} ({$presets['ekonomis']['explicit_ratio']}). {$presets['ekonomis']['description']}\n"
                .'4. **Kustom Bebas (10% – 450%):** Bebas atur sendiri dari bakso urat super daging sampai bakso aci / cilok daging.';
        }

        // 6. Topik Tips Penggilingan di Mesin Selepan & Garam di Awal
        if (str_contains($q, 'selepan') || str_contains($q, 'giling') || str_contains($q, 'pengenyal') || str_contains($q, 'stpp')) {
            return "⚙️ **Tips Emas Saat Giling di Los Selepan Pasar:**\n\n"
                ."1. **Garam di Awal:** Pastikan garam kasar dimasukkan saat daging pertama kali digiling bersama sebagian pecahan es batu. Garam berfungsi membuka ikatan serat protein miosin agar getah daging keluar.\n"
                ."2. **Tepung di Akhir:** Jangan masukkan tepung di awal! Masukkan tapioka dan bumbu bawang setelah serat daging lumat sempurna agar adonan tidak liat atau bantat.\n"
                .'3. **Segera Rebus:** Begitu adonan selesai digiling, bawa pulang dalam wadah tertutup dingin dan segera cetak ke dalam air hangat kuku (sekitar 60°C–70°C) sebelum dididihkan.';
        }

        // 7. Default Fallback Umum
        return "👋 **Halo sedulur BASOKU!**\n\n"
            ."Saya siap membantu konsultasi adonan bakso pasar Anda. Kunci utama adonan bakso sempurna ada pada 3 pilar:\n"
            ."1. **Kesegaran Daging:** Gunakan daging segar yang belum terkena air mentah atau pembekuan lama.\n"
            ."2. **Suhu Dingin:** Tambahkan es batu 15%-20% saat giling agar protein miosin tidak rusak.\n"
            ."3. **Keseimbangan Formula:** Sesuaikan takaran tepung tapioka (10%-20%) dan garam kasar untuk kekenyalan alami.\n\n"
            .'Ada takaran racikan spesifik atau pertanyaan seputar porsi yang ingin Anda tanyakan?';
    }
}
