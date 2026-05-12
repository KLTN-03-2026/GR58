#!/bin/bash

# Script to update database URLs from port 8000 to 8001
# This fixes URLs that were saved when backend was running on port 8000

echo "🔄 Updating database URLs from port 8000 to 8001..."

cd petty_BE

# Update KhachHang table
php artisan tinker --execute="
\$updated = \App\Models\KhachHang::where('anh_dai_dien', 'like', '%127.0.0.1:8000%')
    ->orWhere('anh_dai_dien', 'like', '%localhost:8000%')
    ->get()
    ->each(function(\$customer) {
        \$customer->anh_dai_dien = str_replace(
            ['http://127.0.0.1:8000', 'http://localhost:8000'],
            ['http://localhost:8001', 'http://localhost:8001'],
            \$customer->anh_dai_dien
        );
        \$customer->save();
    });
echo 'Updated ' . \$updated->count() . ' customer avatars' . PHP_EOL;
"

echo "✅ Database URLs updated!"
echo ""
echo "🔍 To verify, check:"
echo "   php artisan tinker --execute=\"echo \App\Models\KhachHang::find(2)->anh_dai_dien;\""
