FROM php:8.1-fpm

# Installer les extensions PHP nécessaires
RUN apt-get update && apt-get install -y \
    libzip-dev \
    zip \
    unzip \
    git \
    curl \
    && docker-php-ext-install pdo pdo_mysql

# Installer Node.js
RUN curl -fsSL https://deb.nodesource.com/setup_16.x | bash - \
    && apt-get install -y nodejs

# Installer Chromium pour Puppeteer
RUN apt-get install -y chromium chromium-driver

# Définir Puppeteer pour utiliser Chromium du système (et éviter les téléchargements)
ENV PUPPETEER_EXECUTABLE_PATH=/usr/bin/chromium

# Vérifier les versions de Node.js et npm (utile pour déboguer)
RUN node -v && npm -v

# Installer Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Définir le répertoire de travail
WORKDIR /var/www/html

# Copier le contenu du projet dans le conteneur
COPY . .

# Installer les dépendances PHP avec Composer
RUN composer install --prefer-dist --no-dev --no-scripts --no-progress --no-interaction || true

# Fixer les permissions
RUN chown -R www-data:www-data /var/www/html

# Exposer le port utilisé par PHP-FPM
EXPOSE 9000

CMD ["php-fpm"]
