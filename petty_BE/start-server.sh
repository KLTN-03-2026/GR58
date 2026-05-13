#!/bin/bash

# Script to start Laravel server with dynamic port configuration
# Usage: ./start-server.sh [port]
# Example: ./start-server.sh 8002

PORT=${1:-8001}  # Default to 8001 if no argument provided

echo "🚀 Starting Laravel server on port $PORT..."

# Update APP_URL in .env to match the port
if [[ "$OSTYPE" == "darwin"* ]]; then
    # macOS
    sed -i '' "s|APP_URL=http://localhost:[0-9]*|APP_URL=http://localhost:$PORT|g" .env
else
    # Linux
    sed -i "s|APP_URL=http://localhost:[0-9]*|APP_URL=http://localhost:$PORT|g" .env
fi

echo "✅ Updated APP_URL to http://localhost:$PORT"

# Clear config cache
php artisan config:clear

# Start server
php artisan serve --port=$PORT
