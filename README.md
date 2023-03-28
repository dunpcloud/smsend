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

## Utilizzo
Il pacchetto Smsend offre due tipologie di SMS: testo normale e parametrizzato.\
Nel primo caso, il messaggio di testo è costante per tutti i destinatari, mentre nel secondo caso è possibile personalizzarlo utilizzando delle variabili parametriche.

Ci sono tre modi per inviare sms tramite il pacchetto Smsend:

### Notifiche preimpostate
Il primo metodo di utilizzo è rappresentato dalle notifiche preimpostate, quattro classi già pronte all'uso: SingleSmsendNotification, SingleParamSmsendNotification, MultiSmsendNotification e MultiParamSmsendNotification.\
È sufficiente estenderle per utilizzarle, impostando il messaggio di invio con il metodo setContentMessage().

Esempio:
```
namespace App\Notifications;

use Dunp\Smsend\Notifications\SingleSmsendNotification;

class MiaNotificaSingola extends SingleSmsendNotification
{
	public function setContentMessage(): string
	{
		return 'Questo è un messaggio di prova singolo.';
	}
}
```

tre modi per l'invio di messaggi tramite il servizio di SMS di smsend.it:
* Il primo è rappresentato dalle **notifiche preimpostate**, quattro classi già pronte all'uso: SingleSmsendNotification, SingleParamSmsendNotification, MultiSmsendNotification, MultiParamSmsendNotification.\
Per utilizzarle, è sufficiente estenderle e impostare il contenuto del messaggio con il metodo setContentMessage().

* Il secondo metodo consiste nell'estendere la classe SmsendNotification per creare una propria notifica personalizzata.

* Il terzo metodo, infine, permette di utilizzare la classe Smsend al di fuori delle notifiche di Laravel.

### Notifiche preimpostate


## Licenza
Il pacchetto è rilasciato sotto licenza MIT. Vedi il [File Licenza](LICENSE.md) per ulteriori dettagli.
