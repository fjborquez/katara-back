FROM serversideup/php:8.3-fpm-apache

USER root
ENV APP_ENV=local
ENV APP_DEBUG=true
ENV PHP_MEMORY_LIMIT=512M
ENV HOST 0.0.0.0
EXPOSE 8080
RUN chown -R www-data:www-data /run
RUN chmod -R 755 /run

ENV TZ=America/Santiago
RUN ln -snf /usr/share/zoneinfo/$TZ /etc/localtime && echo $TZ > /etc/timezone
RUN printf '[PHP]\ndate.timezone = "America/Santiago"\n' > /usr/local/etc/php/conf.d/tzone.ini

USER www-data
COPY --chown=www-data:www-data . /var/www/html
RUN composer install --optimize-autoloader

RUN php artisan key:generate
RUN php artisan config:cache
RUN php artisan route:cache
RUN php artisan view:cache
RUN chmod 755 -R /var/www/html/storage/
RUN a2enmod rewrite
