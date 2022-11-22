FROM php:8.1.12-apache

RUN apt-get update && apt-get install -y libpq-dev && docker-php-ext-install pgsql

COPY ./backend/ /var/www/html/
