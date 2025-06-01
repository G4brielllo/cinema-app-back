<?php
$sandbox = filter_var(env('PAYU_SANDBOX', true), FILTER_VALIDATE_BOOLEAN);

return [
    'pos_id' => env('PAYU_POS_ID'),
    'client_id' => env('PAYU_CLIENT_ID'),
    'client_secret' => env('PAYU_CLIENT_SECRET'),
    'second_key' => env('PAYU_SECOND_KEY'),
    'sandbox' => $sandbox = filter_var(env('PAYU_SANDBOX', true), FILTER_VALIDATE_BOOLEAN),
    'base_url' => $sandbox
    ? 'https://secure.snd.payu.com'
    : 'https://secure.payu.com',

];
