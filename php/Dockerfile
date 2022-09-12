FROM php:8.0-apache
RUN apt-get update
RUN apt-get install -y vim
RUN docker-php-ext-install pdo pdo_mysql
RUN pecl install xdebug && \
    docker-php-ext-enable xdebug

COPY php.ini /usr/local/etc/php/php.ini