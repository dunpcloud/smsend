<?php

namespace Dunp\Smsend\Traits;

use Dunp\Smsend\Models\SmsendUser;

trait HasSmsendUser
{
    /**
    * Ottieni l'oggetto SmsendUser associato all'utente.
    */
    public function smsendUser()
    {
        return $this->hasOne(SmsendUser::class);
    }
}
