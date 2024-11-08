# Utiliser une image PHP avec FPM (FastCGI Process Manager) pour Symfony
FROM php:8.1-fpm

# Installer les extensions PHP nécessaires
RUN apt-get update && apt-get install -y \
    libzip-dev \
    zip \
    unzip \
    git \
    && docker-php-ext-install pdo pdo_mysql

# Installer Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Définir le répertoire de travail
WORKDIR /var/www/html

# Copier le contenu du projet dans le conteneur
COPY . .

# Installer les dépendances Symfony
RUN composer install --prefer-dist --no-dev --no-scripts --no-progress --no-interaction

# Fixer les permissions
RUN chown -R www-data:www-data /var/www/html

RUN docker-php-ext-install pdo pdo_mysql


# Exposer le port utilisé par PHP-FPM
EXPOSE 9000

CMD ["php-fpm"]
