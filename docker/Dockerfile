FROM php:8.4-apache-bookworm

ARG UID=1000
ARG GID=1000

RUN apt-get update && apt-get install -y --no-install-recommends \
    git unzip libfreetype6-dev libjpeg62-turbo-dev libpng-dev \
    libicu-dev libonig-dev libzip-dev libxml2-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j"$(nproc)" \
        bcmath gd intl mbstring pdo_mysql zip dom simplexml xml xmlreader xmlwriter pcntl opcache \
    && a2enmod rewrite \
    && groupmod -o -g "$GID" www-data \
    && usermod -o -u "$UID" -g www-data www-data \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/local/bin/composer
COPY docker/apache/000-default.conf /etc/apache2/sites-available/000-default.conf
COPY docker/apache/php.ini /usr/local/etc/php/conf.d/local.ini

WORKDIR /var/www/html
