# Fix Permissions Script
Write-Host "=== FIX PERMISSIONS SCRIPT ===" -ForegroundColor Green
Write-Host ""

# 1. Disable git file mode tracking
Write-Host "1. Disabling git file mode tracking..." -ForegroundColor Yellow
git config core.fileMode false
Write-Host "   Done!" -ForegroundColor Green
Write-Host ""

# 2. Set permissions for storage
Write-Host "2. Setting permissions for storage..." -ForegroundColor Yellow
if (Test-Path "storage") {
    icacls storage /grant Everyone:F /T /C /Q | Out-Null
    Write-Host "   Done!" -ForegroundColor Green
}
Write-Host ""

# 3. Set permissions for bootstrap/cache
Write-Host "3. Setting permissions for bootstrap/cache..." -ForegroundColor Yellow
$cachePath = Join-Path "bootstrap" "cache"
if (Test-Path $cachePath) {
    icacls $cachePath /grant Everyone:F /T /C /Q | Out-Null
    Write-Host "   Done!" -ForegroundColor Green
}
Write-Host ""

# 4. Clear Laravel cache
Write-Host "4. Clearing Laravel cache..." -ForegroundColor Yellow
php artisan config:clear 2>&1 | Out-Null
php artisan cache:clear 2>&1 | Out-Null
php artisan route:clear 2>&1 | Out-Null
php artisan view:clear 2>&1 | Out-Null
Write-Host "   Done!" -ForegroundColor Green
Write-Host ""

# 5. Recreate cache
Write-Host "5. Recreating cache..." -ForegroundColor Yellow
php artisan config:cache 2>&1 | Out-Null
php artisan route:cache 2>&1 | Out-Null
Write-Host "   Done!" -ForegroundColor Green
Write-Host ""

Write-Host "=== COMPLETED ===" -ForegroundColor Green
Write-Host ""
Write-Host "If you still have issues, run:" -ForegroundColor Cyan
Write-Host "   composer dump-autoload" -ForegroundColor White
Write-Host ""
