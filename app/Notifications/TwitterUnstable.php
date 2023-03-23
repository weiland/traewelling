<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use JetBrains\PhpStorm\ArrayShape;

/**
 * With this notification, we ask our users who have a twitter login but no email/password login, to create that second way of authenticating.
 *
 * The notification is the same for everyone who gets it, and does not have custom information.
 */
class TwitterUnstable extends BaseNotification
{
    use Queueable;

    public function __construct() { }

    public static function render($notification): array|null {
        return [
            'color'           => "warning",
            'icon'            => "fas fa-exclamation-triangle",
            'lead'            => __('notifications.twitterUnstable.lead'),
            "link"            => route('settings'),
            'notice'          => __('notifications.twitterUnstable.notice'),
            'date_for_humans' => $notification->created_at->diffForHumans()
        ];
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    #[ArrayShape([])]
    public function toArray($notifiable)
    {
        return [];
    }
}
