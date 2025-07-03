FROM php:8.2-fpm

RUN apt-get update && apt-get install -y \
    nginx supervisor curl git unzip zip libpng-dev libjpeg-dev libfreetype6-dev libonig-dev libxml2-dev libzip-dev \
    && docker-php-ext-install pdo pdo_mysql mbstring exif pcntl bcmath zip

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

COPY . .

RUN composer install --no-dev --optimize-autoloader

RUN chown -R www-data:www-data /var/www \
    && chmod -R 775 /var/www/storage /var/www/bootstrap/cache

COPY [nginx.conf](http://_vscodecontentref_/2) /etc/nginx/nginx.conf
COPY [supervisord.conf](http://_vscodecontentref_/3) /etc/supervisor/conf.d/supervisord.conf
COPY docker/startup.sh /usr/local/bin/startup.sh

RUN chmod +x /usr/local/bin/startup.sh

EXPOSE 10000

CMD ["/usr/local/bin/startup.sh"]