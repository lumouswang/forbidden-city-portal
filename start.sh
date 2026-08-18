#!/bin/sh
# Restart PHP CLI server if it dies
while true; do
    echo "Starting PHP server at $(date)..."
    php -S 0.0.0.0:${PORT:-8080} -t .
    echo "PHP server exited at $(date), restarting in 1s..."
    sleep 1
done