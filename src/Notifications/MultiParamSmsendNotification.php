<?php

namespace Dunp\Smsend\Notifications;

use Dunp\Smsend\SmsendMessage;
use Illuminate\Database\Eloquent\Collection;

class MultiParamSmsendNotification extends SmsendNotification
{
    protected Collection $collection;

    /**
     * Costruttore della classe.
     *
     * @param Collection $collection Collection di oggetti destinatari con i parametri per la personalizzazione del messaggio.
     * @return void
     */
    public function __construct(Collection $collection)
    {
        parent::__construct();
        
        $this->collection = $collection;
    }

    /**
     * Restituisce il messaggio da inviare come SMS.
     *
     * @param mixed $notifiable Oggetto notificabile.
     * @return SmsendMessage
     */
    public function toSms($notifiable): SmsendMessage
    {
        return $this->message
            ->multiToParametric($this->collection);
    }
}