#!/bin/bash

# Chạy migrate (tuỳ chọn)
php artisan migrate --force &

# Chạy queue worker ở chế độ nền
php artisan queue:work --tries 3 --timeout 60 &

# Hoặc dùng queue:listen nếu không muốn mất job khi restart
# php artisan queue:listen --tries 3 --timeout 60 &

# Cuối cùng chạy server Laravel
php artisan serve --host=0.0.0.0 --port=9000
