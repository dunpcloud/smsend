<?php

namespace Dunp\Smsend\Notifications;

use Dunp\Smsend\SmsendMessage;

class SingleSmsendNotification extends SmsendNotification
{
    /**
     * Crea il messaggio Smsend da inviare per la notifica.
     *
     * @param mixed $notifiable Oggetto notificabile.
     * @return SmsendMessage
     */
    public function toSms($notifiable): SmsendMessage
    {
		return $this->message
		    ->to($notifiable);
    }
}