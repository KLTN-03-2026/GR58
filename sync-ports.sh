#!/bin/bash

# Script to sync port configuration between backend and frontend
# Usage: ./sync-ports.sh [port]
# Example: ./sync-ports.sh 8002

PORT=${1:-8001}  # Default to 8001 if no argument provided

echo "🔄 Syncing port configuration to $PORT..."

# Get real path of backend .env (follow symlink)
BACKEND_ENV=$(readlink -f petty_BE/.env 2>/dev/null || realpath petty_BE/.env 2>/dev/null || echo "petty_BE/.env")

# Update Backend .env
echo "📝 Updating Backend ($BACKEND_ENV)..."
if [[ "$OSTYPE" == "darwin"* ]]; then
    # macOS
    sed -i '' "s|APP_URL=http://localhost:[0-9]*|APP_URL=http://localhost:$PORT|g" "$BACKEND_ENV"
else
    # Linux
    sed -i "s|APP_URL=http://localhost:[0-9]*|APP_URL=http://localhost:$PORT|g" "$BACKEND_ENV"
fi

# Update Frontend .env
echo "📝 Updating Frontend (petty_FE/.env)..."
if [[ "$OSTYPE" == "darwin"* ]]; then
    # macOS
    sed -i '' "s|VITE_API_BASE=http://localhost:[0-9]*/api|VITE_API_BASE=http://localhost:$PORT/api|g" petty_FE/.env
else
    # Linux
    sed -i "s|VITE_API_BASE=http://localhost:[0-9]*/api|VITE_API_BASE=http://localhost:$PORT/api|g" petty_FE/.env
fi

# Clear Laravel cache
echo "🧹 Clearing Laravel cache..."
cd petty_BE
php artisan config:clear > /dev/null 2>&1
php artisan cache:clear > /dev/null 2>&1
cd ..

echo "✅ Port configuration synced!"
echo ""
echo "Backend APP_URL: http://localhost:$PORT"
echo "Frontend VITE_API_BASE: http://localhost:$PORT/api"
echo ""
echo "🚀 To start servers:"
echo "   Backend:  cd petty_BE && php artisan serve --port=$PORT"
echo "   Frontend: cd petty_FE && npm run dev"
echo ""
echo "⚠️  IMPORTANT: You MUST restart both servers for changes to take effect!"
