# docker build -t gofabian/php-negotiation-middleware .

FROM composer:1.10.15 as composer

FROM php:7.4

COPY --from=composer /usr/bin/composer /usr/bin/composer

RUN pecl install xdebug-2.8.1 \
    && docker-php-ext-enable xdebug
