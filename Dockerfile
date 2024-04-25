FROM laratips/laravel10:latest

ENV APP_ENV=local
ENV APP_DEBUG=false
ENV PHP_MEMORY_LIMIT=128M

COPY . /var/www/html

RUN composer install --optimize-autoloader

RUN php artisan config:cache && \
    php artisan cache:clear && \
    php artisan route:cache && \
    php artisan view:cache && \
    php artisan migrate && \
    php artisan db:seed && \
    chmod 777 -R /var/www/html/storage/ && \
    chown -R www-data:www-data /var/www/ && \
    a2enmod rewrite
