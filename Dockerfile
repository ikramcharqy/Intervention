FROM php:8.2-cli

# Extensions PHP necessaires a Laravel
RUN apt-get update && apt-get install -y \
    git unzip libzip-dev libpng-dev ca-certificates curl \
    && docker-php-ext-install pdo pdo_mysql zip gd

# Installer Node.js (pour compiler les assets Vite)
RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs

# Installer Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /app
COPY . .

# Installer les dependances PHP
RUN composer install --optimize-autoloader --no-dev

# Installer les dependances Node et builder les assets (Vite)
RUN npm install
RUN npm run build

EXPOSE 10000
CMD sh -c "php artisan config:clear && php artisan migrate --force && php artisan serve --host 0.0.0.0 --port 10000"