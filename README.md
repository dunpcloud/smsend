# Package SmSend Laravel
Smsend è un pacchetto Laravel progettato per semplificare l'invio di SMS attraverso il provider italiano smsend.it. Grazie a questo pacchetto, gli sviluppatori possono facilmente integrare l'invio di messaggi SMS nelle loro applicazioni Laravel con poche righe di codice.

## Compatibilità
Il pacchetto è compatibile con le versioni 9.x e 10.x di Laravel.

## Installazione e configurazione
Per installare e configurare il pacchetto Smsend Laravel, segui i seguenti passaggi.

### Installazione tramite Composer
Per installare il pacchetto, utilizza Composer eseguendo il comando:

`composer require dunp/smsend-laravel`

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

### Notifiche preimpostate
Le notifiche preimpostate sono un modo semplice ed efficace per utilizzare il pacchetto Smsend Laravel. Esse sono implementate come quattro classi già pronte all'uso: SingleSmsendNotification, SingleParamSmsendNotification, MultiSmsendNotification e MultiParamSmsendNotification, che possono essere estese per personalizzare il messaggio di invio.\
Per utilizzare il sistema di notifiche di Laravel, è necessario inserire il tratto "Notifiable" nel Model sul quale verranno utilizzate le notifiche. Ad esempio, se vogliamo applicare le notifiche al Model User, dovremo aggiungere il seguente codice:

```
namespace App\Models;
...
use Illuminate\Notifications\Notifiable;
...
class User
{
    use Notifiable;
    
    ...

}
```

Inoltre, se necessario, è possibile personalizzare la colonna del numero di telefono utilizzata da una notifica specifica attraverso il metodo setColumnNumber(), che va inserito nella classe della notifica. In questo modo è possibile effettuare un override all'impostazione globale contenuta nel file di configurazione smsend.php.

```
namespace Dunp\Smsend\Notifications;

use Dunp\Smsend\SmsendMessage;

class LaMiaNotifica extends SmsendNotification
{
...
    public function setColumnNumber(): string
    {
        return 'numero_telefono';
    }
...
}
```

#### Invio singolo
Per l'invio di un singolo messaggio, si utilizza la classe SingleSmsendNotification che eredita dalla classe astratta SmsendNotification.

Per utilizzare questa classe, è necessario creare un file chiamato, ad esempio, SingleSmsNotification.php nella directory app\Notifications e definire la classe SingleSmsNotification che estende la classe SingleSmsendNotification. In questa classe, è necessario definire il metodo setContentMessage che restituisce il contenuto del messaggio da inviare.

Esempio di SingleSmsNotification.php:

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

Successivamente, per inviare la notifica ad un utente specifico, nel controller si può utilizzare il seguente codice:

```
use App\Models\User;
use App\Notifications\SingleSmsNotification;
...
$user = User::find(1);
$user->notify(new SingleSmsNotification());
```

#### Invio multiplo
Per utilizzare il metodo di invio multiplo tramite notifiche preimpostate, si deve creare un file chiamato, ad esempio, MultiSmsNotification.php all'interno della directory app/Notifications. In questo file si estende la classe MultiSmsendNotification del pacchetto Smsend e si implementa il metodo setContentMessage per impostare il contenuto del messaggio.

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

La classe MultiSmsendNotification si aspetta una collection di destinatari, in modo da impostare un invio multiplo a più utenti attraverso una singola richiesta HTTP alle API di Smsend.

Nel controller, si può utilizzare il metodo Notification::send di Laravel per inviare la notifica ai destinatari.

```
use App\Models\User;
use App\Notifications\MultiSmsNotification;
use Illuminate\Support\Facades\Notification;
...
$users = User::all();
Notification::send(null, new MultiSmsNotification($users));
```

In questo esempio si vogliono inviare SMS a tutti gli utenti presenti nel database. Per farlo, si selezionano tutti gli utenti dal database e si passano alla classe MultiSmsNotification tramite una collection. Il messaggio del SMS viene impostato nel metodo setContentMessage della classe MultiSmsNotification.

#### Invio singolo con parametri
Nel caso si voglia inviare un SMS con parametri personalizzati per ogni utente, sarà necessario creare una classe che estenda SingleParamSmsendNotification. Questa classe dovrà implementare il metodo setContentMessage, all'interno del quale sarà possibile utilizzare la sintassi ${nomevariabile} per inserire le variabili personalizzate.

Ad esempio, creiamo un file chiamato SingleParamSmsNotification.php all'interno della cartella app\Notifications:

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

Nel nostro esempio, abbiamo utilizzato la variabile ${name}, che dovrà essere contenuta nell'oggetto User.

Per inviare la notifica ad un utente specifico nel nostro controller, inseriamo il seguente codice:

```
use App\Models\User;
use App\Notifications\SingleParamSmsNotification;
...
$user = User::find(1);
$user->notify(new SingleParamSmsNotification());
```

In questo modo, l'utente riceverà un SMS con il proprio nome inserito all'interno del messaggio.

#### Invio multiplo con parametri
L'invio multiplo con parametri consente di personalizzare il messaggio di testo per ogni destinatario utilizzando delle variabili parametriche.

Per creare una notifica di questo tipo, create un file chiamato MultiParamSmsNotification.php nella cartella app/Notifications del vostro progetto Laravel, e inserite il seguente codice:

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

In questo esempio, viene utilizzata la variabile ${name} per personalizzare il messaggio per ogni destinatario. Assicuratevi che la variabile utilizzata sia presente nell'oggetto User.

Per inviare la notifica a tutti gli utenti, nel vostro controller inserite il seguente codice:

```
use App\Models\User;
use App\Notifications\MultiParamSmsNotification;
use Illuminate\Support\Facades\Notification;
...
$users = User::all();
Notification::send(null, new MultiParamSmsNotification($users));
```

In questo modo, il messaggio di testo sarà personalizzato per ogni destinatario utilizzando le variabili parametriche specificate nella classe MultiParamSmsNotification.

### Notifiche personalizzate
Se le notifiche preimpostate non sono sufficienti per le tue esigenze, puoi creare una notifica personalizzata estendendo la classe SmsendNotification e impostando il metodo toSms().

La classe SmsendNotification fornisce anche i metodi che abbiamo visto in precedenza, come setContentMessage() e setColumnNumber(). Inoltre, di default, la classe SmsendNotification istanzia un oggetto SmsendMessage nella proprietà $message, sulla quale è possibile utilizzare diversi metodi per personalizzare la notifica in base alle tue esigenze.

<table>
  <thead>
    <tr>
      <th>Metodo</th>
      <th>Descrizione</th>
    </tr>
  </thead>
  <tbody>
    <tr>
      <td>to(object $recipient)</td>
      <td>Aggiunge un destinatario al messaggio</td>
    </tr>
    <tr>
      <td>multiTo(Collection $recipients)</td>
      <td>Aggiunge più destinatari al messaggio</td>
    </tr>
    <tr>
      <td>toParametric(object $recipient)</td>
      <td>Aggiunge un destinatario parametrico al messaggio</td>
    </tr>
    <tr>
      <td>multiToParametric(Collection $recipients)</td>
      <td>Aggiunge più destinatari parametrici al messaggio</td>
    </tr>
    <tr>
      <td>content(string $message)</td>
      <td>Imposta il contenuto del messaggio</td>
    </tr>
    <tr>
      <td>quality(string $quality)</td>
      <td>Imposta la qualità del messaggio</td>
    </tr>
    <tr>
      <td>from(string $sender)</td>
      <td>Imposta il mittente del messaggio</td>
    </tr>
    <tr>
      <td>scheduledAt(string $scheduled_delivery_time)</td>
      <td>Imposta la data e l'ora programmata per la consegna del messaggio nel formato YYYYMMDDhhmmss</td>
    </tr>
    <tr>
      <td>setColumn(string $columnNumber)</td>
      <td>Imposta la colonna del destinatario del messaggio</td>
    </tr>
  </tbody>
</table>

Il metodo toSms() utilizza l'oggetto $message, istanziato dalla classe SmsendNotification, per personalizzare la notifica tramite l'utilizzo dei vari metodi messi a disposizione dall'oggetto SmsendMessage.

```
namespace Dunp\Smsend\Notifications;

use Dunp\Smsend\SmsendMessage;

class LaMiaNotificaPersonalizzata extends SmsendNotification
{
    public function toSms($notifiable): SmsendMessage
    {
        return $this->message
	    ->quality('N')
	    ->content('Questo è il mio messaggio.')
	    ->from('NomeAzienda')
	    ->scheduledAt('20230331121020')
	    ->to($notifiable);
    }
}
```

L'oggetto passato al metodo toSms, ovvero $notifiable, rappresenta l'oggetto sul quale viene utilizzato il sistema di notifiche di Laravel. In genere, si tratta di un'istanza del modello Eloquent associato al destinatario della notifica.

```
$user = User::find(1);
$user->notify(new LaMiaNotificaPersonalizzata());
```

$notifiable sarà appunto $user che conterrà la proprietà $user->phone_number ed eventuali altre proprietà per l'invio con parametri.

Inoltre, è possibile inviare la notifica in maniera più veloce impostando direttamente i valori di content, quality, from e scheduled_at al momento dell'istanza della classe della notifica, senza doverli definire all'interno del metodo toSms. Ecco un esempio di come farlo:

```
// Controller
$user = User::find(1);
$user->notify(new LaMiaNotificaPersonalizzata([
    'content' => 'Questo è il mio messaggio.',
    'quality' => 'N',
    'from' => 'NomeAzienda',
    'scheduledAt' => '20230331121020'
]);

//Notifica
public function toSms($notifiable): SmsendMessage
    {
        return $this->message
	    ->to($notifiable);
    }
```

Per creare una notifica personalizzata per l'invio multiplo è necessario definire un costruttore all'interno della classe della tua notifica per creare una proprietà Collection, poiché a differenza dell'invio singolo abbiamo bisogno di una Collection di oggetti e non di un oggetto singolo. In questo modo sarà possibile passare una collection di utenti alla notifica e utilizzarli come destinatari dell'invio multiplo.

```
namespace Dunp\Smsend\Notifications;

use Dunp\Smsend\SmsendMessage;
use Illuminate\Database\Eloquent\Collection;

class LaMiaNotificaMultipla extends SmsendNotification
{
    protected Collection $collection;

    public function __construct(Collection $collection)
    {
        parent::__construct();
        $this->collection = $collection;
    }

    public function toSms($notifiable): SmsendMessage
    {
        return $this->message
	    ->multiTo($this->collection);
    }
}
```

Per gli invii con parametri la logica di costruzione è la stessa, la differenza sarà nell'utilizzo dei metodi per definire i destinatari. Per l'invio singolo si utilizza il metodo toParametric(object $recipient) e per l'invio multiplo si utilizza il metodo multiToParametric(Collection $recipients).

È possibile anche fare invii multipli sfruttando soltanto l'invio singolo, ma questa soluzione non è conveniente in quanto viene effettuata una richiesta HTTP alle API di Smsend per ogni invio, anziché una singola richiesta HTTP come nell'invio multiplo descritto in precedenza.

```
$users = User::all();
Notification::send($users, new SingleSmsendNotification());
```

Questa soluzione non è molto conveniente perché per ogni invio viene effettuata una richiesta HTTP alle API di Smsend, mentre per l'invio multiplo descritto in precedenza viene effettuata una singola richiesta HTTP, riducendo così il carico di lavoro del server.

## Licenza
Il pacchetto è rilasciato sotto licenza MIT. Vedi il [File Licenza](LICENSE.md) per ulteriori dettagli.
