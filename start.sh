#!/bin/bash
# Sử dụng PORT từ Railway nếu có, mặc định là 8080
SERVER_PORT=${PORT:-8080}
echo "Starting PHP Server on port $SERVER_PORT..."
php -S 0.0.0.0:$SERVER_PORT 