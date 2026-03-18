<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$token = \Laravel\Sanctum\PersonalAccessToken::find(19);

if ($token) {
    echo "Token ID: 19\n";
    echo "Tokenable Type: " . $token->tokenable_type . "\n";
    echo "Tokenable ID: " . $token->tokenable_id . "\n";
    echo "Name: " . $token->name . "\n";

    if ($token->tokenable) {
        echo "Tokenable Class: " . get_class($token->tokenable) . "\n";
        if ($token->tokenable instanceof \App\Models\Admin) {
            echo "User: Admin - " . $token->tokenable->ho_ten . "\n";
        } elseif ($token->tokenable instanceof \App\Models\NhanVien) {
            echo "User: NhanVien - " . $token->tokenable->full_name . "\n";
        } elseif ($token->tokenable instanceof \App\Models\KhachHang) {
            echo "User: KhachHang - " . $token->tokenable->ho_ten . "\n";
        }
    }
} else {
    echo "Token not found\n";
}

echo "\n=== Recent Admin Tokens ===\n";
$adminTokens = \Laravel\Sanctum\PersonalAccessToken::where('tokenable_type', 'App\\Models\\Admin')
    ->orderBy('created_at', 'desc')
    ->limit(5)
    ->get();

foreach ($adminTokens as $t) {
    $admin = \App\Models\Admin::find($t->tokenable_id);
    echo "Token ID: {$t->id} | Name: {$t->name} | Admin: " . ($admin ? $admin->ho_ten : 'N/A') . " | Created: {$t->created_at}\n";
}
