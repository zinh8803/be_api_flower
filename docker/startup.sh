#!/bin/bash

# Start queue worker in background
php artisan queue:work --sleep=3 --tries=3 &

# Đợi 2-3 giây cho worker khởi động (tùy ý)
sleep 3

# Start web server (artisan serve hoặc nginx+php-fpm)
php artisan serve --host=0.0.0.0 --port=10000