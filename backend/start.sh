#!/bin/bash
# Start nginx and PHP-FPM

# Create Laravel directory structure if not exists
mkdir -p /var/www/html/public
mkdir -p /var/www/html/storage/logs
mkdir -p /var/www/html/bootstrap/cache

# Ensure index.php exists for testing
if [ ! -f /var/www/html/public/index.php ]; then
    echo '<?php echo phpinfo(); ?>' > /var/www/html/public/index.php
fi

# Fix permissions
chown -R www-data:www-data /var/www/html
chmod -R 755 /var/www/html
chmod -R 777 /var/www/html/storage
chmod -R 777 /var/www/html/bootstrap/cache

# Remove the default site config if it exists
rm -f /etc/nginx/sites-enabled/default

# Start services
service nginx start
php-fpm
