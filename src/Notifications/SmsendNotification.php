<?php

namespace Dunp\Smsend\Notifications;

use Illuminate\Notifications\Notification;
use Dunp\Smsend\SmsendChannel;
use Dunp\Smsend\SmsendMessage;

/**
 * La classe SmsendNotification rappresenta una notifica inviabile tramite Smsend.it
 */

abstract class SmsendNotification extends Notification
{
    protected SmsendMessage $message;
	protected string $columnNumber;

    /**
     * Costruttore della classe.
     *
     * @param array $params Parametri del messaggio SMS.
     * @return void
     */
    public function __construct(array $params = [])
    {
        $this->message = new SmsendMessage();
		
		if($this->setColumnNumber() != '') {
			$this->message->setColumn($this->setColumnNumber());
		}
        
        if (isset($params['content'])) {
            $this->message->content($params['content']);
        } elseif($this->setContentMessage() != '') {
			$this->message->content($this->setContentMessage());
		}
        
        if (isset($params['quality'])) {
            $this->message->quality($params['quality']);
        }
        
        if (isset($params['from'])) {
            $this->message->from($params['from']);
        }
        
        if (isset($params['scheduled_at'])) {
            $this->message->scheduledAt($params['scheduled_at']);
        }
    }

    /**
     * Restituisce la lista dei canali tramite cui inviare la notifica.
     *
     * @param mixed $notifiable Oggetto notificabile.
     * @return array
     */
    public function via($notifiable): array
    {
        return [SmsendChannel::class];
    }

    /**
     * Restituisce il messaggio da inviare come SMS.
     *
     * @param mixed $notifiable Oggetto notificabile.
     * @return SmsendMessage
     */
    abstract public function toSms($notifiable): SmsendMessage;
	
	/**
     * Restituisce il numero della colonna da cui estrarre il numero del destinatario.
     *
     * @return string
     */
    public function setColumnNumber(): string
    {
        return '';
    }

    /**
     * Restituisce il contenuto del messaggio SMS.
     *
     * @return string
     */
    public function setContentMessage(): string
    {
        return '';
    }
}

