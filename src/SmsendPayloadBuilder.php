<?php

namespace Dunp\Smsend;

use Illuminate\Support\Facades\Config;
use InvalidArgumentException;

/**
 * La classe SmsendPayloadBuilder si occupa della costruzione del payload per l'invio del messaggio tramite Smsend.it
 */
class SmsendPayloadBuilder
{
    private array $payload;

    /**
     * SmsendPayloadBuilder constructor.
     */
    public function __construct()
    {
        $this->payload = [
            'returnCredits' => true,
            'message_type' => Config::get('smsend.quality'),
        ];
    }

    /**
     * Aggiunge un destinatario alla lista dei destinatari del messaggio.
     *
     * @param mixed $recipient L'oggetto che rappresenta il destinatario oppure un array di oggetti destinatari.
     * Se viene passato un oggetto destinatario, viene usata la colonna di default definita nella configurazione per estrarre il numero di telefono.
     * Se viene passato un array di oggetti destinatari, viene usata la chiave specificata nella configurazione per estrarre il numero di telefono.
     *
     * @return $this
     */
    public function addRecipient(mixed $recipient): self
    {
        if (is_array($recipient)) {
            $this->payload['recipients'][] = (object) $recipient;
        } else {
            $normalizedNumber = preg_replace('/(?!^\+)\D/', '', $recipient);
            $this->payload['recipient'][] = $normalizedNumber;
        }
		
        return $this;
    }

    /**
     * Imposta il testo del messaggio.
     *
     * @param string $message Il testo del messaggio.
     * @return $this
     */
    public function setMessage(string $message): self
    {
        $this->payload['message'] = $message;
		
        return $this;
    }

    /**
     * Imposta la qualità del messaggio.
     *
     * @param string $quality La qualità del messaggio (N, L o LL).
     * @return $this
     * @throws \InvalidArgumentException Se la qualità del messaggio non è una delle tre previste.
     */
    public function setMessageQuality(string $quality): self
    {
        if (!in_array($quality, ['N', 'L', 'LL'])) {
            throw new InvalidArgumentException('Il tipo di messaggio deve essere "N", "L" o "LL"');
        }

        $this->payload['message_type'] = $quality;
		
        return $this;
    }

    /**
     * Imposta il mittente del messaggio.
     *
     * @param string $sender Il mittente del messaggio (opzionale).
     * @return $this
     */
    public function setSender(string $sender = ''): self
    {
        if (!empty($sender)) {
            $this->payload['sender'] = $sender;
        } else {
            $this->payload['sender'] = Config::get('smsend.sender');
        }
		
        return $this;
    }

    /**
     * Imposta la data e l'ora di consegna programmata del messaggio.
     *
     * @param string $scheduled_delivery_time La data e l'ora di consegna programmata nel formato YYYYMMDDhhmmss.
     * @return $this
     */
    public function setScheduledDeliveryTime(string $scheduled_delivery_time): self
    {
        $this->payload['scheduled_delivery_time'] = $scheduled_delivery_time;
		
        return $this;
    }
	
	/**
	 * Converte l'array dei destinatari in un oggetto stdClass.
	 *
	 * @param array $recipients L'array dei destinatari.
	 * @return object L'oggetto stdClass dei destinatari.
	 */
	private function toObjectRecipients(array $recipients): object 
	{
		$json_recipients = new \stdClass;

		foreach($recipients as $key => $recipient) {
			$json_recipients->{$key} = $recipient;
		}
		
		return $json_recipients;
	}

	/**
	 * Restituisce il payload del messaggio costruito dal builder.
	 *
	 * @return array Il payload del messaggio.
	 */
	public function build(): array
	{
		if(isset($this->payload['recipients'])) {
			$this->payload['recipients'] = $this->toObjectRecipients($this->payload['recipients']);
		}

		return $this->payload;
	}
}
