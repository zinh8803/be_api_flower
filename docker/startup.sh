#!/bin/bash

if [ "$RUN_QUEUE" = "true" ]; then
    # Chạy queue worker
    php artisan queue:work --sleep=3 --tries=3
else
    # Chạy web server (artisan serve hoặc supervisor)
    # php artisan serve --host=0.0.0.0 --port=10000
    /usr/bin/supervisord
fi