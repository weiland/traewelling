<?php declare(strict_types=1);

namespace App\Services;

use Illuminate\Support\Facades\Http;

class TelegramService
{

    const TELEGRAM_API_URL = 'https://api.telegram.org/bot';

    private string $chatId;
    private string $token;

    public function __construct(string $chatId, string $token) {
        $this->chatId = $chatId;
        $this->token  = $token;
    }

    public static function isAdminActive(): bool {
        return config('services.telegram.admin.active');
    }

    public static function admin(): self {
        return new self(config('services.telegram.admin.chat_id'), config('services.telegram.admin.token'));
    }

    /**
     * @return int Telegram Message ID
     */
    public function sendMessage(string $text, string $parseMode = 'HTML'): int {
        $response = Http::post(self::TELEGRAM_API_URL . $this->token . '/sendMessage', [
            'chat_id'    => $this->chatId,
            'text'       => $text,
            'parse_mode' => $parseMode,
        ]);
        return $response->json('result.message_id');
    }

    public function deleteMessage(int $messageId): void {
        Http::post(self::TELEGRAM_API_URL . $this->token . '/deleteMessage', [
            'chat_id'    => $this->chatId,
            'message_id' => $messageId,
        ]);
    }
}