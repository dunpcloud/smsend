<?php

namespace Dunp\Smsend\Console;

use Illuminate\Console\Command;
use Dunp\Smsend\SmsendServiceProvider;

/**
 * Il comando Artisan per pubblicare il file di configurazione di Smsend
 */
class SmsendConfigCommand extends Command
{
    protected $signature = 'smsend:config';
    protected $description = 'Publish Smsend configuration file';

    /**
     * Gestisce l'esecuzione del comando
     *
     * @return void
     */
    public function handle(): void
    {
        $this->call('vendor:publish', [
            '--provider' => SmsendServiceProvider::class,
            '--tag' => 'config'
        ]);
    }
}