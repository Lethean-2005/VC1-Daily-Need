FROM php:8.3-cli

RUN docker-php-ext-install pdo pdo_mysql

WORKDIR /app
COPY . .

# Railway injects $PORT at runtime — bind to it, and route every request
# through index.php (same front-controller pattern used by the app locally).
CMD php -S 0.0.0.0:${PORT:-8080} index.php
