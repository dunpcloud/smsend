<?php

namespace Dunp\Smsend\Notifications;

use Dunp\Smsend\SmsendMessage;
use Illuminate\Database\Eloquent\Collection;

/**
 * La classe MultiSmsendNotification rappresenta una notifica inviabile a una lista di destinatari tramite Smsend.it.
 */
class MultiSmsendNotification extends SmsendNotification
{
    protected Collection $collection;

    /**
     * Costruttore della classe.
     *
     * @param Collection $collection La collezione di destinatari a cui inviare la notifica.
     */
    public function __construct(Collection $collection)
    {
        parent::__construct();
        $this->collection = $collection;
    }

    /**
     * Restituisce il messaggio da inviare come SMS.
     *
     * @param mixed $notifiable L'oggetto notificabile.
     * @return SmsendMessage Il messaggio da inviare come SMS.
     */
    public function toSms($notifiable): SmsendMessage
    {
        return $this->message
            ->multiTo($this->collection);
    }
}
