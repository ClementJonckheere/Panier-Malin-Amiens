# Utiliser l'image officielle PHP avec PHP-FPM
FROM php:8.1-fpm

# Installer les extensions PHP nécessaires
RUN apt-get update && apt-get install -y \
    libzip-dev \
    zip \
    unzip \
    git \
    curl \
    chromium \
    chromium-driver \
    && docker-php-ext-install pdo pdo_mysql

# Installer Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Définir le répertoire de travail comme le dossier Symfony
WORKDIR /var/www/html/api

# Copier les fichiers nécessaires au conteneur
COPY ./panier_malin_amiens_api /var/www/html/api

# Installer les dépendances PHP avec Composer
RUN composer install --no-scripts --no-dev --no-progress --no-interaction

# Fixer les permissions pour éviter les problèmes d'écriture
RUN chown -R www-data:www-data /var/www/html/api

# Exposer le port utilisé par PHP-FPM
EXPOSE 9000

# Commande pour exécuter PHP-FPM
CMD ["php-fpm"]
