<?php

namespace Dunp\Smsend;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Config;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

/**
 * La classe SmsendAuth si occupa dell'autenticazione presso il servizio di Smsend.it
 */
 
class SmsendAuth
{
    private string $base_url;
    private string $username;
    private string $password;
    private int $timeout;
	private string $secret_key;
	private int $expiration;
	
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
		$this->secret_key = Config::get('smsend.secret_key');
		$this->expiration = Config::get('smsend.expiration_jwt');
		
		if( Config::get('smsend.multi_user') !== true ) {
			if (empty($this->base_url) || empty($this->username) || empty($this->password) || empty($this->timeout)) {
				throw new \Exception('I parametri di configurazione di SMSend sono mancanti');
			}
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
		
		$keysFromJwt = $this->getKeysFromJwt();

		if($keysFromJwt) {

			$response = $client->get('checksession', [
                'headers' => [
					'user_key' => $keysFromJwt['user_key'],
					'Session_key' => $keysFromJwt['session_key']
                ]
            ]);

			if($response->getStatusCode() == 200) {
				return [
					'user_key' => $keysFromJwt['user_key'],
					'Session_key' => $keysFromJwt['session_key'],
					'client' => $client
				];
			}
		}
		
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
			
			$jwt = $this->generateJwt($values[0], $values[1]);
			$this->saveJwtToCookie($jwt);
			
            return [
                'user_key' => $values[0],
                'Session_key' => $values[1],
                'client' => $client
            ];
        } catch (\Exception $e) {
            throw new \Exception('Errore durante l\'autenticazione con SMSend.it: ' . $e->getMessage());
        }
    }
	
	 /**
	 * Genera un JSON Web Token (JWT) contenente le chiavi.
	 *
	 * @param string $userKey La chiave dell'utente
	 * @param string $sessionKey La chiave di sessione
	 * @return string Il JSON Web Token generato
	 */
	private function generateJwt(string $userKey, string $sessionKey): string
	{
		$payload = [
			'iss' => Config::get('app.url'),
			'iat' => time(),
			'nbf' => time() + 1,
			'exp' => time() + $this->expiration,
			'user_key' => $userKey,
			'session_key' => $sessionKey
		];

		return JWT::encode($payload, $this->secret_key, 'HS256');
	}
	
	 /**
     * Salva il JWT nei cookie.
     *
     * @param string $jwt Il JSON Web Token da salvare
     * @return void
     */
    private function saveJwtToCookie(string $jwt): void
    {
        setcookie('smsend', $jwt, time() + $this->expiration, '/');
    }
	
	 /**
     * Controlla se il JWT esiste e restituisce le chiavi "user_key" e "session_key" se presenti.
     *
     * @return array|null L'array contenente le chiavi "user_key" e "session_key", o null se il JWT non esiste
     */
    public function getKeysFromJwt(): ?array
    {
        $jwt = $_COOKIE['smsend'] ?? null;

        if (!$jwt) {
            return null;
        }

        try {
            $decoded = JWT::decode($jwt, new Key($this->secret_key, 'HS256'));
            $userKey = $decoded->user_key ?? null;
            $sessionKey = $decoded->session_key ?? null;

            if (!$userKey || !$sessionKey) {
                return null;
            }
			
			if(isset($decoded->exp) && time() > $decoded->exp) {
				return null;
			}

            return [
                'user_key' => $userKey,
                'session_key' => $sessionKey,
            ];
        } catch (\Exception $e) {
            throw new \Exception('Errore durante la decodifica JWT: ' . $e->getMessage());
        }
    }
}