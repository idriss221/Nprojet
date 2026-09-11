FROM composer:2 AS dependencies
WORKDIR /app
COPY composer.json composer.lock ./
RUN composer install --no-scripts --no-interaction --prefer-dist --optimize-autoloader

# On utilise l'image PHP-FPM officielle (pas Apache)
FROM php:8.3-fpm-alpine

# Installation de Nginx et des extensions PHP nécessaires
RUN apk add --no-cache nginx \
    && docker-php-ext-install pdo_mysql \
    && echo 'variables_order = "EGPCS"' > /usr/local/etc/php/conf.d/zz-app.ini

# Configuration de Nginx pour PHP-FPM
COPY docker/nginx.conf /etc/nginx/http.d/default.conf

WORKDIR /var/www/html
COPY . .
COPY --from=dependencies /app/vendor ./vendor

EXPOSE 80

# On lance à la fois PHP-FPM et Nginx
CMD php-fpm -D && nginx -g "daemon off;"
