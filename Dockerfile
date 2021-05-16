# docker build -t gofabian/php-negotiation-middleware .

FROM composer:2 as composer

FROM php:8

COPY --from=composer /usr/bin/composer /usr/bin/composer

RUN pecl install xdebug-3.0.4 \
    && docker-php-ext-enable xdebug

RUN apt-get update \
    && apt-get install -y libzip-dev zip \
    && docker-php-ext-install zip