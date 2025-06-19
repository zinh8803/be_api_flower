# FROM php:8.2.28-fpm

# # Install extensions
# RUN apt-get update && apt-get install -y \
#     libonig-dev libxml2-dev zip unzip git curl \
#     && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath

# # Install Composer
# COPY --from=composer:2.5 /usr/bin/composer /usr/bin/composer

# RUN chmod -R 775 storage bootstrap/cache

# # Set working dir
# WORKDIR /var/www/html
FROM php:8.2-cli

# Install dependencies
RUN apt-get update && apt-get install -y \
    git curl zip unzip libonig-dev libxml2-dev libzip-dev \
    && docker-php-ext-install pdo pdo_mysql zip

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www

# Copy app code
COPY . .

# Install PHP dependencies
RUN composer install --no-dev --optimize-autoloader
RUN php artisan config:cache && php artisan route:cache
RUN chmod -R 775 storage bootstrap/cache

# Expose correct port
EXPOSE 10000

# Start Laravel app using built-in PHP server
CMD ["php", "-S", "0.0.0.0:10000", "-t", "public"]

