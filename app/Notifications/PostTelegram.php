<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

use NotificationChannels\Telegram\TelegramChannel;
use NotificationChannels\Telegram\TelegramMessage;

use Illuminate\Support\Str;

class PostTelegram extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return [TelegramChannel::class];
    }
    /**
     * Send this to Telegram Channel
     * <b>bold</b>, <strong>bold</strong>
     * <i>italic</i>, <em>italic</em>
     * <a href="http://www.example.com/">inline URL</a>
     * <a href="tg://user?id=123456789">inline mention of a user</a>
     * <code>inline fixed-width code</code>
     * <pre>pre-formatted fixed-width code block</pre>
     *
     * Add some analitycs to this
     */
    public function toTelegram($blog_post)
    {

        $telegram_notif = TelegramMessage::create();
        $telegram_notif->to('@elBacheChannel');
        $telegram_notif->content("https://t.me/iv?url=" . urlencode(post_url($blog_post)) . "&rhash=0929b8713a7588");

        return $telegram_notif;
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
