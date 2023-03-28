# smsend
Smsend è un pacchetto Laravel progettato per semplificare l'invio di SMS attraverso il provider italiano smsend.it. Grazie a questo pacchetto, gli sviluppatori possono integrare facilmente l'invio di messaggi SMS nelle loro applicazioni Laravel con poche righe di codice.

## Installazione e configurazione

### Installazione tramite Composer
Per installare il pacchetto, utilizza Composer eseguendo il comando:

`composer require dunp/smsend`

### Registrazione del service provider
Dopo aver installato il pacchetto, è necessario registrare il service provider in Laravel. Verifica se il provider è già presente nel file config/app.php sotto la voce "providers". In caso contrario, aggiungi il seguente provider alla lista dei provider:

`Dunp\Smsend\SmsendServiceProvider::class,`

### Pubblicazione del file di configurazione
Per pubblicare il file di configurazione, esegui il comando:

`php artisan vendor:publish --provider="Dunp\Smsend\SmsendServiceProvider"`

Questo copierà il file di configurazione smsend.php nella cartella config del tuo progetto.

In caso di problemi con il comando vendor:publish, è possibile copiare manualmente il file di configurazione dalla cartella vendor/dunp/smsend-laravel/config alla cartella config del tuo progetto.

### Inserimento delle credenziali
Per utilizzare il pacchetto Smsend, è necessario inserire le credenziali del tuo account Smsend nel file .env del tuo progetto. Apri il file .env e aggiungi le seguenti righe:
```
SMSEND_USERNAME=tuo_username
SMSEND_PASSWORD=tua_password
```
Sostituisci "tuo_username" con il tuo nome utente Smsend e "tua_password" con la tua password Smsend.\
Se non hai ancora un account Smsend, puoi crearne uno gratuitamente sul sito [smsend.it](https://www.smsend.it/).

Le credenziali inserite saranno utilizzate per l'autenticazione al servizio di Smsend durante l'invio degli SMS.

## Licenza
Il pacchetto è rilasciato sotto licenza MIT. Vedi il file [License File](LICENSE.md) per ulteriori dettagli.
