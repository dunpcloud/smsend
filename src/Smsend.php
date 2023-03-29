<?php

namespace Dunp\Smsend;

use GuzzleHttp\Client;

/**
 * La classe Smsend rappresenta la classe principale per l'invio di messaggi tramite Smsend.it
 */

class Smsend
{
    private string $userKey;
    private string $sessionKey;
    private Client $client;

    /**
     * Crea una nuova istanza di Smsend.
     *
     * @param string $userKey
     * @param string $sessionKey
     * @param Client $client
     * @return void
     */
    public function __construct(string $userKey, string $sessionKey, Client $client)
    {
        $this->userKey = $userKey;
        $this->sessionKey = $sessionKey;
        $this->client = $client;
    }
	
    /**
     * Invia un messaggio utilizzando l'API di Smsend.
     *
     * @param SmsendMessage $message
     * @return bool Ritorna true se il messaggio è stato inviato con successo, false altrimenti.
     * @throws \Exception
     */
    public function sendMessage(SmsendMessage $message): bool
    {
        try {
            $payload = $message->toPayload();
            $url = $message->getUrl();

            $response = $this->client->post($url, [
                'headers' => [
                    'Content-type' => 'application/json',
                    'user_key' => $this->userKey,
                    'Session_key' => $this->sessionKey,
                ],
                'json' => $payload
            ]);

            return $response->getStatusCode() == 201;
        } catch (\Exception $e) {
            throw new \Exception('Errore durante l\'invio del messaggio: ' . $e->getMessage());
        }
    }
}
