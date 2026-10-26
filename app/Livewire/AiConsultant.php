<?php

namespace App\Livewire;

use App\Services\GeminiConsultantService;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class AiConsultant extends Component
{
    /** Input pertanyaan pengguna */
    public string $question = '';

    /** Riwayat percakapan interaktif */
    public array $chatHistory = [];

    /** Status apakah laci/widget chat sedang terbuka */
    public bool $isOpen = false;

    /** Daftar pertanyaan cepat untuk memudahkan pengguna */
    public array $quickPrompts = [
        'Berapa takaran es batu yang ideal saat giling daging?',
        'Daging bagian apa yang paling bagus untuk bakso urat?',
        'Berapa porsi bakso yang dihasilkan dari 1 kg daging sapi?',
        'Berapa takaran tepung tapioka untuk bakso enak padat?',
    ];

    /**
     * Inisialisasi pesan sambutan awal asisten AI.
     */
    public function mount(): void
    {
        if (empty($this->chatHistory)) {
            $this->chatHistory = [
                [
                    'role' => 'assistant',
                    'message' => "Halo sedulur BASOKU! 👋\n\nSaya Asisten Konsultan Ahli Adonan & Selepan Pasar BASOKU. Butuh panduan takaran es batu, rahasia kenyal miosin, atau estimasi butir porsi hari ini? Silakan pilih topik di bawah atau ketik langsung pertanyaan Anda!",
                    'time' => now()->format('H:i'),
                ],
            ];
        }
    }

    /**
     * Buka atau tutup widget mengambang konsultasi AI.
     */
    public function toggleChat(): void
    {
        $this->isOpen = ! $this->isOpen;
    }

    /**
     * Ajukan pertanyaan cepat berdasarkan index array untuk keamanan dan stabilitas.
     */
    public function askQuickPromptByIndex(int $index): void
    {
        if (isset($this->quickPrompts[$index])) {
            $this->isOpen = true;
            $this->question = $this->quickPrompts[$index];
            $this->sendMessage();
        }
    }

    /**
     * Ajukan pertanyaan cepat dari daftar tombol rekomendasi.
     */
    public function askQuickPrompt(string $prompt): void
    {
        $this->isOpen = true;
        $this->question = $prompt;
        $this->sendMessage();
    }

    /**
     * Kirim pertanyaan ke layanan Gemini Consultant.
     */
    public function sendMessage(?string $customText = null): void
    {
        if ($customText !== null) {
            $this->question = $customText;
        }

        $this->validate([
            'question' => ['required', 'string', 'min:3', 'max:500'],
        ], [
            'question.required' => 'Silakan ketik pertanyaan konsultasi Anda.',
            'question.min' => 'Pertanyaan minimal terdiri dari 3 karakter.',
            'question.max' => 'Pertanyaan maksimal 500 karakter.',
        ]);

        $this->isOpen = true;
        $userPrompt = trim($this->question);
        $currentTime = now()->format('H:i');

        // 1. Simpan pesan pengguna ke riwayat
        $this->chatHistory[] = [
            'role' => 'user',
            'message' => $userPrompt,
            'time' => $currentTime,
        ];

        // 2. Kosongkan input form
        $this->question = '';

        // 3. Dapatkan jawaban cerdas dari service (didukung Graceful Degradation)
        $consultant = app(GeminiConsultantService::class);
        $assistantReply = $consultant->ask($userPrompt, [
            'history' => $this->chatHistory,
        ]);

        // 4. Masukkan jawaban asisten ke riwayat
        $this->chatHistory[] = [
            'role' => 'assistant',
            'message' => $assistantReply,
            'time' => now()->format('H:i'),
        ];
    }

    /**
     * Bersihkan riwayat chat dan kembali ke pesan pembuka.
     */
    public function clearChat(): void
    {
        $this->chatHistory = [];
        $this->mount();
        $this->resetErrorBag();
        $this->resetValidation();
    }

    public function render(): View
    {
        return view('livewire.ai-consultant');
    }
}
