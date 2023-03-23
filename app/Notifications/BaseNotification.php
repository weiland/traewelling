<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;
use JetBrains\PhpStorm\ArrayShape;

abstract class BaseNotification extends Notification
{
    #[ArrayShape([
        'color' => "string",
        'icon' => "string",
        'lead' => 'string',
        'link' => 'string',
        'notice' => "string",
        "date_for_humans" => "string"
    ])]
    public abstract static function render(mixed $notification): array|null;
}
