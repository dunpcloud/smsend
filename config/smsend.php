<?php

return [
    // URL base dell'API di SMSend
    'base_url' => 'https://app.gateway.smsend.it/API/v1.0/REST/',
    // Nome utente per l'autenticazione con l'API di SMSend (prelevato dall'ambiente)
    'username' => env('SMSEND_USERNAME'),
    // Password per l'autenticazione con l'API di SMSend (prelevata dall'ambiente)
    'password' => env('SMSEND_PASSWORD'),
    // Tempo massimo in secondi per la connessione all'API di SMSend
    'timeout' => 5,
    // Colonna di default per l'inserimento dei numeri di telefono dei destinatari
    'default_column' => 'phone_number',
    // Qualità del messaggio di default (N, L o LL)
    'quality' => 'L',
    // Mittente di default per i messaggi (lasciare vuoto per utilizzare quello impostato da SMSend)
    'sender' => ''
];