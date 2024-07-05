FROM serversideup/php:8.3-fpm-apache

USER root
ENV APP_ENV=local
ENV APP_DEBUG=true
ENV PHP_MEMORY_LIMIT=512M
ENV HOST 0.0.0.0
EXPOSE 8080
RUN chown -R www-data:www-data /run
RUN chmod -R 755 /run
USER www-data

COPY --chown=www-data:www-data . /var/www/html
RUN composer install --optimize-autoloader

RUN php artisan key:generate
RUN php artisan config:cache
RUN php artisan cache:clear
RUN php artisan route:cache
RUN php artisan view:cache
RUN php artisan migrate
RUN php artisan db:seed
RUN chmod 755 -R /var/www/html/storage/
RUN a2enmod rewrite
