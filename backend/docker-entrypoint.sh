#!/bin/bash
set -e

# Run database migration in background or wait-and-run
(
  echo "Checking database migration..."
  php /var/www/html/database/migrate.php || true
) &

# Start Apache in foreground
exec apache2-foreground

