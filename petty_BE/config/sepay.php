<?php

return [
    'api_key' => env('SEPAY_API_KEY'),
    'bank_code' => env('SEPAY_BANK_CODE', 'MB'),
    'account_number' => env('SEPAY_ACCOUNT_NUMBER'),
    'account_name' => env('SEPAY_ACCOUNT_NAME'),
    'webhook_url' => env('SEPAY_WEBHOOK_URL'),
    'payment_expiry_minutes' => (int) env('SEPAY_PAYMENT_EXPIRY_MINUTES', 15),
    'qr_template' => 'compact2',
];
