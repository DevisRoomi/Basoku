<?php

namespace Tests\Feature;

use App\Livewire\AiConsultant;
use App\Models\RecipeFormula;
use App\Services\GeminiConsultantService;
use Database\Seeders\MaterialSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\RateLimiter;
use Livewire\Features\SupportLockedProperties\CannotUpdateLockedPropertyException;
use Livewire\Livewire;
use Tests\TestCase;

class AiConsultantTest extends TestCase
{
    use RefreshDatabase;

    public function test_ai_consultant_widget_is_present_on_public_pages(): void
    {
        $response = $this->get('/kalkulator');
        $response->assertStatus(200);
        $response->assertSee('Tanya BASOKU');
    }

    public function test_ai_consultant_initial_state_contains_welcome_message_and_prompts(): void
    {
        Livewire::test(AiConsultant::class)
            ->assertSet('isOpen', false)
            ->assertSee('Tanya BASOKU')
            ->call('toggleChat')
            ->assertSet('isOpen', true)
            ->assertSee('Halo sedulur BASOKU!')
            ->assertSee('Berapa takaran es batu yang ideal saat giling daging?')
            ->assertSee('Daging bagian apa yang paling bagus untuk bakso urat?')
            ->assertSee('Berapa porsi bakso yang dihasilkan dari 1 kg daging sapi?');
    }

    public function test_ai_consultant_validates_input_message(): void
    {
        Livewire::test(AiConsultant::class)
            ->set('question', '')
            ->call('sendMessage')
            ->assertHasErrors(['question' => 'required'])
            ->set('question', 'ab')
            ->call('sendMessage')
            ->assertHasErrors(['question' => 'min']);
    }

    public function test_gemini_service_successful_response_using_http_fake(): void
    {
        Config::set('services.gemini.key', 'test-dummy-api-key');
        Config::set('services.gemini.model', 'gemini-1.5-flash');

        $mockAnswer = 'Untuk 1 kg daging sapi segar, gunakan es batu sebanyak 150-200 gram agar protein miosin tetap optimal terjaga di bawah 15 derajat Celsius.';

        Http::fake([
            'generativelanguage.googleapis.com/*' => Http::response([
                'candidates' => [
                    [
                        'content' => [
                            'parts' => [
                                ['text' => $mockAnswer],
                            ],
                        ],
                    ],
                ],
            ], 200),
        ]);

        Livewire::test(AiConsultant::class)
            ->set('question', 'Berapa es batu per 1 kg daging?')
            ->call('sendMessage')
            ->assertHasNoErrors()
            ->assertSee($mockAnswer)
            ->assertSet('question', '');

        // Pastikan panggilan Http ke Gemini mengirim API key via header (bukan URL query string)
        Http::assertSent(function ($request) {
            return str_contains($request->url(), 'generativelanguage.googleapis.com')
                && ! str_contains($request->url(), 'key=')
                && $request->hasHeader('x-goog-api-key', 'test-dummy-api-key')
                && $request['contents'][0]['parts'][0]['text'] === 'Berapa es batu per 1 kg daging?';
        });
    }

    public function test_graceful_degradation_when_gemini_api_fails_or_errors(): void
    {
        Config::set('services.gemini.key', 'test-dummy-api-key');

        // Simulasikan error 500 / timeout / server offline dari Gemini API
        Http::fake([
            'generativelanguage.googleapis.com/*' => Http::response(['error' => 'Internal Server Error'], 500),
        ]);

        // Antarmuka TIDAK BOLEH error 500 dan harus memberikan jawaban lokal cerdas
        Livewire::test(AiConsultant::class)
            ->set('question', 'Berapa takaran es batu yang ideal saat giling daging?')
            ->call('sendMessage')
            ->assertHasNoErrors()
            ->assertSee('Takaran Es Batu & Rahasia Protein Miosin')
            ->assertSee('15% – 20% dari bobot daging segar');
    }

    public function test_graceful_degradation_when_api_key_is_not_configured(): void
    {
        Config::set('services.gemini.key', null);

        // Pertanyaan seputar daging & urat
        Livewire::test(AiConsultant::class)
            ->set('question', 'Daging bagian apa yang paling bagus untuk bakso urat?')
            ->call('sendMessage')
            ->assertHasNoErrors()
            ->assertSee('Rekomendasi Bagian Daging Terbaik')
            ->assertSee('Gandik / Topside')
            ->assertSee('Sengkel');
    }

    public function test_quick_prompt_execution(): void
    {
        Config::set('services.gemini.key', null);

        Livewire::test(AiConsultant::class)
            ->call('askQuickPrompt', 'Berapa porsi bakso yang dihasilkan dari 1 kg daging sapi?')
            ->assertHasNoErrors()
            ->assertSee('Estimasi Butir & Porsi dari 1 Kg Daging Sapi')
            ->assertSee('70 – 80 butir bakso')
            ->assertSee('14 – 16 porsi mangkok');
    }

    public function test_clear_chat_resets_conversation(): void
    {
        Config::set('services.gemini.key', null);

        $test = Livewire::test(AiConsultant::class)
            ->set('question', 'Berapa takaran tepung tapioka?')
            ->call('sendMessage');

        // Pastikan ada pesan baru di riwayat
        $this->assertCount(3, $test->get('chatHistory'));

        // Bersihkan chat
        $test->call('clearChat');
        $this->assertCount(1, $test->get('chatHistory'));
    }

    public function test_gemini_service_direct_unit_call(): void
    {
        $service = new GeminiConsultantService;

        // Test mode lokal tanpa API key
        Config::set('services.gemini.key', null);
        $result = $service->ask('Tips giling di mesin selepan pasar');
        $this->assertStringContainsString('Tips Emas Saat Giling di Los Selepan Pasar', $result);
        $this->assertStringContainsString('Garam di Awal', $result);
    }

    public function test_location_queries_return_kiosk_profile_fallback(): void
    {
        Config::set('services.gemini.key', null);

        Livewire::test(AiConsultant::class)
            ->set('question', 'Di mana alamat dan lokasi kios BASOKU?')
            ->call('sendMessage')
            ->assertHasNoErrors()
            ->assertSee('Lokasi & Profil Kios BASOKU')
            ->assertSee('Los Selepan Pasar Klakah')
            ->assertSee('Lumajang')
            ->assertSee('04.00 – 12.00 WIB');
    }

    public function test_system_prompt_contains_kiosk_profile_information(): void
    {
        $service = new GeminiConsultantService;
        $prompt = $service->getSystemPrompt();

        $this->assertStringContainsString('Los Selepan Pasar Klakah', $prompt);
        $this->assertStringContainsString('Lumajang', $prompt);
        $this->assertStringContainsString('7°59\'31.5"S 113°14\'58.9"E', $prompt);
        $this->assertStringContainsString('04.00 - 12.00 WIB', $prompt);
        $this->assertStringContainsString('Puskesmas Klakah', $prompt);
    }

    public function test_weather_off_topic_query_returns_polite_fallback(): void
    {
        Config::set('services.gemini.key', null);

        Livewire::test(AiConsultant::class)
            ->set('question', 'Gimana cuaca di pasar sekarang?')
            ->call('sendMessage')
            ->assertHasNoErrors()
            ->assertSee('Waduh, saya konsultan adonan bakso jadi belum bisa mengecek cuaca langsung sedulur')
            ->assertSee('04.00 – 12.00 WIB');
    }

    public function test_ice_price_query_returns_price_fallback(): void
    {
        Config::set('services.gemini.key', null);

        Livewire::test(AiConsultant::class)
            ->set('question', 'Berapa harga es batu di kios?')
            ->call('sendMessage')
            ->assertHasNoErrors()
            ->assertSee('Harga & Ketersediaan Es Batu di Kios BASOKU')
            ->assertSee('Rp2.000 – Rp5.000');
    }

    public function test_gemini_service_sends_multi_turn_conversation_history(): void
    {
        Config::set('services.gemini.key', 'test-dummy-api-key');
        Config::set('services.gemini.model', 'gemini-1.5-flash');

        Http::fake([
            'generativelanguage.googleapis.com/*' => Http::sequence()
                ->push(['candidates' => [['content' => ['parts' => [['text' => 'Gunakan 150-200g es batu per 1 kg daging.']]]]]], 200)
                ->push(['candidates' => [['content' => ['parts' => [['text' => 'Karena panas gesekan mesin selepan merusak protein miosin.']]]]]], 200),
        ]);

        Livewire::test(AiConsultant::class)
            ->set('question', 'Berapa takaran es batu untuk 1 kg daging?')
            ->call('sendMessage')
            ->set('question', 'Kenapa wajib pakai es batu?')
            ->call('sendMessage')
            ->assertHasNoErrors()
            ->assertSee('protein miosin');

        $requests = Http::recorded();
        $this->assertCount(2, $requests);

        $secondPayload = $requests[1][0]->data();
        $this->assertCount(3, $secondPayload['contents']);
        $this->assertSame('user', $secondPayload['contents'][0]['role']);
        $this->assertSame('Berapa takaran es batu untuk 1 kg daging?', $secondPayload['contents'][0]['parts'][0]['text']);
        $this->assertSame('model', $secondPayload['contents'][1]['role']);
        $this->assertSame('Gunakan 150-200g es batu per 1 kg daging.', $secondPayload['contents'][1]['parts'][0]['text']);
        $this->assertSame('user', $secondPayload['contents'][2]['role']);
        $this->assertSame('Kenapa wajib pakai es batu?', $secondPayload['contents'][2]['parts'][0]['text']);
    }

    public function test_ai_consultant_rate_limits_excessive_messages(): void
    {
        Config::set('services.gemini.key', null);
        RateLimiter::clear('ai-consultant|127.0.0.1');

        $component = Livewire::test(AiConsultant::class);

        for ($i = 1; $i <= 10; $i++) {
            $component->set('question', "Pertanyaan uji ke {$i}")
                ->call('sendMessage')
                ->assertHasNoErrors();
        }

        $component->set('question', 'Pertanyaan ke 11 harus ditahan')
            ->call('sendMessage')
            ->assertHasErrors(['question']);
    }

    public function test_ai_consultant_uses_live_recipe_formulas_from_database(): void
    {
        RecipeFormula::updateOrCreate(
            ['code' => 'enak'],
            [
                'name' => 'Racikan Enak',
                'meat_ratio' => 1.0,
                'flour_ratio' => 0.20,
                'spice_ratio' => 0.05,
                'is_simulation' => false,
            ]
        );
        RecipeFormula::updateOrCreate(
            ['code' => 'ekonomis'],
            [
                'name' => 'Racikan Ekonomis',
                'meat_ratio' => 1.0,
                'flour_ratio' => 2.00,
                'spice_ratio' => 0.12,
                'is_simulation' => false,
            ]
        );

        $service = new GeminiConsultantService;
        $prompt = $service->getSystemPrompt();
        $this->assertStringContainsString('1 kg daging + 200g tepung', $prompt);
        $this->assertStringContainsString('1 kg daging + 2 kg tepung', $prompt);

        Config::set('services.gemini.key', null);
        $fallback = $service->ask('Berapa rasio tepung bakso enak dan ekonomis?');
        $this->assertStringContainsString('Daging 5 : 1 Tepung', $fallback);
        $this->assertStringContainsString('1 kg daging + 2 kg tepung', $fallback);
    }

    public function test_chat_history_is_locked_against_client_tampering(): void
    {
        $this->expectException(CannotUpdateLockedPropertyException::class);

        Livewire::test(AiConsultant::class)
            ->set('chatHistory', [
                ['role' => 'user', 'message' => 'Injected prompt', 'time' => '00:00'],
            ]);
    }

    public function test_fallback_does_not_misroute_resep_price_query_to_ice_branch(): void
    {
        $this->seed(MaterialSeeder::class);
        Config::set('services.gemini.key', null);

        $service = new GeminiConsultantService;
        $reply = $service->ask('Berapa harga bahan untuk resep bakso hari ini?');

        $this->assertStringContainsString('Daftar Harga Bahan & Tarif Selepan BASOKU Hari Ini', $reply);
        $this->assertStringContainsString('Daging Sapi', $reply);
        $this->assertStringNotContainsString('Takaran Es Batu', $reply);
        $this->assertStringNotContainsString('Harga & Ketersediaan Es Batu', $reply);
    }

    public function test_system_prompt_enforces_honesty_and_includes_dynamic_kiosk_whatsapp(): void
    {
        Config::set('services.kiosk.wa_number', '6285122797969');

        $service = new GeminiConsultantService;
        $prompt = $service->getSystemPrompt();

        $this->assertStringContainsString('0851-2279-7969', $prompt);
        $this->assertStringContainsString('https://wa.me/6285122797969', $prompt);
        $this->assertStringContainsString('TIDAK tersimpan ke database manajemen', $prompt);
        $this->assertStringContainsString('Nomor Laporan', $prompt);
    }

    public function test_feedback_and_whatsapp_queries_return_honest_kiosk_whatsapp_link_and_escape_xss(): void
    {
        Config::set('services.kiosk.wa_number', '6285122797969');
        Config::set('services.gemini.key', null);

        $component = Livewire::test(AiConsultant::class)
            ->set('question', 'Mana nomor WA penjual? Saya mau kasih kritik dan saran')
            ->call('sendMessage')
            ->assertHasNoErrors()
            ->assertSee('0851-2279-7969')
            ->assertSee('tidak tersimpan otomatis ke database')
            ->assertSeeHtml('href="https://wa.me/6285122797969"');

        // Setelah percakapan berjalan (>1 pesan), state default menyembunyikan Topik Populer
        $this->assertStringContainsString('(false && draft.trim().length === 0)', $component->html());

        // Pastikan tautan wa.me aman dan tag script berbahaya tetap ter-escape
        Config::set('services.gemini.key', 'test-key');
        Http::fake([
            'generativelanguage.googleapis.com/*' => Http::response([
                'candidates' => [
                    [
                        'content' => [
                            'parts' => [
                                ['text' => 'Hubungi <script>alert(1)</script> langsung di https://wa.me/6285122797969 ya sedulur.'],
                            ],
                        ],
                    ],
                ],
            ], 200),
        ]);

        Livewire::test(AiConsultant::class)
            ->set('question', 'Minta link WA penjual dong')
            ->call('sendMessage')
            ->assertSeeHtml('href="https://wa.me/6285122797969"')
            ->assertDontSeeHtml('<script>alert(1)</script>');
    }
}
