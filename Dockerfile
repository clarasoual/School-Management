FROM php:8.2-fpm

RUN apt-get update && apt-get install -y nginx libssl-dev pkg-config \
    && rm -rf /var/lib/apt/lists/* \
    && pecl install mongodb \
    && docker-php-ext-enable mongodb \
    && docker-php-ext-install pdo pdo_mysql

COPY . /var/www/html/
COPY nginx.conf /etc/nginx/sites-available/default
COPY start.sh /start.sh

RUN chown -R www-data:www-data /var/www/html/ \
    && chmod -R 755 /var/www/html/ \
    && chmod +x /start.sh

EXPOSE 80

CMD ["/start.sh"]