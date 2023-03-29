<?php

namespace Dunp\Smsend\Notifications;

use Dunp\Smsend\SmsendMessage;

class SingleParamSmsendNotification extends SmsendNotification
{
    /**
     * Costruisce il messaggio da inviare come SMS tramite Smsend.it.
     *
     * @param mixed $notifiable Oggetto notificabile.
     * @return SmsendMessage Il messaggio da inviare come SMS.
     */
    public function toSms($notifiable): SmsendMessage
    {
        return $this->message
            ->toParametric($notifiable);
    }
}
