#!/bin/bash
set -e

PORT="${PORT:-8080}"

echo "Listen ${PORT}" > /etc/apache2/ports.conf

cat > /etc/apache2/sites-enabled/000-default.conf <<EOF
<VirtualHost *:${PORT}>
    DocumentRoot /var/www/html
    <Directory /var/www/html>
        AllowOverride All
        Require all granted
    </Directory>
    ErrorLog \${APACHE_LOG_DIR}/error.log
    CustomLog \${APACHE_LOG_DIR}/access.log combined
</VirtualHost>
EOF

exec apache2-foreground
