<?php

namespace Tests\Feature;

use App\Livewire\AiConsultant;
use App\Services\GeminiConsultantService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
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

        // Pastikan panggilan Http ke Gemini benar-benar terjadi
        Http::assertSent(function ($request) {
            return str_contains($request->url(), 'generativelanguage.googleapis.com')
                && str_contains($request->url(), 'key=test-dummy-api-key')
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
}
