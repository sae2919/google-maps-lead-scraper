#!/bin/sh

# Exit immediately if a command exits with a non-zero status
set -e

# Cache configuration, routes, and views for production
echo "Caching Laravel configuration and routes..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Start Supervisor (which starts PHP-FPM and Nginx)
exec supervisord -c /etc/supervisor/conf.d/supervisord.conf
