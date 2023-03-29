<?php

namespace Dunp\Smsend;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Config;

/**
 * La classe SmsendAuth si occupa dell'autenticazione presso il servizio di Smsend.it
 */
 
class SmsendAuth
{
    private string $base_url;
    private string $username;
    private string $password;
    private int $timeout;
	
    /**
     * Crea una nuova istanza di SmsendAuth.
     *
     * @throws \Exception Se i parametri di configurazione di SMSend non sono presenti
     * @return void
     */
    public function __construct()
    {
        $this->base_url = Config::get('smsend.base_url');
        $this->username = Config::get('smsend.username');
        $this->password = Config::get('smsend.password');
        $this->timeout = Config::get('smsend.timeout');

        if (empty($this->base_url) || empty($this->username) || empty($this->password) || empty($this->timeout)) {
            throw new \Exception('I parametri di configurazione di SMSend sono mancanti');
        }
    }

    /**
     * Effettua l'autenticazione con il servizio SMSend.
     *
     * @throws \Exception Se si verificano errori durante l'autenticazione con SMSend
     * @return array L'array contenente user_key, Session_key e l'istanza di GuzzleHttp\Client
     */
    public function authenticate(): array
    {
        $client = new Client([
            'base_uri' => $this->base_url,
            'timeout' => $this->timeout,
        ]);
		
        try {
            $response = $client->get('login', [
                'query' => [
                    'username' => $this->username,
                    'password' => $this->password,
                ],
            ]);

            if ($response->getStatusCode() != 200) {
                throw new \Exception('Errore durante l\'autenticazione con SMSend.it');
            }

            $values = explode(';', $response->getBody()->getContents());

            return [
                'user_key' => $values[0],
                'Session_key' => $values[1],
                'client' => $client
            ];
        } catch (\Exception $e) {
            throw new \Exception('Errore durante l\'autenticazione con SMSend.it: ' . $e->getMessage());
        }
    }

}
