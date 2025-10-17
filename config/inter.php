<?php

return [
    'client_id' => env('INTER_CLIENT_ID'),
    'client_secret' => env('INTER_CLIENT_SECRET'),
    'cert_path' => storage_path('inter/InterAPI_Certificado.crt'),
    'key_path' => storage_path('inter/InterAPI_Chave.key'),
    'environment' => env('INTER_ENV', 'prod'), // ou 'sandbox' se ainda for teste
];
