<?php

namespace Dunp\Smsend\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SmsendUser extends Model
{
    use HasFactory;

    /**
     * La tabella associata a questo modello.
     *
     * @var string
     */
    protected $table = 'smsend_users';

    /**
    * Gli attributi che sono assegnabili.
    *
    * @var array<int, string>
    */
    protected $fillable = [
        'username',
        'password',
        'user_id',
    ];
}