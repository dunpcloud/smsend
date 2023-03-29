<?php

namespace Dunp\Smsend;

use Illuminate\Notifications\Notification;

/**
 * La classe SmsendChannel rappresenta il canale di notifica per l'invio di messaggi tramite Smsend.it
 */
 
class SmsendChannel
{
    protected Smsend $smsend;

    /**
     * Crea una nuova istanza di SmsendChannel.
     *
     * @param Smsend $smsend
     * @return void
     */
    public function __construct(Smsend $smsend)
    {
        $this->smsend = $smsend;
    }

    /**
     * Invia la notifica tramite il canale di SMSend.
     *
     * @param object $notifiable
     * @param Notification $notification
     * @return int
     * @throws \Exception
     */
    public function send(null|object $notifiable, Notification $notification): int
    {
        $message = $notification->toSms($notifiable);
        return $this->smsend->sendMessage($message);
    }
}
