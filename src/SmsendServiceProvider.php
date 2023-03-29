<?php

namespace Dunp\Smsend;

use Illuminate\Support\ServiceProvider;
use Dunp\Smsend\Console\SmsendConfigCommand;

/**
 * Il service provider per l'integrazione della libreria Smsend in Laravel
 */
class SmsendServiceProvider extends ServiceProvider
{
    /**
     * Esegue le operazioni di boot del service provider
     *
     * @return void
     */
    public function boot()
    {
        // Merge del file di configurazione del package nella configurazione del progetto
        $this->mergeConfigFrom(__DIR__.'/../config/smsend.php', 'smsend');
    }

    /**
    * Registra il servizio Smsend come singleton e si occupa di effettuare l'autenticazione dell'utente.
    *
    * @return void
    */
    public function register()
    {
        // Registra il service Smsend come singleton
        $this->app->singleton(Smsend::class, function ($app) {
            $auth = new SmsendAuth();
            $credentials = $auth->authenticate();

            $userKey = $credentials['user_key'];
            $sessionKey = $credentials['Session_key'];
            $client = $credentials['client'];

            return new Smsend($userKey, $sessionKey, $client);
        });

        // Registra il comando per la configurazione del package
        if ($this->app->runningInConsole()) {
            $this->commands([
                SmsendConfigCommand::class,
            ]);
        }
    }
}
