<?php

    return [
        // FUC merchant code (required)
        'dsMerchantCode' => env('DS_MERCHANT_CODE', '999008881'),
        // Currency code ISO-4217 (required)
        'dsCurrencyCode' => env('DS_CURRENCY_CODE', \Orumad\LaravelRedsys\Services\Redsys\DsMerchantCurrency::EUR),
        // Transaction type (required)
        'dsTransactionType' => env('DS_TRANSACTION_TYPE', \Orumad\LaravelRedsys\Services\Redsys\DsMerchantTransactionType::AUTHORIZATION),
        // Terminal number. Asigned by the bank
        'dsTerminalNumber' => env('DS_TERMINAL_NUMBER', '01'),
        // Signature version. As the moment is a fixed value
        'dsSignatureVersion' => env('DS_SIGNATURE_VERSION', 'HMAC_SHA256_V1'),
        // Customer language
        'dsCustomerLanguage' => env('DS_CUSTOMER_LANGUAGE', \Orumad\LaravelRedsys\Services\Redsys\DsMerchantCustomerLanguage::UNSPECIFIED),
        // Merchant name (as appear in the payment receipt)
        'dsMerchantName' => env('DS_MERCHANT_NAME', 'Business, Inc'),
        // KEY secret (AKA "clave de comercio"). You should access to the Administration module
        // of the Redsys platform and navigate to the "Consulta de datos de Comercio". Then you
        // can access the KEY SECRET clicking "Ver clave"
        'keySecret' => env('REDSYS_KEY_SECRET', 'sq7HjrUOBfKmC576ILgskD5srU870gJ7'),
        // Platform URL. During your develoment and test you can use the "testing" URL
        'url' => [
            'testing' => env('REDSYS_TESTING_URL', 'https://sis-t.redsys.es:25443/sis/realizarPago'),
            'production' => env('REDSYS_PRODUCTION_URL', 'https://sis.redsys.es/sis/realizarPago'),
        ],
    ];
