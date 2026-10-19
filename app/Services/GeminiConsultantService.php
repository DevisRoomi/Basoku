<?php

namespace App\Services;

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

        $payload = [
            'systemInstruction' => [
                'parts' => [
                    ['text' => $this->getSystemPrompt()],
                ],
            ],
            'contents' => [
                [
                    'role' => 'user',
                    'parts' => [
                        ['text' => $question],
                    ],
                ],
            ],
            'generationConfig' => [
                'temperature' => 0.7,
                'maxOutputTokens' => 800,
            ],
        ];

        try {
            $response = Http::timeout(10)->post($endpoint, $payload);

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
            Log::warning('Gemini API gagal dihubungi (timeout/offline): '.$e->getMessage());

            return $this->getLocalFallback($question);
        }
    }

    /**
     * System Prompt persona konsultan adonan pasar BASOKU.
     */
    public function getSystemPrompt(): string
    {
        return <<<'PROMPT'
Anda adalah "Tanya BASOKU" — Konsultan Ahli Adonan & Selepan Pasar Tradisional terpercaya dari platform BASOKU.
Persona & Gaya Komunikasi:
1. Ramah, hangat, bersahabat, santun khas pedagang pasar nusantara yang penuh kearifan lokal ("Halo sedulur bakso!").
2. Sangat menguasai ilmu adonan bakso:
   - Kimia protein daging dingin: peran protein miosin yang harus diekstrak garam pada suhu dingin (< 15°C) menggunakan es batu (15%-20% dari bobot daging).
   - Rasio tepung tapioka/aren: Enak (10-15%), Seimbang (20%), Ekonomis (30-40%).
   - Pemilihan bagian daging: Gandik/Paha Belakang (Topside) untuk bakso halus kenyal, Sengkel/Urat Brisket untuk bakso urat.
   - Urutan penggilingan di mesin selepan: Daging + es + garam di awal; tepung dan bumbu penyedap di akhir adonan.
   - Estimasi butir & porsi: 1 kg daging sapi menghasilkan sekitar 70-80 butir ukuran sedang (~18-20g) atau 14-16 porsi mangkok.
3. Berikan saran yang praktis, higienis, dan realistis untuk diaplikasikan di dapur rumahan maupun pengusaha bakso keliling/kios.
4. Jika pertanyaan di luar adonan bakso atau daging, jawab dengan sopan dan kembalikan obrolan ke seputar racikan bakso pasar.
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

        // 1. Topik Es Batu & Suhu Dingin (Protein Miosin)
        if (str_contains($q, 'es batu') || str_contains($q, 'suhu') || str_contains($q, 'dingin') || str_contains($q, 'miosin') || preg_match('/\bes\b/i', $q)) {
            return "🧊 **Takaran Es Batu & Rahasia Protein Miosin:**\n\n"
                ."Saat menggiling adonan di mesin selepan, gunakan es batu sebanyak **15% – 20% dari bobot daging segar** (sekitar 150g – 200g es per 1 kg daging).\n\n"
                ."• **Mengapa wajib pakai es?** Gesekan pisau mesin selepan menimbulkan panas tinggi. Suhu adonan wajib dijaga **di bawah 15°C** agar protein miosin daging tidak rusak (terdenaturasi).\n"
                ."• **Hasilnya:** Protein miosin yang terjaga dingin akan mengikat air dan lemak secara sempurna, menghasilkan tekstur bakso yang kenyal alami ('kemripik') tanpa perlu obat pengenyal kimia berbahaya!";
        }

        // 2. Topik Estimasi Porsi & Jumlah Butir dari 1 kg
        if (str_contains($q, 'porsi') || str_contains($q, 'butir') || str_contains($q, 'hasil') || str_contains($q, '1 kg')) {
            return "🥣 **Estimasi Butir & Porsi dari 1 Kg Daging Sapi:**\n\n"
                ."Dengan formula racikan standar pasar (1 kg daging + 200g tepung tapioka + 200g es batu + 50g bumbu), total bobot adonan basah adalah sekitar **1,45 kg**.\n\n"
                ."• **Bakso Ukuran Sedang (Diameter ± 3 cm, bobot ~18–20g):** Menghasilkan **70 – 80 butir bakso**.\n"
                ."• **Penyajian Mangkok:** Jika per mangkok berisi 5 butir bakso, maka 1 kg daging cukup untuk **14 – 16 porsi mangkok bakso** kenyang.\n"
                .'• *Tips:* Jika menggunakan formula ekonomis (tepung 400g), hasil butir bisa bertambah hingga 95 – 110 butir!';
        }

        // 3. Topik Bagian Daging & Bakso Urat
        if (str_contains($q, 'urat') || str_contains($q, 'sengkel') || str_contains($q, 'gandik') || str_contains($q, 'bagian') || str_contains($q, 'paha')) {
            return "🥩 **Rekomendasi Bagian Daging Terbaik:**\n\n"
                ."• **Bakso Halus Kenyal:** Gunakan daging sapi bagian **Paha Belakang (Gandik / Topside)** atau **Pendas**. Bagian ini memiliki serat panjang, padat miosin, dan minim lemak sehingga daya ikat airnya paling prima.\n"
                ."• **Bakso Urat Istimewa:** Kombinasikan 70% daging gandik dengan 30% urat Sengkel atau Sandung Lamur (*brisket*). Minta operator selepan untuk mencincang kasar uratnya di tahap akhir penggilingan agar tekstur 'krenyes' uratnya terasa mantap!";
        }

        // 4. Topik Rasio Tepung Tapioka / Sagu
        if (str_contains($q, 'tepung') || str_contains($q, 'tapioka') || str_contains($q, 'sagu') || str_contains($q, 'rasio')) {
            return "⚖️ **Kaidah Rasio Tepung Tapioka BASOKU:**\n\n"
                ."Tepung tapioka berfungsi sebagai pengikat adonan. Sesuaikan persentasenya dengan target selera Anda:\n\n"
                ."1. **Racikan Enak (Spesial Daging):** Tepung 10% – 15% dari bobot daging (100g – 150g per 1 kg daging). Rasa daging sangat dominan, padat, dan premium.\n"
                ."2. **Racikan Seimbang (Standar Pasar):** Tepung 20% (200g per 1 kg daging). Keseimbangan paling ideal antara kekenyalan, rasa gurih, dan harga jual.\n"
                .'3. **Racikan Ekonomis (Hemat Usaha):** Tepung 30% – 40% (300g – 400g per 1 kg daging). Cocok untuk pesanan hajatan atau porsi banyak dengan biaya ekonomis.';
        }

        // 5. Topik Tips Penggilingan di Mesin Selepan & Garam di Awal
        if (str_contains($q, 'selepan') || str_contains($q, 'giling') || str_contains($q, 'pengenyal') || str_contains($q, 'stpp')) {
            return "⚙️ **Tips Emas Saat Giling di Los Selepan Pasar:**\n\n"
                ."1. **Garam di Awal:** Pastikan garam kasar dimasukkan saat daging pertama kali digiling bersama sebagian pecahan es batu. Garam berfungsi membuka ikatan serat protein miosin agar getah daging keluar.\n"
                ."2. **Tepung di Akhir:** Jangan masukkan tepung di awal! Masukkan tapioka dan bumbu bawang setelah serat daging lumat sempurna agar adonan tidak liat atau bantat.\n"
                .'3. **Segera Rebus:** Begitu adonan selesai digiling, bawa pulang dalam wadah tertutup dingin dan segera cetak ke dalam air hangat kuku (sekitar 60°C–70°C) sebelum dididihkan.';
        }

        // 6. Default Fallback Umum
        return "👋 **Halo sedulur BASOKU!**\n\n"
            ."Saya siap membantu konsultasi adonan bakso pasar Anda. Kunci utama adonan bakso sempurna ada pada 3 pilar:\n"
            ."1. **Kesegaran Daging:** Gunakan daging segar yang belum terkena air mentah atau pembekuan lama.\n"
            ."2. **Suhu Dingin:** Tambahkan es batu 15%-20% saat giling agar protein miosin tidak rusak.\n"
            ."3. **Keseimbangan Formula:** Sesuaikan takaran tepung tapioka (10%-20%) dan garam kasar untuk kekenyalan alami.\n\n"
            .'Ada takaran racikan spesifik atau pertanyaan seputar porsi yang ingin Anda tanyakan?';
    }
}
