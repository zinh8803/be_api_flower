FROM php:8.2-fpm

# Cài đặt các package cần thiết
RUN apt-get update && apt-get install -y \
    nginx \
    supervisor \
    curl \
    git \
    unzip \
    zip \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd pdo pdo_mysql mbstring exif pcntl bcmath zip

# Cài Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www

# Copy source code
COPY . .

# Cài đặt Laravel dependencies
RUN composer install --no-dev --optimize-autoloader

# Phân quyền thư mục
RUN chown -R www-data:www-data /var/www \
    && chmod -R 775 /var/www/storage /var/www/bootstrap/cache

# Copy file cấu hình
COPY docker/nginx.conf /etc/nginx/nginx.conf
COPY docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# Tạo thư mục logs nếu cần
RUN mkdir -p /var/log/supervisor

# Expose port
EXPOSE 8000

# Lệnh chạy chính
CMD ["/usr/bin/supervisord", "-n"]
