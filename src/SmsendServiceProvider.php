<?php

namespace Dunp\Smsend;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Collection;
use Illuminate\Support\ServiceProvider;
use Dunp\Smsend\Console\SmsendConfigCommand;
use Dunp\Smsend\Console\SmsendMigrationsCommand;

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
        // Pubblica il file di configurazione nella directory di configurazione del progetto
        $this->publishes([
            __DIR__.'/../config/smsend.php' => config_path('smsend.php')
        ], 'config');
		
		// Pubblica il file migrazione nella directory delle migrazioni del progetto
		$this->publishes([
            __DIR__.'/../database/migrations/create_smsend_users_table.php.stub' => $this->getMigrationFileName('create_smsend_users_table.php'),
        ], 'migrations');
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
			$auth = $this->getAuthInstance();

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
				SmsendMigrationsCommand::class,
            ]);
        }
    }
	
    /**
    * Restituisce un'istanza dell'autenticazione Smsend in base alla modalità configurata
    *
    * @return SmsendAuth|MultiUserSmsendAuth
    */
	private function getAuthInstance()
    {
        $multi_user = config('smsend.mode');

        if ($multi_user === true && Auth::check()) {
            $user = Auth::user();
            return new MultiUserSmsendAuth($user);
        }

        return new SmsendAuth();
    }
	
    /**
    * Restituisce il nome del file di migrazione con un timestamp univoco
    *
    * @param string $migrationFileName
    * @return string
    */
    protected function getMigrationFileName($migrationFileName): string
    {
        $timestamp = date('Y_m_d_His');

        $filesystem = $this->app->make(Filesystem::class);

        return Collection::make($this->app->databasePath().DIRECTORY_SEPARATOR.'migrations'.DIRECTORY_SEPARATOR)
            ->flatMap(function ($path) use ($filesystem, $migrationFileName) {
                return $filesystem->glob($path.'*_'.$migrationFileName);
            })
            ->push($this->app->databasePath()."/migrations/{$timestamp}_{$migrationFileName}")
            ->first();
    }
}