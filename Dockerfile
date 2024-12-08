FROM php:8.1-fpm

RUN apt-get update && apt-get install -y \
    libzip-dev \
    zip \
    unzip \
    git \
    curl \
    chromium \
    chromium-driver \
    && docker-php-ext-install pdo pdo_mysql

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html/api

COPY ./panier_malin_amiens_api /var/www/html/api

RUN composer install --no-scripts --no-dev --no-progress --no-interaction

RUN chown -R www-data:www-data /var/www/html/api

EXPOSE 9000

CMD ["php-fpm"]
