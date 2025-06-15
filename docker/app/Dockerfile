FROM php:8.2.28-fpm

# Install extensions
RUN apt-get update && apt-get install -y \
    libonig-dev libxml2-dev zip unzip git curl \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath

# Install Composer
COPY --from=composer:2.5 /usr/bin/composer /usr/bin/composer

# Set working dir
WORKDIR /var/www/html
