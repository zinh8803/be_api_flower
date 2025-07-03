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
# --- Base PHP image ---
FROM php:8.2-fpm

# Install system dependencies & PHP extensions
RUN apt-get update && apt-get install -y \
    nginx supervisor curl git unzip zip libpng-dev libjpeg-dev libfreetype6-dev libonig-dev libxml2-dev libzip-dev \
    && docker-php-ext-install pdo pdo_mysql mbstring exif pcntl bcmath zip

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www

# Copy Laravel project
COPY . .

# Install Laravel dependencies
RUN composer install --no-dev --optimize-autoloader

# Set permissions
RUN chown -R www-data:www-data /var/www \
    && chmod -R 775 /var/www/storage /var/www/bootstrap/cache

# Copy nginx config
COPY docker/nginx.conf /etc/nginx/nginx.conf

# Copy supervisor config
COPY docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# Expose Render-required port
EXPOSE 10000

# Start both nginx and php-fpm via supervisord
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=10000"]


