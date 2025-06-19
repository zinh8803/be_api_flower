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
FROM php:8.2-fpm

# Install system dependencies
RUN apt-get update && apt-get install -y \
    libpng-dev libjpeg-dev libonig-dev libxml2-dev zip unzip curl git \
    && docker-php-ext-install pdo pdo_mysql mbstring exif pcntl bcmath gd

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www

# Copy project files
COPY . .

# Install Laravel dependencies
RUN composer install --no-dev --optimize-autoloader

# Laravel artisan commands
RUN php artisan config:cache && php artisan route:cache

# Expose port & start Laravel
EXPOSE 8000
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]
