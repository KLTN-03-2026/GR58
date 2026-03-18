# 🔧 Quick Fix: Permission Issues After Git Pull

## Problem

Getting permission errors (403 Forbidden) after pulling code from GitHub?

## Solution

### Option 1: Run Auto-Fix Script (Recommended) ⚡

```powershell
.\fix-permissions.ps1
```

### Option 2: Manual Commands

```powershell
# 1. Disable git file mode tracking
git config core.fileMode false

# 2. Clear cache
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

# 3. Recreate cache
php artisan config:cache
php artisan route:cache
```

## What the script does:

✅ Disables git file mode tracking  
✅ Sets correct permissions for storage folders  
✅ Clears Laravel cache  
✅ Recreates optimized cache

## Need More Help?

See detailed documentation: [HUONG_DAN_SUA_LOI_PHAN_QUYEN.md](./HUONG_DAN_SUA_LOI_PHAN_QUYEN.md)

---

**Note:** Always run this script after pulling from GitHub to avoid permission issues.
