<?php

namespace App\Notifications;

use App\Models\Event;
use App\Models\EventSuggestion;
use Illuminate\Bus\Queueable;
use JetBrains\PhpStorm\ArrayShape;

class EventSuggestionProcessed extends BaseNotification
{
    use Queueable;

    private EventSuggestion $eventSuggestion;
    private ?Event          $event;

    public function __construct(EventSuggestion $eventSuggestion, ?Event $event) {
        $this->eventSuggestion = $eventSuggestion;
        $this->event           = $event;
    }

    #[ArrayShape([
        'color'           => "string",
        'icon'            => "string",
        'lead'            => 'string',
        'link'            => 'string',
        'notice'          => "string",
        "date_for_humans" => "string"
    ])]
    public static function render(mixed $notification): array {
        return [
            'color'           => 'neutral',
            'icon'            => 'fa-regular fa-calendar',
            'lead'            => __('notifications.eventSuggestionProcessed.lead', ["name" => $notification->data["name"]]),
            'link'            => $notification->data["accepted"] ? route('statuses.byEvent', [
                'eventSlug' => $notification->data["event"]["slug"]
            ]) : "#",
            'notice'          => __('notifications.eventSuggestionProcessed.' . ($notification->data["accepted"] ? "accepted" : "denied")),
            'date_for_humans' => $notification->created_at->diffForHumans()
        ];
    }

    public function via($notifiable): array {
        return ['database'];
    }

    #[ArrayShape(['accepted' => 'bool', 'event' => Event::class, 'name' => 'string'])]
    public function toArray($notifiable): array {
        return [
            'accepted' => $this->event !== null,
            'event'    => $this->event,
            'name'     => $this->eventSuggestion->name,
        ];
    }
}
