
FROM composer:2 AS dependencies
WORKDIR /app
COPY composer.json composer.lock ./
RUN composer install --no-dev --no-scripts --no-interaction --prefer-dist --optimize-autoloader

FROM php:8.3-apache

RUN docker-php-ext-install pdo_mysql \
    && a2enmod rewrite \
    && echo 'variables_order = "EGPCS"' > /usr/local/etc/php/conf.d/zz-app.ini

ENV APACHE_DOCUMENT_ROOT=/var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf \
    -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

WORKDIR /var/www/html
COPY . .
COPY --from=dependencies /app/vendor ./vendor

EXPOSE 80
CMD ["apache2-foreground"]