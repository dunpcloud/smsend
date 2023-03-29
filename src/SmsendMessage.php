<?php

namespace Dunp\Smsend;

use Illuminate\Support\Facades\Config;
use Illuminate\Database\Eloquent\Collection;

/**
 * La classe SmsendMessage si occupa della costruzione del messaggio da inviare tramite Smsend.it
 */
class SmsendMessage
{
    private SmsendPayloadBuilder $payloadBuilder;
    private string $url;
    private string $columnNumber;
    private array $params;

    /**
     * Crea una nuova istanza di SmsendMessage.
     */
    public function __construct()
    {
        $this->payloadBuilder = new SmsendPayloadBuilder();
        $this->columnNumber = Config::get('smsend.default_column');
    }

    /**
     * Restituisce il payload costruito dal payload builder.
     *
     * @return array il payload del messaggio
     */
    public function toPayload(): array
    {
        return $this->payloadBuilder->build();
    }

    /**
     * Aggiunge un destinatario al messaggio.
     *
     * @param object $recipient il destinatario del messaggio
     * @return $this
     */
    public function to(object $recipient): SmsendMessage
    {
        $this->payloadBuilder->addRecipient($recipient->{$this->columnNumber});

        return $this;
    }

    /**
     * Aggiunge più destinatari al messaggio.
     *
     * @param Collection $recipients gli array dei destinatari del messaggio
     * @return $this
     */
    public function multiTo(Collection $recipients): SmsendMessage
    {
        $recipients = $recipients->pluck($this->columnNumber)->toArray();

        foreach ($recipients as $recipient) {
            $this->payloadBuilder->addRecipient($recipient);
        }

        return $this;
    }

    /**
     * Aggiunge un destinatario parametrico al messaggio.
     *
     * @param object $recipient il destinatario parametrico del messaggio
     * @return $this
     */
    public function toParametric(object $recipient): SmsendMessage
    {
        $parametricRecipient['recipient'] = $recipient->{$this->columnNumber};

        foreach ($this->params as $param) {
            if (isset($recipient->{$param})) {
                $parametricRecipient[$param] = $recipient->{$param};
            }
        }

        $this->payloadBuilder->addRecipient($parametricRecipient);
		
        return $this;
    }

    /**
     * Aggiunge più destinatari parametrici al messaggio.
     *
     * @param Collection $recipients gli array dei destinatari parametrici del messaggio
     * @return $this
     */
    public function multiToParametric(Collection $recipients): SmsendMessage
	{
		foreach($recipients as $recipient) {
			$this->toParametric($recipient);
		}
		
		return $this;
	}

    /**
     * Imposta il contenuto del messaggio
     *
     * @param string $message il contenuto del messaggio
     * @return $this
     */
    public function content(string $message): SmsendMessage
    {
		preg_match_all('/\{([^\}]+)\}/', $message, $matches);
        $this->params = $matches[1];
		$this->url = !empty($this->params) ? 'paramsms' : 'sms';
        $this->payloadBuilder->setMessage($message);
		
        return $this;
    }

    /**
     * Imposta la qualità del messaggio.
     *
     * @param string $quality La qualità del messaggio (N, L o LL)
     *
     * @return $this
     */
    public function quality(string $quality): SmsendMessage
    {
        $this->payloadBuilder->setMessageQuality($quality);

        return $this;
    }

    /**
     * Imposta il mittente del messaggio.
     *
     * @param string $sender Il mittente del messaggio
     *
     * @return $this
     */
    public function from(string $sender): SmsendMessage
    {
        $this->payloadBuilder->setSender($sender);

        return $this;
    }

    /**
     * Imposta la data e l'ora programmata per la consegna del messaggio.
     *
     * @param string $scheduled_delivery_time La data e l'ora programmata per la consegna del messaggio nel formato YYYYMMDDhhmmss
     *
     * @return $this
     */
    public function scheduledAt(string $scheduled_delivery_time): SmsendMessage
    {
        $this->payloadBuilder->setScheduledDeliveryTime($scheduled_delivery_time);

        return $this;
    }

    /**
     * Restituisce l'url del messaggio.
     *
     * @return string L'url del messaggio
     */
    public function getUrl(): string
    {
        return $this->url;
    }

    /**
     * Imposta la colonna del destinatario del messaggio.
     *
     * @param string $columnNumber Il nome della colonna del destinatario del messaggio
     *
     * @return void
     */
    public function setColumn(string $columnNumber): void
    {
        $this->columnNumber = $columnNumber;
    }
}

