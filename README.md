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

#### Impostazioni file di configurazione smsend.php
Nel file di configurazione sarà possibile modificare le seguenti impostazioni:
<table>
  <thead>
    <tr>
      <th>Opzione</th>
      <th>Valore di default</th>
      <th>Descrizione</th>
    </tr>
  </thead>
  <tbody>
    <tr>
      <td>timeout</td>
      <td>5</td>
      <td>Tempo massimo in secondi per la connessione all'API di SMSend</td>
    </tr>
    <tr>
      <td>default_column</td>
      <td>phone_number</td>
      <td>Colonna di default per l'inserimento dei numeri di telefono dei destinatari</td>
    </tr>
    <tr>
      <td>quality</td>
      <td>L</td>
      <td>Qualità del messaggio di default (N, L o LL)</td>
    </tr>
    <tr>
      <td>sender</td>
      <td>vuoto</td>
      <td>Mittente di default per i messaggi (lasciare vuoto per utilizzare quello impostato da SMSend)</td>
    </tr>
  </tbody>
</table>

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
Il primo metodo di utilizzo è rappresentato dalle notifiche preimpostate che sfruttano il servizio di notifiche di Laravel, quattro classi già pronte all'uso: SingleSmsendNotification, SingleParamSmsendNotification, MultiSmsendNotification e MultiParamSmsendNotification.\
È sufficiente estenderle per utilizzarle, impostando il messaggio di invio con il metodo setContentMessage().

#### Invio singolo
Create un file chiamandolo per esempio SingleSmsNotification.php nel percorso app\Notifications.
```
namespace App\Notifications;

use Dunp\Smsend\Notifications\SingleSmsendNotification;

class SingleSmsNotification extends SingleSmsendNotification
{
	public function setContentMessage(): string
	{
		return 'Questo è un messaggio di prova singolo.';
	}
}
```
Nel vostro controller inserite il seguente codice per inviare la notifica ad un utente specifico.
```
$user = User::find(1);
$user->notify(new SingleSmsNotification());
```
#### Invio multiplo
Create un file chiamandolo per esempio MultiSmsNotification.php nel percorso app\Notifications.
```
namespace App\Notifications;

use Dunp\Smsend\Notifications\MultiSmsendNotification;

class MultiSmsNotification extends MultiSmsendNotification
{
	public function setContentMessage(): string
	{
		return 'Questo è un messaggio di prova multiplo.';
	}
}
```
A differenza dell'invio singolo la classe MultiSmsendNotification si aspetta una collection in modo da impostare un'invio a più utenti attraverso un'unica richiesta http alle api di Smsend.
Nel vostro controller inserite il seguente codice per inviare la notifica ad un utente specifico.
```
$users = User::all();
Notification::send(null, new MultiSmsNotification($users));
```
#### Invio singolo con parametri
La grande differenza tra un sms normale ed uno con parametri è che il contenuto del messaggio potrà essere personalizzato per ogni utente a cui verrà inviato il messaggio. Per inserire delle variabili nel messaggio sarà sufficiente utilizzare la sintassi ${nomevariabile}.
Create un file chiamandolo per esempio SingleParamSmsNotification.php nel percorso app\Notifications.
```
namespace App\Notifications;

use Dunp\Smsend\Notifications\SingleParamSmsendNotification;

class SingleParamSmsNotification extends SingleParamSmsendNotification
{
	public function setContentMessage(): string
	{
		return 'Ciao ${name}, questo un messaggio di prova singolo.';
	}
}
```
Sarà importante che la variabile inserita nel messaggio sia contenuta nell'oggetto User, nel nosstro esempio è $user->name.
Nel vostro controller inserite il seguente codice per inviare la notifica ad un utente specifico.
```
$user = User::find(1);
$user->notify(new SingleParamSmsNotification());
```
#### Invio multiplo con parametri
```
namespace App\Notifications;

use Dunp\Smsend\Notifications\MultiParamSmsendNotification;

class MultiParamSmsNotification extends MultiParamSmsendNotification
{
	public function setContentMessage(): string
	{
		return 'Ciao ${name}, questo un messaggio di prova multiplo.';
	}
}
```
Il codice da inserire nel vostro controller è uguale a quella dell'invio multiplo senza parametri, con l'unica differenza della notifica utilizzata
```
$users = User::all();
Notification::send(null, new MultiParamSmsNotification($users));
```

tre modi per l'invio di messaggi tramite il servizio di SMS di smsend.it:
* Il primo è rappresentato dalle **notifiche preimpostate**, quattro classi già pronte all'uso: SingleSmsendNotification, SingleParamSmsendNotification, MultiSmsendNotification, MultiParamSmsendNotification.\
Per utilizzarle, è sufficiente estenderle e impostare il contenuto del messaggio con il metodo setContentMessage().

* Il secondo metodo consiste nell'estendere la classe SmsendNotification per creare una propria notifica personalizzata.

* Il terzo metodo, infine, permette di utilizzare la classe Smsend al di fuori delle notifiche di Laravel.

### Notifiche preimpostate


## Licenza
Il pacchetto è rilasciato sotto licenza MIT. Vedi il [File Licenza](LICENSE.md) per ulteriori dettagli.
